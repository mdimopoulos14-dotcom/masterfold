<?php
/**
 * Plugin Name: Masterfold Core
 * Description: Site-specific functionality for Masterfold: product attribute sections (Elementor widget), catalog mode, and the custom features that used to live in the theme.
 * Version: 1.8.8
 * Author: Masterfold
 * Requires Plugins: woocommerce
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MF_CORE_VERSION', '1.8.8' );
define( 'MF_CORE_URL', plugin_dir_url( __FILE__ ) );
define( 'MF_CORE_DIR', __DIR__ );

require_once __DIR__ . '/includes/legacy-theme-functions.php';
require_once __DIR__ . '/includes/cache/pixel-cache-compat.php';
require_once __DIR__ . '/includes/snippets/snippets.php';
require_once __DIR__ . '/includes/performance/images.php';
require_once __DIR__ . '/includes/performance/assets.php';
require_once __DIR__ . '/includes/performance/local-fonts.php';
require_once __DIR__ . '/includes/performance/slim-css.php';
require_once __DIR__ . '/includes/performance/early-hide.php';
require_once __DIR__ . '/includes/performance/local-cdn.php';
require_once __DIR__ . '/includes/header/sticky-header.php';
require_once __DIR__ . '/includes/media/alt-text.php';

add_action(
	'plugins_loaded',
	function () {
		if ( class_exists( 'WooCommerce' ) ) {
			require_once __DIR__ . '/includes/product-options/product-options.php';
		}
		require_once __DIR__ . '/includes/mobile-menu/mobile-menu.php';
	}
);
