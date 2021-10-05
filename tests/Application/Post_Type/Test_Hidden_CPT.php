<?php

declare(strict_types=1);

/**
 * Hidden CPT Test Cases
 *
 * @since 0.1.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Application\Post_Type;

use PinkCrab\Registerables\Tests\Fixtures\CPT\Hidden_CPT;
use PinkCrab\Registerables\Tests\Application\Post_Type\Base_CPT_Case;


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
		'description'           => 'Hidden',
		'public'                => \false,
		'hierarchical'          => \false,
		'exclude_from_search'   => \false,
		'publicly_queryable'    => \true,
		'show_ui'               => \false,
		'show_in_menu'          => \false,
		'show_in_nav_menus'     => \false,
		'show_in_admin_bar'     => \false,
		'menu_position'         => 60,
		'menu_icon'             => 'dashicons-pets',
		'capability_type'       => 'post',
		'map_meta_cap'          => \false,
		'register_meta_box_cb'  => \false,
		'taxonomies'            => array(),
		'has_archive'           => \false,
		'query_var'             => \false,
		'can_export'            => \true,
		'delete_with_user'      => null,
		'rest_base'             => 'hidden_cpt',
		'template'              => array(),
		'template_lock'         => false,
		'rewrite'               => false,
		'rest_controller_class' => \WP_REST_Posts_Controller::class,
		'rest_controller'       => null,
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

	protected $allow_gutenberg = false;
}
