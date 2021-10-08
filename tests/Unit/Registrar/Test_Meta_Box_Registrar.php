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
use PHPUnit\Framework\TestCase;
use PinkCrab\Loader\Hook_Loader;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Registerables\Meta_Box;
use PinkCrab\Perique\Interfaces\Renderable;
use PinkCrab\Perique\Interfaces\DI_Container;
use PinkCrab\Registerables\Registrar\Meta_Box_Registrar;
use PinkCrab\Registerables\Validator\Meta_Box_Validator;

class Test_Meta_Box_Registrar extends TestCase {

	/** @testdox If a Meta Box fails validation and exception should be thrown */
	public function test_fails_validation_if_none_post_type_registerable(): void {

		$validator = $this->createMock( Meta_Box_Validator::class );
		$validator->method( 'validate' )->willReturn( false );
		$registrar = new Meta_Box_Registrar( $validator, $this->createMock( DI_Container::class ), $this->createMock( Hook_Loader::class ) );

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Invalid meta box model' );
		$registrar->register( $this->createMock( Meta_Box::class ) );
	}

	/** @testdox It should be possible to use the Renderable implementation to render the meta box view from a tempalte.*/
	public function test_populates_render_with_view(): void {

		// Setup the validator, DI Container and Loader
		$validator = $this->createMock( Meta_Box_Validator::class );
		$validator->method( 'verify_meta_box' )->willReturn( true );

		$renderable = $this->createMock( Renderable::class );
		$renderable->method( 'render' )->will(
			$this->returnCallback(
				function( ...$a ): void {
					print 'MOCK OUTPUT';
				}
			)
		);

		$di_container = $this->createMock( DI_Container::class );
		$di_container->method( 'create' )->willReturn( $renderable );

		// Build registrar
		$registrar = new Meta_Box_Registrar( $validator, $di_container, $this->createMock( Hook_Loader::class ) );

		// Register our meta box
		$meta_box                = new Meta_Box( 'test' );
		$meta_box->view_template = 'foo';
		$registrar->register( $meta_box );

		// This should populate the view callable to use the Renderable
		$this->expectOutputString( 'MOCK OUTPUT' );
		( $meta_box->view )( get_post( wp_insert_post( array( 'post_title' => 'My post' ) ) ), array( 2 ) );
	}

