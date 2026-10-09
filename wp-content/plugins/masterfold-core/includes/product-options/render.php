<?php
/**
 * Server-side rendering of the product attribute sections.
 *
 * Replaces the old approach (seven copies of the WooCommerce add-to-cart form,
 * trimmed down with JavaScript) with plain markup rendered once per section.
 * The markup keeps the same classes so the existing styles and the attribute
 * lightbox keep working unchanged.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attributes of a product that are used for variations, in product order:
 * [ 'pa_binding' => [ WP_Term, ... ], ... ].
 */
function mf_product_option_terms( $product ) {
	static $cache = array();

	if ( ! $product instanceof WC_Product ) {
		return array();
	}

	$id = $product->get_id();
	if ( isset( $cache[ $id ] ) ) {
		return $cache[ $id ];
	}

	$result = array();
	foreach ( $product->get_attributes() as $attribute ) {
		if ( ! $attribute->get_variation() || ! $attribute->is_taxonomy() ) {
			continue;
		}
		$taxonomy = $attribute->get_name();
		$terms    = wc_get_product_terms( $id, $taxonomy, array( 'fields' => 'all' ) );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$result[ $taxonomy ] = $terms;
		}
	}

	$cache[ $id ] = $result;
	return $result;
}

/**
 * Taxonomies of $product that belong to $group_key, in the product's order.
 */
function mf_product_option_group_taxonomies( $product, $group_key ) {
	$groups = mf_product_option_groups();
	if ( ! isset( $groups[ $group_key ] ) ) {
		return array();
	}

	$all = array_keys( mf_product_option_terms( $product ) );
	if ( 'all' === $group_key ) {
		return $all;
	}

	return array_values( array_intersect( $all, $groups[ $group_key ]['attributes'] ) );
}

/**
 * Swatch image URL for an option: "/wp-content/uploads/OPTION-NAME.webp".
 */
function mf_product_option_image_url( $option_name ) {
	$file = strtoupper( str_replace( ' ', '-', $option_name ) ) . '.webp';
	return apply_filters( 'mf_product_option_image_url', '/wp-content/uploads/' . $file, $option_name );
}

/**
 * Render one attribute section. Returns '' when the product has none of the
 * group's attributes, so the section can hide itself.
 */
function mf_render_product_option_group( $product, $group_key, $style = '' ) {
	$groups = mf_product_option_groups();
	if ( ! isset( $groups[ $group_key ] ) ) {
		return '';
	}

	$style      = $style ? $style : $groups[ $group_key ]['style'];
	$taxonomies = mf_product_option_group_taxonomies( $product, $group_key );
	if ( ! $taxonomies ) {
		return '';
	}

	$terms_by_tax = mf_product_option_terms( $product );

	ob_start();
	?>
	<div class="elementor-add-to-cart elementor-product-variable">
		<form class="cart mf-product-options" data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" onsubmit="return false">
			<table class="variations" cellspacing="0" role="presentation">
				<tbody>
				<?php foreach ( $taxonomies as $taxonomy ) : ?>
					<tr>
						<th class="label"><label><?php echo esc_html( wc_attribute_label( $taxonomy, $product ) ); ?></label></th>
						<td class="value">
							<select hidden disabled aria-hidden="true" tabindex="-1" style="display:none !important" data-attribute_name="<?php echo esc_attr( 'attribute_' . $taxonomy ); ?>"></select><span class="elementor-widget-container"><span class="elementor-shortcode"><span class="wood-images-container" style="display: flex; flex-wrap: wrap; gap: 0px;">
							<?php
							foreach ( $terms_by_tax[ $taxonomy ] as $term ) {
								echo mf_render_product_option_item( $taxonomy, $term, $style ); // phpcs:ignore WordPress.Security.EscapeOutput
							}
							?>
							</span></span></span>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</form>
	</div>
	<?php
	return trim( ob_get_clean() );
}

function mf_render_product_option_item( $taxonomy, $term, $style ) {
	$name  = $term->name;
	$image = mf_product_option_image_url( $name );
	$group = 'lightbox-' . sanitize_title( $taxonomy );

	$html  = '<div class="custom-lightbox-attribute-wrapper" data-group="' . esc_attr( $group ) . '">';
	$html .= '<div class="custom-lightbox-attribute" data-image="' . esc_url( $image ) . '" data-caption="' . esc_attr( $name ) . '" data-group="' . esc_attr( $group ) . '">';

	if ( 'text' === $style ) {
		// Text options (binding / imprint): the image is never shown, so it is not downloaded.
		$html .= '<img alt="' . esc_attr( $name ) . '" class="wood-images-custom-thumb" style="height: 66px; width: 66px; margin: 1px; cursor: pointer; display: none;">';
		$html .= '<a href="#" class="custom-attribute-option" data-value="' . esc_attr( $term->slug ) . '" data-attribute="' . esc_attr( $taxonomy ) . '">' . esc_html( $name ) . '</a>';
	} else {
		$html .= '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $name ) . '" class="wood-images-custom-thumb" style="height:66px; width:66px; margin:1px; cursor:pointer;" loading="lazy" decoding="async" width="66" height="66">';
	}

	$html .= '</div></div>';
	return $html;
}

/**
 * True when the product has at least one variation attribute (used to hide
 * the specifications block on products without attributes).
 */
function mf_product_has_options( $product ) {
	return (bool) mf_product_option_terms( $product );
}
