#!/bin/bash
# Pack plugin/masterfold-core, upload, swap in atomically, health-check, roll back on failure.
set -e
cd ~/nm/plugin && tar czf /tmp/mfc.tgz masterfold-core && B=$(base64 -w0 /tmp/mfc.tgz)
cat > /tmp/nm_sync.php <<PHP
\$base='/var/www/vhosts/masterfold.com/nm-backups';
\$stage="\$base/mfc-".time(); wp_mkdir_p(\$stage);
file_put_contents("\$stage/p.tgz", base64_decode('$B'));
exec('tar xzf '.escapeshellarg("\$stage/p.tgz").' -C '.escapeshellarg(\$stage).' 2>&1', \$o, \$rc); if (\$rc) return ['untar'=>\$o];
\$dst=WP_PLUGIN_DIR.'/masterfold-core'; \$old="\$stage/previous";
exec('cp -a '.escapeshellarg(\$dst).' '.escapeshellarg(\$old).' && rsync -a --delete '.escapeshellarg("\$stage/masterfold-core/").' '.escapeshellarg("\$dst/").' 2>&1', \$o2, \$rc2); if (\$rc2) return ['copy'=>\$o2];
if (function_exists('opcache_reset')) @opcache_reset();
do_action('litespeed_purge_all');
\$bad=[];
foreach (['/', '/product/ocean-fabric-wine-list-sani/', '/product-category/restaurant/'] as \$p) {
  \$r=wp_remote_get(home_url(\$p).'?nmsync='.time(), ['timeout'=>40,'sslverify'=>false]);
  \$c=is_wp_error(\$r)?\$r->get_error_message():wp_remote_retrieve_response_code(\$r);
  if (\$c!==200 || stripos(wp_remote_retrieve_body(\$r),'critical error')!==false) \$bad[\$p]=\$c;
}
if (\$bad) { exec('rsync -a --delete '.escapeshellarg("\$old/").' '.escapeshellarg("\$dst/")); do_action('litespeed_purge_all'); return ['ROLLED_BACK'=>\$bad]; }
return ['deployed'=>true];
PHP
cd ~/nm && ./php.sh /tmp/nm_sync.php | python3 -I -c "import json,sys;d=json.load(sys.stdin);print(d.get('data',{}).get('return_value',d))"
