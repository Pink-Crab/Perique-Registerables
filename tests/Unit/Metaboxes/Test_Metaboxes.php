<?php

declare(strict_types=1);

/**
 * Base class for all taxonomy tests.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Registerables\Tests\Unit\Metaboxes;

use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Registerables\Meta_Box;
use WP_UnitTestCase;


class Test_Metaboxes extends WP_UnitTestCase {

	/** @testdox It should be possible to create a NORMAL meta box from a static constructor */
	public function test_static_constructor_normal(): void {
		$mb = Meta_Box::normal( 'normal' );
		$this->assertEquals( 'normal', $mb->context );
		$this->assertEquals( 'normal', $mb->key );
		$this->assertEquals( 'default', $mb->priority );
	}

	/** @testdox It should be possible to create a SIDE meta box from a static constructor */
	public function test_static_constructor_side(): void {
		$mb = Meta_Box::side( 'side' );
		$this->assertEquals( 'side', $mb->context );
		$this->assertEquals( 'side', $mb->key );
		$this->assertEquals( 'default', $mb->priority );
	}

	/** @testdox it should be possible to set a label for a meta box */
	public function test_set_label(): void {
		$mb = new Meta_Box( 'label' );
		$mb->label( 'foo' );
		$this->assertEquals( 'foo', $mb->label );
	}

	/** @testdox it should be possible to set multiple screens the meta box is displayed on. */
	public function test_add_screen(): void {
		$mb = new Meta_Box( 'screen' );
		$mb->screen( 'foo' );
		$mb->screen( 'bar' );
		$this->assertContains( 'foo', $mb->screen );
		$this->assertContains( 'bar', $mb->screen );
	}

	/** @testdox It should be possible to add view vars which are passed through to the view */
	public function test_can_add_view_vars(): void {
		$mb = new Meta_Box( 'view_vars' );
		$mb->view_vars(
			array(
				'a' => 'apple',
				'b' => 'bananas',
			)
		);
		$this->assertArrayHasKey( 'a', $mb->view_vars );
		$this->assertEquals( 'apple', $mb->view_vars['a'] );
		$this->assertArrayHasKey( 'b', $mb->view_vars );
		$this->assertEquals( 'bananas', $mb->view_vars['b'] );
	}

	/** @testdox it should be possible to set a view template for a meta box */
	public function test_set_view_template(): void {
		$mb = new Meta_Box( 'view_template' );
		$mb->view_template( 'foo' );
		$this->assertEquals( 'foo', $mb->view_template );
	}

	/** @testdox it should be possible to set a view callable for a meta box */
	public function test_set_view_callable(): void {
		$mb   = new Meta_Box( 'view_callable' );
		$func = function(){};
		$mb->view( $func );
		$this->assertSame( $func, $mb->view );
	}

	/**
	 * Test can add actions to a metabox
	 *
	 * @return void
	 */
	public function test_can_add_actions(): void {
		$mb = Meta_Box::normal( 'test' );
		$mb->add_action( 'true', '__return_true', 999, 5 );
		$mb->add_action( 'false', '__return_false' );
		$this->assertNotEmpty( $mb->actions );
		$this->assertCount(2, $mb->actions);
		
		$this->assertArrayHasKey('true', $mb->actions);
		$this->assertEquals('__return_true', $mb->actions['true']['callback']);
		$this->assertEquals(999, $mb->actions['true']['priority']);
		$this->assertEquals(5, $mb->actions['true']['params']);

		$this->assertArrayHasKey('false', $mb->actions);
		$this->assertEquals('__return_false', $mb->actions['false']['callback']);
		$this->assertEquals(10, $mb->actions['false']['priority']);
		$this->assertEquals(1, $mb->actions['false']['params']);
	}

}
