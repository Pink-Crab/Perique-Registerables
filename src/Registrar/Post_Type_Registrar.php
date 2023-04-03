<?php

declare(strict_types=1);

/**
 * Registration Registrar for all post types.
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

namespace PinkCrab\Registerables\Registrar;

use Exception;
use PinkCrab\Registerables\Post_Type;
use PinkCrab\Registerables\Registerable_Hooks;
use PinkCrab\Registerables\Registrar\Registrar;
use PinkCrab\Registerables\Registrar\Meta_Data_Registrar;
use PinkCrab\Registerables\Validator\Post_Type_Validator;
use PinkCrab\Registerables\Module\Middleware\Registerable;

class Post_Type_Registrar implements Registrar {

	protected Post_Type_Validator $validator;
	protected Meta_Data_Registrar $meta_data_registrar;

	public function __construct(
		Post_Type_Validator $validator,
		Meta_Data_Registrar $meta_data_registrar
	) {
		$this->validator           = $validator;
		$this->meta_data_registrar = $meta_data_registrar;
	}

	/**
	 * Register a post type
	 *
	 * @param \PinkCrab\Registerables\Module\Middleware\Registerable $registerable
	 * @return void
	 */
	public function register( Registerable $registerable ): void {
		/** @var Post_Type $registerable, Validation call below catches no Post_Type Registerables */

		if ( ! $this->validator->validate( $registerable ) ) {
			throw new Exception(
				sprintf(
					'Failed validating post type model(%s) with errors: %s',
					get_class( $registerable ),
					join( ', ', $this->validator->get_errors() )
				)
			);
		}

		// Attempt to register the post type.
		try {
			/* @phpstan-ignore-next-line */
			$result = register_post_type( $registerable->key, $this->compile_args( $registerable ) );
			if ( is_a( $result, \WP_Error::class ) ) {
				throw new Exception( join( $result->get_error_messages() ) );
			}
		} catch ( \Throwable $th ) {
			throw new Exception( "Failed to register {$registerable->key} post type ({$th->getMessage()})" );
		}

		// Register all meta data for post type.
		$this->register_meta_data( $registerable );
	}

	/**
	 * Registers all meta data for post_type.
	 *
	 * @param \PinkCrab\Registerables\Post_Type $post_type
	 * @return void
	 */
	protected function register_meta_data( Post_Type $post_type ): void {

		// Get all meta fields for post_type.
		$meta_fields = $post_type->meta_data( array() );
		// Attempt to register all Meta for post_type.
		try {
			foreach ( $meta_fields as $meta_field ) {
				$this->meta_data_registrar
					->register_for_post_type( $meta_field, $post_type->key );
			}
		} catch ( \Throwable $th ) {
			throw new Exception( $th->getMessage() );
		}
	}



	/**
	 * Compiles the args used to register the post type.
	 *
	 * @param \PinkCrab\Registerables\Post_Type $post_type
	 * @return array<string, string|int|array<string, string>>
	 */
	protected function compile_args( Post_Type $post_type ): array {
		// Create the labels.
		$labels = array(
			'name'                     => $post_type->plural,
			'singular_name'            => $post_type->singular,
			'add_new'                  => _x( 'Add New', 'Add new post label of custom post type', 'pinkcrab' ),
			/* translators: %s: Post type singular name */
			'add_new_item'             => wp_sprintf( _x( 'Add New %s', 'Label for adding a new singular item. Default is ‘Add New {post type singular name}’.', 'pinkcrab' ), $post_type->singular ),
			/* translators: %s: Post type singular name */
			'edit_item'                => wp_sprintf( _x( 'Edit %s', 'Label for editing a singular item. Default is ‘Edit {post type singular name}’.', 'pinkcrab' ), $post_type->singular ),
			/* translators: %s: Post type singular name */
			'new_item'                 => wp_sprintf( _x( 'New %s', 'Label for the new item page title. Default is ‘New {post type singular name}’.', 'pinkcrab' ), $post_type->singular ),
			/* translators: %s: Post type singular name */
			'view_item'                => wp_sprintf( _x( 'View %s', 'Label for viewing a singular item. Default is ‘View {post type singular name}’.', 'pinkcrab' ), $post_type->singular ),
			/* translators: %s: Post type plural name */
			'view_items'               => wp_sprintf( _x( 'View %s', 'Label for viewing post type archives. Default is ‘View {post type plural name}’.', 'pinkcrab' ), $post_type->plural ),
			/* translators: %s: Post type singular name */
			'search_items'             => wp_sprintf( _x( 'Search %s', 'Label for searching plural items. Default is ‘Search {post type plural name}’.', 'pinkcrab' ), $post_type->singular ),
			/* translators: %s: Post type plural name */
			'not_found'                => wp_sprintf( _x( 'No %s found', 'Label used when no items are found. Default is ‘No {post type plural name} found’.', 'pinkcrab' ), $post_type->plural ),
			/* translators: %s: Post type plural name */
			'not_found_in_trash'       => wp_sprintf( _x( 'No %s found in Trash', 'Label used when no items are in the Trash. Default is ‘No {post type plural name} found in Trash’.', 'pinkcrab' ), $post_type->plural ),
			/* translators: %s: Post type singular name */
			'parent_item_colon'        => wp_sprintf( _x( 'Parent %s:', 'Label used to prefix parents of hierarchical items. Not used on non-hierarchical post types. Default is ‘Parent {post type plural name}:’.', 'pinkcrab' ), $post_type->singular ),
			/* translators: %s: Post type singular name */
			'all_items'                => wp_sprintf( _x( 'All %s', 'Label to signify all items in a submenu link. Default is ‘All {post type plural name}’.', 'pinkcrab' ), $post_type->plural ),
			/* translators: %s: Post type plural name */
			'archives'                 => wp_sprintf( _x( '%s Archives', ' Label for archives in nav menus. Default is ‘Post Archives’.', 'pinkcrab' ), \ucfirst( $post_type->plural ) ),
			/* translators: %s: Post type plural name */
			'attributes'               => wp_sprintf( _x( '%s Attributes', 'Label for the attributes meta box. Default is ‘{post type plural name} Attributes’.', 'pinkcrab' ), \ucfirst( $post_type->plural ) ),
			/* translators: %s: Post type singular name */
			'insert_into_item'         => wp_sprintf( _x( 'Insert into %s', 'Label for the media frame button. Default is ‘Insert into {post type plural name}’.', 'pinkcrab' ), $post_type->singular ),
			/* translators: %s: Post type singular name */
			'uploaded_to_this_item'    => wp_sprintf( _x( 'Uploaded to this %s', 'Label for the media frame filter. Default is ‘Uploaded to this {post type plural name}’.', 'pinkcrab' ), $post_type->singular ),
			'featured_image'           => _x( 'Featured image', 'Label for the featured image meta box title. Default is ‘Featured image’.', 'pinkcrab' ),
			'set_featured_image'       => _x( 'Set featured image', 'Label for setting the featured image. Default is ‘Set featured image’.', 'pinkcrab' ),
			'remove_featured_image'    => _x( 'Remove featured image', 'Label for removing the featured image. Default is ‘Remove featured image’.', 'pinkcrab' ),
			'use_featured_image'       => _x( 'Use as featured image', 'Label in the media frame for using a featured image. Default is ‘Use as featured image’.', 'pinkcrab' ),
			'menu_name'                => $post_type->plural,
			/* translators: %s: Post type plural name */
			'filter_items_list'        => wp_sprintf( _x( 'Filter %s list', 'Label for the table views hidden heading. Default is ‘Filter {post type plural name} list’.', 'pinkcrab' ), $post_type->plural ),
			'filter_by_date'           => _x( 'Filter by date', 'Label for the date filter in list tables. Default is ‘Filter by date’.', 'pinkcrab' ),
			/* translators: %s: Post type plural name */
			'items_list'               => wp_sprintf( _x( '%s list', 'Label for the table hidden heading. Default is ‘{post type plural name} list’.', 'pinkcrab' ), \ucfirst( $post_type->plural ) ),
			/* translators: %s: Post type singular name */
			'item_published'           => wp_sprintf( _x( '%s published', 'Label used when an item is published. Default is ‘{post type singular name} published’.', 'pinkcrab' ), \ucfirst( $post_type->singular ) ),
			/* translators: %s: Post type singular name */
			'item_published_privately' => wp_sprintf( _x( '%s published privately', 'Label used when an item is published with private visibility. Default is ‘{post type singular name} published privately.’.', 'pinkcrab' ), \ucfirst( $post_type->singular ) ),
			/* translators: %s: Post type singular name */
			'item_reverted_to_draft'   => wp_sprintf( _x( '%s reverted to draft', 'Label used when an item is switched to a draft. Default is ‘{post type singular name} reverted to draft’.', 'pinkcrab' ), \ucfirst( $post_type->singular ) ),
			/* translators: %s: Post type singular name */
			'item_scheduled'           => wp_sprintf( _x( '%s scheduled', 'Label used when an item is scheduled for publishing. Default is ‘{post type singular name} scheduled.’ ', 'pinkcrab' ), \ucfirst( $post_type->singular ) ),
			/* translators: %s: Post type singular name */
			'item_updated'             => wp_sprintf( _x( '%s updated', 'Label used when an item is updated. Default is ‘{post type singular name} updated.’.', 'pinkcrab' ), \ucfirst( $post_type->singular ) ),
			/* translators: %s: Post type singular name */
			'item_link'                => wp_sprintf( _x( '%s Link', 'Title for a navigation link block variation. Default is ‘{post type singular name} Link’.', 'pinkcrab' ), \ucfirst( $post_type->singular ) ),
			/* translators: %s: Post type singular name */
			'item_link_description'    => wp_sprintf( _x( 'A link to a %s', 'Description for a navigation link block variation. Default is ‘A link to a {post type singular name}’.', 'pinkcrab' ), $post_type->singular ),
		);

		/**
		 * Allow 3rd party plugins to filter the labels also.
		 *
		 * @filter_handle PinkCrab/Registerable/post_type_labels
		 * @param array<string, string> $labels
		 * @param Post_Type $cpt
		 * @return array<string, string>
		 */
		$labels = apply_filters( Registerable_Hooks::POST_TYPE_LABELS, $post_type->filter_labels( $labels ), $post_type );

		// Set the rewrite rules if not defined.
		if ( is_null( $post_type->rewrite ) ) {
			$post_type->rewrite = array(
				'slug'       => $post_type->key,
				'with_front' => true,
				'feeds'      => false,
				'pages'      => false,
			);
		}

		// Set the meta cap based on its definition and if uses gutenberg.
		// See https://github.com/Pink-Crab/Perique-Registerables/issues/66
		if ( null === $post_type->map_meta_cap ) {
			$meta_cap = $post_type->gutenberg ? true : false;
		} else {
			$meta_cap = $post_type->map_meta_cap ?? false;
		}

		// Compose args.
		$args = array(
			'labels'                => $labels,
			'description'           => $post_type->description ?? $post_type->plural,
			'hierarchical'          => is_bool( $post_type->hierarchical ) ? $post_type->hierarchical : false,
			'supports'              => $post_type->supports,
			'public'                => is_bool( $post_type->public ) ? $post_type->public : true,
			'show_ui'               => is_bool( $post_type->show_ui ) ? $post_type->show_ui : true,
			'show_in_menu'          => is_bool( $post_type->show_in_menu ) ? $post_type->show_in_menu : true,
			'show_in_admin_bar'     => is_bool( $post_type->show_in_admin_bar ) ? $post_type->show_in_admin_bar : true,
			'menu_position'         => $post_type->menu_position,
			'menu_icon'             => $post_type->dashicon,
			'show_in_nav_menus'     => is_bool( $post_type->show_in_nav_menus ) ? $post_type->show_in_nav_menus : true,
			'publicly_queryable'    => is_bool( $post_type->publicly_queryable ) ? $post_type->publicly_queryable : true,
			'exclude_from_search'   => is_bool( $post_type->exclude_from_search ) ? $post_type->exclude_from_search : true,
			'has_archive'           => is_bool( $post_type->has_archive ) ? $post_type->has_archive : true,
			'query_var'             => is_bool( $post_type->query_var ) ? $post_type->query_var : false,
			'can_export'            => is_bool( $post_type->can_export ) ? $post_type->can_export : true,
			'rewrite'               => is_bool( $post_type->rewrite ) ? $post_type->rewrite : false,
			'capability_type'       => $post_type->capability_type,
			'capabilities'          => $post_type->capabilities,
			'taxonomies'            => $post_type->taxonomies,
			'show_in_rest'          => is_bool( $post_type->show_in_rest ) ? $post_type->show_in_rest : true,
			'rest_base'             => $post_type->rest_base ?? $post_type->key,
			'rest_controller_class' => \class_exists( $post_type->rest_controller_class ) ? $post_type->rest_controller_class : \WP_REST_Posts_Controller::class,
			'delete_with_user'      => \is_bool( $post_type->delete_with_user ) ? $post_type->delete_with_user : null,
			'template'              => \is_array( $post_type->template ) ? $post_type->template : array(),
			'template_lock'         => \is_string( $post_type->template_lock ) ? $post_type->template_lock : false,
			'map_meta_cap'          => $meta_cap,
		);

		/**
		 * Allow 3rd party plugins to filter this also.
		 * @filter_handle PinkCrab/Registerable/post_type_args
		 * @parm string $hook_name Hook Handle
		 * @param array<string, string|bool|int|null|array<string, string> $args
		 * @param Post_Type $post_type
		 * @return array<string, string|bool|int|null|array<string, string>
		 */
		/* @phpstan-ignore-next-line, this is due to apply_filters type hints being wrong. */
		return apply_filters( Registerable_Hooks::POST_TYPE_ARGS, $post_type->filter_args( $args ), $post_type );
	}
}
