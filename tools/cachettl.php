// Longer public cache (pages are purged automatically on edits) and serve stale copies while regenerating.
$before = ['ttl_pub'=>get_option('litespeed.conf.cache-ttl_pub'), 'purge_stale'=>get_option('litespeed.conf.purge-stale')];
update_option('litespeed.conf.cache-ttl_pub', 604800);
update_option('litespeed.conf.purge-stale', 1);
try { \LiteSpeed\Conf::cls()->load_options(); } catch (\Throwable $e) {}
return ['before'=>$before, 'after'=>['ttl_pub'=>get_option('litespeed.conf.cache-ttl_pub'), 'purge_stale'=>get_option('litespeed.conf.purge-stale')]];
