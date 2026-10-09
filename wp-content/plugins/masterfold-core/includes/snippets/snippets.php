<?php
/**
 * Former WPCode "everywhere" PHP snippets, now regular plugin files so PHP can cache them.
 *
 * They run at the same moment WPCode ran them (plugins_loaded, priority 5) and in the same order.
 * Each file only runs once its original WPCode snippet is no longer active, so the
 * two can never run at the same time. The originals stay in WPCode as drafts.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Former snippets: WPCode snippet ID => file, in WPCode's original order.
 */
function mf_migrated_snippets() {
	return array(
		45176 => '45176-special-product-sizes.php', // Special Product: SIZES
		40505 => '40505-product-page-id.php', // product page id
		39291 => '39291-recycled-leather-b-w.php', // RECYCLED LEATHER B&W
		38453 => '38453-product-page-image-slider-mobile.php', // PRODUCT PAGE IMAGE SLIDER MOBILE
		37574 => '37574-cork.php', // CORK
		37532 => '37532-corium.php', // CORIUM
		36565 => '36565-attribute-pages-redirect.php', // ATTRIBUTE PAGES REDIRECT
		404 => '404-completely-disable-comments.php', // Completely Disable Comments
		34980 => '34980-custom-breadcrump-for-products.php', // CUSTOM BREADCRUMP for products
		34946 => '34946-suedel-luxe.php', // SUEDEL® LUXE
		33211 => '33211-fabric.php', // Fabric
		32336 => '32336-you-may-also-like-similar-products.php', // You may also like (similar products)
		31849 => '31849-untitled-snippet.php', // Untitled Snippet
		30858 => '30858-product-search.php', // PRODUCT SEARCH
		30206 => '30206-untitled-snippet.php', // Untitled Snippet
		30167 => '30167-pvc.php', // PVC
		30144 => '30144-standard-paper.php', // STANDARD PAPER
		29957 => '29957-fancybox-v3-product-images-lightbox.php', // Fancybox v3 (PRODUCT IMAGES LIGHTBOX)
		29812 => '29812-natural-surfaces-natural-surfaces.php', // NATURAL SURFACES natural_surfaces
		29738 => '29738-wood.php', // WOOD®
		29449 => '29449-check-login.php', // Check Login
		29430 => '29430-remove-metadata.php', // REMOVE METADATA
		28752 => '28752-recycled-leather.php', // RECYCLED LEATHER
		28747 => '28747-leather.php', // LEATHER
		28713 => '28713-quinel.php', // QUINEL®
		28373 => '28373-corvon.php', // CORVON®
		28347 => '28347-napura-sisal.php', // NAPURA® SISAL
		28329 => '28329-napura-sangha.php', // NAPURA® SANGHA
		28312 => '28312-napura-madera.php', // NAPURA® MADERA
		28300 => '28300-napura-khepera.php', // NAPURA® KHEPERA
		28260 => '28260-napura-kazar.php', // NAPURA® KAZAR
		28256 => '28256-napura-canvas.php', // NAPURA® CANVAS
		28232 => '28232-napura-bamboa.php', // NAPURA® BAMBOA
		28211 => '28211-premium-papers-color.php', // PREMIUM PAPERS COLOR
		4873 => '4873-final-lightbox.php', // FINAL LIGHTBOX
		2600 => '2600-attributes-shortcodes.php', // attributes shortcodes
		1212 => '1212-back-to-category-from-product.php', // Back to Category from product
	);
}

add_action(
	'plugins_loaded',
	function () {
		global $wpdb;

		$snippets = mf_migrated_snippets();
		$ids      = implode( ',', array_map( 'intval', array_keys( $snippets ) ) );

		// Snippets still active in WPCode keep running there instead.
		$active = wp_cache_get( 'mf_wpcode_active', 'masterfold' );
		if ( false === $active ) {
			$active = array_map( 'intval', $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'wpcode' AND post_status = 'publish' AND ID IN ($ids)" ) ); // phpcs:ignore WordPress.DB
			wp_cache_set( 'mf_wpcode_active', $active, 'masterfold', 300 );
		}

		foreach ( $snippets as $id => $file ) {
			if ( ! in_array( $id, $active, true ) ) {
				require_once __DIR__ . '/' . $file;
			}
		}
	},
	5
);

// Refresh the check as soon as a snippet is switched on or off in WPCode.
add_action(
	'transition_post_status',
	function ( $new, $old, $post ) {
		if ( 'wpcode' === $post->post_type ) {
			wp_cache_delete( 'mf_wpcode_active', 'masterfold' );
		}
	},
	10,
	3
);
