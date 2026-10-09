#!/bin/bash
# usage: wpbg.sh <logname> <wp args...>  -> runs wp-cli in background on server, logs to nm-backups/logs/<logname>.log
LOG="$1"; shift
python3 -I - "$LOG" "$@" > /tmp/nm_bg.php <<'PY'
import sys,json
log=sys.argv[1]; args=sys.argv[2:]
print("""$d='/var/www/vhosts/masterfold.com/nm-backups/logs'; wp_mkdir_p($d);
$args=json_decode(%s,true);
$cmd='/opt/plesk/php/8.3/bin/php -d memory_limit=1024M /usr/local/bin/wp --path='.escapeshellarg(ABSPATH).' '.implode(' ',array_map('escapeshellarg',$args));
exec('nohup sh -c '.escapeshellarg($cmd.'; echo "EXIT=$?"').' > '.escapeshellarg("$d/%s.log").' 2>&1 &');
return $cmd;""" % (json.dumps(json.dumps(args)).replace("\\\\","\\\\\\\\") if False else "'"+json.dumps(args).replace("\\","\\\\").replace("'","\\'")+"'", log))
PY
./php.sh /tmp/nm_bg.php | python3 -I -c "import json,sys;print(json.load(sys.stdin)['data']['return_value'])"
