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
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Perique\Application\App_Factory;
use PinkCrab\Registerables\Module\Registerable as Registerable_Module;

trait App_Helper_Trait {

	/**
	 * Resets the any existing App instance with default properties.
	 *
	 * @return void
	 */
	protected static function unset_app_instance(): void {
		$app = new App( \FIXTURES );
		Objects::set_property( $app, 'app_config', null );
		Objects::set_property( $app, 'container', null );
		Objects::set_property( $app, 'module_manager', null );
		Objects::set_property( $app, 'loader', null );
		Objects::set_property( $app, 'booted', false );
		$app = null;
	}

	/**
	 * Pre populated App Factory instance, all loaded and ready to boot.
	 *
	 * @param string ...$class
	 * @return App_Factory
	 */
	protected static function create_with_registerables( string ...$class ): App_Factory {

		$factory = new App_Factory( \FIXTURES );
		$factory->set_base_view_path( \FIXTURES . '/Views' );
		$factory->default_setup();
		$factory->module( Registerable_Module::class );
		$factory->registration_classes( $class );

		return $factory;
	}

}
