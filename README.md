# Registerables

![Current Version 0.7.1](https://img.shields.io/badge/Current_Version-0.7.1-yellow.svg?style=flat " ") 
[![Open Source Love](https://badges.frapsoft.com/os/mit/mit.svg?v=102)](https://github.com/ellerbrock/open-source-badge/)
[![GitHub_CI](https://github.com/Pink-Crab/Perique-Registerables/actions/workflows/php.yaml/badge.svg)](https://github.com/Pink-Crab/Perique-Registerables/actions/workflows/php.yaml)
[![codecov](https://codecov.io/gh/Pink-Crab/Perique-Registerables/branch/master/graph/badge.svg?token=R3SB4WDL8Z)](https://codecov.io/gh/Pink-Crab/Perique-Registerables)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Pink-Crab/Perique-Registerables/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Pink-Crab/Perique-Registerables/?branch=master)

A collection of Abstract Classes for creating common WordPress fixtures which need registering.

* Post Types
* Taxonomies
* Meta boxes
* Meta Data


## Version ##

**Release 0.7.1**

> For older versions of the PinkCrab Plugin Framework please use Registerables V0.5.\*

## Why? ##

Creating many of WordPress's internal fixtures can sometimes be very verbose with large arrays of values which do not throw errors if incorrect. 

The PinkCrab Registerables module provides a small selection of Abstract Classes which can be extended and added to the registration system.  

## Setup ##

```bash 
$ composer require pinkcrab/registerables

``` 

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

The module comes with a polyfill for all WP Functions, allowing for the testing of all core files. The current config omits the Dice file as this is not ours. To run the suite call.
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



