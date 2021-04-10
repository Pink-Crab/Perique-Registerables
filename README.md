# Registerables

A collection of Abstract Classes for creating common WordPress fixtires which need registering.

* Post Types
* Taxonomies
* Metaboxes
* WP_Ajax Call
* Meta Data

![alt text](https://img.shields.io/badge/Current_Version-0.4.2-yellow.svg?style=flat " ")

 
[![Open Source Love](https://badges.frapsoft.com/os/mit/mit.svg?v=102)](https://github.com/ellerbrock/open-source-badge/)

![](https://github.com/Pink-Crab/Module__Registerables/workflows/GitHub_CI/badge.svg " ")
[![codecov](https://codecov.io/gh/Pink-Crab/Module__Registerables/branch/master/graph/badge.svg?token=R3SB4WDL8Z)](https://codecov.io/gh/Pink-Crab/Module__Registerables)

For more details please visit our docs.
https://app.gitbook.com/@glynn-quelch/s/pinkcrab/

## Version ##

**Release 0.4.2**

## Why? ##

Creating many of WordPress's internal fixtures can sometimes be very verbose with large arrays of values which do not throw errors if incorrect. 

The PinkCrab Registerables module provides a small selection of Abstract Classes which can be extended and added to the registration system.  

## Setup ##

```bash 
$ composer require pinkcrab/registerables

``` 

If you are planning on using the Ajax abstracts, you will need to ensure that a valid PS7 ServerRequest is injected into your Ajax class. By default the nyholm PS7 library is included and we have a helper method in our HTTP helper class. Just copy the example below into your dependencies file.

Alternatively you can use Guzzle or any other HTTP library, so long as the isntance passed in implements the PS7 ServerRequestInterface.

```php
// file config/dependencies.php

// Ajax Request Injection.
Ajax::class       => array(
    'constructParams' => array( HTTP_Helper::global_server_request() ),
    'shared'          => true,
    'inherit'         => true,
),
```

## Dependencies ##

## Example ##

Creates a simple post type.

``` php
use PinkCrab\Registerables\Post_Type;

class Basic_CPT extends Post_Type {

	public $key      = 'basic_cpt';
	public $singular = 'Basic';
	public $plural   = 'Basics';
}
```

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

Creates a basic ajax call.

``` php
use PinkCrab\Registerables\Ajax;

class Simple_Ajax extends Ajax {
	// None key
    protected $nonce_handle = 'basic_ajax_get';
	
    // WP Ajax action
    protected $action       = 'basic_ajax_get';

	/**
	 * Handles the callback.
	 */
	public function callback( ResponseInterface $response ): ResponseInterface {
		$response_array = array( 'result' => $this->request->getQueryParams()['ajax_get_data'] );
		return $response->withBody(
			HTTP_Helper::stream_from_scalar( $response_array )
		);
	}
}
```

## Testing ##

### PHP Unit ###

If you would like to run the tests for this package, please ensure you add your database details into the test/wp-config.php file before running phpunit.

``` bash
$ phpunit
```

```bash 
$ composer test

``` 

### PHP Stan ###

The module comes with a pollyfill for all WP Functions, allowing for the testing of all core files. The current config omits the Dice file as this is not ours. To run the suite call.
```bash 
$ vendor/bin/phpstan analyse src/ -l8 
```

```bash 
$ composer analyse
```

## License ##

### MIT License ###

http://www.opensource.org/licenses/mit-license.html  

## Change Log ##

* 0.4.2 - Finalised Meta_Data, can now be added for Term and Post meta's when either CPT or taxonomy definied. Added in missing tests.
* 0.4.1 - Minor bugfixes
* 0.4.0 - Bumped inline with core, moved to min requirments of core v0.4.0
* 0.3.5 - Updated all code in src and tests to reflect the new Loader setup in core.
* 0.3.4 - Removed the use !function_exists('get_current_screen') as phpscoper cant create a pollyfill due to not being loaded in global wp scope until needed. Now has custom method in metabo class to avoid.
* 0.3.3 - Fixed version issue with Core
* 0.3.2 - Added in missing 'hierarchical' => $this->hierarchical for taxonomy registration
* 0.3.1 - Extended tests for 100 coverage.
* 0.3.0 - Finalised the move to composer, v2 was skipped as larger internal changes made. External API remained unchanged
* 0.2.beta - Moved to composer, removed Guzzle for nyholm ps7 in its place. Uses HTTP helper for PS7 responses and tests now include form-urlend requests.



