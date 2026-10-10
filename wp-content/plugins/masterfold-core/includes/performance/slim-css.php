<?php
/**
 * Slim copies of large plugin stylesheets for pages that don't use the
 * plugin's widgets.
 *
 * Royal Elementor Addons' frontend.min.css is ~450 KB, but on pages without
 * Royal widgets only its generic rules (and rules for the "wpr-*-no" flag
 * classes it adds to every container) can match anything. The slim copy keeps
 * every rule except those whose selectors all need a Royal widget class, and
 * is used only when the rendered page contains no other "wpr-" class.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Royal Addons classes present on every Elementor container. */
function mf_wpr_flag_classes() {
	return array( 'wpr-particle-no', 'wpr-jarallax-no', 'wpr-parallax-no', 'wpr-sticky-section-no', 'wpr-column-slider-no', 'wpr-equal-height-no' );
}

/**
 * Whether a selector list can match without Royal widget classes: true if any
 * selector in the list references no wpr class other than the flag classes.
 */
function mf_wpr_selector_needed( $selector_list ) {
	$flags = mf_wpr_flag_classes();
	foreach ( explode( ',', $selector_list ) as $selector ) {
		// Attribute selectors mentioning wpr could match flag classes: keep.
		if ( preg_match( '/\[[^\]]*wpr/i', $selector ) ) {
			return true;
		}
		preg_match_all( '/\.(wpr[\w-]*)/i', $selector, $m );
		$others = array_diff( $m[1], $flags );
		if ( empty( $others ) && false === stripos( preg_replace( '/\.wpr[\w-]*/i', '', $selector ), 'wpr' ) ) {
			return true;
		}
	}
	return false;
}

/** Filter a CSS string (handles nested @media/@supports; keeps other at-rules). */
function mf_wpr_slim_filter( $css ) {
	$out = '';
	$len = strlen( $css );
	$i   = 0;
	while ( $i < $len ) {
		$open = strpos( $css, '{', $i );
		if ( false === $open ) {
			break;
		}
		$prelude = trim( substr( $css, $i, $open - $i ) );
		// Find the matching closing brace.
		$depth = 1;
		$k     = $open + 1;
		while ( $depth && $k < $len ) {
			$c = $css[ $k ];
			if ( '{' === $c ) {
				++$depth;
			} elseif ( '}' === $c ) {
				--$depth;
			}
			++$k;
		}
		$body = substr( $css, $open + 1, $k - $open - 2 );
		if ( '@' === substr( $prelude, 0, 1 ) ) {
			if ( preg_match( '/^@(media|supports)/i', $prelude ) ) {
				$inner = mf_wpr_slim_filter( $body );
				if ( '' !== trim( $inner ) ) {
					$out .= $prelude . '{' . $inner . '}';
				}
			} else {
				$out .= $prelude . '{' . $body . '}'; // @font-face, @keyframes, ...
			}
		} elseif ( mf_wpr_selector_needed( $prelude ) ) {
			$out .= $prelude . '{' . $body . '}';
		}
		$i = $k;
	}
	return $out;
}

/**
 * URL of the slim Royal Addons stylesheet, built on first use and rebuilt
 * whenever the source file changes. Returns '' when it can't be built.
 */
function mf_wpr_slim_css_url() {
	$src = WP_PLUGIN_DIR . '/royal-elementor-addons/assets/css/frontend.min.css';
	if ( ! is_readable( $src ) ) {
		return '';
	}
	$uploads = wp_upload_dir();
	$dir     = trailingslashit( $uploads['basedir'] ) . 'mf-cache';
	$name    = 'wpr-slim-' . substr( md5( filemtime( $src ) . '|' . filesize( $src ) . '|' . MF_CORE_VERSION ), 0, 12 ) . '.css';
	$file    = $dir . '/' . $name;
	if ( ! file_exists( $file ) ) {
		$css = file_get_contents( $src ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( ! $css ) {
			return '';
		}
		$css = preg_replace( '#/\*.*?\*/#s', '', $css );
		// Relative url()s point into the plugin's assets folder.
		$base = plugins_url( 'royal-elementor-addons/assets/css/' );
		$css  = preg_replace_callback(
			'/url\(\s*([\'"]?)(?!data:|https?:|\/)([^\'")]+)\1\s*\)/i',
			function ( $m ) use ( $base ) {
				return 'url(' . $m[1] . $base . $m[2] . $m[1] . ')';
			},
			$css
		);
		wp_mkdir_p( $dir );
		if ( false === file_put_contents( $file, mf_wpr_slim_filter( $css ) ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			return '';
		}
	}
	return set_url_scheme( trailingslashit( $uploads['baseurl'] ) . 'mf-cache/' . $name );
}

/**
 * Swap the Royal Addons stylesheet for the slim copy when the page body has
 * no Royal widget classes. $body is the visible markup (scripts/styles removed).
 */
function mf_use_slim_wpr_css( $html, $body ) {
	if ( false === strpos( $html, 'wpr-addons-css-css' ) ) {
		return $html;
	}
	preg_match_all( '/class\s*=\s*["\']([^"\']*)["\']/i', $body, $m );
	$flags = mf_wpr_flag_classes();
	foreach ( $m[1] as $classes ) {
		foreach ( preg_split( '/\s+/', $classes ) as $class ) {
			if ( 0 === stripos( $class, 'wpr' ) && ! in_array( $class, $flags, true ) ) {
				return $html; // A Royal widget is on the page: keep the full stylesheet.
			}
		}
	}
	$url = mf_wpr_slim_css_url();
	if ( ! $url ) {
		return $html;
	}
	return preg_replace_callback(
		'/(<link\b[^>]*\bid=[\'"]wpr-addons-css-css[\'"][^>]*\bhref=[\'"])([^\'"]+)/i',
		function ( $mm ) use ( $url ) {
			return $mm[1] . esc_url( $url );
		},
		$html,
		1
	);
}
