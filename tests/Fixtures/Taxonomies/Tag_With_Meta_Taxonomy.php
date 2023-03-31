<?php

declare(strict_types=1);
/**
 * Taxonomy with defined meta data.
 *
 * @since 0.2.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Registerables
 */

namespace PinkCrab\Registerables\Tests\Fixtures\Taxonomies;

use PinkCrab\Registerables\Taxonomy;
use PinkCrab\Registerables\Meta_Data;
use PinkCrab\WP_Rest_Schema\Argument\Argument;
use PinkCrab\WP_Rest_Schema\Argument\Number_Type;

class Tag_With_Meta_Taxonomy extends Taxonomy {

	public const META_1 = array(
		'key'               => 'meta_key_1',
		'type'              => 'string',
		'default'           => 'default value 1',
		'description'       => 'test 1',
		'single'            => true,
		'sanitize_callback' => 'strtoupper',
		'auth_callback'     => '__return_true',
	);

	public const META_2 = array(
		'key'               => 'meta_key_2',
		'type'              => 'number',
		'default'           => 3.14,
		'description'       => 'test 2',
		'single'            => true,
		'sanitize_callback' => 'floatval',
		'auth_callback'     => '__return_true',
	);

	public const DEFAULT_TERM_SLUG = 'def_term_for_tax_w_meta';

		// The schema for key 1 as array.
	public static function meta_rest_key_1_schema(): array {
		return array(
			'type'        => 'string',
			'default'     => 'default value 1',
			'description' => 'test 1',
		);
	}

	// The schema for key 2 as Argument type
	public static function meta_rest_key_2_schema(): Argument {
		return Number_Type::on( self::META_2['key'] )
			->description( 'test 2' )
			->default( 3.14 );
	}

	// Returns as an array for comparison.
	public static function meta_rest_key_2_schema_as_array(): array {
		return array(
			'type'        => 'number',
			'description' => 'test 2',
			'default'     => 3.14,
		);
	}

	public string $slug         = 'tag_with_meta';
	public ?string $singular    = 'Tag with meta Taxonomy';
	public string $plural       = 'Tag with meta Taxonomies';
	public ?string $description = 'Tag with meta Taxonomy.';
	public bool $hierarchical        = false;
	public array $object_type         = array( 'basic_cpt' );

	// Default term
	public ?array $default_term = array(
		'name'        => 'Default Term for Taxonomy With Meta',
		'slug'        => self::DEFAULT_TERM_SLUG,
		'description' => 'Like the name',
	);

	/**
	 * Allows for the setting of meta data specifically for this taxonomy.
	 *
	 * @param Meta_Data[] $collection
	 * @return Meta_Data[]
	 */
	public function meta_data( array $collection ): array {
		$collection[] = ( new Meta_Data( self::META_1['key'] ) )
			->type( self::META_1['type'] )
			->default( self::META_1['default'] )
			->description( self::META_1['description'] )
			->single( self::META_1['single'] )
			->sanitize( self::META_1['sanitize_callback'] )
			->permissions( self::META_1['auth_callback'] )
			->rest_schema( self::meta_rest_key_1_schema() );

		$collection[] = ( new Meta_Data( self::META_2['key'] ) )
			->type( self::META_2['type'] )
			->default( self::META_2['default'] )
			->description( self::META_2['description'] )
			->single( self::META_2['single'] )
			->sanitize( self::META_2['sanitize_callback'] )
			->permissions( self::META_2['auth_callback'] )
			->rest_schema( self::meta_rest_key_2_schema() );

		return $collection;
	}
}
