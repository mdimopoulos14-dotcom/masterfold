<?php
/**
 * Load page-specific code only on the pages that use it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the current page shows attribute swatches (class
 * "custom-lightbox-attribute"), which is what the swatch lightbox opens.
 * Content is rendered before the footer, so by wp_footer this is known.
 */
function mf_page_has_swatches( $set = null ) {
	static $found = false;
	if ( true === $set ) {
		$found = true;
	}
	return $found || ( function_exists( 'is_product' ) && is_product() );
}

$mf_detect_swatches = function ( $html ) {
	if ( is_string( $html ) && false !== strpos( $html, 'custom-lightbox-attribute' ) ) {
		mf_page_has_swatches( true );
	}
	return $html;
};
add_filter( 'do_shortcode_tag', $mf_detect_swatches, 999 );
add_filter( 'elementor/widget/render_content', $mf_detect_swatches, 999 );
add_filter( 'the_content', $mf_detect_swatches, 999 );

/*
 * Stylesheets that are only needed when their plugin's markup is on the page.
 *
 * After the page is rendered, a stylesheet is dropped when none of its
 * markers appear in the HTML — so adding e.g. a Royal Addons widget to a
 * page in Elementor automatically brings its CSS back.
 */
function mf_conditional_styles() {
	return apply_filters(
		'mf_conditional_styles',
		array(
			// Royal Elementor Addons extras. (Its main stylesheet, wpr-addons-css, also styles
			// regular Elementor layouts, so it always loads.)
			'wpr-animations-css-css'             => array( 'elementor-widget-wpr-', 'wpr-anim', 'wpr-popup' ),
			'wpr-button-animations-css-css'      => array( 'elementor-widget-wpr-', 'wpr-button-' ),
			'wpr-loading-animations-css-css'     => array( 'elementor-widget-wpr-', 'wpr-loading' ),
			'wpr-text-animations-css-css'        => array( 'elementor-widget-wpr-', 'wpr-anim-text' ),
			'wpr-lightgallery-css-css'           => array( 'elementor-widget-wpr-', 'wpr-lightbox' ),
			// Icon font for the admin bar / dashicons classes.
			'dashicons-css'                      => array( 'dashicons', 'wpadminbar' ),
			// Slide Anything (owl carousel + lightgallery).
			'owl_carousel_css-css'               => array( 'owl-carousel', 'sa_owl' ),
			'owl_theme_css-css'                  => array( 'owl-carousel', 'sa_owl' ),
			'owl_animate_css-css'                => array( 'owl-carousel', 'sa_owl' ),
			'lightgallery_css-css'               => array( 'owl-carousel', 'sa_owl', 'lightgallery' ),
			'lightgallery_bundle_css-css'        => array( 'owl-carousel', 'sa_owl', 'lightgallery' ),
			// Super Interactive Maps icon font. (Its main stylesheet, mega-interactivemap-css,
			// contains generic rules the home layout depends on, so it always loads.)
			'mega-font-awesome-css'              => array( 'mega-interactivemap', 'interactive-map' ),
			// Simply Gallery.
			'pgc-simply-gallery-plugin-lightbox-style-css' => array( 'pgc-sgb', 'simply-gallery' ),
			// Woo Category Slider Pro.
			'woo-category-slider-pro-css'        => array( 'sp-wcsp' ),
			'woo-category-slider-pro-icon-css'   => array( 'sp-wcsp' ),
			'sp-wcsp-font-awesome-css'           => array( 'sp-wcsp' ),
			'sp-wcsp-swiper-css'                 => array( 'sp-wcsp' ),
		)
	);
}

function mf_strip_unused_styles( $html ) {
	if ( false === stripos( $html, '</head>' ) ) {
		return $html;
	}

	// Look for markers in the visible markup only: the body without scripts,
	// inline styles and stylesheet links (plugins mention their own names there).
	$body_start = stripos( $html, '<body' );
	$body       = false === $body_start ? $html : substr( $html, $body_start );
	$body       = preg_replace( array( '#<script\b[^>]*>.*?</script>#is', '#<style\b[^>]*>.*?</style>#is', '#<link\b[^>]*>#i' ), '', $body );

	foreach ( mf_conditional_styles() as $handle => $markers ) {
		$used = false;
		foreach ( $markers as $marker ) {
			if ( false !== strpos( $body, $marker ) ) {
				$used = true;
				break;
			}
		}
		if ( ! $used ) {
			$html = preg_replace( '/<link\b[^>]*\bid=[\'"]' . preg_quote( $handle, '/' ) . '[\'"][^>]*>\s*/i', '', $html, 1 );
		}
	}

	return $html;
}

add_action(
	'template_redirect',
	function () {
		if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		if ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) {
			return;
		}
		ob_start( 'mf_strip_unused_styles' );
	},
	2
);
