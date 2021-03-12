<?php

/**
 * WordPress helpers for downloading and adding plugins/themes to test setups.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @since 1.0.0
 * @package Gin0115/WPUnit_Helpers
 */

declare( strict_types=1 );

namespace Gin0115\WPUnit_Helpers\WP;

use Exception;
use ZipArchive;
use Gin0115\WPUnit_Helpers\Output;

class WP_Dependencies {

	/**
	 * Downloads and installs a plugin from a url
	 *
	 * @param string $url
	 * @param string $wp_base_path
	 * @return bool
     * @throws Exception
	 */
	public static function install_remote_plugin_from_zip( string $url, string $wp_base_path ): bool {
		Output::println( '' );
		Output::println( '**********************************************************************************' );
		Output::println( '******************************* DOWNLOADING PLUGIN *******************************' );
		Output::println( '************************************ FROM ZIP ************************************' );
		Output::println( '**********************************************************************************' );

		$zip       = new ZipArchive();
		$temp_file = \tmpfile();
		$temp_file = stream_get_meta_data( $temp_file )['uri'];

		Output::println( sprintf( '** Downloading zip from %s', $url ) );
		$download = file_put_contents( $temp_file, fopen( $url, 'r' ) );
		if ( $download === false ) {
			throw new Exception( "Failed to download remote plugin from {$url}" );
		}

		Output::println( '** Opening Zip file......' );
		$plugin = $zip->open( $temp_file );
		if ( $plugin === false ) {
			throw new Exception( "Failed to open downloaded zip file from {$url}" );
		}

		Output::println( sprintf( '** Extracting %d files..........', $zip->numFiles ) );
		$result = $zip->extractTo( $wp_base_path . '/wp-content/plugins/' );
		if ( $result === true ) {
			Output::println( sprintf( '** Plugin installed to %s', $zip->getNameIndex( 0 ) ) );
		} else {
			throw new Exception( 'Failed to extract plugin' );
		}

		$zip->close();
		unlink( $temp_file );
		Output::println( '** Cleaned up all temp files, dont forget to activate this plugin in your bootstrap.' );
		Output::println( '**********************************************************************************' );
		Output::println( '' );

		return $result;
	}


}
