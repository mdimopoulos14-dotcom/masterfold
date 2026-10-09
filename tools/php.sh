#!/bin/bash
python3 -I -c "import json,sys;print(json.dumps({'code':open(sys.argv[1]).read()}))" "$1" > /tmp/nm_in.json
novamira --site staging.masterfold.com run novamira/execute-php --input @/tmp/nm_in.json --yes --json 2>/dev/null
