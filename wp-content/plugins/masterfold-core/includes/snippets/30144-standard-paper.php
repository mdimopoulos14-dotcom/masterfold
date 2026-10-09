<?php
/**
 * STANDARD PAPER
 *
 * Moved from WPCode snippet #30144 (location: everywhere). Code unchanged.
 */

function display_all_product_standard_paper_velvet() {
    $terms = get_terms([
        'taxonomy' => 'pa_standard-paper-velvet',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No Standard Paper found.</p>';
    }

    $group_id = 'lightbox-standard-paper-velvet';
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
add_shortcode('product_standard_paper_velvet', 'display_all_product_standard_paper_velvet');



function display_all_product_standard_paper_uncoated() {
    $terms = get_terms([
        'taxonomy' => 'pa_standard-paper-uncoated',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No Standard Paper found.</p>';
    }

    $group_id = 'lightbox-standard-paper-uncoated';
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
add_shortcode('product_standard_paper_uncoated', 'display_all_product_standard_paper_uncoated');




function display_all_product_standard_paper_coated() {
    $terms = get_terms([
        'taxonomy' => 'pa_standard-paper-coated',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No Standard Paper found.</p>';
    }

    $group_id = 'lightbox-standard-paper-coated';
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
add_shortcode('product_standard_paper_coated', 'display_all_product_standard_paper_coated');






function display_all_product_standard_paper() {
    $terms = get_terms([
        'taxonomy' => 'pa_standard-paper',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No Standard Paper found.</p>';
    }

    $group_id = 'lightbox-standard-paper';
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
add_shortcode('product_standard_paper', 'display_all_product_standard_paper');


