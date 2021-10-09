<?php

declare(strict_types=1);

/**
 * An abstract class for registering custom post types.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables;

use PinkCrab\Registerables\Registration_Middleware\Registerable;
use PinkCrab\Registerables\Meta_Box;
use PinkCrab\Registerables\Meta_Data;

abstract class Post_Type implements Registerable {

	/**
	 * Default values for post type.
	 *
	 * @var string
	 * @required
	 */
	public $key;

	/**
	 * The singular key name
	 *
	 * @var string
	 * @required
	 */
	public $singular;

	/**
	 * The plural name.
	 *
	 * @var string
	 * @required
	 */
	public $plural;

	/**
	 * The Dashicon for wp-admin
	 *
	 * @var string
	 */
	public $dashicon = 'dashicons-pets';

	/**
	 * The post types description.
	 *
	 * @var string|null
	 */
	public $description;

	/**
	 * Position in wp-admin menu list.
	 *
	 * @var int|null
	 */
	public $menu_position = 60;

	/**
	 * Should all meta fields use the capabilities
	 *
	 * @var bool|null
	 */
	public $map_meta_cap = false;

	/**
	 * Does this post type have public functionality.
	 *
	 * @var bool|null
	 */
	public $public = true;

	/**
	 * Include post type in frontend menu choices.
	 *
	 * @var bool|null
	 */
	public $show_in_nav_menus = true;

	/**
	 * INclude post type in wp-admin list.
	 *
	 * @var bool|null
	 */
	public $show_in_menu = true;

	/**
	 * Should this be included in the admin bar.
	 *
	 * @var bool|null
	 */
	public $show_in_admin_bar = true;

	/**
	 * Generate any post type UI in wp-admin.
	 *
	 * @var bool|null
	 */
	public $show_ui = true;

	/**
	 * Generate archives on front end.
	 *
	 * @var bool|null
	 */
	public $has_archive = true;

	/**
	 * Is post type hierarchical
	 *
	 * @var bool|null
	 */
	public $hierarchical = false;

	/**
	 * Exclude from search results.
	 *
	 * @var bool|null
	 */
	public $exclude_from_search = false;

	/**
	 * Allow post type to be queried via url.
	 *
	 * @var bool|null
	 */
	public $publicly_queryable = true;

	/**
	 * Can post type be exported.
	 *
	 * @var bool|null
	 */
	public $can_export = true;

	/**
	 * Sets the query_var key for this post type
	 *
	 * @var bool|string
	 */
	public $query_var = false;

	/**
	 * Should all posts by a user be removed if user removed.
	 *
	 * @var bool|null
	 */
	public $delete_with_user = null;

	/**
	 * Triggers the handling of rewrites for this post type.
	 * If false to prevent any rewrites.
	 * Setting to true will use the defined key as the slug.
	 * Passing null will set this as.
	 * array(
	 *  'slug'       => $this->key,
	 *  'with_front' => true,
	 *  'feeds'      => $this->has_archive,
	 *  'pages'      => true,
	 * );
	 *
	 * @var bool|array<string, mixed>|null
	 */
	public $rewrite = null;

	/**
	 * Defines the capabilities of the post type.
	 *
	 * @var string|array<int, string>
	 */
	public $capability_type = 'post';

	/**
	 * Array of capabilities for the post type.
	 *
	 * @var array<int, string>
	 */
	public $capabilities = array();

	/**
	 * Which features are included with the post type (editor, author etc)
	 *
	 * @var array<int, string>
	 */
	public $supports = array();

	/**
	 * Rest
	 */

	/**
	 * Should this post type be shown in rest.
	 *
	 * @var bool|null
	 */
	public $show_in_rest = true;

	/**
	 * The base to use for all CPT routes
	 * If null, will use the $key value.
	 *
	 * @var string|null
	 */
	public $rest_base = null;

	/**
	 * The CPY Rest Controller, defaults to WP_REST_Posts_Controller
	 *
	 * @var string;
	 */
	public $rest_controller_class = \WP_REST_Posts_Controller::class;

	/**
	 * Gutenberg
	 */

	/**
	 * Sets if this post type should use the Gutenberg page builder.
	 *
	 * @var bool|null
	 */
	public $gutenberg = false;

	/**
	 * All block templates included with this cpt.
	 *
	 * @var string[]|null
	 */
	public $templates = array();

	/**
	 * Should the defined templates above be locked
	 * True will lock all defined templates
	 * False can add/remove/move blocks
	 * 'all' will be unable to add/remove/move blocks
	 * 'insert' will be able to only move blocks, add/remove is restricted.
	 *
	 * @var bool|string
	 */
	public $template_lock = false;

	/**
	 * Which taxonomies should be included with this post types.
	 *
	 * @var string[]
	 */
	public $taxonomies = array();

	/**
	 * Filters the labels through child class.
	 *
	 * @param array<string, mixed> $labels
	 * @return array<string, mixed>
	 */
	public function filter_labels( array $labels ): array {
		return $labels;
	}

	/**
	 * Filters the args used to register the CPT.
	 *
	 * @param array<string, mixed> $args
	 * @return array<string, mixed>
	 */
	public function filter_args( array $args ): array {
		return $args;
	}

	/**
	 * Allows for the setting of meta data specifically for this post type.
	 *
	 * @param Meta_Data[] $collection
	 * @return Meta_Data[]
	 */
	public function meta_data( array $collection ): array {
		return $collection;
	}

	/**
	 * Allows for the setting of meta boxes specifically for this post type.
	 *
	 * @param Meta_Box[] $collection
	 * @return Meta_Box[]
	 */
	public function meta_boxes( array $collection ): array {
		return $collection;
	}
}
