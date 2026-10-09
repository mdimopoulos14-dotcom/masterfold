#!/bin/bash
# swap.sh <slug> old|new : old = restore from backup (keeping new aside), new = put new back
SLUG=$1; MODE=$2
cat > /tmp/nm_swap.php <<PHP
\$bk='/var/www/vhosts/masterfold.com/nm-backups/20261009-111056/code.tgz';
\$aside='/var/www/vhosts/masterfold.com/nm-backups/newver'; wp_mkdir_p(\$aside);
\$dst=WP_PLUGIN_DIR.'/$SLUG';
if('$MODE'==='old'){
  \$tmp='/var/www/vhosts/masterfold.com/nm-backups/restore-'.time(); wp_mkdir_p(\$tmp);
  exec('tar xzf '.escapeshellarg(\$bk).' -C '.escapeshellarg(\$tmp).' plugins/$SLUG 2>&1',\$o,\$rc); if(\$rc) return \$o;
  exec('rm -rf '.escapeshellarg("\$aside/$SLUG").'; mv '.escapeshellarg(\$dst).' '.escapeshellarg("\$aside/$SLUG").' && mv '.escapeshellarg("\$tmp/plugins/$SLUG").' '.escapeshellarg(\$dst).' 2>&1',\$o,\$rc);
} else {
  exec('rm -rf '.escapeshellarg(\$dst).' && mv '.escapeshellarg("\$aside/$SLUG").' '.escapeshellarg(\$dst).' 2>&1',\$o,\$rc);
}
do_action('litespeed_purge_all');
\$f=glob(\$dst.'/*.php'); \$v=''; foreach(\$f as \$x){\$d=get_plugin_data(\$x,false,false); if(\$d['Version']){\$v=\$d['Version'];break;}}
return "$SLUG now ".\$v;
PHP
./php.sh /tmp/nm_swap.php | python3 -I -c "import json,sys;print(json.load(sys.stdin)['data']['return_value'])"
