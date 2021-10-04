<?php

declare(strict_types=1);

/**
 * An abstract class for registering taxonomies.
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

use InvalidArgumentException;
use PinkCrab\Registerables\Meta_Data;

use PinkCrab\Perique\Application\App;
use PinkCrab\Perique\Interfaces\Registerable;
use PinkCrab\Loader\Hook_Loader;


abstract class Taxonomy implements Registerable {

	/**
	 * The singular label
	 *
	 * @var string
	 * @required
	 */
	public $singular;

	/**
	 * Plural label
	 *
	 * @var string
	 * @required
	 */
	public $plural;

	/**
	 * Taxonomy slug
	 *
	 * @var string
	 * @required
	 */
	public $slug;

	/**
	 * The taxononmies label.
	 * Uses plural if not set.
	 *
	 * @var string|null
	 */
	public $label;

	/**
	 * The taxonomy description.
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Which post types should this taxonomy be applied to.
	 *
	 * @var array<int, mixed>
	 */
	public $object_type = array( 'post' );

	/**
	 * Should this taxonomy have a hierarchy
	 *
	 * @var bool
	 */
	public $hierarchical = false;

	/**
	 * Render WP_Admin UI
	 *
	 * @var bool
	 */
	public $show_ui = true;

	/**
	 * Show in WP_Admin menu list.
	 *
	 * @var bool
	 */
	public $show_in_menu = true;

	/**
	 * Undocumented variable
	 *
	 * @var bool
	 */
	public $show_admin_column = true;

	/**
	 * Include in the tag cloud.
	 *
	 * @var bool
	 */
	public $show_tagcloud = false;

	/**
	 * Inlcude in quick edit.
	 *
	 * @var bool
	 */
	public $show_in_quick_edit = true;

	/**
	 * Should terms remain in the order added
	 * if false will be alphabetical.
	 *
	 * @var bool
	 */
	public $sort = true;

	/**
	 * Render wp meta box.
	 *
	 * @var callable|null
	 */
	public $meta_box_cb;

	/**
	 * Include in rest
	 *
	 * @var bool
	 */
	public $show_in_rest = false;

	/**
	 * Base rest path.
	 * If not set, will use taxonomy slug
	 *
	 * @var string|null
	 */
	public $rest_base;

	/**
	 * Rest base controller.
	 *
	 * @var string
	 */
	public $rest_controller_class = 'WP_REST_Terms_Controller';

	/**
	 * Is this Taxonomy to be used frontend wise
	 *
	 * @var bool
	 */
	public $public = true;

	/**
	 * Whether the taxonomy is publicly queryable.
	 *
	 * @var bool
	 */
	public $publicly_queryable = true;

	/**
	 * Define a custom query var, if false with use $this->slug
	 *
	 * @var bool|string
	 */
	public $query_var = false;

	/**
	 * Rewrite the peramlinks structure.
	 * If set to true will use the default of the slug.
	 *
	 * @var array<string, mixed>|bool
	 */
	public $rewrite = true;

	/**
	 * String of function name used for counting.
	 * If blank string will use the internal counting functions.
	 * Must be a string and not an inline callable.
	 *
	 * @var string|null
	 */
	public $update_count_callback;

	/**
	 * Array of capabilities for the taxonomy
	 *
	 * @var array<string, mixed>|null
	 */
	public $capabilities;

	/**
	 * Sets the default term for the taxonomy
	 *
	 * @var array<string, mixed>|null
	 */
	public $default_term;

	/**
	 * Array of all pre determined term meta.
	 *
	 * @var array<Meta_Data>
	 */
	public $meta_data = array();

	/**
	 * Runs prior to registration.
	 *
	 * @return void
	 */
	public function set_up(): void {}

	/**
	 * Used to regiser meta_data.
	 *
	 * @return void
	 */
	public function meta_data(): void {}

	/**
	 * Compiles the labels for a hierarchical taxonomy.
	 *
	 * @return array<string, mixed>
	 */
	protected function hierarchical_labels(): array {
		return array(
			'name'              => $this->label ?? $this->plural,
			'singular_name'     => $this->singular,
			'search_items'      => 'Search ' . $this->plural,
			'all_items'         => "All {$this->plural}",
			'parent_item'       => "Parent {$this->singular}",
			'parent_item_colon' => "Parent {$this->singular}:",
			'edit_item'         => "Edit {$this->singular}",
			'update_item'       => "Update {$this->singular}",
			'add_new_item'      => "Add New {$this->singular}",
			'new_item_name'     => "New {$this->singular} Name",
			'view_item'         => "View {$this->singular}",
			'menu_name'         => $this->plural,
			'popular_items'     => "Popular {$this->plural}",
			'back_to_items'     => "← Back to {$this->plural}",
		);
	}

	/**
	 * Gets the labels for a flat taxonomy.
	 *
	 * @return array<string, mixed>
	 */
	protected function flat_labels(): array {
		return array(
			'name'                       => $this->label ?? $this->plural,
			'label'                      => $this->label ?? $this->plural,
			'singular_name'              => $this->singular,
			'search_items'               => 'Search ' . $this->plural,
			'popular_items'              => "Popular {$this->plural}",
			'all_items'                  => "All {$this->plural}",
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => "Edit {$this->singular}",
			'update_item'                => "Update {$this->singular}",
			'add_new_item'               => "Add New {$this->singular}",
			'new_item_name'              => "New {$this->singular} Name",
			'menu_name'                  => $this->plural,
			'separate_items_with_commas' => "Separate {$this->plural} with commas",
			'add_or_remove_items'        => "Add or remove {$this->plural}",
			'choose_from_most_used'      => "Choose from the most used {$this->plural}",
			'view_item'                  => "View {$this->singular}",
			'not_found'                  => "No {$this->plural} found.",
			'back_to_items'              => "← Back to {$this->plural}",
		);
	}

	/**
	 * Registers the taxonomy.
	 *
	 * @param Hook_Loader $loader
	 * @return void
	 */
	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterface
	final public function register( Hook_Loader $loader ): void {

		// Run setup
		$this->set_up();

		$args = array(
			'publicly_queryable'    => $this->publicly_queryable,
			'show_ui'               => $this->show_ui,
			'show_in_menu'          => $this->show_in_menu,
			'show_in_nav_menus'     => $this->public,
			'show_in_rest'          => $this->show_in_rest,
			'rest_base'             => $this->rest_base ?? $this->slug,
			'rest_controller_class' => $this->rest_controller_class,
			'show_tagcloud'         => $this->show_tagcloud,
			'show_in_quick_edit'    => $this->show_in_quick_edit,
			'show_admin_column'     => $this->show_admin_column,
			'sort'                  => $this->sort,
			'description'           => $this->description,
			'update_count_callback' => $this->update_count_callback,
			'rewrite'               => $this->slug,
			'label'                 => $this->label ?? $this->plural,
			'query_var'             => $this->query_var,
			'hierarchical'          => $this->hierarchical,
		);

		// Add optional fields & args.
		$args           = $this->optional_args( $args );
		$args['labels'] = $this->hierarchical ? $this->hierarchical_labels() : $this->flat_labels();

		// Validate and maybe register.
		$this->validate();

		// If we have any metaboxes, register them.
		$this->meta_data();
		if ( ! empty( $this->meta_data ) ) {
			$this->register_meta_data( $loader );
		}

		register_taxonomy( $this->slug, $this->object_type, $this->filter_args( $args ) );
	}

	/**
	 * Overwriteable filter for the args.
	 *
	 * @param array<string, mixed> $args
	 * @return array<string, mixed>
	 */
	public function filter_args( array $args ): array {
		return $args;
	}

	/**
	 * Sets the option values, if set in properties.
	 *
	 * @param array<string, mixed> $args
	 * @return array<string, mixed>
	 */
	final protected function optional_args( array $args ): array {
		if ( $this->capabilities ) {
			$args['capabilities'] = $this->capabilities;
		}
		if ( $this->update_count_callback ) {
			$args['update_count_callback'] = $this->update_count_callback;
		}
		if ( $this->meta_box_cb ) {
			$args['meta_box_cb'] = $this->meta_box_cb;
		}
		if ( get_bloginfo( 'version' ) >= '5.5.0' && is_array( $this->default_term ) ) {
			$args['default_term'] = $this->default_term;
		}

		return $args;
	}

	/**
	 * Check we have valid properties
	 *
	 * @return void
	 * @throws InvalidArgumentException
	 */
	final protected function validate() {
		if ( ! $this->slug ) {
			throw new InvalidArgumentException( 'No slug defined.' );
		}
		if ( ! $this->singular ) {
			throw new InvalidArgumentException( 'No singular defined.' );
		}
		if ( ! $this->plural ) {
			throw new InvalidArgumentException( 'No plural defined.' );
		}
	}

	/**
	 * Registers all defined
	 *
	 * @param Hook_Loader $loader
	 * @return void
	 */
	public function register_meta_data( Hook_Loader $loader ): void {

		$meta_fields = array_filter(
			$this->meta_data,
			function( $e ): bool {
				return is_a( $e, Meta_Data::class );
			}
		);

		foreach ( $meta_fields as $meta ) {
			$meta->object_subtype( $this->slug );
			$meta->meta_type( 'term' );
			$meta->register( $loader );
		}
	}

	/**
	 * Gets the slug statically.
	 *
	 * @return string
	 */
	public static function get_slug(): string {
		$tax = App::make( static::class );
		return $tax && is_a( $tax, static::class )
			? $tax->slug
			: '';
	}
}
