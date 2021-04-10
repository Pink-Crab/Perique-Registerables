<?php

declare(strict_types=1);

/**
 * Tests taxonomies with default terms and term meta.
 *
 * @since 0.4.1
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Registerables\Tests\Taxonomies;

use WP_UnitTestCase;
use PinkCrab\Loader\Loader;
use PinkCrab\Registerables\Tests\Fixtures\Taxonomies\Tag_With_Meta_Taxonomy;

class Test_Taxonomy_With_Term_Meta extends WP_UnitTestCase {

    /** @return array<\WP_Taxonomy> */
	protected $taxonomy;

	public function setUp(): void {
		$this->taxonomy = new Tag_With_Meta_Taxonomy();
		$this->taxonomy->register( new Loader() );
	}

    /** @return array<\WP_Term> */
	protected function get_terms(): array {
		return get_terms(
			array(
				'taxonomy'   => $this->taxonomy::get_slug(),
				'hide_empty' => false,
			)
		);
	}

	/** @testdox It should be possible to set a default term and have it created when the taxonomy is registered. */
	public function test_default_term(): void {
		$this->assertNotEmpty(
			array_filter(
				$this->get_terms(),
				function( \WP_Term $term ): bool {
					return $term->slug === Tag_With_Meta_Taxonomy::DEFAULT_TERM_SLUG;
				}
			)
		);
	}

    /** @testdox It should be possible to set term meta when defining a taxonomy. */
	public function test_can_set_term_meta(): void {
        $terms = $this->get_terms();
        $term_id = $terms[0]->term_id;

        // Check default values.
        $meta1 = get_term_meta($term_id, Tag_With_Meta_Taxonomy::META_1['key'], true);
        $this->assertEquals(Tag_With_Meta_Taxonomy::META_1['default'], $meta1);
        $meta2 = get_term_meta($term_id, Tag_With_Meta_Taxonomy::META_2['key'], true);
        $this->assertEquals(Tag_With_Meta_Taxonomy::META_2['default'], $meta2);
	}


}
