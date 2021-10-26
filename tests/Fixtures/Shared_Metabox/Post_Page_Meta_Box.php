<?php

declare(strict_types=1);
/**
 * Mock Shared Meta Box Controller
 *
 * @since 0.7.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Fixtures\Shared_Metabox;

use PinkCrab\Registerables\Meta_Box;
use PinkCrab\Registerables\Meta_Data;
use PinkCrab\Registerables\Shared_Meta_Box_Controller;

class Post_Page_Meta_Box extends Shared_Meta_Box_Controller {

	/**
	 * Registers the meta box
	 *
	 * @return \PinkCrab\Registerables\Meta_Box
	 */
	public function meta_box(): Meta_Box {
		return Meta_Box::side( 'post_page_mb' )
			->label( 'Post and Page Meta Box' )
			->screen( 'post' )
			->screen( 'page' )
			->view(
				function( \WP_Post $post, array $args ) {
					print( $post->post_type );
				}
			);
	}

	/**
	 * Sets any meta data against the meta box.
	 *
	 * @param Meta_Data[] $meta_data
	 * @return Meta_Data[]
	 */
	public function meta_data( array $meta_data ): array {
		$meta_data[] = ( new Meta_Data( 'pnp_string' ) )
			->meta_type( 'post' )
			->type( 'string' )
			->rest_schema( true );

		$meta_data[] = ( new Meta_Data( 'pnp_words' ) )
			->meta_type( 'post' )
			->type( 'string' )
			->rest_schema( true )
            ->single();

		return $meta_data;
	}
}
