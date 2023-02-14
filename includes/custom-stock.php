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
	public function build_custom_stock( $stock, $id_woo_product, $is_new ): int {
		// Update api stock for new and existing products
		update_post_meta( $id_woo_product, SYSCOM_API_STOCK_PRODUCT, $stock );

		// Only for existing products
		if ( ! $is_new ) {

			$custom_stock = get_post_meta( $id_woo_product, SYSCOM_CUSTOM_STOCK_PRODUCT, true );

			// Update product inventory
			if ( $custom_stock ) {
				return $stock + intval( $custom_stock );
			}
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
	
	// Update custom stock
	public function order_status_change_custom( $order_id, $old_status, $new_status, $order ) {
		$items = $order->get_items();
		foreach ( $items as $item ) {
			$product_id = $item->get_product_id();

			// TODO
			if ( $new_status === 'completed' ){

			} else {

			}
			error_log(print_r($old_status,true));
			error_log(print_r($new_status,true));
			error_log(print_r('El producto en la orden',true));
			error_log(print_r($product_id,true));
		}
	}

}