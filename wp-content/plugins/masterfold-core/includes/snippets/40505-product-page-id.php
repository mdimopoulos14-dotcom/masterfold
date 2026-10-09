<?php
/**
 * product page id
 *
 * Moved from WPCode snippet #40505 (location: everywhere). Code unchanged.
 */

/**
 * WooCommerce Custom Shortcode to Display Product ID
 * Usage: [product_page_id_display]
 */
function display_product_page_id_shortcode() {
    // Check if we are on a single product page and if the global $product object exists
    if ( is_product() && is_a( $GLOBALS['product'], 'WC_Product' ) ) {
        // Get the current product ID
        $product_id = $GLOBALS['product']->get_id();

        // Define the styles based on your request (color #9E9797, size 17px)
        $style = 'color: #9E9797; font-size: 15px;';

        // Output the formatted ID in a span tag for styling
        $output = '<span style="' . esc_attr( $style ) . '">ID: ' . esc_html( $product_id ) . '</span>';
        
        return $output;
    }

    // Return nothing if not on a single product page
    return '';
}
// Register the new shortcode tag: [product_page_id_display]
add_shortcode( 'product_page_id_display', 'display_product_page_id_shortcode' );
