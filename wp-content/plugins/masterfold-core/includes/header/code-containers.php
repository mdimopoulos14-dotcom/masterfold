<?php
/**
 * Hide header containers that only hold code (HTML widgets with <style>/<script>)
 * from the first paint.
 *
 * The header's code container (32268fb) is hidden by a site CSS rule that only
 * matches once Elementor's script has added "e-lazyloaded", so it first takes
 * 100px and then collapses, shifting the whole page up (layout shift on every
 * page, on live too). Hiding it from the start gives the same final layout
 * without the jump. Styles and scripts inside still apply when hidden.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_head',
	function () {
		$ids = apply_filters( 'mf_header_code_containers', array( '32268fb' ) );
		if ( ! $ids ) {
			return;
		}
		$selectors = array();
		foreach ( $ids as $id ) {
			$selectors[] = 'body:not(.elementor-editor-active) .elementor-location-header .elementor-element-' . sanitize_html_class( $id );
		}
		echo '<style id="mf-header-code-containers">' . implode( ',', $selectors ) . '{display:none}</style>' . "\n";
	},
	20
);
