<?php

declare(strict_types=1);

/**
 * Integration test for post type with defined post meta and rest.
 *
 * @since 0.7.1
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Registerables\Tests\Application\Post_Type;

use stdClass;
use WP_UnitTestCase;
use Gin0115\WPUnit_Helpers\WP\Meta_Data_Inspector;
use PinkCrab\Registerables\Tests\App_Helper_Trait;
use Gin0115\WPUnit_Helpers\WP\Entities\Meta_Data_Entity;
use PinkCrab\Registerables\Tests\Fixtures\CPT\Meta_Data_CPT;
use PinkCrab\Registerables\Tests\Fixtures\CPT\Meta_Data_Rest_CPT;

class Test_Meta_Data_Rest_CPT extends WP_UnitTestCase {

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
	 * @var Meta_Data_Inspector
	 */
	protected $meta_data_inspector;

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
		$this->cpt = new Meta_Data_Rest_CPT();

		self::create_with_registerables( Meta_Data_Rest_CPT::class )->boot();
		do_action( 'init' );

		// Build inspector.
		$this->meta_data_inspector = Meta_Data_Inspector::initialise();

		\do_action( 'rest_api_init' );

	}

	/** @testdox When defining meta in the Post Types meta_data array, should see these meta values created within wp core, when we register the post type. */
	public function test_meta_data_registered(): void {
		// Check post type has 2 meta fields applied.
		$this->assertCount( 2, $this->meta_data_inspector->for_post_types( $this->cpt->key ) );

		// Meta 1 Values.
		$meta1 = $this->meta_data_inspector->find_post_meta( $this->cpt->key, Meta_Data_Rest_CPT::META_1['key'] );
		$this->assertInstanceOf( Meta_Data_Entity::class, $meta1 );
		$this->assertEquals( Meta_Data_Rest_CPT::meta_rest_key_1_schema(), $meta1->show_in_rest );

		// Meta 2 Values.
		$meta2 = $this->meta_data_inspector->find_post_meta( $this->cpt->key, Meta_Data_Rest_CPT::META_2['key'] );
		$this->assertInstanceOf( Meta_Data_Entity::class, $meta2 );
		$this->assertEquals( Meta_Data_Rest_CPT::meta_rest_key_2_schema_as_array(), $meta2->show_in_rest );

		// Test that rest field were registered.
		$rest_fields = $GLOBALS['wp_rest_additional_fields'];
		$field_1 = Meta_Data_Rest_CPT::META_1['key'];
		$field_2 = Meta_Data_Rest_CPT::META_2['key'];
		
		$this->assertArrayHasKey( $this->cpt->key, $rest_fields );
		$this->assertArrayHasKey( $field_1 , $rest_fields[ $this->cpt->key ] );
		$this->assertArrayHasKey( $field_2, $rest_fields[ $this->cpt->key ] );

		// Check schema matches from meta data to meta field.
		$this->assertEquals( $meta1->show_in_rest, $rest_fields[ $this->cpt->key ][ $field_1  ]['schema'] );
		$this->assertEquals( $meta2->show_in_rest, $rest_fields[ $this->cpt->key ][ $field_2 ]['schema'] );

		// Check GET/VIEW callbacks are registered.
		$meta_1_view = $rest_fields[ $this->cpt->key ][ $field_1  ]['get_callback'];
		$meta_1_view(['id' => 'meta_1']);	
		$meta2_view = $rest_fields[ $this->cpt->key ][ $field_2 ]['get_callback'];
		$meta2_view(['id' => 'meta_2']);
		
		$this->assertArrayHasKey( 'meta_1', $this->cpt::$call_log );
		$this->assertArrayHasKey( 'meta_2', $this->cpt::$call_log );
		$this->assertContains( $field_1, $this->cpt::$call_log['meta_1'] );
		$this->assertContains( $field_2, $this->cpt::$call_log['meta_2'] );

		// Check POST/CREATE callbacks are registered.
		$meta_1_create = $rest_fields[ $this->cpt->key ][ $field_1  ]['update_callback'];
		$meta_1_create('meta_1',new stdClass);
		$meta_2_create = $rest_fields[ $this->cpt->key ][ $field_2 ]['update_callback'];
		$meta_2_create('meta_2',new stdClass);

		$this->assertArrayHasKey( stdClass::class, $this->cpt::$call_log );
		$this->assertArrayHasKey( $field_1, $this->cpt::$call_log[stdClass::class] );
		$this->assertArrayHasKey( $field_2, $this->cpt::$call_log[stdClass::class] );
		$this->assertEquals( 'meta_1', $this->cpt::$call_log[stdClass::class][$field_1] );
		$this->assertEquals( 'meta_2', $this->cpt::$call_log[stdClass::class][$field_2] );
	}

}
