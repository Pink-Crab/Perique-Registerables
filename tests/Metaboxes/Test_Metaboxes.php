<?php

declare(strict_types=1);

/**
 * Base class for all taxonomy tests.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Registerables\Tests\Metaboxes;

use PinkCrab\Loader\Loader;
use PinkCrab\PHPUnit_Helpers\Reflection;
use PinkCrab\Registerables\MetaBox;
use WP_UnitTestCase;


class Test_Metaboxes extends WP_UnitTestCase {

	/**
	 * Test can add actions to a metabox
	 *
	 * @return void
	 */
	public function test_can_add_actions(): void {
		$metabox = MetaBox::normal( 'test' );
		$metabox->add_action(
			'test',
			function() {

			}
		);
		$this->assertNotEmpty( Reflection::get_private_property( $metabox, 'actions' ) );
	}

	/**
	 * Tests that actions added, are added laoder on register()
	 *
	 * @return void
	 */
	public function test_registers_actions(): void {
		$metabox = MetaBox::normal( 'test' );
		$metabox->add_action(
			'test',
			function() {

			}
		);

		$loader = new Loader();
		$metabox->register( $loader );

		// Extract all global hooks.
		$actions = Reflection::get_private_property( $loader, 'global' );
		$actions = Reflection::get_private_property( $actions, 'hooks' );

		// Extract our options.
		$extracted_action = array_filter($actions, function($e){
			return $e['handle'] === 'test';
		});

		// Ensure we have our hook
		$this->assertNotEmpty( $extracted_action );
	}

	/**
	 * Tests is_active method, based on screen type.
	 *
	 * @return void
	 */
	public function test_is_active(): void {
		// Set screen to admin dashboard
		set_current_screen( 'dashboard' );

		$metabox = MetaBox::normal( 'test' );
		$metabox->screen( 'post' );

		// test not currently active.
		$this->assertFalse(
			Reflection::invoke_private_method( $metabox, 'is_active', array() )
		);

		// Mock the current screen to edit post.
		set_current_screen( 'edit.php' );
		$screen            = get_current_screen();
		$screen->post_type = 'post';

		// Should now be active.
		$this->assertTrue(
			Reflection::invoke_private_method( $metabox, 'is_active', array() )
		);

		// Set screen to admin dashboard
		set_current_screen( 'dashboard' );
	}
}
