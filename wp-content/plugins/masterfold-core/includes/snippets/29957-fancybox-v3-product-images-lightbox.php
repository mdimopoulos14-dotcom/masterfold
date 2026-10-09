<?php
/**
 * Fancybox v3 (PRODUCT IMAGES LIGHTBOX)
 *
 * Moved from WPCode snippet #29957 (location: everywhere). Code unchanged.
 */

function disable_plugin_lightbox_assets() {
    // Example: Dequeue scripts/styles from the plugin
    wp_dequeue_script('fancybox');
    wp_dequeue_style('fancybox');

    // Replace 'fancybox' with the actual handle of the plugin's script/style
}
add_action('wp_enqueue_scripts', 'disable_plugin_lightbox_assets', 100);

function load_custom_fancybox_v3() {
    wp_enqueue_style('fancybox-v3-css', 'https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css');
    wp_enqueue_script('fancybox-v3-js', 'https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'load_custom_fancybox_v3');

add_filter('woocommerce_get_image_size_single', function($size) {
    return array(
        'width' => 800,
        'height' => 1000, // Change to desired height
        'crop' => 1        // Crop to exact dimensions
    );
});


