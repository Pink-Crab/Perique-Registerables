<?php

declare(strict_types=1);

/**
 * APPLICATION - Post Type Registration via Registerable Middleware
 *
 * @since 0.6.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables-Registerables
 */

namespace PinkCrab\Registerables\Tests\Application\Registration_Middleware;

use WP_UnitTestCase;
use PinkCrab\Registerables\Tests\App_Helper_Trait;
use PinkCrab\Registerables\Tests\Fixtures\CPT\Basic_CPT;

class Test_Meta_Data_CPT extends WP_UnitTestCase {

	/**
	 * @method self::unset_app_instance();
	 */
	use App_Helper_Trait;

	/**
	 * Reset the app data after each test.
	 *
	 * @return void
	 */
	public function tearDown(): void {
		self::unset_app_instance();
	}

	/** @testdox It should be possible to register a valid post type using the registerable Middleware. */
	public function test_can_register_valid_cpt(): void {
		self::create_with_registerables( Basic_CPT::class )->boot();

		$admin_user = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_user );
		set_current_screen( 'dashboard' );
		do_action( 'init' );

		$this->assertArrayHasKey( ( new Basic_CPT() )->key, \get_post_types() );
		$this->assertContains( ( new Basic_CPT() )->key, \get_post_types() );
	}

}
