<?php

declare(strict_types=1);

/**
 * Unit tests for the Post Type registrar
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Unit\Registrar;

use Exception;
use PHPUnit\Framework\TestCase;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Registerables\Meta_Data;
use PinkCrab\Registerables\Post_Type;
use PinkCrab\Registerables\Tests\Fixtures\CPT\Basic_CPT;
use PinkCrab\Registerables\Registrar\Meta_Data_Registrar;
use PinkCrab\Registerables\Registrar\Post_Type_Registrar;
use PinkCrab\Registerables\Validator\Post_Type_Validator;
use PinkCrab\Registerables\Registration_Middleware\Registerable;

class Test_Post_Type_Registrar extends TestCase {

	/** @testdox If a Post Type fails validation and exception should be thrown */
	public function test_fails_validation_if_none_post_type_registerable(): void {

		$validator = $this->createMock( Post_Type_Validator::class );
		$validator->method( 'validate' )->willReturn( false );
		$validator->method( 'get_errors' )->willReturn( array( 'error1', 'error2' ) );
		$md_registrar = $this->createMock( Meta_Data_Registrar::class );

		$registrar = new Post_Type_Registrar( $validator, $md_registrar );

		$post_type = $this->createMock( Registerable::class );

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Failed validating post type model(' . get_class( $post_type ) . ') with errors: error1, error2' );
		$registrar->register( $post_type );
	}

	/** @testdox If a WP_Error class is returned when registering the post type, this should be translated into an exception */
	public function test_throws_exception_if_register_post_type_returns_wp_error(): void {

		$validator = $this->createMock( Post_Type_Validator::class );
		$validator->method( 'validate' )->willReturn( true );
		$md_registrar = $this->createMock( Meta_Data_Registrar::class );

		$registrar = new Post_Type_Registrar( $validator, $md_registrar );

		$post_type = new class() extends Post_Type {
			// Name is capped between 1 and 20
			public $key      = '0123456789012345678901234567890123456789';
			public $singular = '0123456789012345678901234567890123456789';
			public $plural   = '0123456789012345678901234567890123456789';
		};

		$this->expectException( \Exception::class );

		// Based on the phpunit version.
		if ( \method_exists( $this, 'expectExceptionMessageMatches' ) ) {
			$this->expectExceptionMessageMatches( '#Failed to register 0123456789012345678901234567890123456789 post type (.*)$#' );
		} else {
			$this->expectExceptionMessageRegExp( '#Failed to register 0123456789012345678901234567890123456789 post type (.*)$#' );
		}
		$registrar->register( $post_type );
	}

	/** @testdox When compiled, all args should be set based on the post type defined. */
	public function test_can_compile_args(): void {
		$cpt       = new Basic_CPT();
		$validator = $this->createMock( Post_Type_Validator::class );
		$validator->method( 'validate' )->willReturn( true );
		$md_registrar = $this->createMock( Meta_Data_Registrar::class );

		$registrar = new Post_Type_Registrar( $validator, $md_registrar );

		// Get the args array for registering post type.
		$args = Objects::invoke_method( $registrar, 'compile_args', array( $cpt ) );

		// Check args.
		$expected = array(
			'description'           => 'Basics',
			'hierarchical'          => false,
			'supports'              => array(),
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_admin_bar'     => true,
			'menu_position'         => 60,
			'menu_icon'             => 'dashicons-pets',
			'show_in_nav_menus'     => true,
			'publicly_queryable'    => true,
			'exclude_from_search'   => false,
			'has_archive'           => true,
			'query_var'             => false,
			'can_export'            => true,
			'rewrite'               => false,
			'capability_type'       => 'post',
			'capabilities'          => array(),
			'taxonomies'            => array(),
			'show_in_rest'          => true,
			'rest_base'             => 'basic_cpt',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'delete_with_user'      => null,
			'template'              => array(),
			'template_lock'         => false,
		);

		foreach ( $expected as $key => $value ) {
			$this->assertEquals( $value, $args[ $key ] );
		}

		// Check labels
		$expected = array(
			'name'                     => "{$cpt->plural}",
			'singular_name'            => "{$cpt->singular}",
			'add_new'                  => 'Add New',
			'add_new_item'             => "Add New {$cpt->singular}",
			'edit_item'                => "Edit {$cpt->singular}",
			'new_item'                 => "New {$cpt->singular}",
			'view_item'                => "View {$cpt->singular}",
			'view_items'               => "View {$cpt->plural}",
			'search_items'             => "Search {$cpt->singular}",
			'not_found'                => "No {$cpt->plural} found",
			'not_found_in_trash'       => "No {$cpt->plural} found in Trash",
			'parent_item_colon'        => "Parent {$cpt->singular}:",
			'all_items'                => "All {$cpt->plural}",
			'archives'                 => "{$cpt->plural} Archives",
			'attributes'               => "{$cpt->plural} Attributes",
			'insert_into_item'         => "Insert into {$cpt->singular}",
			'uploaded_to_this_item'    => "Uploaded to this {$cpt->singular}",
			'featured_image'           => 'Featured image',
			'set_featured_image'       => 'Set featured image',
			'remove_featured_image'    => 'Remove featured image',
			'use_featured_image'       => 'Use as featured image',
			'menu_name'                => "{$cpt->plural}",
			'filter_items_list'        => "Filter {$cpt->plural} list",
			'filter_by_date'           => 'Filter by date',
			'items_list'               => "{$cpt->plural} list",
			'item_published'           => "{$cpt->singular} published",
			'item_published_privately' => "{$cpt->singular} published privately",
			'item_reverted_to_draft'   => "{$cpt->singular} reverted to draft",
			'item_scheduled'           => "{$cpt->singular} scheduled",
			'item_updated'             => "{$cpt->singular} updated",
			'item_link'                => "{$cpt->singular} Link",
			'item_link_description'    => "A link to a {$cpt->singular}",
		);

		foreach ( $expected as $key => $value ) {
			$this->assertEquals( $value, $args['labels'][ $key ] );
		}
	}

	/** @testdox When registering the post type the internal filter_args and filter_labels methods should allow to overwrite values */
	public function test_uses_post_type_label_filters() {
		$cpt       = new class() extends Basic_CPT{
			public function filter_labels( array $e ): array {
				  return array( 'foo' => 'bar' );
			}
			public function filter_args( array $e ): array {
				  return array(
					  'labels' => $e['labels'],
					  'bar'    => 'foo',
				  );
			}
		};
		$validator = $this->createMock( Post_Type_Validator::class );
		$validator->method( 'validate' )->willReturn( true );
		$md_registrar = $this->createMock( Meta_Data_Registrar::class );

		$registrar = new Post_Type_Registrar( $validator, $md_registrar );

		// Get the args array for registering post type.
		$args = Objects::invoke_method( $registrar, 'compile_args', array( $cpt ) );

		$this->assertArrayHasKey( 'bar', $args );
		$this->assertEquals( 'foo', $args['bar'] );

		$this->assertArrayHasKey( 'foo', $args['labels'] );
		$this->assertEquals( 'bar', $args['labels']['foo'] );
	}

	/** @testdox When registering meta data for a post type, any exceptions throws should be caught and rethrown */
	public function test_throws_upper_exception_if_exception_thrown_during_meta_data_registration(): void {
		// Mock CPT which uses the failing meta data.
		$cpt = new class() extends Basic_CPT  {

			// This is a method that is written to populate an array with objects
			public function meta_data( array $collection ): array {
				// This mock object, is designed to throw exception if called.
				$collection[] = new class('test') extends Meta_Data{};

				return $collection;
			}
		};

		$validator = $this->createMock( Post_Type_Validator::class );
		$validator->method( 'validate' )->willReturn( true );
		$md_registrar = $this->createMock( Meta_Data_Registrar::class );
		$md_registrar->method( 'register_for_post_type' )->willThrowException( new Exception( 'MOCK EXCEPTION' ) );

		$registrar = new Post_Type_Registrar( $validator, $md_registrar );

		$this->expectExceptionMessage( 'MOCK EXCEPTION' );
		$this->expectException( \Exception::class );

		Objects::invoke_method( $registrar, 'register_meta_data', array( $cpt ) );
	}

}
