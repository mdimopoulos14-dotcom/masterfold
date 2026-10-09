<?php
/**
 * Attribute groups shown in the product page sections.
 *
 * Each group maps to one "Masterfold Attribute Section" widget in the product
 * templates (e.g. "Pick a different color"). Edit these lists to move an
 * attribute to a different section.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mf_product_option_groups() {
	$groups = array(
		'color'              => array(
			'label'      => __( 'Color / material', 'masterfold' ),
			'attributes' => array(
				'pa_wicotex-brillianta', 'pa_wicotex-naturlinen', 'pa_hat', 'pa_hex', 'pa_premium-papers-s1', 'pa_premium-papers-s3',
				'pa_standard-paper', 'pa_cork', 'pa_corium', 'pa_acrylic', 'pa_acrylic-opaque', 'pa_acrylic-translucent', 'pa_card-wood',
				'pa_corvon-carbon-x', 'pa_corvon-iridescents-bengaline', 'pa_corvon-iridescents-weave', 'pa_corvon-metal-mesh',
				'pa_corvon-metal-x-brush', 'pa_corvon-metal-x-diago', 'pa_corvon-metal-x-dimple', 'pa_corvon-metal-x-hammered',
				'pa_corvon-metal-x-weave', 'pa_corvon-rock', 'pa_corvon-rust', 'pa_corvon-senzo', 'pa_corvon-senzo-black-designs',
				'pa_corvon-senzo-metallic', 'pa_corvon-shimmer', 'pa_ecorel-matte-chevo', 'pa_leather', 'pa_nabuka-persano',
				'pa_napura-bamboa', 'pa_napura-canvas', 'pa_napura-kazar', 'pa_napura-khepera', 'pa_napura-madera', 'pa_napura-sangha',
				'pa_napura-sisal', 'pa_natural-surfaces', 'pa_pellaq-croco', 'pa_pellaq-glean', 'pa_pellaq-iguana', 'pa_pellaq-lizard',
				'pa_pellaq-mallory', 'pa_premium-papers', 'pa_pvc-black', 'pa_quinel-andhra', 'pa_quinel-creda',
				'pa_quinel-kidra', 'pa_quinel-nubuk', 'pa_quinel-silk', 'pa_quinel-soft', 'pa_quinel-vision', 'pa_silktouch-nuba',
				'pa_silktouch-yana-thermo', 'pa_skivertex-classica', 'pa_skivertex-galuchat', 'pa_skivertex-matara-crispel',
				'pa_skivertex-ostra', 'pa_skivertex-samala', 'pa_skivertex-ubonga', 'pa_standard-paper-coated',
				'pa_standard-paper-uncoated', 'pa_standard-paper-velvet', 'pa_suedel-luxe', 'pa_sustainable-boards', 'pa_toile-canvas',
				'pa_toile-du-marais', 'pa_toile-ocean', 'pa_wicotex-halblinen', 'pa_wicotex-magic', 'pa_wintan-caiman',
				'pa_wintan-carena', 'pa_wintan-cervo', 'pa_wintan-corral', 'pa_wintan-elda', 'pa_wintan-hydra', 'pa_wintan-hydra-thermo',
				'pa_wintan-lhasa', 'pa_wintan-natural-palma', 'pa_wintan-nubes', 'pa_wintan-oporto', 'pa_wintan-palma',
				'pa_wintan-safia', 'pa_wintan-vintage', 'pa_wood', 'pa_wood-samples-page', 'pa_binding-paper',
				'pa_wood-samples-page-colored',
			),
			'style'      => 'image',
		),
		'additional-filters' => array(
			'label'      => __( 'Additional filters', 'masterfold' ),
			'attributes' => array( 'pa_hand-fan', 'pa_acrylic-ps', 'pa_acrylic-ps2', 'pa_acrylic-ps3' ),
			'style'      => 'image',
		),
		'binding'            => array(
			'label'      => __( 'Binding', 'masterfold' ),
			'attributes' => array( 'pa_binding' ),
			'style'      => 'text',
		),
		'imprint'            => array(
			'label'      => __( 'Imprint', 'masterfold' ),
			'attributes' => array( 'pa_imprint' ),
			'style'      => 'text',
		),
		'size'               => array(
			'label'      => __( 'Size', 'masterfold' ),
			'attributes' => array( 'pa_dif-size' ),
			'style'      => 'text',
		),
		'binding-paper'      => array(
			'label'      => __( 'Binding paper', 'masterfold' ),
			'attributes' => array( 'pa_binding-paper' ),
			'style'      => 'image',
		),
		'all'                => array(
			'label'      => __( 'All attributes', 'masterfold' ),
			'attributes' => array(),
			'style'      => 'image',
		),
	);

	return apply_filters( 'mf_product_option_groups', $groups );
}
