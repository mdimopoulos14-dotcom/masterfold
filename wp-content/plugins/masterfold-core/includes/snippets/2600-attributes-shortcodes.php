<?php
/**
 * attributes shortcodes
 *
 * Moved from WPCode snippet #2600 (location: everywhere). Code unchanged.
 */

// Shortcode for displaying the Color attribute with swatches
function custom_color_attribute_shortcode( $atts ) {
    global $product;

    // Check if the 'Color' attribute exists
    $attribute = 'pa_color';  // Replace with your attribute name (slug)
    $taxonomy = 'pa_color'; // Replace with your attribute taxonomy

    // Check if the attribute exists for the product
    if( $product->has_attributes() && $product->get_attribute( $attribute ) ) {
        // Get the terms for the Color attribute
        $terms = get_terms( array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ) );

        // Display swatches for Color
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            $output = '<div class="product-attribute color-attribute">';
            $output .= '<label>' . __( 'Color', 'woocommerce' ) . ':</label>';
            foreach ( $terms as $term ) {
                $image = get_term_meta( $term->term_id, 'image', true ); // Assuming the plugin stores the image in the 'image' meta field
                $output .= '<span class="swatch" style="background-image: url(' . esc_url( $image ) . ');" data-value="' . esc_attr( $term->slug ) . '" title="' . esc_attr( $term->name ) . '"></span>';
            }
            $output .= '</div>';
            return $output;
        }
    }
    return '';  // Return empty if the attribute does not exist
}
add_shortcode( 'color_attribute', 'custom_color_attribute_shortcode' );

// Shortcode for displaying the Size attribute with swatches
function custom_size_attribute_shortcode( $atts ) {
    global $product;

    // Check if the 'Size' attribute exists
    $attribute = 'pa_size';  // Replace with your attribute name (slug)
    $taxonomy = 'pa_size'; // Replace with your attribute taxonomy

    // Check if the attribute exists for the product
    if( $product->has_attributes() && $product->get_attribute( $attribute ) ) {
        // Get the terms for the Size attribute
        $terms = get_terms( array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ) );

        // Display swatches for Size
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            $output = '<div class="product-attribute size-attribute">';
            $output .= '<label>' . __( 'Size', 'woocommerce' ) . ':</label>';
            foreach ( $terms as $term ) {
                $output .= '<span class="swatch" data-value="' . esc_attr( $term->slug ) . '" title="' . esc_attr( $term->name ) . '">' . esc_html( $term->name ) . '</span>';
            }
            $output .= '</div>';
            return $output;
        }
    }
    return '';  // Return empty if the attribute does not exist
}
add_shortcode( 'size_attribute', 'custom_size_attribute_shortcode' );

// Shortcode for displaying the Weight (kg) attribute (if it's an attribute with image swatches)
function custom_weight_attribute_shortcode( $atts ) {
    global $product;

    // Check if the 'Weight' attribute exists
    $attribute = 'pa_weight';  // Replace with your attribute name (slug)
    $taxonomy = 'pa_weight'; // Replace with your attribute taxonomy

    // Check if the attribute exists for the product
    if( $product->has_attributes() && $product->get_attribute( $attribute ) ) {
        // Get the terms for the Weight (kg) attribute
        $terms = get_terms( array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ) );

        // Display swatches for Weight (kg)
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            $output = '<div class="product-attribute weight-attribute">';
            $output .= '<label>' . __( 'Weight (kg)', 'woocommerce' ) . ':</label>';
            foreach ( $terms as $term ) {
                $image = get_term_meta( $term->term_id, 'image', true ); // Assuming the plugin stores the image in the 'image' meta field
                $output .= '<span class="swatch" style="background-image: url(' . esc_url( $image ) . ');" data-value="' . esc_attr( $term->slug ) . '" title="' . esc_attr( $term->name ) . '"></span>';
            }
            $output .= '</div>';
            return $output;
        }
    }
    return '';  // Return empty if the attribute does not exist
}
add_shortcode( 'weight_attribute', 'custom_weight_attribute_shortcode' );

