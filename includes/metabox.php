<?php
/**
 * Class to manage stock metabox in the product page
 */

namespace dcms\syscom\stock\includes;

class Metabox {
	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_metabox_stock_product' ] );
		add_action( 'save_post_product', [ $this, 'save_metabox_stock_product' ], 20, 2 );
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
		?>
        <div>
            <label for="custom-stock">API stock</label>
            <input id="custom-stock" name="custom-stock" type="number" value="<?= $api_stock ?>" disabled>
        </div>
        <div>
            <label for="custom-stock">Custom stock</label>
            <input id="custom-stock" name="custom-stock" type="number" value="<?= $custom_stock ?>">
        </div>

		<?php
	}

	public function save_metabox_stock_product( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$custom_stock = $_POST['custom-stock'] ?? 0;
		if ( is_numeric( $custom_stock ) ) {
			update_post_meta( $post_id, SYSCOM_CUSTOM_STOCK_PRODUCT, $custom_stock );
		}
	}

}