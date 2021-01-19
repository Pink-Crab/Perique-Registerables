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
use PHPUnit\Framework\Exception;
use PinkCrab\Registerables\Ajax;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\Registerables\Tests\Fixtures\Ajax\Ajax_Post_Simple;

/**
 * @preserdveGlobalState disabled
 */
class Test_Ajax_Post_Simple extends  TestCase {

	/**
	 * Holds the instance of the
	 *
	 * @var Ajax
	 */
	protected static $ajax_instance;

	protected static $app;

	protected function set_up() {
		parent::set_up();

		$this->set_post_globals();
		self::$ajax_instance = new Ajax_Post_Simple();
		$loader              = new Loader;
		self::$ajax_instance->register( $loader );
		$loader->register_hooks();

	}

	protected function tare_down() {
		$this->reset_post_globals();
	}

	/**
	 * Sets our fake globals.
	 *
	 * @return array
	 */
	protected function set_post_globals() {
		$_SERVER['REQUEST_METHOD']      = 'POST';
		$_POST['nonce']                 = wp_create_nonce( 'ajax_post_simple' );
		$_POST['ajax_post_simple_data'] = 'Test_Ajax_Post_Simple';
	}

	public function reset_post_globals() {
		$_POST = array();
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
	 * Undocumented variable
	 *
	 * @var bool
	 */
	protected $preserveGlobalState = false;

	/**
	 * Check a none logged in user can use.
	 *
	 * @runInSeparateProcess
	 * @return void
	 */
	public function test_callable_logged_out() {

		// Mock the $_POST global.
		// $this->set_post_globals();
		// dump( self::$ajax_instance, $_POST );
		// $this->expectOutputRegex( '/^(.*?(\bWP_VALUE\b)[^$]*)$/' );

		do_action( 'wp_ajax_' . 'ajax_post_simple', null );

		// try {
		// } catch ( \WPAjaxDieStopException $th ) {
		// 	// Ignore the wpdieexception, we dont actually return JSON!
		// } catch ( \WPAjaxDieContinueException $th ) {

		// }
		// $this->expectOutputRegex( '/^(.*?(\bWP_VALUE\b)[^$]*)$/' );
		// $this->expectException( Exception::class );
		// ini_set( 'implicit_flush', false );
		// ob_start();

		// try {
		// 	// do_action( 'admin_init' );
		// 	// do_action( 'wp_ajax_' . 'ajax_post_simple', null );
		// } catch ( \Throwable $th ) {
		// 	dump( $th );
		// }

		// $buffer = ob_get_clean();
		// dd( $buffer );

		// // $this->_handleAjax( 'ajax_post_simple' );
		// // wp_die();

		// dump( $this->_last_response );

		// $response = json_decode( $this->_last_response );
		// $this->assertIsObject( $response );
		// $this->assertObjectHasAttribute( 'success', $response );
		// $this->assertTrue( $response->success );
		// $this->assertEquals( 'Test_Ajax_Post_Simple', $response->data->ajax_post_simple_data );
	}

	/**
	 * Test that we can create the nonce field.
	 *
	 * @return void
	 */
	public function test_nonce_field(): void {
		// Mock the $_POST global.
		$this->set_post_globals();

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
		// Mock the $_POST global.
		$this->set_post_globals();

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
