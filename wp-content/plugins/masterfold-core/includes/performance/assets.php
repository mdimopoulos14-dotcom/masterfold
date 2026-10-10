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

/*
 * Scripts that are only needed when their plugin's markup is on the page.
 * Same rule as the stylesheets: dropped when none of the markers appear in
 * the rendered page, together with their inline before/after/extra data.
 */
function mf_conditional_scripts() {
	$slide_anything = array( 'owl-carousel', 'sa_owl' );
	$maps           = array( 'mega-interactivemap', 'interactive-map' );
	$royal_widgets  = array( 'elementor-widget-wpr-' );
	return apply_filters(
		'mf_conditional_scripts',
		array(
			// Slide Anything (owl carousel + lightgallery).
			'owl_carousel_js'          => $slide_anything,
			'mousewheel_js'            => $slide_anything,
			'owl_thumbs_js'            => $slide_anything,
			'lightgallery_js'          => $slide_anything,
			'lightgallery_video_js'    => $slide_anything,
			'lightgallery_zoom_js'     => $slide_anything,
			'lightgallery_autoplay_js' => $slide_anything,
			'vimeo_player_js'          => $slide_anything,
			// Super Interactive Maps (also loads Google's chart loaders).
			'mega-chart'               => $maps,
			'mega-google-load'         => $maps,
			'PanZoom'                  => $maps,
			'interactivemap'           => $maps,
			// Royal Elementor Addons libraries used by its own widgets/effects.
			'wpr-particles'            => array( 'wpr-particle-yes' ),
			'wpr-jarallax'             => array( 'wpr-jarallax-yes' ),
			'wpr-parallax-hover'       => array( 'wpr-parallax-yes' ),
			'wpr-isotope'              => $royal_widgets,
			'wpr-slick'                => $royal_widgets,
			'wpr-lightgallery'         => $royal_widgets,
			// Simply Gallery lightbox.
			'pgc-simply-gallery-plugin-lightbox-script' => array( 'pgc-sgb', 'simply-gallery' ),
			// Fancybox v3 (binds to data-fancybox links).
			'fancybox-v3-js'           => array( 'data-fancybox' ),
			// 3D FlipBook.
			'3d-flip-book-client-locale-loader' => array( '3d-flip-book', 'fb3d' ),
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

	if ( function_exists( 'mf_use_slim_wpr_css' ) ) {
		$html = mf_use_slim_wpr_css( $html, $body );
	}

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

	foreach ( mf_conditional_scripts() as $handle => $markers ) {
		$used = false;
		foreach ( $markers as $marker ) {
			if ( false !== strpos( $body, $marker ) ) {
				$used = true;
				break;
			}
		}
		if ( ! $used ) {
			$id   = preg_quote( $handle, '/' ) . '-js(?:-extra|-before|-after|-translations)?';
			$html = preg_replace( '/<script\b[^>]*\bid=[\'"]' . $id . '[\'"][^>]*>.*?<\/script>\s*/is', '', $html );
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
		ob_start(
			function ( $html ) {
				$html = mf_strip_unused_styles( $html );
				if ( function_exists( 'mf_early_hide_lazyloaded' ) ) {
					$html = mf_early_hide_lazyloaded( $html );
				}
				return function_exists( 'mf_localize_google_fonts' ) ? mf_localize_google_fonts( $html ) : $html;
			}
		);
	},
	2
);
