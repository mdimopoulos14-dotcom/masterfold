#!/bin/bash
echo "return @file_get_contents('/var/www/vhosts/masterfold.com/nm-backups/logs/$1.log');" > /tmp/nm_log.php
./php.sh /tmp/nm_log.php | python3 -I -c "import json,sys;print(json.load(sys.stdin)['data']['return_value'])"
