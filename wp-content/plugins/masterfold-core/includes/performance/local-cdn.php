<?php
/**
 * Serve CDN-hosted library files (cdn.jsdelivr.net) from this site.
 *
 * Plugins load Swiper and Fancybox from jsDelivr, which costs every visitor a
 * connection to another host before the page can render. The exact same files
 * are downloaded once into uploads/mf-cache/vendor and the page links to the
 * local copies. If a download fails, the original CDN URL is kept.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mf_local_cdn_url( $url ) {
	$clean = html_entity_decode( $url );
	$path  = wp_parse_url( $clean, PHP_URL_PATH );
	$ext   = strtolower( pathinfo( (string) $path, PATHINFO_EXTENSION ) );
	if ( ! in_array( $ext, array( 'js', 'css' ), true ) ) {
		return '';
	}
	$uploads = wp_upload_dir();
	$name    = substr( md5( $clean ), 0, 16 ) . '.' . $ext;
	$file    = $uploads['basedir'] . '/mf-cache/vendor/' . $name;
	$local   = set_url_scheme( $uploads['baseurl'] . '/mf-cache/vendor/' . $name );
	if ( file_exists( $file ) ) {
		return $local;
	}
	// Don't retry a failed download on every request.
	if ( get_transient( 'mf_cdn_fail_' . md5( $clean ) ) ) {
		return '';
	}
	$response = wp_remote_get( ( 0 === strpos( $clean, '//' ) ? 'https:' : '' ) . $clean, array( 'timeout' => 10 ) );
	$body     = is_wp_error( $response ) ? '' : wp_remote_retrieve_body( $response );
	if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) || '' === $body ) {
		set_transient( 'mf_cdn_fail_' . md5( $clean ), 1, HOUR_IN_SECONDS );
		return '';
	}
	wp_mkdir_p( dirname( $file ) );
	if ( false === file_put_contents( $file, $body ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		return '';
	}
	return $local;
}

function mf_localize_cdn_assets( $html ) {
	if ( false === strpos( $html, 'cdn.jsdelivr.net' ) ) {
		return $html;
	}
	return preg_replace_callback(
		'/(<(?:script|link)\b[^>]*\b(?:src|href)=[\'"])((?:https?:)?\/\/cdn\.jsdelivr\.net\/[^\'"]+)/i',
		function ( $m ) {
			$local = mf_local_cdn_url( $m[2] );
			return $m[1] . ( $local ? esc_url( $local ) : $m[2] );
		},
		$html
	);
}
