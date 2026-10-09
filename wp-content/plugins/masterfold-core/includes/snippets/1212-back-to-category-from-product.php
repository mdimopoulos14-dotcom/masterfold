<?php
/**
 * Back to Category from product
 *
 * Moved from WPCode snippet #1212 (location: everywhere). Code unchanged.
 */

// Helper function to check if a term is a child of another
function term_is_child_of($child_id, $parent_id) {
    $term = get_term($child_id, 'product_cat');
    while ($term->parent) {
        if ($term->parent == $parent_id) {
            return true;
        }
        $term = get_term($term->parent, 'product_cat');
    }
    return false;
}

// Shortcode function for product navigation buttons
function product_navigation_buttons_shortcode($atts) {
    $atts = shortcode_atts(array(
        'category'   => '',
        'product_id' => '',
    ), $atts, 'product_navigation_buttons');

    if (empty($atts['product_id'])) {
        global $product;
        if (!$product) return '';
        $atts['product_id'] = $product->get_id();
    }

    $product_id = $atts['product_id'];
    $deepest_category = null;

    if (empty($atts['category'])) {
        $terms = get_the_terms($product_id, 'product_cat');
        if ($terms && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                if (!$deepest_category || term_is_child_of($term->term_id, $deepest_category->term_id)) {
                    $deepest_category = $term;
                }
            }
        }
    } else {
        $deepest_category = get_term($atts['category'], 'product_cat');
    }

    if ($deepest_category) {

        // ✅ Find the category at depth 2 (e.g., "Business Cards")
        $ancestors = get_ancestors($deepest_category->term_id, 'product_cat');
        $ancestors = array_reverse($ancestors);
        $ancestors[] = $deepest_category->term_id;

        // If hierarchy has at least 2 levels, pick the 2nd one
        if (isset($ancestors[1])) {
            $second_level_cat_id = $ancestors[1];
        } else {
            // fallback — use top-level if less depth
            $second_level_cat_id = $ancestors[0];
        }

        // get term object for navigation link
        $second_level_category = get_term($second_level_cat_id, 'product_cat');
        $parent_category_link = get_term_link($second_level_category, 'product_cat');

        // ✅ Query products inside the 2nd-level category
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $second_level_cat_id,
                ),
            ),
        );

        $products = get_posts($args);

        if ($products) {
            $product_ids = array_map('intval', $products);
            $current_index = array_search($product_id, $product_ids);

            $prev_product = ($current_index > 0) ? $product_ids[$current_index - 1] : null;
            $next_product = ($current_index < count($product_ids) - 1) ? $product_ids[$current_index + 1] : null;

            $navigation = '<div class="product-navigation" style="display: flex; gap: 25px; align-items: center;">';

            if ($prev_product) {
                $navigation .= '<a href="' . get_permalink($prev_product) . '" class="prev-product" style="text-decoration: none;">'
                    . '<img style="width: 20px; height: 20px;" src="/wp-content/uploads/left-arrow-svgrepo-com-1.svg" alt="Previous"></a>';
            }

            if ($parent_category_link) {
                $navigation .= '<a href="' . esc_url($parent_category_link) . '" class="category-back-button" style="text-decoration: none;">'
                    . '<img style="width: 22px; height: 22px;" src="/wp-content/uploads/keyboard-buttons-or-visualization-button-of-nine-squares-svgrepo-com3.png" alt="Back to Category"></a>';
            }

            if ($next_product) {
                $navigation .= '<a href="' . get_permalink($next_product) . '" class="next-product" style="text-decoration: none;">'
                    . '<img style="width: 20px; height: 20px;" src="/wp-content/uploads/right-arrow-svgrepo-com-1.svg" alt="Next"></a>';
            }

            $navigation .= '</div>';

            return $navigation;
        }
    }

    return '';
}

// Register the shortcode
add_shortcode('product_navigation_buttons', 'product_navigation_buttons_shortcode');

// Automatically show navigation on single product page
add_action('woocommerce_before_single_product_summary', function() {
    echo do_shortcode('[product_navigation_buttons]');
}, 5);

