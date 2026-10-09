<?php
/**
 * Image delivery: same pictures, appropriately sized files.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Category product grid ([dynamic_filtering_tabs]).
 *
 * The grid used to print WooCommerce's cropped thumbnail and then swap it in
 * the browser for the full-size original (up to 2560px, downloaded twice).
 * It now prints the uncropped "large" size (same framing as the original)
 * with a srcset, so each screen downloads one sharp image of the right size.
 */
add_action(
	'init',
	function () {
		if ( ! shortcode_exists( 'dynamic_filtering_tabs' ) ) {
			return;
		}

		global $shortcode_tags;
		$original = $shortcode_tags['dynamic_filtering_tabs'];

		add_shortcode(
			'dynamic_filtering_tabs',
			function ( $atts, $content = '', $tag = '' ) use ( $original ) {
				$size  = function () {
					return 'large';
				};
				$sizes = function ( $attr ) {
					$attr['sizes'] = '(max-width: 767px) 100vw, 464px';
					return $attr;
				};

				add_filter( 'single_product_archive_thumbnail_size', $size, 99 );
				add_filter( 'wp_get_attachment_image_attributes', $sizes, 99 );
				$html = call_user_func( $original, $atts, $content, $tag );
				remove_filter( 'single_product_archive_thumbnail_size', $size, 99 );
				remove_filter( 'wp_get_attachment_image_attributes', $sizes, 99 );

				return $html;
			}
		);
	},
	20
);
