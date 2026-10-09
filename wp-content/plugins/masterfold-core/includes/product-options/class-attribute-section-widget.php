<?php
/**
 * Elementor widget: "Masterfold Attribute Section".
 *
 * Drop it into a product template to show one group of product attributes
 * (color, binding, imprint, ...) as swatches. When the current product has
 * none of the group's attributes, the widget renders nothing and its
 * surrounding section (any container with a "selected-...-column" class)
 * is hidden by CSS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( '\\ElementorPro\\Modules\\Woocommerce\\Widgets\\Product_Add_To_Cart' ) ) {
	abstract class MF_Attribute_Section_Widget_Base extends \ElementorPro\Modules\Woocommerce\Widgets\Product_Add_To_Cart {}
} else {
	abstract class MF_Attribute_Section_Widget_Base extends \Elementor\Widget_Base {}
}

/*
 * Built on Elementor Pro's "Add To Cart" widget so every style control and
 * saved style setting of the old sections keeps working unchanged; only the
 * output differs (no cart form, attributes rendered on the server).
 */
class MF_Attribute_Section_Widget extends MF_Attribute_Section_Widget_Base {

	public function get_name() {
		return 'mf-attribute-section';
	}

	public function get_title() {
		return __( 'Masterfold Attribute Section', 'masterfold' );
	}

	public function get_icon() {
		return 'eicon-product-meta';
	}

	public function get_categories() {
		return array( 'woocommerce-elements-single', 'woocommerce-elements', 'general' );
	}

	public function get_keywords() {
		return array( 'masterfold', 'attribute', 'swatch', 'color', 'binding', 'imprint', 'variation' );
	}

	public function get_style_depends(): array {
		return array_merge( parent::get_style_depends(), array( 'mf-product-options' ) );
	}

	public function get_script_depends(): array {
		return array();
	}

	public function get_html_wrapper_class() {
		// Keep the old widget class so existing CSS keeps matching.
		return parent::get_html_wrapper_class() . ' elementor-widget-woocommerce-product-add-to-cart';
	}

	protected function is_dynamic_content(): bool {
		return true;
	}

	protected function register_controls() {
		parent::register_controls();

		$options = array();
		foreach ( mf_product_option_groups() as $key => $group ) {
			$options[ $key ] = $group['label'];
		}

		$this->start_controls_section( 'mf_attributes', array( 'label' => __( 'Masterfold Attributes', 'masterfold' ) ) );

		$this->add_control(
			'mf_group',
			array(
				'label'   => __( 'Attribute group', 'masterfold' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $options,
				'default' => 'color',
			)
		);

		$this->add_control(
			'mf_display',
			array(
				'label'   => __( 'Show options as', 'masterfold' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => array(
					''      => __( 'Group default', 'masterfold' ),
					'image' => __( 'Image swatches', 'masterfold' ),
					'text'  => __( 'Text buttons', 'masterfold' ),
				),
				'default' => '',
			)
		);

		$this->add_control(
			'mf_note',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( 'Swatch images are read from /wp-content/uploads/OPTION-NAME.webp. If the product has none of these attributes, the section containing this widget (class "selected-…-column") is hidden automatically.', 'masterfold' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$product = mf_current_product();
		if ( ! $product ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Attribute swatches appear here for the previewed product.', 'masterfold' ) . '</p>';
			}
			return;
		}

		$settings = $this->get_settings_for_display();
		$html     = mf_render_product_option_group( $product, $settings['mf_group'], $settings['mf_display'] );

		if ( '' === $html ) {
			echo '<span class="mf-attr-empty" hidden></span>';
			return;
		}

		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in renderer.
	}
}
