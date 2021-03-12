<?php

/**
 * Functions which have no real place to live.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @since 1.0.0
 * @package Gin0115/WPUnit_Helpers
 */

declare( strict_types=1 );

namespace Gin0115\WPUnit_Helpers;

class Utils {

	/**
	 * Array map, which gives access to array key and a selection of static
	 * values.
	 *
	 * @param callable $function
	 * @param iterable $data
	 * @param mixed ...$with
	 * @return array
	 */
	public static function array_map_with( callable $function, iterable $data, ...$with ): array {
		$return = array();
		foreach ( $data as $key => $value ) {
			$return[] = $function( $key, $value, ...$with );
		}
		return $return;
	}
}
