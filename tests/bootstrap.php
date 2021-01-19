<?php

use PinkCrab\HTTP\HTTP;
use GuzzleHttp\Psr7\Response;
use PinkCrab\Registerables\Ajax;
use GuzzleHttp\Psr7\ServerRequest;
use PinkCrab\Core\Application\App;
use PinkCrab\Core\Services\Dice\WP_Dice;
use PinkCrab\Core\Services\Dice\Dice; //@TODO!!!
use PinkCrab\Core\Services\ServiceContainer\Container;

/**
 * PHPUnit bootstrap file
 */

// Composer autoloader must be loaded before WP_PHPUNIT__DIR will be available
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Give access to tests_add_filter() function.
require_once getenv( 'WP_PHPUNIT__DIR' ) . '/includes/functions.php';

tests_add_filter(
	'muplugins_loaded',
	function() {
		$app = App::init( new Container() );
		$di  = WP_Dice::constructWith( new Dice() );
		$di->addRules(
			array(
				Ajax::class => array(
					'constructParams' => array( ( new HTTP() )->request_from_globals() ),
					'shared'          => true,
					'inherit'         => true,
				),
			)
		);
		$app->set( 'di', $di );
	}
);

// Start up the WP testing environment.
require getenv( 'WP_PHPUNIT__DIR' ) . '/includes/bootstrap.php';
