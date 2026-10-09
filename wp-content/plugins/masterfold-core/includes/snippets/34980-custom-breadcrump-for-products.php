<?php
/**
 * CUSTOM BREADCRUMP for products
 *
 * Moved from WPCode snippet #34980 (location: everywhere). Code unchanged.
 */

function custom_breadcrumb_shortcode() {
    global $post;

    $separator = ' / ';
    $breadcrumb = '<div class="custom-breadcrumb">';
    $breadcrumb .= '<a href="' . home_url() . '">Home</a>';

    // WooCommerce single product
    if (function_exists('is_product') && is_product()) {
        $terms = wp_get_post_terms($post->ID, 'product_cat');
        if ($terms && !is_wp_error($terms)) {
            // Pick the deepest category
            $deepest = null;
            foreach ($terms as $term) {
                if (!$deepest || count(get_ancestors($term->term_id, 'product_cat')) > count(get_ancestors($deepest->term_id, 'product_cat'))) {
                    $deepest = $term;
                }
            }

            if ($deepest) {
                $ancestors = get_ancestors($deepest->term_id, 'product_cat');
                $ancestors = array_reverse($ancestors);

                // Root category
                if (!empty($ancestors)) {
                    $root = get_term($ancestors[0], 'product_cat');
                    $breadcrumb .= $separator . '<a href="' . get_term_link($root) . '">' . $root->name . '</a>';

                    // Find first child under root in the path to product
                    $first_child_id = (count($ancestors) > 1) ? $ancestors[1] : $deepest->term_id;
                    $first_child = get_term($first_child_id, 'product_cat');

                    // Only add if not the root
                    if ($first_child->term_id != $root->term_id) {
                        $breadcrumb .= $separator . '<a href="' . get_term_link($first_child) . '">' . $first_child->name . '</a>';
                    }
                } else {
                    // No ancestors, show the term itself
                    $breadcrumb .= $separator . '<a href="' . get_term_link($deepest) . '">' . $deepest->name . '</a>';
                }
            }
        }

        // Current product
        $breadcrumb .= $separator . '<span>' . get_the_title() . '</span>';
    }

    // WooCommerce category archive
    elseif (function_exists('is_product_category') && is_product_category()) {
        $current_term = get_queried_object();
        $ancestors = get_ancestors($current_term->term_id, 'product_cat');
        $ancestors = array_reverse($ancestors);

        if (!empty($ancestors)) {
            $root = get_term($ancestors[0], 'product_cat');
            $breadcrumb .= $separator . '<a href="' . get_term_link($root) . '">' . $root->name . '</a>';

            $first_child_id = (count($ancestors) > 1) ? $ancestors[1] : $current_term->term_id;
            $first_child = get_term($first_child_id, 'product_cat');

            if ($first_child->term_id != $root->term_id && $first_child->term_id != $current_term->term_id) {
                $breadcrumb .= $separator . '<a href="' . get_term_link($first_child) . '">' . $first_child->name . '</a>';
            }
        }

        $breadcrumb .= $separator . '<span>' . $current_term->name . '</span>';
    }

    // Pages
    elseif (is_page() && !is_front_page()) {
        $parents = array();
        $parent_id = $post->post_parent;
        while ($parent_id) {
            $page = get_page($parent_id);
            $parents[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
            $parent_id = $page->post_parent;
        }
        $parents = array_reverse($parents);
        foreach ($parents as $parent) {
            $breadcrumb .= $separator . $parent;
        }
        $breadcrumb .= $separator . '<span>' . get_the_title() . '</span>';
    }

    // Posts
    elseif (is_single() && get_post_type() == 'post') {
        $categories = get_the_category();
        if ($categories) {
            $main_cat = $categories[0];
            $ancestors = get_ancestors($main_cat->term_id, 'category');
            $ancestors = array_reverse($ancestors);
            foreach ($ancestors as $ancestor) {
                $ancestor_term = get_term($ancestor, 'category');
                $breadcrumb .= $separator . '<a href="' . get_category_link($ancestor_term) . '">' . $ancestor_term->name . '</a>';
            }
            $breadcrumb .= $separator . '<a href="' . get_category_link($main_cat) . '">' . $main_cat->name . '</a>';
        }
        $breadcrumb .= $separator . '<span>' . get_the_title() . '</span>';
    }

    elseif (is_category() || is_tag() || is_tax()) {
        $breadcrumb .= $separator . '<span>' . single_term_title('', false) . '</span>';
    }
    elseif (is_search()) {
        $breadcrumb .= $separator . '<span>Search results for "' . get_search_query() . '"</span>';
    }
    elseif (is_404()) {
        $breadcrumb .= $separator . '<span>404 Not Found</span>';
    }

    $breadcrumb .= '</div>';
    return $breadcrumb;
}
add_shortcode('breadcrumb', 'custom_breadcrumb_shortcode');

