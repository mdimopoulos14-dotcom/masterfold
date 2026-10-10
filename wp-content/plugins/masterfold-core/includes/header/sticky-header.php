<?php
/**
 * CSS-only sticky header.
 *
 * Give a top-level header container the CSS class "mf-sticky-top" (Advanced >
 * CSS Classes in Elementor) instead of Elementor's Motion Effects > Sticky.
 * Elementor's sticky script clones the whole container as a hidden spacer
 * (doubling the desktop mega menu's DOM) and repositions it on every scroll;
 * position: sticky gives the same result with no script and no clone.
 *
 * The class works on desktop only; tablet and mobile keep their own settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_head',
	function () {
		if ( is_admin() ) {
			return;
		}
		// The header location wrapper is display:contents so the sticky container
		// is constrained by <body>, not by the (short) header wrapper. Its ::before
		// re-applies the wrapper's own top margin, which display:contents drops
		// (the tiny padding stops that margin merging with the container's), and the
		// container takes over the wrapper's z-index so it stays above other sticky bars.
		echo '<style id="mf-sticky-header">@media (min-width:1025px){body:not(.elementor-editor-active) .elementor-location-header:has(>.mf-sticky-top){display:contents}body:not(.elementor-editor-active) .elementor-location-header:has(>.mf-sticky-top)::before{content:"";display:block;margin-top:inherit;padding-top:.02px}body:not(.elementor-editor-active) .mf-sticky-top{position:sticky;top:var(--wp-admin--admin-bar--height,0px);z-index:inherit}}</style>' . "\n";
	},
	20
);
