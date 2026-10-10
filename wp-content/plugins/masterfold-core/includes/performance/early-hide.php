<?php
/**
 * Apply "hide once lazy-loaded" rules from the first paint.
 *
 * Some page CSS hides an Elementor container with a selector that requires
 * Elementor's "e-lazyloaded" class (added by script after load), e.g. the
 * header's code-only container on product pages. The element first takes its
 * space, then collapses, shifting the page. For each such display:none rule
 * found in the page's inline styles, the same rule without ".e-lazyloaded" is
 * added, so the element starts in its final, hidden state. Pages without such
 * a rule are untouched.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mf_early_hide_lazyloaded( $html ) {
	if ( false === strpos( $html, 'e-lazyloaded' ) ) {
		return $html;
	}
	preg_match_all( '#<style\b[^>]*>(.*?)</style>#is', $html, $styles );
	$extra = array();
	foreach ( $styles[1] as $css ) {
		if ( false === strpos( $css, 'e-lazyloaded' ) ) {
			continue;
		}
		// Top-level rules only: selector{declarations} with display:none.
		preg_match_all( '/([^{}@]+)\{([^{}]*)\}/', $css, $rules, PREG_SET_ORDER );
		foreach ( $rules as $rule ) {
			$selector = trim( $rule[1] );
			if ( ! preg_match( '/^\s*display\s*:\s*none\s*(!important)?\s*;?\s*$/i', $rule[2] ) ) {
				continue;
			}
			if ( false === strpos( $selector, 'elementor-element-' ) || ! preg_match( '/\.e-lazyloaded(?![\w-])/', $selector ) || false !== strpos( $selector, ':not(' ) ) {
				continue;
			}
			$extra[] = preg_replace( '/\.e-lazyloaded(?![\w-])/', '', $selector );
		}
	}
	if ( ! $extra ) {
		return $html;
	}
	$style = '<style id="mf-early-hide">' . implode( ',', array_unique( $extra ) ) . '{display:none}</style>';
	return preg_replace( '#</head>#i', $style . '</head>', $html, 1 );
}
