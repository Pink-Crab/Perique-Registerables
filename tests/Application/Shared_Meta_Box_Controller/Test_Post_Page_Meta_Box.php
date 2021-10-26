<?php

declare(strict_types=1);

/**
 * Application test for a shared meta box controller
 *
 * @since 0.7.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Registerables\Tests\Application\Post_Type;

use WP_UnitTestCase;
use Gin0115\WPUnit_Helpers\WP\Meta_Box_Inspector;
use Gin0115\WPUnit_Helpers\WP\Meta_Data_Inspector;
use PinkCrab\Registerables\Tests\App_Helper_Trait;
use PinkCrab\Registerables\Tests\Fixtures\Shared_Metabox\Post_Page_Meta_Box;

class Test_Post_Page_Meta_Box extends WP_UnitTestCase {

	use App_Helper_Trait;

	/**
	 * Holds instance of the Meta Box Controller
	 *
	 * @var Shared_Meta_Box_Controller
	 */
	protected $controller;

	/**
	 * Holds all the current meta box global
	 *
	 * @var array
	 */
	protected $wp_meta_boxes;

	/**
	 * Holds the instance of the meta box inspector.
	 *
	 * @var Meta_Data_Inspector
	 */
	protected $meta_data_inspector;

	/**
	 * Holds the instance of the meta box inspector.
	 *
	 * @var Meta_Box_Inspector
	 */
	protected $meta_box_inspector;

	/**
	 * Reset the app data after each test.
	 *
	 * @return void
	 */
	public function tearDown(): void {
		self::unset_app_instance();
	}

	public function setUp(): void {
		parent::setup();
		// Create the CPT and Loader instances.
		$this->cpt = new Post_Page_Meta_Box();

		self::create_with_registerables( Post_Page_Meta_Box::class )->boot();
		do_action( 'init' );

		// Build inspector.
		$this->meta_data_inspector = Meta_Data_Inspector::initialise();

		// Register the metaboxes.
		do_action( 'add_meta_boxes' );
		global $wp_meta_boxes;
		$this->wp_meta_boxes = $wp_meta_boxes;

		// Build inspector.
		$this->meta_box_inspector = Meta_Box_Inspector::initialise();
	}

	/** @testdox When registering a shared meta box controller, whatever screens are defined, should see the meta box registered for. */
	public function test_meta_boxes_registered() {
		$post_meta_box = array_values(
			$this->meta_box_inspector->filter(
				function( $meta_box ) {
					return $meta_box->post_type === 'post' && $meta_box->id === 'post_page_mb';
				}
			)
		)[0];
		$page_meta_box = array_values(
			$this->meta_box_inspector->filter(
				function( $meta_box ) {
					return $meta_box->post_type === 'page' && $meta_box->id === 'post_page_mb';
				}
			)
		)[0];

		// Check box are the same.
		$this->assertSame( $post_meta_box->position, $page_meta_box->position );
		$this->assertSame( $post_meta_box->priority, $page_meta_box->priority );
		$this->assertSame( $post_meta_box->name, $page_meta_box->name );
		$this->assertSame( $post_meta_box->id, $page_meta_box->id );
		$this->assertSame( $post_meta_box->callback, $page_meta_box->callback );
		$this->assertSame( $post_meta_box->args, $page_meta_box->args );
	}

	/** @testdox When the shared meta box controller is registered, the meta data assigned should also be registered to the same post types. */
	public function test_meta_data_is_registered(): void {
		$post_meta_1 = $this->meta_data_inspector->find_post_meta( 'post', 'pnp_string' );
		$page_meta_1 = $this->meta_data_inspector->find_post_meta( 'page', 'pnp_string' );

		$this->assertSame( $post_meta_1->meta_type, $page_meta_1->meta_type );
		$this->assertSame( $post_meta_1->meta_key, $page_meta_1->meta_key );
		$this->assertSame( $post_meta_1->value_type, $page_meta_1->value_type );
		$this->assertSame( $post_meta_1->description, $page_meta_1->description );
		$this->assertSame( $post_meta_1->single, $page_meta_1->single );
		$this->assertSame( $post_meta_1->sanitize_callback, $page_meta_1->sanitize_callback );
		$this->assertSame( $post_meta_1->auth_callback, $page_meta_1->auth_callback );
		$this->assertSame( $post_meta_1->show_in_rest, $page_meta_1->show_in_rest );
		$this->assertSame( $post_meta_1->default, $page_meta_1->default );

		$post_meta_2 = $this->meta_data_inspector->find_post_meta( 'post', 'pnp_words' );
		$page_meta_2 = $this->meta_data_inspector->find_post_meta( 'page', 'pnp_words' );

		$this->assertSame( $post_meta_2->meta_type, $page_meta_2->meta_type );
		$this->assertSame( $post_meta_2->meta_key, $page_meta_2->meta_key );
		$this->assertSame( $post_meta_2->value_type, $page_meta_2->value_type );
		$this->assertSame( $post_meta_2->description, $page_meta_2->description );
		$this->assertSame( $post_meta_2->single, $page_meta_2->single );
		$this->assertSame( $post_meta_2->sanitize_callback, $page_meta_2->sanitize_callback );
		$this->assertSame( $post_meta_2->auth_callback, $page_meta_2->auth_callback );
		$this->assertSame( $post_meta_2->show_in_rest, $page_meta_2->show_in_rest );
		$this->assertSame( $post_meta_2->default, $page_meta_2->default );
	}
}
