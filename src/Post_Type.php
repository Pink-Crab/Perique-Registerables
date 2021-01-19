<?php

declare(strict_types=1);
/**
 * An abstract class for resitering custom post types.
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

use PinkCrab\Core\Application\App;
use PinkCrab\Core\Interfaces\Registerable;
use PinkCrab\Core\Services\Registration\Loader;

abstract class Post_Type implements Registerable {


	/**
	 * Defulat values for post type.
	 *
	 * @var string
	 * @required
	 */
	public $key;

	/**
	 * The signular key name
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
	 * A custom slug
	 * If not defined, will use the key.
	 * @var string|null
	 */
	public $slug = null;

	/**
	 * The Dashicon for wp-admin
	 *
	 * @var string
	 */
	public $dashicon = 'dashicons-pets';

	/**
	 * Position in wp-admin menu list.
	 *
	 * @var int
	 */
	public $menu_position = 60;

	/**
	 * Array of meta boxes for the wp-edit screen.
	 *
	 * @var array<int, MetaBox>
	 */
	protected $metaboxes = array();

	/**
	 * Does this post type have public functionality.
	 *
	 * @var bool
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
	 * @var bool
	 */
	public $show_in_menu = true;

	/**
	 * Generate any post type UI in wp-admin.
	 *
	 * @var bool
	 */
	public $show_ui = true;

	/**
	 * Generate archives on front end.
	 *
	 * @var bool
	 */
	public $has_archive = true;

	/**
	 * Is post type hierarchical
	 *
	 * @var bool
	 */
	public $hierarchical = false;

	/**
	 * Exclude from search results.
	 *
	 * @var bool
	 */
	public $exclude_from_search = false;

	/**
	 * Allow post type to be quiered via url.
	 *
	 * @var bool
	 */
	public $publicly_queryable = true;

	/**
	 * Can post type be exported.
	 *
	 * @var bool
	 */
	public $can_export = true;

	/**
	 * Sets the query_var key for this post type
	 *
	 * @var bool|string
	 */
	public $query_var = false;

	/**
	 * Triggers the handling of rewrites for this post type.
	 * If false uses slug as base for permalinks.
	 *
	 * @var bool|array<string, mixed>|null
	 */
	public $rewrite = null;

	/**
	 * Defines the cabailities of the post type.
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
	 * Which taxonomies should be included with this post types.
	 *
	 * @var array<int, string>
	 */
	public $taxonmies = array();


	/**
	 * Creates an instance of the
	 */
	public function __construct() {
		// Set the rewrite rules if not defined.
		if ( is_null( $this->rewrite ) ) {
			$this->rewrite = array(
				'slug'       => $this->slug(),
				'with_front' => true,
				'feeds'      => false,
				'pages'      => false,
			);
		}
	}

	/**
	 * Used to regiser metaboxes.
	 *
	 * @return void
	 */
	public function metaboxes(): void {}


	/**
	 * Check we have valid
	 *
	 * @return void
	 */
	private function validate() {
		if ( ! $this->key ) {
			trigger_error( 'No key defined.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		}
		if ( ! $this->singular ) {
			trigger_error( 'No singular defined.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		}
		if ( ! $this->plural ) {
			trigger_error( 'No plural defined.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
		}
	}

	/**
	 * Register the post type using defined variables within the
	 *
	 * @param Loader $loader
	 * @return void
	 */
	public function register( Loader $loader ): void {

		// Ensure we have all essential values.
		$this->validate();

		$labels = array(
			'name'               => $this->plural,
			'singular_name'      => $this->singular,
			'add_new'            => _x( 'Add New', 'pinkcrab' ),
			'add_new_item'       => 'Add New ' . $this->singular,
			'edit_item'          => 'Edit ' . $this->singular,
			'new_item'           => 'New ' . $this->singular,
			'view_item'          => 'View ' . $this->singular,
			'search_items'       => 'Search ' . $this->singular,
			'not_found'          => 'No ' . strtolower( $this->plural ) . ' found',
			'not_found_in_trash' => 'No ' . strtolower( $this->plural ) . ' found in Trash',
			'parent_item_colon'  => 'Parent ' . $this->singular . ':',
			'menu_name'          => $this->plural,
		);

		$args = array(
			'labels'              => $this->filter_labels( $labels ),           // @phpstan-ignore-next-line
			'hierarchical'        => is_bool( $this->hierarchical ) ? $this->hierarchical : false,
			'supports'            => $this->supports,           // @phpstan-ignore-next-line
			'public'              => is_bool( $this->public ) ? $this->public : true, // @phpstan-ignore-next-line
			'show_ui'             => is_bool( $this->show_ui ) ? $this->show_ui : true, // @phpstan-ignore-next-line
			'show_in_menu'        => is_bool( $this->show_in_menu ) ? $this->show_in_menu : true,
			'menu_position'       => $this->menu_position ?: 60,
			'menu_icon'           => $this->dashicon ?: 'dashicons-pets',
			'show_in_nav_menus'   => is_bool( $this->show_in_nav_menus ) ? $this->show_in_nav_menus : true, // @phpstan-ignore-next-line
			'publicly_queryable'  => is_bool( $this->publicly_queryable ) ? $this->publicly_queryable : true, // @phpstan-ignore-next-line
			'exclude_from_search' => is_bool( $this->exclude_from_search ) ? $this->exclude_from_search : true, // @phpstan-ignore-next-line
			'has_archive'         => is_bool( $this->has_archive ) ? $this->has_archive : true,
			'query_var'           => is_bool( $this->query_var ) ? $this->query_var : false, // @phpstan-ignore-next-line
			'can_export'          => is_bool( $this->can_export ) ? $this->can_export : true,
			'rewrite'             => is_bool( $this->rewrite ) ? $this->rewrite : false,
			'capability_type'     => $this->capability_type ?: 'page',
			'capabilities'        => $this->capabilities ?: array(),
			'taxonomies'          => $this->taxonmies ?: array(),
		);
		register_post_type( $this->key, $this->filter_args( $args ) );

		// If we have any metaboxes, register them.
		$this->metaboxes();
		if ( ! empty( $this->metaboxes ) ) {
			$this->register_metaboxes( $loader );
		}

	}

	/**
	 * Returns the slug if set, else the CPT key.
	 *
	 * @return string|null
	 */
	public function slug(): ?string {
		return $this->slug ?? $this->key;
	}

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
	private function filter_args( array $args ): array {
		return $args;
	}

	/**
	 * Gets the slug statically.
	 *
	 * @return string
	 */
	public static function get_slug(): string {
		$cpt = App::make( static::class );
		return $cpt->slug();
	}

	/**
	 * Registers the metaboxes.
	 *
	 * @param Loader $loader
	 * @return void
	 */
	private function register_metaboxes( Loader $loader ): void {
		foreach ( $this->metaboxes as $metabox ) {
			$metabox->screen( $this->key ); // Add this post type to the screen list.
			$metabox->register( $loader ); // Pass loader into the MetaBoxes for registration.
		}
	}

}
