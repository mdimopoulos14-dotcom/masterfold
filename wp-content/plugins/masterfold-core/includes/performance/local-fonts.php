<?php
/**
 * Serve Google Fonts from this site (same font files, WOFF2), instead of
 * fonts.googleapis.com / fonts.gstatic.com.
 *
 * The first time a Google Fonts stylesheet is seen, it is downloaded in the
 * background together with its font files into uploads/mf-fonts/. Until the
 * local copy exists the page keeps the original Google link, so nothing breaks.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mf_fonts_dir() {
	$u = wp_upload_dir();
	return array(
		'path' => trailingslashit( $u['basedir'] ) . 'mf-fonts/',
		'url'  => trailingslashit( $u['baseurl'] ) . 'mf-fonts/',
	);
}

/**
 * Local stylesheet URL for a Google Fonts stylesheet URL, or '' if not built yet.
 */
function mf_local_font_css( $google_url ) {
	$google_url = html_entity_decode( $google_url );
	$key        = md5( $google_url );
	$dir        = mf_fonts_dir();

	if ( file_exists( $dir['path'] . $key . '.css' ) ) {
		return $dir['url'] . $key . '.css?ver=' . filemtime( $dir['path'] . $key . '.css' );
	}

	// Build it in the background (once).
	$queue = get_option( 'mf_fonts_queue', array() );
	if ( ! isset( $queue[ $key ] ) ) {
		$queue[ $key ] = $google_url;
		update_option( 'mf_fonts_queue', $queue, false );
		if ( ! wp_next_scheduled( 'mf_build_local_fonts' ) ) {
			wp_schedule_single_event( time() + 5, 'mf_build_local_fonts' );
		}
	}
	return '';
}

/**
 * Download one Google Fonts stylesheet and its fonts. Returns true on success.
 */
function mf_build_local_font_css( $google_url ) {
	$dir = mf_fonts_dir();
	wp_mkdir_p( $dir['path'] );

	// A current browser user agent makes Google answer with WOFF2 files.
	$ua  = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36';
	$url = 0 === strpos( $google_url, '//' ) ? 'https:' . $google_url : $google_url;
	$res = wp_remote_get( $url, array( 'timeout' => 20, 'user-agent' => $ua ) );
	if ( is_wp_error( $res ) || 200 !== wp_remote_retrieve_response_code( $res ) ) {
		return false;
	}
	$css = wp_remote_retrieve_body( $res );

	if ( ! preg_match_all( '#url\((https://fonts\.gstatic\.com/[^)]+)\)#', $css, $m ) ) {
		return false;
	}

	foreach ( array_unique( $m[1] ) as $font_url ) {
		$file = md5( $font_url ) . '.' . pathinfo( wp_parse_url( $font_url, PHP_URL_PATH ), PATHINFO_EXTENSION );
		if ( ! file_exists( $dir['path'] . $file ) ) {
			$f = wp_remote_get( $font_url, array( 'timeout' => 20, 'user-agent' => $ua ) );
			if ( is_wp_error( $f ) || 200 !== wp_remote_retrieve_response_code( $f ) ) {
				return false;
			}
			file_put_contents( $dir['path'] . $file, wp_remote_retrieve_body( $f ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		}
		$css = str_replace( $font_url, $dir['url'] . $file, $css );
	}

	file_put_contents( $dir['path'] . md5( $google_url ) . '.css', "/* Local copy of {$google_url} */\n" . $css ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	return true;
}

add_action(
	'mf_build_local_fonts',
	function () {
		$queue = get_option( 'mf_fonts_queue', array() );
		foreach ( $queue as $key => $google_url ) {
			if ( mf_build_local_font_css( $google_url ) ) {
				unset( $queue[ $key ] );
			}
		}
		update_option( 'mf_fonts_queue', $queue, false );
		if ( function_exists( 'do_action' ) ) {
			do_action( 'litespeed_purge_all' );
		}
	}
);

/**
 * Rewrite Google Fonts stylesheet links in the final HTML to the local copies.
 */
function mf_localize_google_fonts( $html ) {
	$html = preg_replace_callback(
		'#(<link\b[^>]*\bhref=)([\'"])((?:https?:)?//fonts\.googleapis\.com/css2?\?[^\'"]+)\2#i',
		function ( $m ) {
			$local = mf_local_font_css( $m[3] );
			return $local ? $m[1] . $m[2] . esc_url( $local ) . $m[2] : $m[0];
		},
		$html
	);

	// Connection hints to Google are only useful while a Google font is still linked.
	if ( false === strpos( $html, 'fonts.googleapis.com/css' ) ) {
		$html = preg_replace( '#<link\b[^>]*\brel=[\'"](?:preconnect|dns-prefetch)[\'"][^>]*fonts\.(?:googleapis|gstatic)\.com[^>]*>\s*#i', '', $html );
	}

	return $html;
}
