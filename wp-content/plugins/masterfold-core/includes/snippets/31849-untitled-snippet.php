<?php
/**
 * Untitled Snippet
 *
 * Moved from WPCode snippet #31849 (location: everywhere). Code unchanged.
 */

function grouped_products_by_category_shortcode($atts) {
    if (!is_woocommerce()) return '';

    global $wp_query;
    $products = $wp_query->posts;

    if (empty($products)) return '<p>No products found.</p>';

    $grouped = [];

    foreach ($products as $product_post) {
        $terms = get_the_terms($product_post->ID, 'product_cat');

        if (!$terms || is_wp_error($terms)) continue;

        $primary_term = $terms[0];

        $grouped[$primary_term->term_id]['term'] = $primary_term;
        $grouped[$primary_term->term_id]['products'][] = $product_post;
    }

    ob_start();

    foreach ($grouped as $group) {
        $term = $group['term'];
        $products = $group['products'];

        $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);
        $image = wp_get_attachment_url($thumbnail_id);

        echo '<div class="category-header" style="margin:40px 0;">';
        if ($image) {
            echo '<img src="' . esc_url($image) . '" alt="' . esc_attr($term->name) . '" style="max-width:150px;">';
        }
        echo '<h2>' . esc_html($term->name) . '</h2>';
        echo '</div>';

        echo '<ul class="products columns-4">';
        foreach ($products as $post) {
            setup_postdata($post);
            wc_get_template_part('content', 'product');
        }
        echo '</ul>';
    }

    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('grouped_products_by_category', 'grouped_products_by_category_shortcode');