	/** @testdox If when creating the view callable using Renderable, an exception should be thrown if the container can not create View class. */
	public function test_throws_exception_if_renderable_cant_be_created_with_DI():void {
		// Setup the validator, DI Container and Loader
		$validator = $this->createMock( Meta_Box_Validator::class );
		$validator->method( 'verify_meta_box' )->willReturn( true );

		$di_container = $this->createMock( DI_Container::class );
		$di_container->method( 'create' )->willReturn( null );

		// Build registrar
		$registrar = new Meta_Box_Registrar( $validator, $di_container, $this->createMock( Hook_Loader::class ) );

		// Register our meta box
		$meta_box                = new Meta_Box( 'test' );
		$meta_box->view_template = 'foo';

		// Should throw an exception when being called if View can not be created.
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'View not defined' );
		$registrar->register( $meta_box );
	}

	/** @testdox When registering all hooks for a meta box, these should only be added when the metabox is being displayed. */
	public function test_can_check_if_meta_box_is_active(): void {
		// Mock the Registrar
		$registrar = $registrar = new Meta_Box_Registrar(
			$this->createMock( Meta_Box_Validator::class ),
			$this->createMock( DI_Container::class ),
			$this->createMock( Hook_Loader::class )
		);

		global $current_screen;
		$current_screen = (object) array( 'post_type' => 'post' );

		// Should be active for post, post type
		$mb_post = Meta_Box::normal( 'mb_post' )->screen( 'post' );
		$this->assertTrue( Objects::invoke_method( $registrar, 'is_active', array( $mb_post ) ) );

		// Should be inactive for page, post type
		$mb_page = Meta_Box::normal( 'mb_page' )->screen( 'page' );
		$this->assertFalse( Objects::invoke_method( $registrar, 'is_active', array( $mb_page ) ) );

		// Reset
		$current_screen = null;
	}

	/** @testdox When registering all valid meta boxes should be added to the loader. */
	public function test_adds_valid_meta_box_to_hook_loader(): void {
		// Setup the validator, DI Container and Loader
		$validator = $this->createMock( Meta_Box_Validator::class );
		$validator->method( 'verify_meta_box' )->willReturn( true );

		$loader    = new Hook_Loader();
		$registrar = new Meta_Box_Registrar(
			$validator,
			$this->createMock( DI_Container::class ),
			$loader
		);

		$meta_box = Meta_Box::normal( 'mb_post' )->view( function( ...$a ) {} );
		$registrar->register( $meta_box );

		// Should now have add_meta_box hook added to loader.
		$hooks = Objects::get_property( $loader, 'hooks' );
		$hooks = Objects::get_property( $hooks, 'hooks' );

		$this->assertCount( 1, $hooks );
		$this->assertEquals( 'action', $hooks[0]->get_type() );
		$this->assertEquals( 'add_meta_boxes', $hooks[0]->get_handle() );
	}

	public function test_can_add_additional_actions_for_valid_screens(): void {
		// Mock current screen
		global $current_screen;
		$current_screen = (object) array( 'post_type' => 'post' );

		// Setup the validator, DI Container and Loader
		$validator = $this->createMock( Meta_Box_Validator::class );
		$validator->method( 'verify_meta_box' )->willReturn( true );

		$loader    = new Hook_Loader();
		$registrar = new Meta_Box_Registrar(
			$validator,
			$this->createMock( DI_Container::class ),
			$loader
		);

		$mb_post = Meta_Box::normal( 'mb_post' )
			->screen( 'post' )
			->add_action( 'init', '__return_false' )
			->add_action( 'foo', '__return_true' )
			->view('__return_true');

		
		$registrar->register( $mb_post );

		// Should now have add_meta_box hook added to loader.
		$hooks = Objects::get_property( $loader, 'hooks' );
		$hooks = Objects::get_property( $hooks, 'hooks' );

		dump($hooks);
		// Should have 3 (register MB and its Actions)
		$this->assertCount( 3, $hooks );
		
		$this->assertEquals( 'action', $hooks[1]->get_type() );
		$this->assertEquals( 'init', $hooks[1]->get_handle() );
		$this->assertEquals( '__return_false', $hooks[1]->get_callback() );

		$this->assertEquals( 'action', $hooks[2]->get_type() );
		$this->assertEquals( 'foo', $hooks[2]->get_handle() );
		$this->assertEquals( '__return_true', $hooks[2]->get_callback() );
		// Reset
		$current_screen = null;

	}

	//$factory = new WP_UnitTest_Factory();



	// /** @testdox If a WP_Error class is returned when registering the post type, this should be translated into an exception */
	// public function test_throws_exception_if_register_post_type_returns_wp_error(): void {

	// 	$validator = $this->createMock( Post_Type_Validator::class );
	// 	$validator->method( 'validate' )->willReturn( true );

	// 	$registrar = new Post_Type_Registrar( $validator );

	// 	$post_type = new class() extends Post_Type {
	// 		// Name is capped between 1 and 20
	// 		public $key      = '0123456789012345678901234567890123456789';
	// 		public $singular = '0123456789012345678901234567890123456789';
	// 		public $plural   = '0123456789012345678901234567890123456789';
	// 	};

	// 	$this->expectException( \Exception::class );
	// 	$this->expectExceptionMessageRegExp( '#Failed to register 0123456789012345678901234567890123456789 post type (.*)$#' );
	// 	$registrar->register( $post_type );
	// }

	// /** @testdox When compiled, all args should be set based on the post type defined. */
	// public function test_can_compile_args(): void {
	// 	$cpt       = new Basic_CPT();
	// 	$validator = $this->createMock( Post_Type_Validator::class );
	// 	$validator->method( 'validate' )->willReturn( true );

	// 	$registrar = new Post_Type_Registrar( $validator );

	// 	// Get the args array for registering post type.
	// 	$args = Objects::invoke_method( $registrar, 'compile_args', array( $cpt ) );

	// 	// Check args.
	// 	$expected = array(
	// 		'description'           => 'Basics',
	// 		'hierarchical'          => false,
	// 		'supports'              => array(),
	// 		'public'                => true,
	// 		'show_ui'               => true,
	// 		'show_in_menu'          => true,
	// 		'show_in_admin_bar'     => true,
	// 		'menu_position'         => 60,
	// 		'menu_icon'             => 'dashicons-pets',
	// 		'show_in_nav_menus'     => true,
	// 		'publicly_queryable'    => true,
	// 		'exclude_from_search'   => false,
	// 		'has_archive'           => true,
	// 		'query_var'             => false,
	// 		'can_export'            => true,
	// 		'rewrite'               => false,
	// 		'capability_type'       => 'post',
	// 		'capabilities'          => array(),
	// 		'taxonomies'            => array(),
	// 		'show_in_rest'          => true,
	// 		'rest_base'             => 'basic_cpt',
	// 		'rest_controller_class' => 'WP_REST_Posts_Controller',
	// 		'delete_with_user'      => null,
	// 		'template'              => array(),
	// 		'template_lock'         => false,
	// 	);

	// 	foreach ( $expected as $key => $value ) {
	// 		$this->assertEquals( $value, $args[ $key ] );
	// 	}

	// 	// Check labels
	// 	$expected = array(
	// 		'name'                     => "{$cpt->plural}",
	// 		'singular_name'            => "{$cpt->singular}",
	// 		'add_new'                  => 'Add New',
	// 		'add_new_item'             => "Add New {$cpt->singular}",
	// 		'edit_item'                => "Edit {$cpt->singular}",
	// 		'new_item'                 => "New {$cpt->singular}",
	// 		'view_item'                => "View {$cpt->singular}",
	// 		'view_items'               => "View {$cpt->plural}",
	// 		'search_items'             => "Search {$cpt->singular}",
	// 		'not_found'                => "No {$cpt->plural} found",
	// 		'not_found_in_trash'       => "No {$cpt->plural} found in Trash",
	// 		'parent_item_colon'        => "Parent {$cpt->singular}:",
	// 		'all_items'                => "All {$cpt->plural}",
	// 		'archives'                 => "{$cpt->plural} Archives",
	// 		'attributes'               => "{$cpt->plural} Attributes",

	// /** @testdox If a WP_Error class is returned when registering the post type, this should be translated into an exception */
	// public function test_throws_exception_if_register_post_type_returns_wp_error(): void {

	// 	$validator = $this->createMock( Post_Type_Validator::class );
	// 	$validator->method( 'validate' )->willReturn( true );

	// 	$registrar = new Post_Type_Registrar( $validator );

	// 	$post_type = new class() extends Post_Type {
	// 		// Name is capped between 1 and 20
	// 		public $key      = '0123456789012345678901234567890123456789';
	// 		public $singular = '0123456789012345678901234567890123456789';
	// 		public $plural   = '0123456789012345678901234567890123456789';
	// 	};

	// 	$this->expectException( \Exception::class );
	// 	$this->expectExceptionMessageRegExp( '#Failed to register 0123456789012345678901234567890123456789 post type (.*)$#' );
	// 	$registrar->register( $post_type );
	// }

	// /** @testdox When compiled, all args should be set based on the post type defined. */
	// public function test_can_compile_args(): void {
	// 	$cpt       = new Basic_CPT();
	// 	$validator = $this->createMock( Post_Type_Validator::class );
	// 	$validator->method( 'validate' )->willReturn( true );

	// 	$registrar = new Post_Type_Registrar( $validator );

	// 	// Get the args array for registering post type.
	// 	$args = Objects::invoke_method( $registrar, 'compile_args', array( $cpt ) );

	// 	// Check args.
	// 	$expected = array(
	// 		'description'           => 'Basics',
	// 		'hierarchical'          => false,
	// 		'supports'              => array(),
	// 		'public'                => true,
	// 		'show_ui'               => true,
	// 		'show_in_menu'          => true,
	// 		'show_in_admin_bar'     => true,
	// 		'menu_position'         => 60,
	// 		'menu_icon'             => 'dashicons-pets',
	// 		'show_in_nav_menus'     => true,
	// 		'publicly_queryable'    => true,
	// 		'exclude_from_search'   => false,
	// 		'has_archive'           => true,
	// 		'query_var'             => false,
	// 		'can_export'            => true,
	// 		'rewrite'               => false,
	// 		'capability_type'       => 'post',
	// 		'capabilities'          => array(),
	// 		'taxonomies'            => array(),
	// 		'show_in_rest'          => true,
	// 		'rest_base'             => 'basic_cpt',
	// 		'rest_controller_class' => 'WP_REST_Posts_Controller',
	// 		'delete_with_user'      => null,
	// 		'template'              => array(),
	// 		'template_lock'         => false,
	// 	);

	// 	foreach ( $expected as $key => $value ) {
	// 		$this->assertEquals( $value, $args[ $key ] );
	// 	}

	// 	// Check labels
	// 	$expected = array(
	// 		'name'                     => "{$cpt->plural}",
	// 		'singular_name'            => "{$cpt->singular}",
	// 		'add_new'                  => 'Add New',
	// 		'add_new_item'             => "Add New {$cpt->singular}",
	// 		'edit_item'                => "Edit {$cpt->singular}",
	// 		'new_item'                 => "New {$cpt->singular}",
	// 		'view_item'                => "View {$cpt->singular}",
	// 		'view_items'               => "View {$cpt->plural}",
	// 		'search_items'             => "Search {$cpt->singular}",
	// 		'not_found'                => "No {$cpt->plural} found",
	// 		'not_found_in_trash'       => "No {$cpt->plural} found in Trash",
	// 		'parent_item_colon'        => "Parent {$cpt->singular}:",
	// 		'all_items'                => "All {$cpt->plural}",
	// 		'archives'                 => "{$cpt->plural} Archives",
	// 		'attributes'               => "{$cpt->plural} Attributes",
	// 		'insert_into_item'         => "Insert into {$cpt->singular}",
	// 		'uploaded_to_this_item'    => "Uploaded to this {$cpt->singular}",
	// 		'featured_image'           => 'Featured image',
	// 		'set_featured_image'       => 'Set featured image',
	// 		'remove_featured_image'    => 'Remove featured image',
	// 		'use_featured_image'       => 'Use as featured image',
	// 		'menu_name'                => "{$cpt->plural}",
	// 		'filter_items_list'        => "Filter {$cpt->plural} list",
	// 		'filter_by_date'           => 'Filter by date',
	// 		'items_list'               => "{$cpt->plural} list",
	// 		'item_published'           => "{$cpt->singular} published",
	// 		'item_published_privately' => "{$cpt->singular} published privately",
	// 		'item_reverted_to_draft'   => "{$cpt->singular} reverted to draft",
	// 		'item_scheduled'           => "{$cpt->singular} scheduled",
	// 		'item_updated'             => "{$cpt->singular} updated",
	// 		'item_link'                => "{$cpt->singular} Link",
	// 		'item_link_description'    => "A link to a {$cpt->singular}",
	// 	);

	// 	foreach ( $expected as $key => $value ) {
	// 		$this->assertEquals( $value, $args['labels'][ $key ] );
	// 	}
	// }

	// /** @testdox When registering the post type the internal filter_args and filter_labels methods should allow to overwrite values */
	// public function test_uses_post_type_label_filters() {
	// 	$cpt       = new class() extends Basic_CPT{
	// 		public function filter_labels( array $e ): array {
	// 			  return array( 'foo' => 'bar' );
	// 		}
	// 		public function filter_args( array $e ): array {
	// 			  return array(
	// 				  'labels' => $e['labels'],
	// 				  'bar'    => 'foo',
	// 			  );
	// 		}
	// 	};
	// 	$validator = $this->createMock( Post_Type_Validator::class );
	// 	$validator->method( 'validate' )->willReturn( true );

	// 	$registrar = new Post_Type_Registrar( $validator );

	// 	// Get the args array for registering post type.
	// 	$args = Objects::invoke_method( $registrar, 'compile_args', array( $cpt ) );

	// 	$this->assertArrayHasKey( 'bar', $args );
	// 	$this->assertEquals( 'foo', $args['bar'] );

	// 	$this->assertArrayHasKey( 'foo', $args['labels'] );
	// 	$this->assertEquals( 'bar', $args['labels']['foo'] );
	// }

}
