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

use PinkCrab\Registerables\Tests\Application\Taxonomies\Base_Taxonomy_Runner;
use PinkCrab\Registerables\Tests\Fixtures\Taxonomies\Basic_Hierarchical_Taxonomy;


class Test_Basic_Hierarchical_Taxonomy extends Base_Taxonomy_Runner {

	protected $taxonomy_class = Basic_Hierarchical_Taxonomy::class;

	protected $post_types = array( 'basic_cpt', 'page' );

	protected $settings = array(
		'name'                  => 'basic_hier_tax',
		'label'                 => 'Basic Hier Taxonomies',
		'description'           => 'The Basic Hier Taxonomy.',
		'publicly_queryable'    => true,
		'show_ui'               => false,
		'show_in_menu'          => false,
		'show_in_nav_menus'     => false,
		'show_tagcloud'         => false,
		'show_in_quick_edit'    => false,
		'show_admin_column'     => false,
		'rewrite'               => 'basic_hier_tax',
		'query_var'             => false,
		'update_count_callback' => '_update_post_term_count',
		'show_in_rest'          => false,
		'rest_base'             => 'basic_hier_tax',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
		'default_term'          => null,
	);

	protected $labels = array(
		'name'              => 'Basic Hier Taxonomies',
		'singular_name'     => 'Basic Hier Taxonomy',
		'search_items'      => 'Search Basic Hier Taxonomies',
		'all_items'         => 'All Basic Hier Taxonomies',
		'parent_item'       => 'Parent Basic Hier Taxonomy', 
		'parent_item_colon' => 'Parent Basic Hier Taxonomy:',
		'edit_item'         => 'Edit Basic Hier Taxonomy',
		'update_item'       => 'Update Basic Hier Taxonomy',
		'add_new_item'      => 'Add New Basic Hier Taxonomy',
		'new_item_name'     => 'New Basic Hier Taxonomy',
		'view_item'         => 'View Basic Hier Taxonomy',
		'menu_name'         => 'Basic Hier Taxonomies',
		'popular_items'     => 'Popular Basic Hier Taxonomies',
		'back_to_items'     => '← Back to Basic Hier Taxonomies',
	);

	/** Additional Tests */

	public function test_permalinks() {
		$this->assertRegexp( '/basic_hier_tax/', get_term_link( $this->terms[0] ) );
	}
}
