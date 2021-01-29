<?php

declare(strict_types=1);

/**
 * Tests that exceptions are thrown for missing values.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Registerables\Tests;

use WP_UnitTestCase;
use InvalidArgumentException;
use PinkCrab\PHPUnit_Helpers\Reflection;
use PinkCrab\Registerables\Tests\Fixtures\CPT\Basic_CPT;
use PinkCrab\Registerables\Tests\Fixtures\CPT\Invlaid_CPT;


class Test_CPT_Failures extends WP_UnitTestCase {

	/**
	 * Test exception thrown if no key
	 *
	 * @return void
	 */
	public function test_throws_exception_no_slug() {
		$cpt = new Basic_CPT();
		Reflection::set_private_property( $cpt, 'key', null );

		$this->expectException( InvalidArgumentException::class );
		Reflection::invoke_private_method( $cpt, 'validate', array() );
	}

	/**
	 * Test exception thrown if no singular
	 *
	 * @return void
	 */
	public function test_throws_exception_no_singular() {
		$cpt = new Basic_CPT();
		Reflection::set_private_property( $cpt, 'singular', false );

		$this->expectException( InvalidArgumentException::class );
		Reflection::invoke_private_method( $cpt, 'validate', array() );
	}

	/**
	 * Test exception thrown if no plural
	 *
	 * @return void
	 */
	public function test_throws_exception_no_plural() {
		$cpt = new Basic_CPT();
		Reflection::set_private_property( $cpt, 'plural', false );

		$this->expectException( InvalidArgumentException::class );
		Reflection::invoke_private_method( $cpt, 'validate', array() );
	}
}
