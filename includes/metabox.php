<?php
/**
 * Class to manage stock metabox in the product page
 */

namespace dcms\syscom\stock\includes;

class Metabox {
	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_metabox_stock_product' ] );
		add_action( 'woocommerce_update_product', [ $this, 'save_metabox_stock_product' ], 10, 2 );
		add_action( 'woocommerce_before_add_to_cart_form', [ $this, 'show_stock_product_detail' ] );
	}

	public function add_metabox_stock_product(): void {
		add_meta_box(
			'syscom_stock_id',
			'Custom stock',
			[ $this, 'build_metabox_html' ],
			'product',
			'side',
		);
	}

	public function build_metabox_html( $post ) {
		$api_stock    = get_post_meta( $post->ID, SYSCOM_API_STOCK_PRODUCT, true );
		$custom_stock = get_post_meta( $post->ID, SYSCOM_CUSTOM_STOCK_PRODUCT, true );

		if ( empty( $api_stock ) ) {
			$api_stock = intval( get_post_meta( $post->ID, '_stock', true ) );
			update_post_meta( $post->ID, SYSCOM_API_STOCK_PRODUCT, $api_stock );
		}
		?>
        <div>
            <label for="custom-stock">API stock</label>
            <input id="custom-stock" name="custom-stock" type="text" value="<?= $api_stock ?>" disabled>
        </div>
        <div>
            <label for="custom-stock">Custom stock</label>
            <input id="custom-stock" name="custom-stock" type="number" step="1" value="<?= $custom_stock ?>">
        </div>

		<?php
	}

	public function save_metabox_stock_product( $product_id_woo, $product ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$custom_stock = intval( $_POST['custom-stock'] ?? 0 );
		update_post_meta( $product_id_woo, SYSCOM_CUSTOM_STOCK_PRODUCT, $custom_stock );

		//Recalculate stock
		$stock_api = intval( get_post_meta( $product_id_woo, SYSCOM_API_STOCK_PRODUCT, true ) );
		wc_update_product_stock( $product_id_woo, $stock_api + $custom_stock, 'set', true );
	}


	// Show info stock in product page
	public function show_stock_product_detail() {
		global $product;

		$api_stock    = get_post_meta( $product->get_id(), SYSCOM_API_STOCK_PRODUCT, true );
		$custom_stock = get_post_meta( $product->get_id(), SYSCOM_CUSTOM_STOCK_PRODUCT, true );

		echo "<div class='stock-detail' style='margin-bottom:10px'>";
		if ( ! empty( $custom_stock ) ) {
			echo "<div class='custom-stock'><span style='display:inline-block; width:100px'>Cantidad $custom_stock </span>➜ Entrega en 1 día</div>";
		}
		if ( ! empty( $api_stock ) ) {
			echo "<div class='api-stock'><span style='display:inline-block; width:100px'>Cantidad $api_stock </span>➜ Entrega en 3 días</div>";
		}
		echo "</div>";
	}

}
