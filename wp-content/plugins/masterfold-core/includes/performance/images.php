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

/*
 * Masterfold Category Slider widget (home page).
 *
 * The widget prints each column picture as a bare <img> of the full upload
 * (about 1075px wide, shown at about 592px), with no size attributes and no
 * lazy loading. Each picture keeps its file and look; it gains the
 * attachment's srcset (so the browser picks the right size), width/height
 * (space reserved, no layout shift). Loading stays eager: native lazy loading
 * does not trigger inside the slider, so slides would stay blank.
 */
function mf_enhance_slider_images( $content, $widget ) {
	if ( 'masterfold_category_slider' !== $widget->get_name() || false === strpos( $content, '<img' ) ) {
		return $content;
	}
	return preg_replace_callback(
		'/<img\b[^>]*>/i',
		function ( $m ) {
			$img = $m[0];
			if ( false !== stripos( $img, 'srcset=' ) || ! preg_match( '/\bsrc="([^"]+)"/i', $img, $src ) ) {
				return $img;
			}
			$id = attachment_url_to_postid( $src[1] );
			if ( ! $id ) {
				return $img;
			}
			$meta = wp_get_attachment_metadata( $id );
			if ( empty( $meta['width'] ) || empty( $meta['height'] ) ) {
				return $img;
			}
			$add = ' width="' . (int) $meta['width'] . '" height="' . (int) $meta['height'] . '"';
			$srcset = wp_get_attachment_image_srcset( $id, 'full', $meta );
			if ( $srcset ) {
				$add .= ' srcset="' . esc_attr( $srcset ) . '" sizes="(max-width: 767px) 50vw, 600px"';
			}
			if ( false === stripos( $img, 'loading=' ) ) {
				$add .= ' loading="eager"'; // Keeps WordPress from adding loading="lazy".
			}
			return preg_replace( '/^<img\b/i', '<img' . $add, $img );
		},
		$content
	);
}
add_filter( 'elementor/widget/render_content', 'mf_enhance_slider_images', 20, 2 );
