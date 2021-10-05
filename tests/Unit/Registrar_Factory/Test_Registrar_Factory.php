<?php

declare(strict_types=1);

/**
 * UNIT tests for the Registrar Factory
 *
 * @since 0.6.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Unit\Registrar_Factory;

use Exception;
use PHPUnit\Framework\TestCase;
use PinkCrab\Registerables\Registrar\Registrar_Factory;
use PinkCrab\Registerables\Tests\Fixtures\CPT\Basic_CPT;
use PinkCrab\Registerables\Registrar\Post_Type_Registrar;
use PinkCrab\Registerables\Registration_Middleware\Registerable;

class Test_Registrar_Factory extends TestCase {

	/** @testdox It should be possible to create the factory using a static method for fluent chaining. */
	public function test_can_use_static_constructor(): void {
		$factory = Registrar_Factory::new();
		$this->assertInstanceOf( Registrar_Factory::class, $factory );
	}

	/** @testdox When trying to create a registrar, any unknown registerable type should result in an exception. */
	public function test_throws_exception_if_unknown_registerable(): void {
		$registerable = $this->createMock( Registerable::class );
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Invalid registerable type (no dispatcher exists)' );
		Registrar_Factory::new()->create_from_registerable( $registerable );
	}

	/**@testdox It should be possible to get a post type dispatcher by passing in a valid Registerable type. */
	public function test_can_create_post_type_dispatcher(): void {
		$dispatcher = Registrar_Factory::new()->create_from_registerable( new Basic_CPT );
		$this->assertInstanceOf( Post_Type_Registrar::class, $dispatcher );
	}
}
