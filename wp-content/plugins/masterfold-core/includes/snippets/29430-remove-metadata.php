<?php
/**
 * REMOVE METADATA
 *
 * Moved from WPCode snippet #29430 (location: everywhere). Code unchanged.
 */

/*// 🛑 Prevent WordPress from generating any additional image sizes
function disable_default_image_sizes( $sizes ) {
    return [];
}
add_filter( 'intermediate_image_sizes_advanced', 'disable_default_image_sizes' );

function remove_custom_image_sizes() {
    foreach ( get_intermediate_image_sizes() as $size ) {
        remove_image_size( $size );
    }
}
add_action( 'init', 'remove_custom_image_sizes' );

// 🛑 Disable scaled "big image" fallback (introduced in WP 5.3)
add_filter( 'big_image_size_threshold', '__return_false' );

// 🛑 Disable responsive images (remove srcset and sizes)
add_filter( 'wp_calculate_image_srcset', '__return_false' );
add_filter( 'wp_calculate_image_sizes', '__return_false' );
add_filter( 'wp_get_attachment_image_srcset', '__return_false' );
add_filter( 'wp_get_attachment_image_sizes', '__return_false' );

// 🛑 Optional: Disable WooCommerce specific sizes
add_filter( 'woocommerce_get_image_size_gallery_thumbnail', '__return_false' );

add_filter( 'woocommerce_get_image_size_single', '__return_false' );*/
