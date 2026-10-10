// Enable LiteSpeed "Load JS Deferred" keeping tracking/consent scripts and their helpers synchronous.
$exc = ["jquery.js","jquery.min.js","gtm.js","analytics.js","cookie-law-info","facebook","fbevents","fbq(","fbq.","mfEventId","_nslDOMReady","gtag","dataLayer","googletagmanager"];
update_option('litespeed.conf.optm-js_defer_exc', wp_json_encode($exc));
update_option('litespeed.conf.optm-js_defer', 1);
if (class_exists('\LiteSpeed\Conf')) { try { \LiteSpeed\Conf::cls()->load_options(); } catch (\Throwable $e) {} }
do_action('litespeed_purge_all');
return [get_option('litespeed.conf.optm-js_defer'), get_option('litespeed.conf.optm-js_defer_exc')];
