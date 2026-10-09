<?php
/**
 * Lets LiteSpeed cache pages for visitors without changing Meta Pixel /
 * Conversions API tracking.
 *
 * Without this, the Meta plugin sets the _fbp/_fbc cookies on every page
 * response, and LiteSpeed never stores a response that sets cookies — so no
 * page was ever cached. On a cacheable visitor page we therefore:
 *
 * 1. leave the _fbp/_fbc cookies off the page response. The same cookies are
 *    set server-side moments later by the pixel's own Open Bridge request
 *    (/?ob=open-bridge), which every page view makes and which is never cached;
 * 2. give every page view its own event IDs in the browser, so a cached page
 *    never shares an event ID between visitors;
 * 3. skip the one server-side copy of the page's events that would be sent
 *    while the page is generated for the cache. The server copy of each
 *    visitor's events is sent through Open Bridge with the matching event ID,
 *    exactly as Meta deduplicates them today.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * True for a normal front-end page view by a logged-out visitor — the only
 * kind of response LiteSpeed caches.
 */
function mf_is_cacheable_visitor_page() {
	static $result = null;
	if ( null !== $result ) {
		return $result;
	}

	$method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) : 'GET';

	$result = in_array( $method, array( 'GET', 'HEAD' ), true )
		&& ! is_admin()
		&& ! wp_doing_ajax()
		&& ! wp_doing_cron()
		&& ! ( defined( 'REST_REQUEST' ) && REST_REQUEST )
		&& ! ( defined( 'WP_CLI' ) && WP_CLI )
		&& ! isset( $_GET['ob'] ) // phpcs:ignore WordPress.Security.NonceVerification -- Meta Open Bridge endpoint.
		&& ! is_user_logged_in();

	return $result;
}

/*
 * 1. Keep _fbp/_fbc off cacheable page responses (other cookies untouched).
 */
add_action(
	'template_redirect',
	function () {
		if ( ! mf_is_cacheable_visitor_page() || headers_sent() ) {
			return;
		}

		$keep    = array();
		$removed = false;
		foreach ( headers_list() as $header ) {
			if ( 0 !== stripos( $header, 'Set-Cookie:' ) ) {
				continue;
			}
			$cookie = ltrim( substr( $header, strlen( 'Set-Cookie:' ) ) );
			if ( 0 === strpos( $cookie, '_fbp=' ) || 0 === strpos( $cookie, '_fbc=' ) ) {
				$removed = true;
			} else {
				$keep[] = $header;
			}
		}

		if ( $removed ) {
			header_remove( 'Set-Cookie' );
			foreach ( $keep as $header ) {
				header( $header, false );
			}
		}
	},
	0
);

/*
 * 2. Per-page-view event IDs on cacheable pages.
 */
add_action(
	'template_redirect',
	function () {
		if ( ! mf_is_cacheable_visitor_page() ) {
			return;
		}

		ob_start(
			function ( $html ) {
				if ( false === strpos( $html, 'eventID' ) ) {
					return $html;
				}

				$count = 0;
				$html  = preg_replace(
					'/"eventID"\s*:\s*"([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})"/i',
					'"eventID": mfEventId("$1")',
					$html,
					-1,
					$count
				);

				if ( $count ) {
					// Same rendered ID → same per-view ID, so the page's own references stay consistent.
					$helper = '<script>window.mfEventId=window.mfEventId||(function(m){function u(){if(window.crypto&&crypto.randomUUID){return crypto.randomUUID();}return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g,function(c){var r=Math.random()*16|0;return(c==="x"?r:(r&3|8)).toString(16);});}return function(id){return m[id]||(m[id]=u());};})({});</script>';
					$html   = preg_replace( '/<head(\s[^>]*)?>/i', '$0' . $helper, $html, 1 );
				}

				return $html;
			}
		);
	},
	1
);

/*
 * 3. Skip the server-side copy of events while a cacheable page is generated
 *    (the visitor's browser sends it through Open Bridge with the matching ID).
 */
add_filter(
	'before_conversions_api_event_sent',
	function ( $events ) {
		return mf_is_cacheable_visitor_page() ? array() : $events;
	},
	99
);
