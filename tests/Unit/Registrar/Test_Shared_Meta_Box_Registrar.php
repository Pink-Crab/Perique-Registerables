<?php

declare(strict_types=1);

/**
 * Unit tests for the Shared Meta Box registrar
 *
 * @since 0.7.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Unit\Registrar;

use Exception;
use PHPUnit\Framework\TestCase;
use PinkCrab\Loader\Hook_Loader;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Registerables\Meta_Box;
use PinkCrab\Registerables\Meta_Data;
use PinkCrab\Perique\Services\View\View;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Registerables\Registrar\Meta_Box_Registrar;
use PinkCrab\Registerables\Validator\Meta_Box_Validator;
use PinkCrab\Registerables\Registrar\Shared_Meta_Box_Registrar;
use PinkCrab\Registerables\Registration_Middleware\Registerable;
use PinkCrab\Registerables\Tests\Fixtures\Shared_Metabox\Post_Page_Meta_Box;

class Test_Shared_Meta_Box_Registrar extends TestCase {

	/** @testdox Attempting to pass a none shared meta box controller to the registrar should skip the class */
	public function test_skips_none_shared_meta_boxes(): void {
		$registered = false;

		$mb_registrar = $this->createMock( Meta_Box_Registrar::class );
		$mb_registrar->method( 'register' )
			->with()
			->will(
				$this->returnCallback(
					function( $meta_box ) use ( &$registered ) {
						$registered = true;
					}
				)
			);

		$registrar = new Shared_Meta_Box_Registrar( $mb_registrar );

		$registrar->register( $this->createMock( Registerable::class ) );

		$this->assertFalse( $registered );
		// $registrar->register( new Post_Page_Meta_Box );
	}

	/** @testdox Passing a valid shared meta box controller to the registrar should register it. */
	public function test_registers_shared_meta_boxes(): void {
		$registered = false;

		$mb_registrar = $this->createMock( Meta_Box_Registrar::class );
		$mb_registrar->method( 'register' )
			->with()
			->will(
				$this->returnCallback(
					function( $meta_box ) use ( &$registered ) {
						$registered = true;
					}
				)
			);

		$registrar = new Shared_Meta_Box_Registrar( $mb_registrar );

		$registrar->register( new Post_Page_Meta_Box );

		$this->assertTrue( $registered );
	}

	/** @testdox When a meta boxes, meta data is registered, all none Meta_Data object should be removed using a filter. */
	public function test_filter_meta_data(): void {
		$data = array(
			new Meta_Data( 'one' ),
			new \stdClass(),
			new Meta_Data( 'two' ),
		);

		$registrar = new Shared_Meta_Box_Registrar(
			$this->createMock( Meta_Box_Registrar::class )
		);

		$filtered = Objects::invoke_method( $registrar, 'filter_meta_data', array( $data ) );

		$this->assertCount( 2, $filtered );
		$this->assertContains( $data[0], $filtered );
		$this->assertContains( $data[2], $filtered );
	}

	/** @testdox When meta data is registered, if any errors are created, an exception should be thrown. */
	public function test_throws_exception_for_invalid_meta_data(): void {
		// Mock the meta data to throw and exception.
		$mock_meta_data = $this->createMock( Meta_Data::class );
		$mock_meta_data->method( 'get_meta_key' )
			->will(
				$this->returnCallback(
					function() {
						throw new \Exception( 'ERROR_INVALID_META_DATA', 1 );

					}
				)
			);

		// Mock the controller to return the mock meta data.
		$mock_controller = $this->createMock( Post_Page_Meta_Box::class );
		$mock_controller->method( 'meta_data' )->willReturn( array( $mock_meta_data ) );
		$mock_controller->method( 'meta_box' )
			->willReturn(
				Meta_Box::side( 'test' )->screen( 'post' )
			);

		// Create the registrar with mock validator
		$mb_registrar = $this->createMock( Meta_Box_Registrar::class );
		$registrar    = new Shared_Meta_Box_Registrar( $mb_registrar );

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'ERROR_INVALID_META_DATA' );
		$registrar->register( $mock_controller );
	}
}
