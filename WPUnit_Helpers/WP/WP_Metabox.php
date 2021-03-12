<?php

/**
 * Helper class for testing Metaboxes.
 * Allows for the verifcation and invoking of metaboxes.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @since 1.0.0
 * @package Gin0115/WPUnit_Helpers
 */

declare( strict_types=1 );

namespace Gin0115\WPUnit_Helpers\WP;

use Gin0115\WPUnit_Helpers\Utils;
use Gin0115\WPUnit_Helpers\WP\Entities\Metabox_Entity;
use PinkCrab\FunctionConstructors\Arrays as Arr;
use PinkCrab\FunctionConstructors\Comparisons as C;
use PinkCrab\FunctionConstructors\GeneralFunctions as F;

class WP_Metabox {

	public $meta_boxes = array();

	/**
	 * Returns all the current meta_boxes, or null if not set.
	 * Fire add_meta_boxes to add any waiting.
	 *
	 * @return array<string, array>|null
	 */
	public function from_global(): ?array {
		global $wp_meta_boxes;
		return $wp_meta_boxes;
	}

	/**
	 * Registers meta_boxes, if they have not already been set.
	 *
	 * @return void
	 */
	public function maybe_register(): void {
		if ( $this->from_global() === null ) {
			do_action( 'add_meta_boxes' );
		}
	}

	/**
	 * Starts the mapping process.
	 *
	 * @return void
	 */
	public function set_meta_boxes(): void {
		foreach ( $this->from_global() ?? array() as $post_type => $meta_boxes ) {
			$this->meta_boxes = array_merge(
				$this->meta_boxes,
				$this->map_position( $post_type, $meta_boxes )
			);
		}
	}

	/**
	 * Maps all meta boxes based on postition (2nd Level)
	 *
	 * @param string $post_type
	 * @param array $meta_boxes
	 * @return array
	 */
	protected function map_position( string $post_type, array $meta_boxes ): array {
		$results = array();
		foreach ( $meta_boxes as $position => $meta_boxes ) {
			$results = array_merge(
				$results,
				$this->map_priority( $post_type, $position, $meta_boxes )
			);
		}
		return $results;
	}

	/**
	 * Maps all based on priority (3rd Level)
	 *
	 * @param string $post_type
	 * @param string $position
	 * @param array $meta_boxes
	 * @return array<Metabox_Entity>
	 */
	protected function map_priority( string $post_type, string $position, array $meta_boxes ): array {
		$results = array();
		foreach ( $meta_boxes as $priority => $named_meta_boxes ) {
			$results = array_merge(
				$results,
				$this->map_named_meta_boxes( $named_meta_boxes, $post_type, $position, $priority )
			);
		}
		return $results;
	}

	/**
	 * Maps all based on meta box names. (4th Level)
	 *
	 * @param string $post_type
	 * @param string $position
	 * @param string $priority
	 * @param array $meta_boxes
	 * @return array<Metabox_Entity>
	 */
	protected function map_named_meta_boxes(
		array $meta_boxes,
		string $post_type,
		string $position,
		string $priority
	): array {
		return Utils::array_map_with(
			function( $name, $metabox_details, $post_type, $position, $priority ): Metabox_Entity {
				$metabox            = new Metabox_Entity();
				$metabox->post_type = $post_type;
				$metabox->position  = $position;
				$metabox->priority  = $priority;
				$metabox->name      = $name;

				if ( \is_array( $metabox_details ) ) {
					$metabox->isset    = true;
					$metabox->id       = $metabox_details['id'];
					$metabox->title    = $metabox_details['title'];
					$metabox->callback = $metabox_details['callback'];
					$metabox->args     = $metabox_details['args'];
				}
				return $metabox;
			},
			$meta_boxes,
			$post_type,
			$position,
			$priority
		);
	}

	/**
	 * Returns all metaboxes for a multiple post types.
	 *
	 * @param string ...$post_type
	 * @return array<Metabox_Entity>
	 */
	public function for_post_types( string ...$post_type ): array {
		return array_filter(
			$this->meta_boxes,
			F\pipe( F\getProperty( 'post_type' ), C\isEqualIn( $post_type ) )
		);
	}

	/**
	 * Attempts to find a metabox based on id.
	 * Returns first instance found.
	 *
	 * @param string $id
	 * @return Metabox_Entity|null
	 */
	public function find( string $id ): ?Metabox_Entity {
		return Arr\filterFirst( F\propertyEquals( 'id', $id ) )( $this->meta_boxes );
	}

	/**
	 * Allows the filtering of meta boxes.
	 *
	 * @param callable $filter
	 * @return array
	 */
	public function filter( callable $filter ): array {
		return array_filter( $this->meta_boxes, $filter );
	}

	/**
	 * Renders a meta_box based on a post type passed.
	 * Prints the contents!
	 *
	 * @param Metabox_Entity $meta_box
	 * @param \WP_Post $post
	 * @return void
	 */
	public function render_meta_box( Metabox_Entity $meta_box, \WP_Post $post ): void {
		$args = Arr\pushHead( $meta_box->args )( $post );
		\call_user_func( array( $meta_box, 'callback' ), ...$args );
	}

}
