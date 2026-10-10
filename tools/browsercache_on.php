// Enable LiteSpeed browser cache (1 year for static files; URLs are versioned).
update_option('litespeed.conf.cache-browser', 1);
try { \LiteSpeed\Conf::cls()->load_options(); } catch (\Throwable $e) {}
// Rewrite the LiteSpeed .htaccess block so the Expires rules are added.
try { \LiteSpeed\Htaccess::cls()->update(\LiteSpeed\Conf::cls()->get_options()); } catch (\Throwable $e) { $err = $e->getMessage(); }
do_action('litespeed_purge_all');
$ht = file_get_contents(ABSPATH.'.htaccess');
return ['opt'=>get_option('litespeed.conf.cache-browser'), 'err'=>$err ?? null, 'has_expires'=>strpos($ht,'ExpiresActive')!==false, 'block'=> preg_match('/### marker BROWSER CACHE start ###(.*?)### marker BROWSER CACHE end ###/s',$ht,$m) ? trim($m[1]) : null];
