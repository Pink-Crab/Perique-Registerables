<?php

declare(strict_types=1);

/**
 * Tests various settings/defaults
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Registerables\Tests\Taxonomies;

use WP_UnitTestCase;
use InvalidArgumentException;
use PinkCrab\PHPUnit_Helpers\Reflection;
use PinkCrab\Registerables\Tests\Fixtures\Taxonomies\Basic_Tag_Taxonomy;



class Test_Taxonony_Settings extends WP_UnitTestCase {


	public function test_sets_optional_args(): void {
		$taxonomy = new Basic_Tag_Taxonomy();

		// Set the options values.
		Reflection::set_private_property( $taxonomy, 'capabilities', array( 'capabilities' ) );
		Reflection::set_private_property( $taxonomy, 'update_count_callback', 'update_count_callback' );
		Reflection::set_private_property( $taxonomy, 'meta_box_cb', array( 'meta_box_cb' ) );
		Reflection::set_private_property( $taxonomy, 'default_term', array( 'default_term' ) );

		// Run though optional args.
		$properties = Reflection::invoke_private_method(
			$taxonomy,
			'optional_args',
			array( array() )
		);

		// Test the args have been added to the array.
		$this->assertArrayHasKey( 'capabilities', $properties );
		$this->assertArrayHasKey( 'update_count_callback', $properties );
		$this->assertArrayHasKey( 'meta_box_cb', $properties );
		$this->assertArrayHasKey( 'default_term', $properties );

	}

    /**
     * Test exception thrown if no slug
     *
     * @return void
     */
	public function test_throws_exception_no_slug() {
		$taxonomy = new Basic_Tag_Taxonomy();
		Reflection::set_private_property( $taxonomy, 'slug', false );

		$this->expectException( InvalidArgumentException::class );
		Reflection::invoke_private_method( $taxonomy, 'validate', array() );
	}

    /**
     * Test exception thrown if no singular
     *
     * @return void
     */
	public function test_throws_exception_no_singular() {
		$taxonomy = new Basic_Tag_Taxonomy();
		Reflection::set_private_property( $taxonomy, 'singular', false );

		$this->expectException( InvalidArgumentException::class );
		Reflection::invoke_private_method( $taxonomy, 'validate', array() );
	}

    /**
     * Test exception thrown if no plural
     *
     * @return void
     */
	public function test_throws_exception_no_plural() {
		$taxonomy = new Basic_Tag_Taxonomy();
		Reflection::set_private_property( $taxonomy, 'plural', false );

		$this->expectException( InvalidArgumentException::class );
		Reflection::invoke_private_method( $taxonomy, 'validate', array() );
	}
}
