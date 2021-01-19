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

use WP_UnitTestCase;
use Nyholm\Psr7\ServerRequest;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\Registerables\Tests\Fixtures\Ajax\Ajax_Get;


class Test_Ajax_Get extends WP_UnitTestCase {

	protected static $ajax_instance;
	protected static $user;

	public function setUp() {

		if ( empty( $_GET['nonce'] ) ) {

			// Set as admin.
			// set_current_screen( 'edit-post' );
set_current_screen( 'edit.php' );
			// Mock the GET
			$_GET['nonce']         = wp_create_nonce( 'basic_ajax_get' );
			$_GET['ajax_get_data'] = 'Test_Ajax_Get';

			// Register ajax.
			$ajax_instance = new Ajax_Get();
			$loader        = new Loader;
			$ajax_instance->register( $loader );
			$loader->register_hooks();
		}

	}

	/**
	 * Ensure the ajax call has been registered.
	 *
	 * @return void
	 */
	public function test_ajax_registered() {
		wp_set_current_user( self::$user );
		$this->assertArrayHasKey( 'wp_ajax_basic_ajax_get', $GLOBALS['wp_filter'] );
		$this->assertArrayHasKey( 'wp_ajax_nopriv_basic_ajax_get', $GLOBALS['wp_filter'] );

	}

	public function test_can_call_nopriv() {
		try {
			wp_set_current_user( self::$user );
			$this->expectOutputRegex( '/Test_Ajax_Get/' );
			do_action( 'wp_ajax_nopriv_basic_ajax_get' );
		} catch ( \Throwable $th ) {
			// Ignore the wpdieexception, we dont actually return JSON!
		}
	}

	public function test_can_call_priv() {
		try {
			wp_set_current_user( self::$user );
			$this->expectOutputRegex( '/Test_Ajax_Get/' );
			do_action( 'wp_ajax_basic_ajax_get' );
		} catch ( \Throwable $th ) {
			// Ignore the wpdieexception, we dont actually return JSON!
		}
	}
}
