<?php

declare(strict_types=1);

/**
 * UNIT tests for the Registerable Middleware
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Unit\Registration_Middleware;

use stdClass;
use PHPUnit\Framework\TestCase;
use PinkCrab\Loader\Hook_Loader;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Registerables\Taxonomy;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Registerables\Registration_Middleware\Registerable;
use PinkCrab\Registerables\Registration_Middleware\Registerable_Middleware;

class Test_Registerable_Middleware extends TestCase {

	/** @testdox It should be possible to load the middleware with an instance of the hook loader. */
	public function test_can_populate_with_hook_loader(): void {
		$middleware = new Registerable_Middleware();

		$loader = $this->createMock( Hook_Loader::class );
		$middleware->set_hook_loader( $loader );

		$this->assertSame( $loader, Objects::get_property( $middleware, 'loader' ) );
	}

	/** @testdox It should be possible to load the middleware with an instance of the DI Container. */
	public function test_can_populate_with_di_container(): void {
		$middleware = new Registerable_Middleware();

		$container = $this->createMock( DI_Container::class );
		$middleware->set_di_container( $container );

		$this->assertSame( $container, Objects::get_property( $middleware, 'container' ) );
	}

	/** @testdox The middleware should skip any objects which are not registerable instances. */
	public function test_doesnt_process_none_registerable_objects(): void {
		$loader     = new Hook_Loader();
		$middleware = new Registerable_Middleware();
		$middleware->set_hook_loader( $loader );

		$middleware->process( new stdClass() );
		$this->assertEquals( 0, Objects::get_property( $loader, 'hooks' )->count() );
	}

	/** @testdox When registering a registerable, the register_xx function should be called on init. */
	public function test_registers_registerable(): void {
		$loader     = new Hook_Loader();
		$middleware = new Registerable_Middleware();
		$middleware->set_hook_loader( $loader );

		$middleware->process( $this->createMock( Taxonomy::class ) );

		// Extract just the hook handles form the loader.
		$hooks = Objects::get_property( $loader, 'hooks' )->export();
		$hooks = array_map(
			function( $e ) {
				return $e->get_handle();
			},
			$hooks
		);

		$this->assertCount( 1, $hooks );
		$this->assertContains( 'init', $hooks );
	}

	/** @testdox If an unhandled class which implements Registerable it should silently skip. */
	public function test_unhandled_registerable_silently_skips(): void {
		$loader     = new Hook_Loader();
		$middleware = new Registerable_Middleware();
		$middleware->set_hook_loader( $loader );
		$registerable = new class() implements Registerable {

		};
		$middleware->process( $registerable );

		$hooks = Objects::get_property( $loader, 'hooks' )->export();
		$this->assertEmpty( $hooks );
	}
}
