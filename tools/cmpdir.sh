#!/bin/bash
# cmpdir.sh BEFORE AFTER -> one line per screenshot, sorted by diff %
for f in "$2"/*.png; do b="$1/$(basename $f)"; [ -f "$b" ] && python3 -I cmp.py "$b" "$f"; done | sort -t'(' -k2 -rn
