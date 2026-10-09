<?php
/**
 * Product page: attribute sections and "catalog mode" (no cart / checkout).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/groups.php';
require_once __DIR__ . '/render.php';

/**
 * The product being displayed (front end or Elementor preview).
 */
function mf_current_product() {
	global $product;

	if ( $product instanceof WC_Product ) {
		return $product;
	}

	$candidate = wc_get_product( get_the_ID() );
	return $candidate instanceof WC_Product ? $candidate : null;
}

add_action(
	'elementor/widgets/register',
	function ( $widgets_manager ) {
		require_once __DIR__ . '/class-attribute-section-widget.php';
		$widgets_manager->register( new MF_Attribute_Section_Widget() );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_register_style( 'mf-product-options', MF_CORE_URL . 'assets/css/product-options.css', array(), MF_CORE_VERSION );
	}
);

/**
 * Hide the specifications block on products without any attributes
 * (previously done in JavaScript after page load).
 */
add_filter(
	'body_class',
	function ( $classes ) {
		if ( is_product() ) {
			$product = wc_get_product( get_queried_object_id() );
			if ( $product && ! mf_product_has_options( $product ) ) {
				$classes[] = 'mf-no-product-options';
			}
		}
		return $classes;
	}
);

/*
 * Catalog mode: products are shown for inspiration and quote requests
 * (wishlist "Ask for estimate"), never sold online.
 */
if ( get_option( 'mf_catalog_mode', 'no' ) === 'yes' ) {
	add_filter( 'woocommerce_is_purchasable', '__return_false', 99 );
	add_filter( 'woocommerce_variation_is_purchasable', '__return_false', 99 );

	add_action(
		'init',
		function () {
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
		}
	);

	// Cart and checkout pages lead nowhere in catalog mode.
	add_action(
		'template_redirect',
		function () {
			if ( ( function_exists( 'is_cart' ) && is_cart() ) || ( function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url() ) ) {
				wp_safe_redirect( home_url( '/' ) );
				exit;
			}
		}
	);
}

/*
 * Placeholder kept in the product templates where the old attribute-check
 * script used to be, so the layout spacing stays exactly the same.
 */
add_shortcode(
	'mf_section_spacer',
	function () {
		return '<span class="mf-section-spacer" hidden></span>';
	}
);
