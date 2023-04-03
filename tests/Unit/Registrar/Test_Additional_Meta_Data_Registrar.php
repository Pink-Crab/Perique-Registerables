<?php

declare(strict_types=1);

/**
 * Unit tests for the Meta Box registrar
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Unit\Registrar;

use WP_UnitTestCase;
use PinkCrab\Registerables\Meta_Data;
use PinkCrab\Registerables\Registrar\Meta_Data_Registrar;
use PinkCrab\Registerables\Additional_Meta_Data_Controller;
use PinkCrab\Registerables\Module\Middleware\Registerable;
use PinkCrab\Registerables\Registrar\Additional_Meta_Data_Registrar;

class Test_Additional_Meta_Data_Registrar extends WP_UnitTestCase {

	/**
	 * @testdox Attempting to use any other registrar other than Additional_Meta_Data_Controller should see an exception.
	 */
	public function test_only_handles_correct_registrar(): void {
		$this->expectExceptionMessage( 'Registerable must be an instance of Additional_Meta_Data_Controller' );
		$this->expectException( \Exception::class );

		$registrar = new Additional_Meta_Data_Registrar( new Meta_Data_Registrar() );
		$registrar->register( $this->createMock( Registerable::class ) );
	}

	/** @testdox Attempting to register meta which isn't USER, TERM, POST or COMMENT should result in an error */
	public function test_throws_exception_for_unknown_meta_types(): void {
		$this->expectExceptionMessage( 'Unexpected meta type' );
		$this->expectException( \Exception::class );
		
		$registrar = new Additional_Meta_Data_Registrar( new Meta_Data_Registrar() );

		$controller = new class() extends Additional_Meta_Data_Controller {
			public function meta_data( array $array ): array {
				return array( ( new Meta_Data( 'ff' ) )->meta_type( 'UNKNOWN' ) );
			}
		};

		$registrar->register( $controller );
	}

	/** @testdox Attempting to register post meta with no defined post type should result in an exception being thrown. */
	public function test_throws_exception_if_post_meta_has_no_post_type(): void {
		$this->expectExceptionMessage( 'A post type must be defined when attempting to register post meta with meta key : mock_post_meta' );
		$this->expectException( \Exception::class );
		
		$registrar = new Additional_Meta_Data_Registrar( new Meta_Data_Registrar() );

		$controller = new class() extends Additional_Meta_Data_Controller {
			public function meta_data( array $array ): array {
				return array( ( new Meta_Data( 'mock_post_meta' ) )->meta_type( 'post' ) );
			}
		};

		$registrar->register( $controller );
	}

	/** @testdox Attempting to register term meta with no defined taxonomy should result in an exception being thrown. */
	public function test_throws_exception_if_term_meta_has_no_taxonomy(): void {
		$this->expectExceptionMessage( 'A taxonomy must be defined when attempting to register tern meta with meta key : mock_term_meta' );
		$this->expectException( \Exception::class );
		
		$registrar = new Additional_Meta_Data_Registrar( new Meta_Data_Registrar() );

		$controller = new class() extends Additional_Meta_Data_Controller {
			public function meta_data( array $array ): array {
				return array( ( new Meta_Data( 'mock_term_meta' ) )->meta_type( 'term' ) );
			}
		};

		$registrar->register( $controller );
	}
}
