<?php

declare(strict_types=1);

/**
 * Loader tests.
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core
 */

namespace PinkCrab\Modules\Registerables\Tests;

// Include our fixtures.
require_once \dirname( __FILE__, 2 ) . '/Fixtures/Base_CPT_Case.php';
require_once \dirname( __FILE__, 2 ) . '/Fixtures/Hidden_CPT.php';


use PinkCrab\Core\Tests\Fixtures\Mock_Objects\Hidden_CPT;


class Test_Hidden_CPT extends Base_CPT_Case {

	protected $cpt_type = Hidden_CPT::class;

	protected $supports = array(
		'title'           => \false,
		'editor'          => \false,
		'author'          => \false,
		'thumbnail'       => \true,
		'excerpt'         => \false,
		'trackbacks'      => \false,
		'custom-fields'   => \false,
		'comments'        => \false,
		'revisions'       => \false,
		'page-attributes' => \false,
		'post-formats'    => \false,
	);

	protected $settings = array(
		'description'          => '',
		'public'               => \false,
		'hierarchical'         => \false,
		'exclude_from_search'  => \false,
		'publicly_queryable'   => \true,
		'show_ui'              => \false,
		'show_in_menu'         => \false,
		'show_in_nav_menus'    => \false,
		'show_in_admin_bar'    => \false,
		'menu_position'        => 60,
		'menu_icon'            => 'dashicons-pets',
		'capability_type'      => 'post',
		'map_meta_cap'         => \false,
		'register_meta_box_cb' => \false,
		'taxonomies'           => array(),
		'has_archive'          => \false,
		'query_var'            => \false,
		'can_export'           => \true,
		'delete_with_user'     => null,
	);

	protected $user_access_create = array(
		'administrator' => \true,
		'editor'        => \false,
		'author'        => \false,
		'contributor'   => \false,
		'subscriber'    => \false,
	);

	protected $user_access_view = array(
		'administrator' => \true,
		'editor'        => \false,
		'author'        => \false,
		'contributor'   => \false,
		'subscriber'    => \false,
	);

	protected $user_access_delete = array(
		'administrator' => \true,
		'editor'        => \false,
		'author'        => \false,
		'contributor'   => \false,
		'subscriber'    => \false,
	);

	protected $user_access_edit_others = array(
		'administrator' => \true,
		'editor'        => \false,
		'author'        => \false,
		'contributor'   => \false,
		'subscriber'    => \false,
	);

	protected $has_single = false;
}
