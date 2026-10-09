#!/bin/bash
python3 -I -c "import json,sys;print(json.dumps({'args':sys.argv[1:]}))" "$@" > /tmp/nm_cli.json
novamira --site staging.masterfold.com run novamira/run-wp-cli --input @/tmp/nm_cli.json --yes --json 2>/dev/null
