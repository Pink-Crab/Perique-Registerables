<?php

declare(strict_types=1);

/**
 * Application test for an additional meta data controller.
 *
 * @since 0.8.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Registerables\Tests\Application\Post_Type;

use WP_UnitTestCase;
use Gin0115\WPUnit_Helpers\WP\Meta_Data_Inspector;
use PinkCrab\Registerables\Tests\App_Helper_Trait;
use PinkCrab\WP_Rest_Schema\Parser\Argument_Parser;
use PinkCrab\Registerables\Additional_Meta_Data_Controller;
use PinkCrab\Registerables\Tests\Fixtures\Additional_Meta_Data;

class Test_Additional_Meta_Data_Controller extends WP_UnitTestCase {

	use App_Helper_Trait;

	/**
	 * Holds instance of the Meta Box Controller
	 *
	 * @var Additional_Meta_Data_Controller
	 */
	protected $controller;

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

		self::create_with_registerables( Additional_Meta_Data::class )->boot();
		do_action( 'init' );

		do_action( 'rest_api_init' );

		// Build inspector.
		$this->meta_data_inspector = Meta_Data_Inspector::initialise();
	}

	/** @testdox It should be possible to add either Post, Term, User or Comment meta to an Additional Meta Controller and have them registered. */
	public function test_meta_data_registered() {
		$this->assertNotNull( $this->meta_data_inspector->find_post_meta( 'page', 'mock_post_meta_data' ) );
		$this->assertNotNull( $this->meta_data_inspector->find_term_meta( 'categories', 'mock_term_meta_data' ) );
		$this->assertNotNull( $this->meta_data_inspector->find_comment_meta( 'mock_comment_meta_data' ) );
		$this->assertNotNull( $this->meta_data_inspector->find_user_meta( 'mock_user_meta_data' ) );
	}

	/** @testdox All values defined in the Meta Data object, should be used to register the POST meta field. */
	public function test_post_meta_values(): void {
		$meta = $this->meta_data_inspector->find_post_meta( 'page', 'mock_post_meta_data' );

		// Check values.
		$this->assertSame( Additional_Meta_Data::post_meta_data()->get_meta_type(), $meta->meta_type );
		$this->assertSame( Additional_Meta_Data::post_meta_data()->get_subtype(), $meta->sub_type );
		$this->assertSame( Additional_Meta_Data::post_meta_data()->get_meta_key(), $meta->meta_key );
		$this->assertSame( Additional_Meta_Data::post_meta_data()->parse_args()['description'], $meta->description );
		$this->assertSame( Additional_Meta_Data::post_meta_data()->get_rest_schema(), $meta->show_in_rest );

		// Check the callables.
		$this->assertEquals( '__return_true', $meta->auth_callback );
		$this->assertEquals( null, $meta->sanitize_callback );
	}

	/** @testdox All values defined in the Meta Data object, should be used to register the TERM meta field. */
	public function test_term_values(): void {
		$meta = $this->meta_data_inspector->find_term_meta( 'categories', 'mock_term_meta_data' );

		// Check values.
		$this->assertSame( Additional_Meta_Data::term_meta_data()->get_meta_type(), $meta->meta_type );
		$this->assertSame( Additional_Meta_Data::term_meta_data()->get_subtype(), $meta->sub_type );
		$this->assertSame( Additional_Meta_Data::term_meta_data()->get_meta_key(), $meta->meta_key );
		$this->assertSame( Additional_Meta_Data::term_meta_data()->parse_args()['description'], $meta->description );
		$this->assertSame( Additional_Meta_Data::term_meta_data()->get_rest_schema(), $meta->show_in_rest );

		// Check the callables.
		$this->assertEquals( '__return_true', $meta->auth_callback );
		$this->assertEquals( null, $meta->sanitize_callback );
	}

	/** @testdox All values defined in the Meta Data object, should be used to register the USER meta field. */
	public function test_user_values(): void {
		$meta = $this->meta_data_inspector->find_user_meta( 'mock_user_meta_data' );

		// Check values.
		$this->assertSame( Additional_Meta_Data::user_meta_data()->get_meta_type(), $meta->meta_type );
		$this->assertSame( Additional_Meta_Data::user_meta_data()->get_meta_key(), $meta->meta_key );
		$this->assertSame( Additional_Meta_Data::user_meta_data()->parse_args()['description'], $meta->description );
		$this->assertSame( Additional_Meta_Data::user_meta_data()->get_rest_schema(), $meta->show_in_rest );

		// Check the callables.
		$this->assertEquals( '__return_true', $meta->auth_callback );
		$this->assertEquals( null, $meta->sanitize_callback );
	}

	/** @testdox All values defined in the Meta Data object, should be used to register the COMMENT meta field. */
	public function test_comment_values(): void {
		$meta = $this->meta_data_inspector->find_comment_meta( 'mock_comment_meta_data' );

		// Check values.
		$this->assertSame( Additional_Meta_Data::comment_meta_data()->get_meta_type(), $meta->meta_type );
		$this->assertSame( Additional_Meta_Data::comment_meta_data()->get_meta_key(), $meta->meta_key );
		$this->assertSame( Additional_Meta_Data::comment_meta_data()->parse_args()['description'], $meta->description );

		// Check the WP Rest Schema is parsed.
		$this->assertSame( Argument_Parser::for_meta_data( Additional_Meta_Data::comment_meta_data()->get_rest_schema() ), $meta->show_in_rest );

		// Check the callables.
		$this->assertEquals( '__return_true', $meta->auth_callback );
		$this->assertEquals( null, $meta->sanitize_callback );
	}

}
