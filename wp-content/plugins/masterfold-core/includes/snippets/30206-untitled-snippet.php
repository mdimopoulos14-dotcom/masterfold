<?php
/**
 * Untitled Snippet
 *
 * Moved from WPCode snippet #30206 (location: everywhere). Code unchanged.
 */

function display_all_product_acrylic_translucent() {
    $terms = get_terms([
        'taxonomy' => 'pa_acrylic-translucent',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No ACRYLIC found.</p>';
    }

    $group_id = 'lightbox-acrylic-translucent';
    $output  = '<span class="elementor-widget-container"><span class="elementor-shortcode"><span class="wood-images-container" style="display: flex; flex-wrap: wrap; gap: 0px;">';

    foreach ($terms as $term) {
        $term_name = $term->name;
        $slug = strtolower($term->slug);

        // Format term name for image filename
        $parts = explode(' ', $term_name);
        $formatted_parts = [];
        foreach ($parts as $part) {
            $formatted_parts[] = preg_match('/\d/', $part) ? $part : strtoupper($part);
        }

        $image_name = implode('-', $formatted_parts) . '.webp';
        $image_url = '/wp-content/uploads/' . $image_name;

        $output .= '<div class="custom-lightbox-attribute-wrapper0" data-group="' . esc_attr($group_id) . '">';
        $output .= '<div class="custom-lightbox-attribute0" data-image="' . esc_url($image_url) . '" data-caption="' . esc_attr($term_name) . '" data-group="' . esc_attr($group_id) . '">';
      /*  $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($term_name) . '" class="wood-images-custom-thumb" style="height:66px; width:66px; margin:1px; cursor:pointer;">';*/
		
		$output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($term_name) . '" class="wood-images-custom-thumb swatch-trigger" data-full="' . esc_url($image_url) . '" style="height:66px; width:66px; margin:1px; cursor:pointer;">';
        $output .= '</div></div>';
    }

    $output .= '</span></span></span>';

    return $output;
}
add_shortcode('product_acrylic_translucent', 'display_all_product_acrylic_translucent');

function display_all_product_acrylic_opaque() {
    $terms = get_terms([
        'taxonomy' => 'pa_acrylic-opaque',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No ACRYLIC found.</p>';
    }

    $group_id = 'lightbox-acrylic-opaque';
    $output  = '<span class="elementor-widget-container"><span class="elementor-shortcode"><span class="wood-images-container" style="display: flex; flex-wrap: wrap; gap: 0px;">';

    foreach ($terms as $term) {
        $term_name = $term->name;
        $slug = strtolower($term->slug);

        // Format term name for image filename
        $parts = explode(' ', $term_name);
        $formatted_parts = [];
        foreach ($parts as $part) {
            $formatted_parts[] = preg_match('/\d/', $part) ? $part : strtoupper($part);
        }

        $image_name = implode('-', $formatted_parts) . '.webp';
        $image_url = '/wp-content/uploads/' . $image_name;

        $output .= '<div class="custom-lightbox-attribute-wrapper0" data-group="' . esc_attr($group_id) . '">';
        $output .= '<div class="custom-lightbox-attribute0" data-image="' . esc_url($image_url) . '" data-caption="' . esc_attr($term_name) . '" data-group="' . esc_attr($group_id) . '">';
      /*  $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($term_name) . '" class="wood-images-custom-thumb" style="height:66px; width:66px; margin:1px; cursor:pointer;">';*/
		
		$output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($term_name) . '" class="wood-images-custom-thumb swatch-trigger" data-full="' . esc_url($image_url) . '" style="height:66px; width:66px; margin:1px; cursor:pointer;">';
        $output .= '</div></div>';
    }

    $output .= '</span></span></span>';

    return $output;
}
add_shortcode('product_acrylic_opaque', 'display_all_product_acrylic_opaque');
















function display_all_product_acrylic_ll() {
    $terms = get_terms([
        'taxonomy' => 'pa_acrylic-ll',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No ACRYLIC found.</p>';
    }

    $group_id = 'lightbox-acrylic-ll';
    $output  = '<span class="elementor-widget-container"><span class="elementor-shortcode"><span class="wood-images-container" style="display: flex; flex-wrap: wrap; gap: 0px;">';

    foreach ($terms as $term) {
        $term_name = $term->name;
        $slug = strtolower($term->slug);

        // Format term name for image filename
        $parts = explode(' ', $term_name);
        $formatted_parts = [];
        foreach ($parts as $part) {
            $formatted_parts[] = preg_match('/\d/', $part) ? $part : strtoupper($part);
        }

        $image_name = implode('-', $formatted_parts) . '.webp';
        $image_url = '/wp-content/uploads/' . $image_name;

        $output .= '<div class="custom-lightbox-attribute-wrapper0" data-group="' . esc_attr($group_id) . '">';
        $output .= '<div class="custom-lightbox-attribute0" data-image="' . esc_url($image_url) . '" data-caption="' . esc_attr($term_name) . '" data-group="' . esc_attr($group_id) . '">';
      /*  $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($term_name) . '" class="wood-images-custom-thumb" style="height:66px; width:66px; margin:1px; cursor:pointer;">';*/
		
		$output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($term_name) . '" class="wood-images-custom-thumb swatch-trigger" data-full="' . esc_url($image_url) . '" style="height:66px; width:66px; margin:1px; cursor:pointer;">';
        $output .= '</div></div>';
    }

    $output .= '</span></span></span>';

    return $output;
}
add_shortcode('product_acrylic_ll', 'display_all_product_acrylic_ll');




function display_all_product_acrylic_ps() {
    $terms = get_terms([
        'taxonomy' => 'pa_acrylic-ps',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No ACRYLIC found.</p>';
    }

    $group_id = 'lightbox-acrylic-ps';
    $output  = '<span class="elementor-widget-container"><span class="elementor-shortcode"><span class="wood-images-container" style="display: flex; flex-wrap: wrap; gap: 0px;">';

    foreach ($terms as $term) {
        $term_name = $term->name;
        $slug = strtolower($term->slug);

        // Format term name for image filename
        $parts = explode(' ', $term_name);
        $formatted_parts = [];
        foreach ($parts as $part) {
            $formatted_parts[] = preg_match('/\d/', $part) ? $part : strtoupper($part);
        }

        $image_name = implode('-', $formatted_parts) . '.webp';
        $image_url = '/wp-content/uploads/' . $image_name;

        $output .= '<div class="custom-lightbox-attribute-wrapper0" data-group="' . esc_attr($group_id) . '">';
        $output .= '<div class="custom-lightbox-attribute0" data-image="' . esc_url($image_url) . '" data-caption="' . esc_attr($term_name) . '" data-group="' . esc_attr($group_id) . '">';
      /*  $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($term_name) . '" class="wood-images-custom-thumb" style="height:66px; width:66px; margin:1px; cursor:pointer;">';*/
		
		$output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($term_name) . '" class="wood-images-custom-thumb swatch-trigger" data-full="' . esc_url($image_url) . '" style="height:66px; width:66px; margin:1px; cursor:pointer;">';
        $output .= '</div></div>';
    }

    $output .= '</span></span></span>';

    return $output;
}
add_shortcode('product_acrylic_ps', 'display_all_product_acrylic_ps');




function display_all_product_acrylic_ps2() {
    $terms = get_terms([
        'taxonomy' => 'pa_acrylic-ps2',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No ACRYLIC found.</p>';
    }

    $group_id = 'lightbox-acrylic-ps2';
    $output  = '<span class="elementor-widget-container"><span class="elementor-shortcode"><span class="wood-images-container" style="display: flex; flex-wrap: wrap; gap: 0px;">';

    foreach ($terms as $term) {
        $term_name = $term->name;
        $slug = strtolower($term->slug);

        // Format term name for image filename
        $parts = explode(' ', $term_name);
        $formatted_parts = [];
        foreach ($parts as $part) {
            $formatted_parts[] = preg_match('/\d/', $part) ? $part : strtoupper($part);
        }

        $image_name = implode('-', $formatted_parts) . '.webp';
        $image_url = '/wp-content/uploads/' . $image_name;

        $output .= '<div class="custom-lightbox-attribute-wrapper0" data-group="' . esc_attr($group_id) . '">';
        $output .= '<div class="custom-lightbox-attribute0" data-image="' . esc_url($image_url) . '" data-caption="' . esc_attr($term_name) . '" data-group="' . esc_attr($group_id) . '">';
      /*  $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($term_name) . '" class="wood-images-custom-thumb" style="height:66px; width:66px; margin:1px; cursor:pointer;">';*/
		
		$output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($term_name) . '" class="wood-images-custom-thumb swatch-trigger" data-full="' . esc_url($image_url) . '" style="height:66px; width:66px; margin:1px; cursor:pointer;">';
        $output .= '</div></div>';
    }

    $output .= '</span></span></span>';

    return $output;
}
add_shortcode('product_acrylic_ps2', 'display_all_product_acrylic_ps2');




function display_all_product_acrylic_ps3() {
    $terms = get_terms([
        'taxonomy' => 'pa_acrylic-ps3',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No ACRYLIC found.</p>';
    }

    $group_id = 'lightbox-acrylic-ps3';
    $output  = '<span class="elementor-widget-container"><span class="elementor-shortcode"><span class="wood-images-container" style="display: flex; flex-wrap: wrap; gap: 0px;">';

    foreach ($terms as $term) {
        $term_name = $term->name;
        $slug = strtolower($term->slug);

        // Format term name for image filename
        $parts = explode(' ', $term_name);
        $formatted_parts = [];
        foreach ($parts as $part) {
            $formatted_parts[] = preg_match('/\d/', $part) ? $part : strtoupper($part);
        }

        $image_name = implode('-', $formatted_parts) . '.webp';
        $image_url = '/wp-content/uploads/' . $image_name;

        $output .= '<div class="custom-lightbox-attribute-wrapper0" data-group="' . esc_attr($group_id) . '">';
        $output .= '<div class="custom-lightbox-attribute0" data-image="' . esc_url($image_url) . '" data-caption="' . esc_attr($term_name) . '" data-group="' . esc_attr($group_id) . '">';
      /*  $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($term_name) . '" class="wood-images-custom-thumb" style="height:66px; width:66px; margin:1px; cursor:pointer;">';*/
		
		$output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($term_name) . '" class="wood-images-custom-thumb swatch-trigger" data-full="' . esc_url($image_url) . '" style="height:66px; width:66px; margin:1px; cursor:pointer;">';
        $output .= '</div></div>';
    }

    $output .= '</span></span></span>';

    return $output;
}
add_shortcode('product_acrylic_ps3', 'display_all_product_acrylic_ps3');
