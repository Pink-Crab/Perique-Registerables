<?php

declare(strict_types=1);
/**
 * Basic CPT Mock Object
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Fixtures\CPT;

use PinkCrab\Registerables\Post_Type;

class Hidden_CPT extends Post_Type {

	public string $key       = 'hidden_cpt';
	public string $singular  = 'Hide';
	public string $plural    = 'Hidden';
	public ?bool $public    = false;
	public array $supports  = array( 'thumbnail' );
	public ?bool $gutenberg = false;

	// Remove all ui
	public ?bool $show_ui           = false;
	public ?bool $show_in_nav_menus = false;
	public ?bool $show_in_admin_bar = false;
	public ?bool $has_archive       = false;

	// Only allow admins to do anything.
	public array $capabilities = array(
		'edit_post'              => 'manage_options',
		'read_post'              => 'manage_options',
		'delete_post'            => 'manage_options',
		'edit_posts'             => 'manage_options',
		'edit_others_posts'      => 'manage_options',
		'delete_posts'           => 'manage_options',
		'publish_posts'          => 'manage_options',
		'read_private_posts'     => 'manage_options',
		'read'                   => 'manage_options',
		'delete_private_posts'   => 'manage_options',
		'delete_published_posts' => 'manage_options',
		'delete_others_posts'    => 'manage_options',
		'edit_private_posts'     => 'manage_options',
		'edit_published_posts'   => 'manage_options',
		'create_posts'           => 'manage_options',
	);
}
