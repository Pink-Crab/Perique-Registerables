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

namespace PinkCrab\Registerables\Tests\Taxonomies;

use WP_UnitTestCase;
use PinkCrab\Loader\Loader;



class Base_Taxonomy_Runner extends WP_UnitTestCase {

	/**
	 * Taxonomy.
	 *
	 * @var Taxonomy
	 */
	protected $taxonomy;

	/**
	 * GLoabls instance of the wp taxonomies object.
	 *
	 * @var wp_taxonomies
	 */
	protected $wp_taxonomies;

	/**
	 * Array of terms.
	 *
	 * @var array
	 */
	protected $terms = array();

	/** Defined values in children classes. */
	protected $taxonomy_class;
	protected $settings   = array();
	protected $labels     = array();
	protected $post_types = array();

	public function setUp(): void {
		parent::setup();
		if ( ! $this->taxonomy ) {
			// Create the CPT and Loader instances.
			$this->taxonomy = new $this->taxonomy_class;
			$loader         = new Loader();

			// Run registration.
			$this->taxonomy->register( $loader );
			$loader->register_hooks();

			// Set the rewrite rules.
			\flush_rewrite_rules();

			// Create 5 random terms.
			$this->create_mock_terms();

			// Set the permalinks.
			$this->set_permalink_structure( '/%postname%/' );

			global $wp_taxonomies;
			$this->wp_taxonomies = $wp_taxonomies;
		}
	}

	/**
	 * Creates random terms.
	 *
	 * @return void
	 */
	protected function create_mock_terms(): void {
		for ( $i = 0; $i < 5; $i++ ) {
			$this->terms[] = $this->factory->term->create(
				array(
					'taxonomy' => $this->taxonomy->slug,
				)
			);
		}
	}

	/**
	 * Test the labels.
	 *
	 * @return void
	 */
	public function test_labels(): void {

		// Get labels from WP and class on test.
		$defined_labels = get_taxonomy_labels( get_taxonomy( $this->taxonomy->slug ) );
		// Check they match.
		foreach ( (array) $defined_labels as $key => $value ) {
			if ( ! array_key_exists( $key, $this->labels ) ) {
				continue;
			}
			$this->assertEquals( $value, $this->labels[ $key ], sprintf( '%s from defined not found', $value ) );
		}
	}

	/**
	 * Check the taxonomy is avliable to all requested.
	 *
	 * @return void
	 */
	public function test_cpt_post_types(): void {
		$registered_post_types = array_key_exists( $this->taxonomy->slug, $this->wp_taxonomies )
			? $this->wp_taxonomies[ $this->taxonomy->slug ]->object_type
			: array();
		$this->assertSame( $this->post_types, $registered_post_types );
	}

	/**
	 * Test that all settings are set as expected or with fallbacks.
	 *
	 * @return void
	 */
	public function test_settings(): void {

		$wp_taxonomy = \get_taxonomy( $this->taxonomy->slug );
		foreach ( $this->settings as $property => $expected ) {
			// Check permalinks in array, else matching
			if ( $property === 'rewrite' ) {
				$this->assertEquals(
					$expected,
					$wp_taxonomy->{$property}['slug'],
					sprintf(
						'Failed asserting setting that %s was %s for %s (rewrite, so looking at slug property',
						$property,
						$expected ? 'TRUE' : 'FALSE',
						$this->taxonomy->slug
					)
				);
			} else {
				// Match all other proprerties.
				$this->assertEquals(
					$expected,
					$wp_taxonomy->{$property},
					sprintf(
						'Failed asserting setting that %s was %s for %s',
						$property,
						$expected ? 'TRUE' : 'FALSE',
						$this->taxonomy->slug
					)
				);
			}
		}
	}
}

