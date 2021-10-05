<?php

declare(strict_types=1);

/**
 * Registration Registrar for all custom Taxonomies.
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
use PinkCrab\Registerables\Taxonomy;
use PinkCrab\Registerables\Post_Type;
use PinkCrab\Registerables\Registerable_Hooks;
use PinkCrab\Registerables\Registrar\Registrar;
use PinkCrab\Registerables\Validator\Taxonomy_Validator;
use PinkCrab\Registerables\Registration_Middleware\Registerable;

class Taxonomy_Registrar implements Registrar {

	/**
	 * Taxonomy Validator
	 *
	 * @var Taxonomy_Validator
	 */
	protected $validator;

	public function __construct( Taxonomy_Validator $validator ) {
		$this->validator = $validator;
	}

	/**
	 * Register a post type
	 *
	 * @param \PinkCrab\Registerables\Registration_Middleware\Registerable $registerable
	 * @return void
	 */
	public function register( Registerable $registerable ): void {
		/** @var Taxonomy $registerable, Validation call below catches no Post_Type Registerables */

		if ( ! $this->validator->validate( $registerable ) ) {
			throw new Exception( 'Invalid taxonomy model' );
		}

		// Attempt to register the post type.
		try {
			$result = \register_taxonomy(
				$registerable->slug,
				$registerable->object_type,
				$this->compile_args( $registerable )
			);

			if ( is_a( $result, \WP_Error::class ) ) {
				throw new Exception( join( $result->get_error_messages() ) );
			}
		} catch ( \Throwable $th ) {
			throw new Exception( "Failed to register {$registerable->slug} taxonomy ({$th->getMessage()})" );
		}
	}

	/**
	 * Compiles the args used to register the post type.
	 *
	 * @param \PinkCrab\Registerables\Taxonomy $taxonomy
	 * @return array<string, string|int|array<string, string>>
	 */
	protected function compile_args( Taxonomy $taxonomy ): array {
		// Create the labels.
		$base_labels = array(
			'name'                  => $taxonomy->plural,
			'singular_name'         => $taxonomy->singular,
			/* translators: %s: Taxonomy plural name */
			'search_items'          => wp_sprintf( _x( 'Search %s', 'Label for searching plural items. Default is ‘Search {taxonomy plural name}’.', 'pinkcrab' ), $taxonomy->plural ),
			/* translators: %s: Taxonomy plural name */
			'popular_items'         => wp_sprintf( _x( 'Popular %s', '**', 'pinkcrab' ), $taxonomy->plural ),
			/* translators: %s: Taxonomy singular name */
			'edit_item'             => wp_sprintf( _x( 'Edit %s', 'Label for editing a singular item. Default is ‘Edit {taxonomy singular name}’.', 'pinkcrab' ), $taxonomy->singular ),
			/* translators: %s: Taxonomy singular name */
			'view_item'             => wp_sprintf( _x( 'View %s', 'Label for viewing a singular item. Default is ‘View {taxonomy singular name}’.', 'pinkcrab' ), $taxonomy->singular ),
			/* translators: %s: Taxonomy singular name */
			'update_item'           => wp_sprintf( _x( 'Update %s', 'Label for editing a singular item. Default is ‘Edit {taxonomy singular name}’.', 'pinkcrab' ), $taxonomy->singular ),
			/* translators: %s: Taxonomy singular name */
			'add_new_item'          => wp_sprintf( _x( 'Add New %s', 'Label for adding a new singular item. Default is ‘Add New {taxonomy singular name}’.', 'pinkcrab' ), $taxonomy->singular ),
			/* translators: %s: Taxonomy singular name */
			'new_item_name'         => wp_sprintf( _x( 'New %s', 'Label for the new item page title. Default is ‘New {taxonomy singular name}’.', 'pinkcrab' ), $taxonomy->singular ),
			/* translators: %s: Taxonomy plural name */
			'not_found'             => wp_sprintf( _x( 'No %s found', 'Label used when no items are found. Default is ‘No {taxonomy plural name} found’.', 'pinkcrab' ), $taxonomy->plural ),
			/* translators: %s: Taxonomy plural name */
			'items_list'            => wp_sprintf( _x( '%s list', 'Label for the table hidden heading. Default is ‘{taxonomy plural name} list’.', 'pinkcrab' ), \ucfirst( $taxonomy->plural ) ),
			/* translators: %s: Taxonomy plural name */
			'items_list_navigation' => wp_sprintf( _x( '%s list navigation', 'Label for the pagination hidden heading. Default is ‘{taxonomy plural name} list’.', 'pinkcrab' ), \ucfirst( $taxonomy->plural ) ),
			/* translators: %s: Taxonomy plural name */
			'all_items'             => wp_sprintf( _x( 'All %s', 'Label for the pagination hidden heading. Default is ‘{taxonomy plural name} list’.', 'pinkcrab' ), \ucfirst( $taxonomy->plural ) ),
			'most_used'             => _x( 'Most Used', 'Title for the Most Used tab. Default \'Most Used\'.', 'pinkcrab' ),
			/* translators: %s: Taxonomy plural name */
			'back_to_items'         => wp_sprintf( _x( '← Back to %s', 'Label for the pagination hidden heading. Default is ‘{taxonomy plural name} list’.', 'pinkcrab' ), \ucfirst( $taxonomy->plural ) ),
			/* translators: %s: Taxonomy singular name */
			'item_link'             => wp_sprintf( _x( '%s Link', 'Title for a navigation link block variation. Default is ‘{taxonomy singular name} Link’.', 'pinkcrab' ), \ucfirst( $taxonomy->singular ) ),
			/* translators: %s: Taxonomy singular name */
			'item_link_description' => wp_sprintf( _x( 'A link to a %s', 'Description for a navigation link block variation. Default is ‘A link to a {taxonomy singular name}’.', 'pinkcrab' ), $taxonomy->singular ),
		);

		$tag_labels = array(
			/* translators: %s: Taxonomy plural name */
			'separate_items_with_commas' => wp_sprintf( _x( 'Separate %s with commas', 'This label is only used for non-hierarchical taxonomies. Default \'Separate {taxonomy plural name} with commas\', used in the meta box.’.', 'pinkcrab' ), $taxonomy->plural ),
			/* translators: %s: Taxonomy plural name */
			'add_or_remove_items'        => wp_sprintf( _x( 'Add or remove %s', 'This label is only used for non-hierarchical taxonomies. Default \'Add or remove {taxonomy plural name}\', used in the meta box when JavaScript is disabled.', 'pinkcrab' ), $taxonomy->plural ),
			/* translators: %s: Taxonomy plural name */
			'choose_from_most_used'      => wp_sprintf( _x( 'Add or remove %s', 'This label is only used on non-hierarchical taxonomies. Default\'Choose from the most used {taxonomy plural name}\', used in the meta box.', 'pinkcrab' ), $taxonomy->plural ),
		);

		$hierarchical_labels = array(
			/* translators: %s: Taxonomy singular name */
			'parent_item_colon' => wp_sprintf( _x( 'Parent %s:', 'Label used to prefix parents of hierarchical items. Not used on non-hierarchical post types. Default is ‘Parent {taxonomy plural name}:’.', 'pinkcrab' ), $taxonomy->singular ),
			/* translators: %s: Taxonomy singular name */
			'parent_item'       => wp_sprintf( _x( 'Parent %s', '**', 'pinkcrab' ), $taxonomy->singular ),
			/* translators: %s: Taxonomy singular name */
			'filter_by_item'    => wp_sprintf( _x( 'Filter by %s', 'This label is only used for hierarchical taxonomies. Default \'Filter by {taxonomy singular name}\', used in the posts list table.', 'pinkcrab' ), $taxonomy->singular ),
		);

		$labels = array_merge(
			$base_labels,
			$taxonomy->hierarchical ? $hierarchical_labels : $tag_labels
		);

		/**
		 * Allow 3rd party plugins to filter the labels also.
		 *
		 * @filter_handle PinkCrab/Registerable/post_type_labels
		 * @param array<string, string> $labels
		 * @param Post_Type $cpt
		 * @return array<string, string>
		 */
		$labels = apply_filters( Registerable_Hooks::POST_TYPE_LABELS, $taxonomy->filter_labels( $labels ), $taxonomy );

		// Compose args.
		$args = array(
			'labels'                => $labels,
			'publicly_queryable'    => $taxonomy->publicly_queryable,
			'show_ui'               => $taxonomy->show_ui,
			'show_in_menu'          => $taxonomy->show_in_menu,
			'show_in_nav_menus'     => $taxonomy->public,
			'show_in_rest'          => $taxonomy->show_in_rest,
			'rest_base'             => $taxonomy->rest_base ?? $taxonomy->slug,
			'rest_controller_class' => $taxonomy->rest_controller_class,
			'show_tagcloud'         => $taxonomy->show_tagcloud,
			'show_in_quick_edit'    => $taxonomy->show_in_quick_edit,
			'show_admin_column'     => $taxonomy->show_admin_column,
			'sort'                  => $taxonomy->sort,
			'description'           => $taxonomy->description,
			'rewrite'               => $taxonomy->slug,
			'label'                 => $taxonomy->label ?? $taxonomy->plural,
			'query_var'             => $taxonomy->query_var,
			'hierarchical'          => $taxonomy->hierarchical,
			'capabilities'          => $taxonomy->capabilities ?? array(
				'manage_terms' => "manage_ {$taxonomy->slug}",
				'edit_terms'   => "edit_ $taxonomy->slug",
				'delete_terms' => "delete $taxonomy->slug",
				'assign_terms' => "assign_ $taxonomy->slug",
			),
			'update_count_callback' => $taxonomy->update_count_callback ?? '_update_post_term_count',
			'meta_box_cb'           => $taxonomy->meta_box_cb ??
				$taxonomy->hierarchical ? 'post_categories_meta_box' : 'post_tags_meta_box',
			'default_term'          => $taxonomy->default_term,
		);

		/**
		 * Allow 3rd party plugins to filter this also.
		 * @filter_handle PinkCrab/Registerable/post_type_args
		 * @param array<string, string|bool|int|null|array<string, string> $args
		 * @param Post_Type $cpt
		 * @return array<string, string|bool|int|null|array<string, string>
		 */
		return apply_filters( Registerable_Hooks::POST_TYPE_ARGS, $taxonomy->filter_args( $args ), $taxonomy );
	}
}
