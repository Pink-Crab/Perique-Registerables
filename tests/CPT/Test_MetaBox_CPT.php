<?php

declare(strict_types=1);

/**
 * Loader tests.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Registerables\Tests;

use WP_UnitTestCase;
use PinkCrab\Loader\Loader;
use Gin0115\WPUnit_Helpers\Output;
use Gin0115\WPUnit_Helpers\WP\Meta_Box_Inspector;
use PinkCrab\Registerables\Tests\Fixtures\CPT\MetaBox_CPT;


class Test_MetaBox_CPT extends WP_UnitTestCase {



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



	/** THE SETUP */

	public function setUp(): void {
		parent::setup();

		if ( ! $this->cpt ) {
			// Create the CPT and Loader instances.
			$this->cpt = new MetaBox_CPT;
			$loader    = new Loader;

			// Run registration.
			$this->cpt->register( $loader );
			$loader->register_hooks();

			// Register the metaboxes.
			do_action( 'add_meta_boxes' );
			global $wp_meta_boxes;
			$this->wp_meta_boxes = $wp_meta_boxes;

			// Build inspector.
			$this->meta_box_inspector = Meta_Box_Inspector::initialise();
		}
	}

	/**
	 * Test the normal metbox is there and all values passed.
	 *
	 * @return void
	 */
	public function test_normal_metabox_registered(): void {
		// Check metabox exists.
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

		// Grab the view contents.
		$view_output = Output::buffer(
			function() use ( $box ) {
				$this->meta_box_inspector->render_meta_box(
					$box,
					\get_post( $this->factory->post->create( array( 'post_type' => $this->cpt->key ) ) )
				);
			}
		);
		$this->assertEquals( 'metabox_cpt_side VIEW', $view_output );

		// Check title.
		$this->assertEquals( 'metabox_cpt_side TITLE', $box->title );

		// Check view vars.
		$this->assertArrayHasKey( 'key2', $box->args );
		$this->assertEquals( 2, $box->args['key2'] );
	}

	/**
	 * Tests the ::get_slug() method creates its own instnace of the CPT internally.
	 *
	 * @return void
	 */
	public function test_can_get_slug_statically(): void {
		$this->assertEquals( 'metabox_cpt', MetaBox_CPT::get_slug() );
	}

}
