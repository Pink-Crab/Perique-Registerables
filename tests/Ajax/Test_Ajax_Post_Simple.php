<?php

declare(strict_types=1);

/**
 * Tests the Ajax_Get mock.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Registerables\Tests;

use WP_Ajax_UnitTestCase;
use PinkCrab\Registerables\Ajax;
use GuzzleHttp\Psr7\ServerRequest;
use PinkCrab\Core\Application\App;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\Core\Services\ServiceContainer\Container;
use PinkCrab\Registerables\Tests\Fixtures\Ajax\Ajax_Post_Simple;


class Test_Ajax_Post_Simple extends  WP_Ajax_UnitTestCase {

	/**
	 * Holds the instance of the
	 *
	 * @var Ajax
	 */
	protected static $ajax_instance;

	protected static $app;

	public function setUp() {
		parent::setUp();

		

		$_SERVER['REQUEST_METHOD'] = 'POST';

		$_POST['nonce']                 = wp_create_nonce( 'ajax_post_simple' );
		$_POST['ajax_post_simple_data'] = 'Test_Ajax_Post_Simple';

		self::$ajax_instance = new Ajax_Post_Simple( ServerRequest::fromGlobals()->withHeader( 'Content-Type', 'application/x-www-form-urlencoded;' ) );
		$loader              = new Loader;
		self::$ajax_instance->register( $loader );
		$loader->register_hooks();
	}

	/**
	 * Ensure the ajax call has been registered.
	 *
	 * @return void
	 */
	public function test_ajax_registered() {
		$this->assertArrayHasKey( 'wp_ajax_nopriv_ajax_post_simple', $GLOBALS['wp_filter'] );

	}

	/**
	 * Check a none logged in user can use.
	 *
	 * @return void
	 */
	public function test_callable_logged_out() {

		try {
			$this->_handleAjax( 'ajax_post_simple' );
		} catch ( \WPAjaxDieStopException $th ) {
			// Ignore the wpdieexception, we dont actually return JSON!
		} catch ( \WPAjaxDieContinueException $th ) {

		}

		$response = json_decode( $this->_last_response );
		$this->assertIsObject( $response );
		$this->assertObjectHasAttribute( 'success', $response );
		$this->assertTrue( $response->success );
		$this->assertEquals( 'Test_Ajax_Post_Simple', $response->data->ajax_post_simple_data );
	}

	/**
	 * Test that we can create the nonce field.
	 *
	 * @return void
	 */
	public function test_nonce_field(): void {
		ob_start();
		self::$ajax_instance::nonce_field();
		$nonce_field = ob_get_contents();
		ob_end_clean();

		$this->assertGreaterThan( 0, strpos( $nonce_field, $_POST['nonce'] ) );
		$this->assertGreaterThan( 0, strpos( $nonce_field, "type='hidden'" ) );
		$this->assertGreaterThan( 0, strpos( $nonce_field, "id='nonce'" ) );
		$this->assertGreaterThan( 0, strpos( $nonce_field, "name='nonce'" ) );
	}

	/**
	 * Test the static get nonce value returns the current nonce.
	 *
	 * @return void
	 */
	public function test_can_get_nonce_value(): void {
		$this->assertEquals( self::$ajax_instance::nonce_value(), $_POST['nonce'] );
	}

	/**
	 * Test the static get nonce value returns the current nonce.
	 *
	 * @return void
	 */
	public function test_can_get_action(): void {
		$this->assertEquals( self::$ajax_instance::action(), 'ajax_post_simple' );
	}
}
