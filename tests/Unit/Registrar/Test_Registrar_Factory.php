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

namespace PinkCrab\Registerables\Tests\Unit\Registrar;

use Exception;
use PHPUnit\Framework\TestCase;
use PinkCrab\Registerables\Registrar\Registrar_Factory;
use PinkCrab\Registerables\Registrar\Taxonomy_Registrar;
use PinkCrab\Registerables\Tests\Fixtures\CPT\Basic_CPT;
use PinkCrab\Registerables\Registrar\Post_Type_Registrar;
use PinkCrab\Registerables\Registration_Middleware\Registerable;
use PinkCrab\Registerables\Tests\Fixtures\Taxonomies\Basic_Hierarchical_Taxonomy;

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
		$this->expectExceptionMessage( 'Invalid registerable (' . get_class( $registerable ) . ')type (no dispatcher exists)' );
		Registrar_Factory::new()->create_from_registerable( $registerable );
	}

	/**@testdox It should be possible to get a post type registrar by passing in a valid Registerable type. */
	public function test_can_create_post_type_registrar(): void {
		$registrar = Registrar_Factory::new()->create_from_registerable( new Basic_CPT() );
		$this->assertInstanceOf( Post_Type_Registrar::class, $registrar );
	}

	/**@testdox It should be possible to get a taxonomy registrar by passing in a valid Registerable type. */
	public function test_can_create_taxonomy_registrar(): void {
		$registrar = Registrar_Factory::new()->create_from_registerable( new Basic_Hierarchical_Taxonomy() );
		$this->assertInstanceOf( Taxonomy_Registrar::class, $registrar );
	}
}
