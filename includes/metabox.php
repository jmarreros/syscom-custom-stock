<?php
/**
 * Class to manage stock metabox in the product page
 */
namespace dcms\syscom\stock\includes;

class Metabox {
	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_metabox_stock_product' ] );
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

	public function build_metabox_html() {
		echo "<h2>Hola como estas?</h2>";
	}
}