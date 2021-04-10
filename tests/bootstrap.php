<?php

use Dice\Dice;
use PinkCrab\HTTP\HTTP;
use PinkCrab\Registerables\Ajax;
use PinkCrab\Core\Application\App;
use PinkCrab\Core\Services\Dice\WP_Dice;
use PinkCrab\Core\Application\App_Factory;
use PinkCrab\Core\Interfaces\DI_Container;
use PinkCrab\Core\Services\ServiceContainer\Container;

/**
 * PHPUnit bootstrap file
 */

// Composer autoloader must be loaded before WP_PHPUNIT__DIR will be available
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Give access to tests_add_filter() function.
require_once getenv( 'WP_PHPUNIT__DIR' ) . '/includes/functions.php';

$wp_install_path = dirname( __FILE__, 2 ) . '/wordpress';
define( 'TEST_WP_ROOT', $wp_install_path );

tests_add_filter(
	'muplugins_loaded',
	function() {
		$app = ( new App_Factory )->with_wp_dice( true )
		->di_rules(
			array(
				Ajax::class => array(
					'constructParams' => array( ( new HTTP() )->request_from_globals() ),
					'shared'          => true,
					'inherit'         => true,
				),
			)
		)->boot();
	}
);

// Start up the WP testing environment.
require getenv( 'WP_PHPUNIT__DIR' ) . '/includes/bootstrap.php';

