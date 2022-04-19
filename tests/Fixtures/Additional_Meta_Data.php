<?php

declare(strict_types=1);
/**
 * Mock Additional_Meta_Data_Controller
 *
 * @since 0.8.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Fixtures;

use PinkCrab\Registerables\Meta_Data;
use PinkCrab\WP_Rest_Schema\Argument\String_Type;
use PinkCrab\Registerables\Additional_Meta_Data_Controller;

class Additional_Meta_Data extends Additional_Meta_Data_Controller {

	/**
	 * Mock meta data for POST
	 *
	 * Has Schema (via ARRAY)
	 *
	 * @return \PinkCrab\Registerables\Meta_Data
	 */
	public static function post_meta_data(): Meta_Data {
		return ( new Meta_Data( 'mock_post_meta_data' ) )
			->post_type( 'page' )
			->rest_schema(
				array(
					'type' => 'string',
				)
			);
	}

	/**
	 * Mock meta data for TERM
	 *
	 * Without Schema
	 *
	 * @return \PinkCrab\Registerables\Meta_Data
	 */
	public static function term_meta_data(): Meta_Data {
		return ( new Meta_Data( 'mock_term_meta_data' ) )
			->taxonomy( 'categories' );
	}

	/**
	 * Mock meta data for COMMENT
	 *
	 * Has Schema (Via WP_Rest_Schema)
	 *
	 * @return \PinkCrab\Registerables\Meta_Data
	 */
	public static function comment_meta_data(): Meta_Data {
		return ( new Meta_Data( 'mock_comment_meta_data' ) )
			->meta_type( 'comment' )
			->rest_schema( String_Type::on( 'mock_comment_meta_data' ) );
	}

	/**
	 * Mock meta data for USER
	 *
	 * Without Schema
	 *
	 * @return \PinkCrab\Registerables\Meta_Data
	 */
	public static function user_meta_data(): Meta_Data {
		return ( new Meta_Data( 'mock_user_meta_data' ) )
			->meta_type( 'user' );
	}

	/**
	 * Registers all the meta data.
	 *
	 * @param Meta_Data[] $array
	 * @return Meta_Data[]
	 */
	public function meta_data( array $array ): array {
		return array(
			self::post_meta_data(),
			self::term_meta_data(),
			self::comment_meta_data(),
			self::user_meta_data(),
		);
	}
}
