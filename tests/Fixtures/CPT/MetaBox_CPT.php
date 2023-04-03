<?php

declare(strict_types=1);
/**
 * Mock Post Type with metaboxes
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Fixtures\CPT;

use PinkCrab\Registerables\Meta_Box;
use PinkCrab\Registerables\Post_Type;

class MetaBox_CPT extends Post_Type {

	public string $key      = 'metabox_cpt';
	public string $singular = 'singular';
	public string $plural   = 'plural';

	public function meta_boxes( array $collection ): array {

		$collection[] = Meta_Box::normal( 'metabox_cpt_normal' )
			->label( 'metabox_cpt_normal TITLE' )
			->view(
				function( \WP_Post $post, array $args ) {
					print( 'metabox_cpt_normal VIEW' );
				}
			)->view_vars( array( 'key1' => 1 ) );

		$collection[] = Meta_Box::side( 'metabox_cpt_side' )
			->label( 'metabox_cpt_side TITLE' )
			->view(
				function( \WP_Post $post, array $args ) {
					print( 'metabox_cpt_side VIEW.' );
					print( ' Meta=' . $args['args']['meta'] );
				}
			)
			->view_vars( array( 'key2' => 2 ) )
			->view_data_filter(
				function( \WP_Post $post, array $args ): array {
					$args['meta'] = 'hello';
					return $args;
				}
			);

		$collection[] = Meta_Box::side( 'metabox_cpt_template' )
			->label( 'metabox_cpt_template TITLE' )
			->view_template('metabox.php')
			->view_vars( array( 'key3' => 3 ) )
			->view_data_filter(
				function( \WP_Post $post, array $args ): array {
					$args['meta'] = 'metabox_cpt_template';
					return $args;
				}
			);

		return $collection;
	}

}
