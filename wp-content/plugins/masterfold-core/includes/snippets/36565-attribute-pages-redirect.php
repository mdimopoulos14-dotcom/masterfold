<?php
/**
 * ATTRIBUTE PAGES REDIRECT
 *
 * Moved from WPCode snippet #36565 (location: everywhere). Code unchanged.
 */

// Disable attribute term links in product pages
add_filter('woocommerce_attribute', function($text, $term){
    // Remove links around attribute terms
    return strip_tags($text);
}, 10, 2);

// Disable WooCommerce attribute archives ONLY (not product categories)
add_action('template_redirect', function() {
    $taxonomies = wc_get_attribute_taxonomy_names(); // Only attribute taxonomies like pa_color, pa_size
    if (is_tax($taxonomies)) {
        wp_redirect(home_url());
        exit;
    }
});


