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

namespace PinkCrab\Modules\Registerables\Tests;

// Include our fixture.
require_once \dirname( __FILE__, 2 ) . '/Fixtures/MetaBox_CPT.php';

use WP_UnitTestCase;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\Core\Tests\Fixtures\Mock_Objects\MetaBox_CPT;


class Test_MetaBox_CPT extends WP_UnitTestCase {



	/**
	 * Holds instance of the Post_Type object.
	 *
	 * @var \PinkCrab\Modules\Registerables\Post_Type
	 */
	protected $cpt;

	/**
	 * Holds all the current meta box global
	 *
	 * @var array
	 */
	protected $wp_meta_boxes;



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
		}
	}

	/**
	 * Test the normal metbox is there and all values passed.
	 *
	 * @return void
	 */
	public function test_normal_metabox_registered(): void {
		// Check metabox exists.
		$this->assertArrayHasKey(
			'metabox_cpt_normal',
			$this->wp_meta_boxes['metabox_cpt']['normal']['default']
		);

		// Grab the view contents.
		$view_output = $this->render_metabox(
			$this->wp_meta_boxes['metabox_cpt']['normal']['default']['metabox_cpt_normal']['callback']
		);
		$this->assertEquals( 'metabox_cpt_normal VIEW', $view_output );

		// Check title.
		$this->assertEquals(
			'metabox_cpt_normal TITLE',
			$this->wp_meta_boxes['metabox_cpt']['normal']['default']['metabox_cpt_normal']['title']
		);

		// Check view vars.
		$this->assertArrayHasKey(
			'key1',
			$this->wp_meta_boxes['metabox_cpt']['normal']['default']['metabox_cpt_normal']['args']
		);
		$this->assertEquals(
			1,
			$this->wp_meta_boxes['metabox_cpt']['normal']['default']['metabox_cpt_normal']['args']['key1']
		);
	}

	/**
	 * Test the side metbox is there and all values passed.
	 *
	 * @return void
	 */
	public function test_side_metabox_registered(): void {

		// Check metabox exists.
		$this->assertArrayHasKey(
			'metabox_cpt_side',
			$this->wp_meta_boxes['metabox_cpt']['side']['default']
		);

		// Grab the view contents.
		$view_output = $this->render_metabox(
			$this->wp_meta_boxes['metabox_cpt']['side']['default']['metabox_cpt_side']['callback']
		);
		$this->assertEquals( 'metabox_cpt_side VIEW', $view_output );

		// Check title.
		$this->assertEquals(
			'metabox_cpt_side TITLE',
			$this->wp_meta_boxes['metabox_cpt']['side']['default']['metabox_cpt_side']['title']
		);

		// Check view vars.
		$this->assertArrayHasKey(
			'key2',
			$this->wp_meta_boxes['metabox_cpt']['side']['default']['metabox_cpt_side']['args']
		);
		$this->assertEquals(
			2,
			$this->wp_meta_boxes['metabox_cpt']['side']['default']['metabox_cpt_side']['args']['key2']
		);
	}

	/**
	 * Renders and returns the contents of a metabox.
	 *
	 * @param callable $metabox_view
	 * @return string
	 */
	protected function render_metabox( callable $metabox_view ): string {
		ob_start();
		$metabox_view(
			\get_post( $this->factory->post->create( array( 'post_type' => $this->cpt->key ) ) ),
			array()
		);
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

}
