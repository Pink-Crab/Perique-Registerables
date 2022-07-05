<?php

declare(strict_types=1);

/**
 * Tests the basic tag like taxonomy
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Perique
 */

namespace PinkCrab\Registerables\Tests\Application\Taxonomies;

use PinkCrab\Registerables\Tests\Fixtures\Taxonomies\Basic_Tag_Taxonomy;
use PinkCrab\Registerables\Tests\Application\Taxonomies\Base_Taxonomy_Runner;


class Test_Basic_Tag_Taxonomy extends Base_Taxonomy_Runner {

	protected $taxonomy_class = Basic_Tag_Taxonomy::class;

	protected $post_types = array( 'basic_cpt' );

	protected $settings = array(
		'name'                  => 'basic_tag_tax',
		'label'                 => 'Basic Tag Taxonomies',
		'description'           => 'The Basic Tag Taxonomy.',
		'public'                => true,
		'publicly_queryable'    => true,
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => true,
		'show_tagcloud'         => false,
		'show_in_quick_edit'    => true,
		'show_admin_column'     => true,
		'rewrite'               => 'basic_tag_tax',
		'query_var'             => false,
		'update_count_callback' => '_update_post_term_count',
		'show_in_rest'          => false,
		'rest_base'             => 'basic_tag_tax',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'default_term'          => null,
	);

	protected $labels = array(
		'name'              => 'Basic Tag Taxonomies',
		'singular_name'     => 'Basic Tag Taxonomy',
		'search_items'      => 'Search Basic Tag Taxonomies',
		'all_items'         => 'All Basic Tag Taxonomies',
		'parent_item'       => null, // Flat
		'parent_item_colon' => null, // Flat
		'edit_item'         => 'Edit Basic Tag Taxonomy',
		'update_item'       => 'Update Basic Tag Taxonomy',
		'add_new_item'      => 'Add New Basic Tag Taxonomy',
		'new_item_name'     => 'New Basic Tag Taxonomy',
		'view_item'         => 'View Basic Tag Taxonomy',
		'menu_name'         => 'Basic Tag Taxonomies',
		'popular_items'     => 'Popular Basic Tag Taxonomies',
		'back_to_items'     => '← Back to Basic Tag Taxonomies',
	);

	/** Additional Tests */

	public function test_permalinks() {
		$this->assertRegexp( '/basic_tag_tax/', get_term_link( $this->terms[0] ) );
	}

	/** @testdox It should be possible to add custom capabilities and have these xored with the defaults */
	public function test_capabilities() {
		$expected = array(
			'manage_terms' => 'custom_terms',
			'edit_terms'   => 'manage_categories',
			'delete_terms' => 'custom_delete',
			'assign_terms' => 'edit_posts',
		);

		// Check values match.
		$this->assertEquals( $expected, (array) \get_taxonomy( 'basic_tag_tax' )->cap );
	}
}
