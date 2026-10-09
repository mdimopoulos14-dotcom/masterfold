<?php
/**
 * You may also like (similar products)
 *
 * Moved from WPCode snippet #32336 (location: everywhere). Code unchanged.
 */

// Helper: Get main parent category ID (returns either 1071 or 971 based on product's category)
function get_main_parent_cat_id($post_id) {
    $product_cats = wp_get_post_terms($post_id, 'product_cat');
    foreach ($product_cats as $cat) {
        // Walk up the category tree to find the main parent
        $parent = $cat;
        while ($parent->parent != 0) {
            $parent = get_term($parent->parent, 'product_cat');
        }
        // Check if this main parent is 1071 or 971
        if (in_array($parent->term_id, [1071, 971, 1231, 1244, 2367, 1263, 2644, 2854, 1263])) {
            return $parent->term_id;
        }
    }
    return null; // fallback if no matching main parent found
}

// Helper: Get current product's level 2 category (under given parent category)
function get_current_level2_category($post_id, $parent_cat_id) {
    $product_cats = wp_get_post_terms($post_id, 'product_cat');

    foreach ($product_cats as $cat) {
        if ($cat->parent == $parent_cat_id) return $cat;

        if ($cat->parent) {
            $parent = get_term($cat->parent, 'product_cat');
            if ($parent && $parent->parent == $parent_cat_id) return $parent;
        }
    }

    return null;
}

// [next_category_product] → shows 1 random product from next category (dynamic parent)
add_shortcode('next_category_product', function () {
    if (!is_singular('product')) return '';

    global $post;

    $parent_cat_id = get_main_parent_cat_id($post->ID);
    if (!$parent_cat_id) return '';

    $level2_cats = get_terms([
        'taxonomy' => 'product_cat',
        'parent' => $parent_cat_id,
        'hide_empty' => true,
        'orderby' => 'term_id',
        'order' => 'ASC',
    ]);

    $current_cat = get_current_level2_category($post->ID, $parent_cat_id);
    if (!$current_cat || empty($level2_cats)) return '';

    $cat_ids = wp_list_pluck($level2_cats, 'term_id');
    $current_index = array_search($current_cat->term_id, $cat_ids);
    if ($current_index === false) return '';

    $next_index = ($current_index + 1) % count($level2_cats);
    $next_cat = $level2_cats[$next_index];

    $products = get_posts([
        'post_type' => 'product',
        'posts_per_page' => 1,
        'orderby' => 'rand',
        'suppress_filters' => false,
        'tax_query' => [[
            'taxonomy' => 'product_cat',
            'terms' => [$next_cat->term_id],
        ]],
        'post__not_in' => [$post->ID]
    ]);

    if (empty($products)) return '';

    $p = wc_get_product($products[0]->ID);

    ob_start();
    ?>
    <ul class="first-product-next-category one-column">
        <li class="<?= esc_attr(implode(' ', get_post_class('product', $p->get_id()))) ?>" data-url="<?= esc_url(get_permalink($p->get_id())) ?>">
            <a href="<?= esc_url(get_permalink($p->get_id())) ?>" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">
                <?= $p->get_image() ?>
                <h2 class="woocommerce-loop-product__title"><?= esc_html($p->get_name()) ?></h2>
            </a>
        </li>
    </ul>
    <?php
    return ob_get_clean();
});

// [next_two_products_offset] → shows 2 products from offset+2 and offset+3 categories (dynamic parent)
add_shortcode('next_two_products_offset', function () {
    if (!is_singular('product')) return '';

    global $post;

    $parent_cat_id = get_main_parent_cat_id($post->ID);
    if (!$parent_cat_id) return '';

    $level2_cats = get_terms([
        'taxonomy' => 'product_cat',
        'parent' => $parent_cat_id,
        'hide_empty' => true,
        'orderby' => 'term_id',
        'order' => 'ASC',
    ]);

    $current_cat = get_current_level2_category($post->ID, $parent_cat_id);
    if (!$current_cat || empty($level2_cats)) return '';

    $cat_ids = wp_list_pluck($level2_cats, 'term_id');
    $current_index = array_search($current_cat->term_id, $cat_ids);
    if ($current_index === false) return '';

    $html = '<ul class="two-products-next-category two-columns">';

    foreach ([2, 3] as $offset) {
        $next_index = ($current_index + $offset) % count($level2_cats);
        $next_cat = $level2_cats[$next_index];

        $products = get_posts([
            'post_type' => 'product',
            'posts_per_page' => 1,
            'orderby' => 'rand',
            'suppress_filters' => false,
            'tax_query' => [[
                'taxonomy' => 'product_cat',
                'terms' => [$next_cat->term_id],
            ]],
            'post__not_in' => [$post->ID]
        ]);

        if (empty($products)) continue;

        $p = wc_get_product($products[0]->ID);

        $html .= '<li class="' . esc_attr(implode(' ', get_post_class('product', $p->get_id()))) . '" data-url="' . esc_url(get_permalink($p->get_id())) . '">';
        $html .= '<a href="' . esc_url(get_permalink($p->get_id())) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
        $html .= $p->get_image();
        $html .= '<h2 class="woocommerce-loop-product__title">' . esc_html($p->get_name()) . '</h2>';
        $html .= '</a>';
        $html .= '</li>';
    }

    $html .= '</ul>';
    return $html;
});

// [first_three_next_category_products] → shows 3 products from next 3 categories (dynamic parent)
add_shortcode('first_three_next_category_products', function () {
    if (!is_singular('product')) return '';

    global $post;

    $parent_cat_id = get_main_parent_cat_id($post->ID);
    if (!$parent_cat_id) return '';

    $level2_cats = get_terms([
        'taxonomy' => 'product_cat',
        'parent' => $parent_cat_id,
        'hide_empty' => true,
        'orderby' => 'term_id',
        'order' => 'ASC',
    ]);

    $current_cat = get_current_level2_category($post->ID, $parent_cat_id);
    if (!$current_cat || empty($level2_cats)) return '';

    $cat_ids = wp_list_pluck($level2_cats, 'term_id');
    $current_index = array_search($current_cat->term_id, $cat_ids);
    if ($current_index === false) return '';

    $html = '<ul class="first-three-products-next-category three-columns">';

    for ($i = 1; $i <= 3; $i++) {
        $next_index = ($current_index + $i) % count($level2_cats);
        $next_cat = $level2_cats[$next_index];

        $products = get_posts([
            'post_type' => 'product',
            'posts_per_page' => 1,
            'orderby' => 'rand',
            'suppress_filters' => false,
            'tax_query' => [[
                'taxonomy' => 'product_cat',
                'terms' => [$next_cat->term_id],
            ]],
            'post__not_in' => [$post->ID]
        ]);

        if (empty($products)) continue;

        $p = wc_get_product($products[0]->ID);

        $html .= '<li class="' . esc_attr(implode(' ', get_post_class('product', $p->get_id()))) . '" data-url="' . esc_url(get_permalink($p->get_id())) . '">';
        $html .= '<a href="' . esc_url(get_permalink($p->get_id())) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">';
        $html .= $p->get_image();
        $html .= '<h2 class="woocommerce-loop-product__title">' . esc_html($p->get_name()) . '</h2>';
        $html .= '</a>';
        $html .= '</li>';
    }

    $html .= '</ul>';
    return $html;
});

