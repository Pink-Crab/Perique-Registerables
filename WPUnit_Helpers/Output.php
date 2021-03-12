<?php

/**
 * Collection of helper methods for working with output.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @since 1.0.0
 * @package Gin0115/WPUnit_Helpers
 */

declare( strict_types=1 );

namespace Gin0115\WPUnit_Helpers;

use Exception;
use ZipArchive;

class Output {

	/**
	 * Prints a printable and strarts a new line.
	 *
	 * @param mixed $arg
	 * @return void
	 */
	public static function println( $arg ): void {
		print $arg . PHP_EOL;
	}
}
