<?php

/**
 * Class to manage stock filter syscom_woocommerce plugin
 */

namespace dcms\syscom\stock\includes;

class CustomStock {

	public function __construct() {
		add_filter( 'syscom_product_stock', [ $this, 'build_custom_stock' ], 10, 3 );

		add_filter( 'manage_product_posts_columns', [ $this, 'add_custom_columns_headers' ], 10, 1 );
		add_action( 'manage_product_posts_custom_column', [ $this, 'show_custom_columns_data' ], 10, 2 );

		add_action( 'woocommerce_order_status_changed', [ $this, 'order_status_change_custom' ], 10, 4 );
	}

	// Callback filter syscom_product_stock
	// Save stock api and custom stock in product metadata
	public function build_custom_stock( $stock, $id_woo_product, $is_new ): int {
		// Update api stock for new and existing products
		update_post_meta( $id_woo_product, SYSCOM_API_STOCK_PRODUCT, $stock );

		// Only for existing products
		if ( ! $is_new ) {

			$custom_stock = get_post_meta( $id_woo_product, SYSCOM_CUSTOM_STOCK_PRODUCT, true );

			// Update product inventory
			if ( intval( $custom_stock ) > 0 ) {

				return $stock + intval( $custom_stock );
			} else {
				update_post_meta( $id_woo_product, SYSCOM_CUSTOM_STOCK_PRODUCT, 0 );
			}
		} else {
			update_post_meta( $id_woo_product, SYSCOM_CUSTOM_STOCK_PRODUCT, 0 );
		}

		return $stock;
	}

	// Add custom headers for showing stock
	public function add_custom_columns_headers( $columns ) {
		$columns['api_stock']    = __( 'API stock', 'syscom-custom-stock' );
		$columns['custom_stock'] = __( 'Custom stock', 'syscom-custom-stock' );

		return $columns;
	}

	// Show data in new columns
	public function show_custom_columns_data( $column, $product_id_woo ) {
		switch ( $column ) {
			case 'api_stock' :
				echo get_post_meta( $product_id_woo, SYSCOM_API_STOCK_PRODUCT, true );
				break;
			case 'custom_stock' :
				echo get_post_meta( $product_id_woo, SYSCOM_CUSTOM_STOCK_PRODUCT, true );
				break;
		}
	}

	// Update custom stock in orders
	public function order_status_change_custom( $order_id, $old_status, $new_status, $order ) {
		$items = $order->get_items();

		foreach ( $items as $item ) {
			$product_id = $item->get_product_id();
			$quantity   = $item->get_quantity(); // quantity in item order

			$custom_stock = intval( get_post_meta( $product_id, SYSCOM_CUSTOM_STOCK_PRODUCT, true ) );
			$api_stock    = intval( get_post_meta( $product_id, SYSCOM_API_STOCK_PRODUCT, true ) );

			// reduce stock
			if ( $new_status === 'completed' ) {
				$new_stock = max( $custom_stock - $quantity, 0 );
				update_post_meta( $product_id, SYSCOM_CUSTOM_STOCK_PRODUCT, $new_stock );
			} // Add stock, old_status
			else if ( $old_status === 'completed' ) {
				$product         = wc_get_product( $product_id );
				$stock_inventory = $product->get_stock_quantity();

				$new_stock = max( $stock_inventory - $api_stock, 0 );
				update_post_meta( $product_id, SYSCOM_CUSTOM_STOCK_PRODUCT, $new_stock );
			}
		} //end foreach
	}


}