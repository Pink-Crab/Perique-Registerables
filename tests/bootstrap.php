<?php

use Dice\Dice;
use PinkCrab\HTTP\HTTP;
use PinkCrab\Registerables\Ajax;
use PinkCrab\Core\Application\App;
use PinkCrab\PHPUnit_Helpers\Output;
use PinkCrab\Core\Services\Dice\WP_Dice;
use PinkCrab\Core\Services\ServiceContainer\Container;

/**
 * PHPUnit bootstrap file
 */

// Composer autoloader must be loaded before WP_PHPUNIT__DIR will be available
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Give access to tests_add_filter() function.
require_once getenv( 'WP_PHPUNIT__DIR' ) . '/includes/functions.php';

tests_add_filter(
	'muplugins_loaded',
	function() {
		$app = App::init( new Container() );
		$di  = WP_Dice::constructWith( new Dice() );
		$di->addRules(
			array(
				Ajax::class => array(
					'constructParams' => array( ( new HTTP() )->request_from_globals() ),
					'shared'          => true,
					'inherit'         => true,
				),
			)
		);
		$app->set( 'di', $di );
	}
);

// Start up the WP testing environment.
require getenv( 'WP_PHPUNIT__DIR' ) . '/includes/bootstrap.php';

class MetaboxHelper {

	/**
	 * Renders a PinkCrab "Registerable" MetaBox's view if defined in global WP.
	 *
	 * @param \PinkCrab\Registerables\MetaBox $metabox
	 * @param \WP_Post $post
	 * @return string
	 */
	public static function render_metabox( \PinkCrab\Registerables\MetaBox $metabox, \WP_Post $post ): string {
		global $wp_meta_boxes;

		// Loop through the registered metabox and try to find the passed instance.
		foreach ( $wp_meta_boxes ?? array() as $screen => $registered_metabox ) {
			if ( in_array( $screen, $metabox->screen, true ) // Correct screen
			&& array_key_exists( $metabox->context, $registered_metabox ) // Correct context
			&& array_key_exists( $metabox->priority, $registered_metabox[ $metabox->context ] ) // Correct priority
			&& array_key_exists( $metabox->key, $registered_metabox[ $metabox->context ][ $metabox->priority ] ) // Correct key
			&& ! is_null( $registered_metabox[ $metabox->context ][ $metabox->priority ][ $metabox->key ]['callback'] ) // Has view assigned.
			) {
				return (string) Output::buffer(
					function() use ( $post, $metabox, $registered_metabox ) {
						$registered_metabox[ $metabox->context ][ $metabox->priority ][ $metabox->key ]['callback'](
							$post, $metabox->view_vars
						);
					}
				);
			}
		}

		return '';
	}
}
