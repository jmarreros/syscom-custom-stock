<?php

/**
 * Class to manage stock filter syscom_woocommerce plugin
 */

namespace dcms\syscom\stock\includes;

class CustomStock {

	public function __construct() {
		add_filter( 'syscom_product_stock', [ $this, 'build_custom_stock' ], 10, 2 );
	}

	public function build_custom_stock( $stock, $id_woo_product ): int {
		// Only for existing products
		if ( $id_woo_product > 0 ) {
			update_post_meta( $id_woo_product, SYSCOM_API_STOCK_PRODUCT, $stock );
			$custom_stock = get_post_meta( $id_woo_product, SYSCOM_CUSTOM_STOCK_PRODUCT, true );

			// Update product inventory
			if ( $custom_stock ) {
				return $stock + intval( $custom_stock );
			}
		}

		return $stock;
	}
}