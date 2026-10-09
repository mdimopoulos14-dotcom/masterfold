<?php
/**
 * RECYCLED LEATHER
 *
 * Moved from WPCode snippet #28752 (location: everywhere). Code unchanged.
 */

/*1st*/
function display_all_product_wintan_cervo() {
    $terms = get_terms([
        'taxonomy' => 'pa_wintan-cervo',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No WINTAN® CERVO found.</p>';
    }

    $group_id = 'lightbox-wintan-cervo';
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
add_shortcode('product_wintan_cervo', 'display_all_product_wintan_cervo');


/*2st*/
function display_all_product_wintan_hydra_thermo() {
    $terms = get_terms([
        'taxonomy' => 'pa_wintan-hydra-thermo',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No WINTAN® HYDRA THERMO found.</p>';
    }

    $group_id = 'lightbox-wintan-hydra-thermo';
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
add_shortcode('product_wintan_hydra_thermo', 'display_all_product_wintan_hydra_thermo');


/*3st*/
function display_all_product_wintan_palma() {
    $terms = get_terms([
        'taxonomy' => 'pa_wintan-palma',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No WINTAN® PALMA found.</p>';
    }

    $group_id = 'lightbox-wintan-palma';
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
add_shortcode('product_wintan_palma', 'display_all_product_wintan_palma');


/*4st*/
function display_all_product_wintan_safia() {
    $terms = get_terms([
        'taxonomy' => 'pa_wintan-safia',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No WINTAN® SAFIA found.</p>';
    }

    $group_id = 'lightbox-wintan-safia';
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
add_shortcode('product_wintan_safia', 'display_all_product_wintan_safia');


/*5st*/
function display_all_product_wintan_natural_palma() {
    $terms = get_terms([
        'taxonomy' => 'pa_wintan-natural-palma',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No WINTAN® NATURAL PALMA found.</p>';
    }

    $group_id = 'lightbox-wintan-natural-palma';
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
add_shortcode('product_wintan_natural_palma', 'display_all_product_wintan_natural_palma');


/*6st*/
function display_all_product_wintan_lhasa() {
    $terms = get_terms([
        'taxonomy' => 'pa_wintan-lhasa',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No WINTAN® LHASA found.</p>';
    }

    $group_id = 'lightbox-wintan-lhasa';
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
add_shortcode('product_wintan_lhasa', 'display_all_product_wintan_lhasa');


/*7st*/
function display_all_product_wintan_oporto() {
    $terms = get_terms([
        'taxonomy' => 'pa_wintan-oporto',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No WINTAN® OPORTO found.</p>';
    }

    $group_id = 'lightbox-masterfold-leather';
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
add_shortcode('product_wintan_oporto', 'display_all_product_wintan_oporto');


/*8st*/
function display_all_product_wintan_caiman() {
    $terms = get_terms([
        'taxonomy' => 'pa_wintan-caiman',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No WINTAN® CAIMAN found.</p>';
    }

    $group_id = 'lightbox-wintan-caiman';
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
add_shortcode('product_wintan_caiman', 'display_all_product_wintan_caiman');


/*9st*/
function display_all_product_wintan_carena() {
    $terms = get_terms([
        'taxonomy' => 'pa_wintan-carena',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No WINTAN® CARENA found.</p>';
    }

    $group_id = 'lightbox-wintan-carena';
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
add_shortcode('product_wintan_carena', 'display_all_product_wintan_carena');


/*10st*/
function display_all_product_wintan_corral() {
    $terms = get_terms([
        'taxonomy' => 'pa_wintan-corral',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No WINTAN® CORRAL found.</p>';
    }

    $group_id = 'lightbox-wintan-corral';
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
add_shortcode('product_wintan_corral', 'display_all_product_wintan_corral');


/*11st*/
function display_all_product_wintan_elda() {
    $terms = get_terms([
        'taxonomy' => 'pa_wintan-elda',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No WINTAN® ELDA found.</p>';
    }

    $group_id = 'lightbox-wintan-elda';
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
add_shortcode('product_wintan_elda', 'display_all_product_wintan_elda');


/*12st*/
function display_all_product_wintan_hydra() {
    $terms = get_terms([
        'taxonomy' => 'pa_wintan-hydra',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No WINTAN® HYDRA found.</p>';
    }

    $group_id = 'lightbox-wintan-hydra';
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
add_shortcode('product_wintan_hydra', 'display_all_product_wintan_hydra');


/*13st*/
function display_all_product_wintan_nubes() {
    $terms = get_terms([
        'taxonomy' => 'pa_wintan-nubes',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No WINTAN® NUBES found.</p>';
    }

    $group_id = 'lightbox-wintan-nubes';
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
add_shortcode('product_wintan_nubes', 'display_all_product_wintan_nubes');


/*14st*/
function display_all_product_wintan_vintage() {
    $terms = get_terms([
        'taxonomy' => 'pa_wintan-vintage',
        'hide_empty' => false,
    ]);

    if (empty($terms) || is_wp_error($terms)) {
        return '<p>No WINTAN® VINTAGE found.</p>';
    }

    $group_id = 'lightbox-wintan-vintage';
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
add_shortcode('product_wintan_vintage', 'display_all_product_wintan_vintage');



