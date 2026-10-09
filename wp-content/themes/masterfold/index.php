<?php
/**
 * Main template: Elementor theme-builder templates first, simple fallbacks otherwise.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$has_location = function_exists( 'elementor_theme_do_location' );

if ( is_singular() ) {
	if ( ! $has_location || ! elementor_theme_do_location( 'single' ) ) {
		get_template_part( 'template-parts/single' );
	}
} elseif ( is_archive() || is_home() || is_search() ) {
	if ( ! $has_location || ! elementor_theme_do_location( 'archive' ) ) {
		get_template_part( 'template-parts/archive' );
	}
} elseif ( ! $has_location || ! elementor_theme_do_location( 'single' ) ) {
	get_template_part( 'template-parts/404' );
}

get_footer();
