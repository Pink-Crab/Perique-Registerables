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

use Exception;
use WP_UnitTestCase;
use PinkCrab\Registerables\Meta_Data;
use PinkCrab\Registerables\Post_Type;
use PinkCrab\Registerables\Registrar\Meta_Data_Registrar;


class Test_Meta_Data_Registrar extends WP_UnitTestCase {

	/** @testdox Any meta data which has no subtype applied for a post object, should bail early. */
	public function test_skip_if_no_subtype_defined(): void {

		// Clear all existing filters.
		$GLOBALS['wp_filter']['rest_api_init']->callbacks = array();

		// Register with no subtype defined.
		$meta      = ( new Meta_Data( 'meta_key' ) )
			->meta_type( 'post' )
			->rest_schema(
				array(
					'type'        => 'number',
					'description' => 'test 2',
					'default'     => 3.245,
				)
			);
		$registrar = new Meta_Data_Registrar();
		$registrar->register_meta_rest_field( $meta );

		// Should not have added the rest field.
		$this->assertEmpty( $GLOBALS['wp_filter']['rest_api_init']->callbacks );
	}

	public function test_throw_exception_if_error_registering_meta_data(): void {
		// Turn off doing_it_wrong, to trigger the silent failure.
		add_filter( 'doing_it_wrong_trigger_error', '__return_false' );

		$this->expectErrorMessage( 'Failed to register meta_key (meta) for page of post type' );
		$this->expectException( Exception::class );

		// Create meta that il fail to register as an array with schema, but no item definition.
		$meta = ( new Meta_Data( 'meta_key' ) )
			->post_type( 'page' )
			->type( 'array' )
			->rest_schema(
				array(
					'type' => 'array',
				)
			);
		
		$registrar = new Meta_Data_Registrar();
		$registrar->register_for_post_type( $meta, 'page' );
		
		add_filter( 'doing_it_wrong_trigger_error', '__return_true' );
	}

	// Hide errors with forcing doing_it_wrong
	public function expectedDeprecated() {}
}
