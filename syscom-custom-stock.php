<?php
/*
Plugin Name: Syscom Custom Stock
Plugin URI: https://decodecms.com
Description: Plugin to control stock in different location, integrate with syscom woocommerce plugin
Version: 1.0
Author: Jhon Marreros Guzmán
Author URI: https://decodecms.com
Text Domain: syscom-custom-stock
Domain Path: languages
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

namespace dcms\syscom\stock;

use dcms\syscom\stock\includes\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin class to handle settings constants and loading files
**/
final class Loader{

	// Define all the constants we need
	public function define_constants(){
		define ('SYSCOM_CUSTOM_STOCK_VERSION', '1.0');
		define ('SYSCOM_CUSTOM_STOCK_PATH', plugin_dir_path( __FILE__ ));
		define ('SYSCOM_CUSTOM_STOCK_URL', plugin_dir_url( __FILE__ ));
		define ('SYSCOM_CUSTOM_STOCK_BASE_NAME', plugin_basename( __FILE__ ));
		define ('DCMS_SUBMENU', 'tools.php');
	}

	// Load all the files we need
	public function load_includes(){
		include_once ( SYSCOM_CUSTOM_STOCK_PATH . '/includes/plugin.php');
	}

	// Load tex domain
	public function load_domain(){
		add_action('plugins_loaded', function(){
			$path_languages = dirname(SYSCOM_CUSTOM_STOCK_BASE_NAME).'/languages/';
			load_plugin_textdomain('syscom-custom-stock', false, $path_languages );
		});
	}

	// Initialize all
	public function init(){
		$this->define_constants();
		$this->load_includes();
		$this->load_domain();
		new Plugin();
	}

}

$syscom_custom_stock_process = new Loader();
$syscom_custom_stock_process->init();


