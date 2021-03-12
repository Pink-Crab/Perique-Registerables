<?php

/**
 * WP_UnitTestCase trait for creating WooCommerce products
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @since 1.0.0
 * @package Gin0115/WPUnit_Helpers
 */

declare( strict_types=1 );

namespace Gin0115\WPUnit_Helpers\WP;

use WC_Product;

trait Product_Factory_Trait {

	/**
	 * Returns a simple product.
	 *
	 * Can set meta by passing in args.
	 *
	 * @param array $args
	 * @return \WC_Product
	 */
	public function create_simple_product( array $args = [] ): WC_Product {
		// Ensure set to product
		$args['post_type'] = 'product';

		$product_id = $this->factory->post->create( $args );
		wp_set_object_terms( $product_id, 'simple', 'product_type' );

		// If meta passed.
		if ( ! empty( $args['meta'] ) && is_array( $args['meta'] ) ) {
			foreach ( $args['meta'] as $key => $value ) {
				update_post_meta( $product_id, $key, $value );
			}
		}

		return \wc_get_product( $product_id );
	}
}
