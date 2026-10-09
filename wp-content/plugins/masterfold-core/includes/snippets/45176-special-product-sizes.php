<?php
/**
 * Special Product: SIZES
 *
 * Moved from WPCode snippet #45176 (location: everywhere). Code unchanged.
 */

// 1. Function to output the HTML
function display_custom_buttons_logic() {
    global $product;
    if (!$product) return;
    $id = $product->get_id();

    if (get_field('enable_custom_buttons', $id)) {
        ob_start(); // Start output buffering
        ?>
        <style>
            .custom-buttons-container { max-width: fit-content; margin-bottom:-20px; }

			body .button.custom-btn-active {
    color: white !important;
    background-color: black !important;
    border-color: black ! Important;
    pointer-events: none !important;
}
			
			.button.custom-btn-active {    border-color: black!important;
    color: black!important;}
			
			.custom-buttons-container .button{ 
				margin-top: 5px ! Important;
				font-size: 14px ! Important;
    color: #9E9797!important;
				font-weight: normal !important;
    border: 1px solid #9E9797!important;
    padding: 5px 10px !important;
    border-radius: 3px !important;
	background-color:white!important;}
			
.custom-buttons-container .button:hover {
    border-color: black !important;
    color: black ! Important;
}


</style>
        
        <div class="custom-buttons-container">
            <p style="margin-bottom: 10px; font-weight: bold;">Available Sizes</p>
            <?php
            for ($i = 1; $i <= 3; $i++) {
                $is_active = get_field('active_' . $i, $id);
                $url       = get_field('link_' . $i, $id);
                $text      = get_field('text_' . $i, $id);

                if (!empty($url)) {
                    $class = $is_active ? 'button custom-btn-active' : 'button';
                    echo '<a href="' . esc_url($url) . '" class="' . $class . '" style="margin-right: 10px;">' . esc_html($text ? $text : 'View Specs') . '</a>';
                }
            }
            ?>
        </div>
        <?php
        return ob_get_clean(); // Return the buffered content
    }
}

// 2. Register as a Shortcode (For Elementor)
add_shortcode('display_custom_buttons', 'display_custom_buttons_logic');

// 3. Keep as an Action Hook (For standard WooCommerce)
add_action('woocommerce_single_product_summary', 'echo_custom_buttons', 25);
function echo_custom_buttons() {
    echo display_custom_buttons_logic();
}

// 4. Filter to remove attributes
add_filter('woocommerce_display_product_attributes', 'remove_specific_attributes_from_table', 10, 2);
function remove_specific_attributes_from_table($attributes, $product) {
    $id = $product->get_id();
    $is_main_enabled = get_field('enable_custom_buttons', $id);
    $is_any_active = (get_field('active_1', $id) || get_field('active_2', $id) || get_field('active_3', $id));
    
    if ($is_main_enabled && !$is_any_active) {
        unset($attributes['attribute_pa_code']);
        unset($attributes['attribute_pa_size']);
		unset($attributes['attribute_pa_capacity']);
    }
    return $attributes;
}
