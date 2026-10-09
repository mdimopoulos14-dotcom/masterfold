#!/bin/bash
# Upload theme/masterfold to wp-content/themes/masterfold (does not activate it).
set -e
cd ~/nm/theme && tar czf /tmp/mft.tgz masterfold && B=$(base64 -w0 /tmp/mft.tgz)
cat > /tmp/nm_tdeploy.php <<PHP
\$base='/var/www/vhosts/masterfold.com/nm-backups'; \$stage="\$base/mft-".time(); wp_mkdir_p(\$stage);
file_put_contents("\$stage/t.tgz", base64_decode('$B'));
exec('tar xzf '.escapeshellarg("\$stage/t.tgz").' -C '.escapeshellarg(\$stage).' 2>&1', \$o, \$rc); if (\$rc) return ['untar'=>\$o];
\$dst=get_theme_root().'/masterfold'; wp_mkdir_p(\$dst);
exec('rsync -a --delete '.escapeshellarg("\$stage/masterfold/").' '.escapeshellarg("\$dst/").' 2>&1', \$o2, \$rc2); if (\$rc2) return ['copy'=>\$o2];
if (function_exists('opcache_reset')) @opcache_reset();
\$t=wp_get_theme('masterfold'); return ['ok'=>\$t->exists(), 'errors'=>\$t->errors() ? \$t->errors()->get_error_messages() : null, 'active'=>get_option('stylesheet')];
PHP
cd ~/nm && ./php.sh /tmp/nm_tdeploy.php | python3 -I -c "import json,sys;d=json.load(sys.stdin);print(d.get('data',{}).get('return_value',d))"
