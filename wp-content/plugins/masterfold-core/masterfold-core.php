<?php
/**
 * Plugin Name: Masterfold Core
 * Description: Site-specific functionality for Masterfold: product attribute sections (Elementor widget), catalog mode, and the custom features that used to live in the theme.
 * Version: 1.2.0
 * Author: Masterfold
 * Requires Plugins: woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MF_CORE_VERSION', '1.2.0' );
define( 'MF_CORE_URL', plugin_dir_url( __FILE__ ) );
define( 'MF_CORE_DIR', __DIR__ );

require_once __DIR__ . '/includes/legacy-theme-functions.php';

add_action(
	'plugins_loaded',
	function () {
		if ( class_exists( 'WooCommerce' ) ) {
			require_once __DIR__ . '/includes/product-options/product-options.php';
		}
		require_once __DIR__ . '/includes/mobile-menu/mobile-menu.php';
	}
);
