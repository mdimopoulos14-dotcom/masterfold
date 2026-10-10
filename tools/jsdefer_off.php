// Roll back: original LiteSpeed JS settings (defer off, original exclusions).
update_option('litespeed.conf.optm-js_defer', 0);
update_option('litespeed.conf.optm-js_defer_exc', wp_json_encode(["jquery.js","jquery.min.js","gtm.js","analytics.js"]));
do_action('litespeed_purge_all');
return [get_option('litespeed.conf.optm-js_defer'), get_option('litespeed.conf.optm-js_defer_exc')];
