<?php
/**
 * Strip a leading "." from image alt text when it is saved.
 *
 * Replaces WPCode snippet #41354, which re-scanned every image in the media
 * library on every request (and exhausted PHP memory). Existing alt texts were
 * already cleaned by it; this keeps new or edited ones clean.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mf_clean_alt_text( $check, $object_id, $meta_key, $meta_value ) {
	if ( '_wp_attachment_image_alt' !== $meta_key || ! is_string( $meta_value ) ) {
		return $check;
	}
	$trimmed = trim( $meta_value );
	if ( 0 !== strpos( $trimmed, '.' ) ) {
		return $check;
	}
	// Save the cleaned value instead (without re-entering this filter).
	remove_filter( 'update_post_metadata', 'mf_clean_alt_text', 10 );
	remove_filter( 'add_post_metadata', 'mf_clean_alt_text', 10 );
	update_post_meta( $object_id, $meta_key, ltrim( substr( $trimmed, 1 ) ) );
	add_filter( 'update_post_metadata', 'mf_clean_alt_text', 10, 4 );
	add_filter( 'add_post_metadata', 'mf_clean_alt_text', 10, 4 );
	return true;
}
add_filter( 'update_post_metadata', 'mf_clean_alt_text', 10, 4 );
add_filter( 'add_post_metadata', 'mf_clean_alt_text', 10, 4 );
