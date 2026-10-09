<?php
/**
 * Masterfold theme.
 *
 * A minimal shell for Elementor: theme supports, menu locations, Elementor
 * theme-builder locations and one small base stylesheet. Site features
 * (product options, menus, performance) are in the Masterfold Core plugin,
 * so they survive a theme switch.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MASTERFOLD_THEME_VERSION', '1.0.0' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

add_action(
	'after_setup_theme',
	function () {
		register_nav_menus(
			array(
				'menu-1' => __( 'Header', 'masterfold' ),
				'menu-2' => __( 'Footer', 'masterfold' ),
			)
		);

		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style', 'navigation-widgets' ) );
		add_theme_support( 'custom-logo', array( 'height' => 100, 'width' => 350, 'flex-height' => true, 'flex-width' => true ) );
		add_theme_support( 'align-wide' );
		add_theme_support( 'responsive-embeds' );

		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'masterfold-base', get_template_directory_uri() . '/assets/css/base.css', array(), MASTERFOLD_THEME_VERSION );
	}
);

// Header, footer, single and archive layouts come from Elementor Pro's theme builder.
add_action(
	'elementor/theme/register_locations',
	function ( $elementor_theme_manager ) {
		$elementor_theme_manager->register_all_core_location();
	}
);

/**
 * Meta description from the excerpt on single pages (kept from the previous
 * theme so the page head stays exactly the same).
 */
add_action(
	'wp_head',
	function () {
		if ( ! apply_filters( 'masterfold_description_meta_tag', true ) || ! is_singular() ) {
			return;
		}
		$post = get_queried_object();
		if ( empty( $post->post_excerpt ) ) {
			return;
		}
		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $post->post_excerpt ) ) . '">' . "\n";
	}
);

/**
 * Whether to print the page title on pages not designed in Elementor.
 * Elementor's "Hide Title" page setting turns it off. The old filter name is
 * kept so existing code that used it keeps working.
 */
function masterfold_show_page_title() {
	$show = true;
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		$document = \Elementor\Plugin::instance()->documents->get( get_the_ID() );
		if ( $document && 'yes' === $document->get_settings( 'hide_title' ) ) {
			$show = false;
		}
	}
	$show = apply_filters( 'hello_elementor_page_title', $show );
	return apply_filters( 'masterfold_page_title', $show );
}
