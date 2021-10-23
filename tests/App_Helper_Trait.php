<?php

declare(strict_types=1);

/**
 * Helper trait for all App tests
 * Includes clearing the internal state of an existing instance.
 *
 * @since 0.4.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests;

use PinkCrab\Perique\Application\App;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Registerables\Registration_Middleware\Registerable_Middleware;
use PinkCrab\Loader\Hook_Loader;
use Dice\Dice;
use PinkCrab\Perique\Services\Dice\PinkCrab_Dice;
use PinkCrab\Perique\Services\Registration\Registration_Service;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique\Interfaces\Renderable;
use PinkCrab\Perique\Services\View\PHP_Engine;

trait App_Helper_Trait {

	/**
	 * Resets the any existing App instance with default properties.
	 *
	 * @return void
	 */
	protected static function unset_app_instance(): void {
		$app = new App();
		Objects::set_property( $app, 'app_config', null );
		Objects::set_property( $app, 'container', null );
		Objects::set_property( $app, 'registration', null );
		Objects::set_property( $app, 'loader', null );
		Objects::set_property( $app, 'booted', false );
		$app = null;
	}

	protected static function create_with_registerables( string ...$class ): App {
		// Build and populate the app.
		$app          = new App();
		$registration = new Registration_Service();
		$container    = new PinkCrab_Dice( new Dice() );
		$loader       = new Hook_Loader();

		$app->set_container( $container );
		$app->set_registration_services( $registration );
		$app->set_loader( $loader );
		$app->construct_registration_middleware( Registerable_Middleware::class );
		if ( ! empty( $class ) ) {
			$app->registration_classes( $class );
		}
		$app->set_app_config( array() );

		$container->addRules(
			array(
				'*' => array(
					'substitutions' => array(
						Renderable::class => new PHP_Engine( \FIXTURES . '/Views' ),
					),
				),
			)
		);

		return $app;
	}

}
