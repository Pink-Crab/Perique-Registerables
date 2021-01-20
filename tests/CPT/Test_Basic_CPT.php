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

namespace PinkCrab\Registerables\Tests;

use PinkCrab\Registerables\Tests\Fixtures\CPT\Basic_CPT;
use PinkCrab\Registerables\Tests\Fixtures\CPT\Base_CPT_Case;


class Test_Basic_CPT extends Base_CPT_Case {

	protected $cpt_type = Basic_CPT::class;

	protected $supports = array(
		'title'           => \true,
		'editor'          => \true,
		'author'          => \false,
		'thumbnail'       => \false,
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
		'public'               => \true,
		'hierarchical'         => \false,
		'exclude_from_search'  => \false,
		'publicly_queryable'   => \true,
		'show_ui'              => \true,
		'show_in_menu'         => \true,
		'show_in_nav_menus'    => \true,
		'show_in_admin_bar'    => \true,
		'menu_position'        => 60,
		'menu_icon'            => 'dashicons-pets',
		'capability_type'      => 'post',
		'map_meta_cap'         => \true,
		'register_meta_box_cb' => \false,
		'taxonomies'           => array(),
		'supports'             => array(),
		'has_archive'          => \true,
		'query_var'            => \false,
		'can_export'           => \true,
		'delete_with_user'     => null,
	);

	protected $user_access_create = array(
		'administrator' => \true,
		'editor'        => \true,
		'author'        => \true,
		'contributor'   => \true,
		'subscriber'    => \false,
	);

	protected $user_access_view = array(
		'administrator' => \true,
		'editor'        => \true,
		'author'        => \true,
		'contributor'   => \true,
		'subscriber'    => \true,
	);

	protected $user_access_delete = array(
		'administrator' => \true,
		'editor'        => \true,
		'author'        => \true,
		'contributor'   => \true,
		'subscriber'    => \false,
	);

	protected $user_access_edit_others = array(
		'administrator' => \true,
		'editor'        => \true,
		'author'        => \false,
		'contributor'   => \false,
		'subscriber'    => \false,
	);

	protected $has_single = true;
}
