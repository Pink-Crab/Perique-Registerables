<?php

declare(strict_types=1);

/**
 * Post Type with Metabox Intergration tests.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Registerables\Tests;

use WP_UnitTestCase;
use PinkCrab\Loader\Hook_Loader;
use Gin0115\WPUnit_Helpers\Output;
use Gin0115\WPUnit_Helpers\WP\Meta_Box_Inspector;
use PinkCrab\Registerables\Tests\App_Helper_Trait;
use PinkCrab\Registerables\Tests\Fixtures\CPT\MetaBox_CPT;


class Test_MetaBox_CPT extends WP_UnitTestCase {

	use App_Helper_Trait;

	/**
	 * Holds instance of the Post_Type object.
	 *
	 * @var \PinkCrab\Registerables\Post_Type
	 */
	protected $cpt;

	/**
	 * Holds all the current meta box global
	 *
	 * @var array
	 */
	protected $wp_meta_boxes;

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

	/** THE SETUP */

	public function setUp(): void {
		parent::setup();

		if ( ! $this->cpt ) {
			// Login user and set to accessing a post type.
			$admin_user = self::factory()->user->create( array( 'role' => 'administrator' ) );
			wp_set_current_user( $admin_user );
			set_current_screen( 'post-new.php' );

			// Create the CPT and Loader instances.
			$this->cpt = new MetaBox_CPT;

			self::create_with_registerables( MetaBox_CPT::class )->boot();
			do_action( 'init' );

			// Register the metaboxes.
			do_action( 'add_meta_boxes' );
			global $wp_meta_boxes;
			$this->wp_meta_boxes = $wp_meta_boxes;

			// Build inspector.
			$this->meta_box_inspector = Meta_Box_Inspector::initialise();
		}
	}

	/**@testdox Test the normal meta box is there and all values passed. */
	public function test_normal_meta_box_registered(): void {
		// Check meta box exists.
		$box = $this->meta_box_inspector->find( 'metabox_cpt_normal' );
		$this->assertNotNull( $box );

		// Test renders view (based on post title)
		$view_output = Output::buffer(
			function() use ( $box ) {
				$this->meta_box_inspector->render_meta_box(
					$box,
					\get_post( $this->factory->post->create( array( 'post_type' => $this->cpt->key ) ) )
				);
			}
		);
		$this->assertEquals( 'metabox_cpt_normal VIEW', $view_output );

		// Check title.
		$this->assertEquals( 'metabox_cpt_normal TITLE', $box->title );

		// Check view vars.
		$this->assertArrayHasKey( 'key1', $box->args );
		$this->assertEquals( 1, $box->args['key1'] );
	}

	/**
	 * Test the side metbox is there and all values passed.
	 *
	 * @return void
	 */
	public function test_side_metabox_registered(): void {
		// Check metabox exists.
		$box = $this->meta_box_inspector->find( 'metabox_cpt_side' );
		$this->assertNotNull( $box );

		// Create the mock post and its matching meta.
		$post = $this->factory->post->create( array( 'post_type' => $this->cpt->key ) );
		\update_post_meta($post, 'pc_mock_meta', 'hello');

		// Grab the view contents.
		$view_output = Output::buffer(
			function() use ( $box, $post ) {
				$this->meta_box_inspector->set_meta_boxes()->render_meta_box(
					$box,
					\get_post( $post )
				);
			}
		);
		$this->assertEquals( 'metabox_cpt_side VIEW. Meta=hello', $view_output );

		// Check title.
		$this->assertEquals( 'metabox_cpt_side TITLE', $box->title );

		// Check view vars.
		$this->assertArrayHasKey( 'key2', $box->args );
		$this->assertEquals( 2, $box->args['key2'] );
	}

	public function test_template_metabox_registered()
	{
		// Check metabox exists.
		$box = $this->meta_box_inspector->find( 'metabox_cpt_template' );
		$this->assertNotNull( $box );

		// Create the mock post and its matching meta.
		$post = $this->factory->post->create( array( 'post_type' => $this->cpt->key ) );
		\update_post_meta($post, 'pc_mock_meta', 'hello');

		// Grab the view contents.
		$view_output = Output::buffer(
			function() use ( $box, $post ) {
				$this->meta_box_inspector->set_meta_boxes()->render_meta_box(
					$box,
					\get_post( $post )
				);
			}
		);

		$this->assertEquals('Post Type: metabox_cpt, Meta: metabox_cpt_template', $view_output);

		// Check title.
		$this->assertEquals( 'metabox_cpt_template TITLE', $box->title );

		// Check view vars.
		$this->assertArrayHasKey( 'key3', $box->args );
		$this->assertEquals( 3, $box->args['key3'] );
	}
}
