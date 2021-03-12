<?php

/**
 * Metabox item entity
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @since 1.0.0
 * @package Gin0115/WPUnit_Helpers
 */

declare( strict_types=1 );

namespace Gin0115\WPUnit_Helpers\WP\Entities;

class Metabox_Entity {

	/**
	 * All post types this metabox is applied to.
	 * @var string
	 */
	public $post_type;
	/**
	 * Position/Context (Side|Normal)
	 * @var string
	 */
	public $position;
	/**
	 * Display priority for sorting.
	 * @var string
	 */
	public $priority;
	/**
	 * Internal WP reference, used as the key in global metabox array.
	 * Should not be used to check compare with a defined metabox key, use $id
	 * @var string
	 */
	public $name;
	/**
	 * Has the callback been registered yet.
	 * Represents false for metabox details in wp_metabox global
	 * @var bool
	 */
	public $isset = false;
	/**
	 * Defined metabox id/key, used when registering.
	 *
	 * @var string
	 */
	public $id;
	/**
	 * Metabox title
	 *
	 * @var string
	 */
	public $title;
	/**
	 * Defined callback
	 *
	 * @var callable
	 */
	public $callback;
	/**
	 * Defined args
	 *
	 * @var array<string, mixed>
	 */
	public $args;

}
