# Registerables


[![Latest Stable Version](http://poser.pugx.org/pinkcrab/registerables/v)](https://packagist.org/packages/pinkcrab/registerables)
[![Total Downloads](http://poser.pugx.org/pinkcrab/registerables/downloads)](https://packagist.org/packages/pinkcrab/registerables)
[![License](http://poser.pugx.org/pinkcrab/registerables/license)](https://packagist.org/packages/pinkcrab/registerables)
[![PHP Version Require](http://poser.pugx.org/pinkcrab/registerables/require/php)](https://packagist.org/packages/pinkcrab/registerables)
![GitHub contributors](https://img.shields.io/github/contributors/Pink-Crab/Perique-Registerables?label=Contributors)
![GitHub issues](https://img.shields.io/github/issues-raw/Pink-Crab/Perique-Registerables)
[![WordPress 5.9 Test Suite [PHP7.2-8.1]](https://github.com/Pink-Crab/Perique-Registerables/actions/workflows/WP_5_9.yaml/badge.svg)](https://github.com/Pink-Crab/Perique-Registerables/actions/workflows/WP_5_9.yaml)
[![WordPress 6.0 Test Suite [PHP7.2-8.1]](https://github.com/Pink-Crab/Perique-Registerables/actions/workflows/WP_6_0.yaml/badge.svg)](https://github.com/Pink-Crab/Perique-Registerables/actions/workflows/WP_6_0.yaml)
[![WordPress 6.1 Test Suite [PHP7.2-8.1]](https://github.com/Pink-Crab/Perique-Registerables/actions/workflows/WP_6_1.yaml/badge.svg)](https://github.com/Pink-Crab/Perique-Registerables/actions/workflows/WP_6_1.yaml)
[![codecov](https://codecov.io/gh/Pink-Crab/Perique-Registerables/branch/master/graph/badge.svg?token=R3SB4WDL8Z)](https://codecov.io/gh/Pink-Crab/Perique-Registerables)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Pink-Crab/Perique-Registerables/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Pink-Crab/Perique-Registerables/?branch=master)
[![Maintainability](https://api.codeclimate.com/v1/badges/19fd3d66720b0c94424d/maintainability)](https://codeclimate.com/github/Pink-Crab/Perique-Registerables/maintainability)

A collection of Abstract Classes for creating common WordPress fixtures which need registering.

- [Registerables](#registerables)
  - [Why?](#why)
  - [Setup](#setup)
  - [Post Type](#post-type)
  - [Taxonomy](#taxonomy)
  - [Meta Box](#meta-box)
    - [Shared Meta Boxes](#shared-meta-boxes)
  - [MetaData](#metadata)
    - [Additional\_Meta\_Data\_Controller](#additional_meta_data_controller)
  - [Contributions](#contributions)
    - [To run test suite](#to-run-test-suite)
  - [License](#license)
    - [MIT License](#mit-license)
  - [Previous Versions](#previous-versions)
  - [Change Log](#change-log)

> ## For Perique V1.4.*


## Why? ##

WordPress has a number of Registerable functions for Post Types, Post Meta and Taxonomies. These tend to require large arrays of arguments to be defined. This library provides Classes which can be registered and used with the Registration process.

## Setup ##

```bash 
$ composer require pinkcrab/registerables

``` 

You need to include the module and the Registerable_Middleware. They come with their own dependencies which will need to be added using the construct_registration_middleware() from the App_Factory instance.
```php
$app = ( new PinkCrab\Perique\Application\App_Factory() )
  // Normal Perique bootstrapping.   
  ->construct_registration_middleware( Registerable_Middleware::class );
  ->boot();
```

Once the middleware has been included, you can use Post_Type, Taxonomies, Meta Data and Meta boxes as part of the usual Registration process

## Post Type

Creates a simple post type.

``` php
use PinkCrab\Registerables\Post_Type;

class Basic_CPT extends Post_Type {

  public $key      = 'basic_cpt';
  public $singular = 'Basic';
  public $plural   = 'Basics';
}
```
 
[See full Post Type docs](docs/Post-Type.md)

## Taxonomy

Creates a flat taxonomy for the **Post** Post Type.

``` php
use PinkCrab\Registerables\Taxonomy;

class Basic_Tag_Taxonomy extends Taxonomy {
  public $slug         = 'basic_tag_tax';
  public $singular     = 'Basic Tag Taxonomy';
  public $plural       = 'Basic Tag Taxonomies';
  public $description  = 'The Basic Tag Taxonomy.';
  public $hierarchical = false;
  public $object_type = array( 'post' );
}
```

[See full Taxonomy Docs](docs/Taxonomy.md)

## Meta Box

Create a simple meta box as part of a post type definition.
```php
class My_CPT extends Post_Type {
  public $key      = 'my_cpt';
  public $singular = 'CPT Post';
  public $plural   = 'CPT Posts';

  public function meta_boxes( array $meta_boxes ): array {
    $meta_boxes = MetaBox::side('my_meta_box')
      ->label('My Meta Box')
      ->view_template($template_path)
      ->view_vars($additional_view_data)
      ->action('save_post', [$this, 'save_method'])
      ->action('update_post', [$this, 'save_method'])
  }
}
```

> **If your meta box has any level of complexity, it is recommended to create a separate service which handles this and inject it into the Post_Type class.**
> 
```php
/** The Meta Box Service */
class Meta_Box_Service {
  public function get_meta_boxes(): array {
    $meta_boxes = array();
    $meta_boxes[] = MetaBox::side('my_meta_box')
      ->label('My Meta Box')
      ->view_template($template_path)
      ->view_vars($additional_view_data)
      ->action('save_post', [$this, 'save_method'])
      ->action('update_post', [$this, 'save_method'])
  }

  public function save_method( int $post_id ): array {
    // Handle validating and updating post meta.
  }
}

/** Injected into post type */
class My_CPT extends Post_Type {
  public $key      = 'my_cpt';
  public $singular = 'CPT Post';
  public $plural   = 'CPT Posts';

  // Pass the service in as a dependency.
  private Meta_Box_Service $meta_box_service;
  
  public function __construct(Meta_Box_Service $meta_box_service){
    $this->meta_box_service = $meta_box_service;
  }

  // Return the populated Meta_Box instances.
  public function meta_boxes( array $meta_boxes ): array {
    return $this->meta_box_service->get_meta_boxes();
  }
}
```

[See full Meta Box Docs](docs/Meta_Box.md)

### Shared Meta Boxes

In case you would like to render the same meta box on multiple Custom Post Types or to add it to existing ones, you can use the `Shared_Meta_Box_Controller` base class and extend it to register independent meta boxes.


```php
class Acme_Meta_Box extends Shared_Meta_Box_Controller {
  /**
   * Return the Meta Box instance.
   */
  public function meta_box(): Meta_Box {
    return Meta_Box::side('acme_box')
      ->label('Acme Meta Box')
      ->screen('acme_post_type_a')
      ->screen('acme_post_type_b')
      ->view_template($template_path)
      ->view_vars($additional_view_data)
      ->action('save_post', [$this, 'save_method'])
      ->action('update_post', [$this, 'save_method'])
  }

  /**
   * Sets any metadata against the meta box.
   * @see Post Type docs for more details
   */
  public function meta_data( array $meta_data ): array {
    $meta_data[] = ( new Meta_Data( 'acme_meta_1' ) )
      ->type( 'integer' )
        ->single......
 
    return $meta_data;
  }

  /** The save_post and update_post hook callback */
  public function save_method( int $post_id ): array {
    // Handle validating and updating post meta.
    update_post_meta($post_id, 'acme_meta_1', $value);
  }
}
```
**[Defining Meta Data](docs/Post-Type.md#registering-meta_data)**

> The above Meta Box would be shown on both `acme_post_type_a` and `acme_post_type_b`  
> You can also inject any dependencies via the constructor too.

## MetaData

You can register `post`, `term`, `user` and `comment` meta fields either as a part of Post Types/Taxonomy Registerables or on there own. This fluent object based definition makes it easy to create these inline.

You can add full REST support by supplying a schema for the field and the Registrar will register the field also.

```php
class Additional_Post_Meta extends Additional_Meta_Data_Controller {
  /** Define the Meta Data */
  public function meta_data(array $meta_data): array {
    $meta_data[] = (new Meta_Data('meta_key'))
      ->post_type('post')
      ->default('foo')
      ->description($description)
      ->single()
      ->sanitize('sanitize_text_field')
      ->rest_schema(['type' => 'string']);

    return $meta_data;
  }
}
```

[See full Meta Data Docs](docs/Meta_Data.md)  

You can also define MetaData for [Post Types](docs/Post-Type.md#registering-meta_data) and [Taxonomies](docs/Taxonomy.md#registering-meta_data) when creating them.

### Additional_Meta_Data_Controller

To register standalone Meta_Data, you can use the `Additional_Meta_Data_Controller` which has a single method `meta_data(array $meta_data): array`. Like in the [example above](#meta-data), you add your Meta_Data instances to the array and return.

The class has an empty constructor, so you can easily inject dependencies in and make use of the `App_Config` meta options.

## Contributions

If you would like to contribute to this project, please feel free to fork and submit a PR. If any issue doesn't exist for the problem, please create one.

Please ensure your changes to do not drop coverage lower than they currently are, unless it can not be helped (include a reason why)

### To run test suite

Setup the dev environment
`$ composer install`

* `$ composer all` - This will run all the tests, static analysis and linter
* `$ composer coverage` - This will produce a HTML coverage report `../coverage-report`
* `$ composer analyse` - This will run PHPStan on lv 8
* `$ composer sniff` - This will run PHPCS with the WP Rule set.

> Please note the CI Actions runs `composer all` on multiple PHP and WP versions. Running locally will only run with your version of PHP and latest major or WP.

## License ##

### MIT License ###

http://www.opensource.org/licenses/mit-license.html  

## Previous Versions ##
* For Perique 1.3.* please use version Registerables 0.9.*  
* For Perique 1.0.* - 1.2.* please use Registerables version 0.8.*  

## Change Log ##
* 1.0.0 - Bumped support for Perique 1.4.0 and finally released as 1.0.0
* 0.9.0 - Move to compatible with Perique 1.3.*, Fixed bug where post types that use Gutenberg do not set meta_cap to true by default.
* 0.8.2 - Fixed bug with Taxonomy Capabilities to not use fallbacks if not defined.
* 0.8.1 - Update dev deps to cover wp6.0.0
* 0.8.0 - 
   * Renamed `Post_Type::$templates` to `Post_Type::$template`
   * Update docs
* 0.7.2 - Improved Meta Data REST support.
* 0.7.1 - Fixed bug where meta box hooks didn't fire due to race conditions getting current screen on init. Now deferred loading to meta box hooks on current_screen action. Extended support for RestSchema including use of the PinkCrab WP Rest Schema lib. Various other small fixes.
* 0.7.0 - Introduced the Shared Meta Box Controller for registering meta boxes and meta data for shared post types.
* 0.6.4 - Added in a filter for meta boxes which allows for setting of view args based on the current post being displayed. Allows for passing of meta values to the view, without using get_post_meta in the view it self.
* 0.6.3 - Now generates meaningful errors when Post Type, Taxonomy or Meta Box fails validation.
* 0.6.2 - Removed issue where Meta_Box Registrar was trying to create an instance of Renderable not View.
* 0.6.1 - Removed old code and tests
* 0.6.0 - Now works with Perique 1.0.0 and upwards. Added in Registration middleware and uses own registrars and validators rather than being part of the the base models.
* 0.5.0 - 
  * Updated to reflect Perique (Plugin Framework) 0.5.0
  * Remove Ajax from registerables
* 0.4.4 - Added wp_die() after emitting psr7 response in ajax.
* 0.4.3 - Fixed merge issue with meta box view data.
* 0.4.2 - Finalised Meta_Data, can now be added for Term and Post meta's when either CPT or taxonomy defined. Added in missing tests.
* 0.4.1 - Minor bugfixes
* 0.4.0 - Bumped inline with core, moved to min requirements of core v0.4.0
* 0.3.5 - Updated all code in src and tests to reflect the new Hook_Loader setup in core.
* 0.3.4 - Removed the use !function_exists('get_current_screen') as phpScoper cant create a pollyfill due to not being loaded in global wp scope until needed. Now has custom method in meta box class to avoid.
* 0.3.3 - Fixed version issue with Core
* 0.3.2 - Added in missing 'hierarchical' => $this->hierarchical for taxonomy registration
* 0.3.1 - Extended tests for 100 coverage.
* 0.3.0 - Finalised the move to composer, v2 was skipped as larger internal changes made. External API remained unchanged
* 0.2.beta - Moved to composer, removed Guzzle for nyholm ps7 in its place. Uses HTTP helper for PS7 responses and tests now include form-urlencode requests.



