<?php

declare(strict_types=1);

/**
 * Post Type for GB posts
 *
 * @since 0.9.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Registerables\Tests;

use WP_UnitTestCase;
use Gin0115\WPUnit_Helpers\Objects;
use PinkCrab\Registerables\Post_Type;
use PinkCrab\Registerables\Registrar\Post_Type_Registrar;

class Test_GB_CPT extends WP_UnitTestCase {

	/**
	 * Get the post type registerable.
	 *
	 * @return \PinkCrab\Registerables\Post_Type
	 */
	protected function get_post_type(): \PinkCrab\Registerables\Post_Type {
		return new class() extends Post_Type{
			public string $key       = 'basic_cpt';
			public string $singular  = 'Basic';
			public string $plural    = 'Basics';
			public ?bool $gutenberg = true;
		};
	}

	/**
	 * @testdox When a post type is registered with GB support, map_meta_cap should be set as true, if not defined.
	 * @issue https://github.com/Pink-Crab/Perique-Registerables/issues/66
	 */
	public function test_map_meta_cap_is_true_when_gutenberg_is_true(): void {
		$post_type = $this->get_post_type();
		$registrar = $this->createMock( Post_Type_Registrar::class );

		// Call the protected compile args method.
		$args = Objects::invoke_method( $registrar, 'compile_args', array( $post_type ) );

		$this->assertTrue( $args['map_meta_cap'] );
	}
}
