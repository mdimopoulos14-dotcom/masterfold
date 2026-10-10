#!/bin/bash
# lhrun.sh TAG : 5 mobile Lighthouse runs per page, prints medians.
TAG=$1; cd ~/nm/lh
declare -A U=([home]=https://staging.masterfold.com/ [product]=https://staging.masterfold.com/product/ocean-fabric-wine-list-sani/ [category]=https://staging.masterfold.com/product-category/restaurant/restaurant-menu/ [material]=https://staging.masterfold.com/materials/standard-materials/leather/)
for p in home product category material; do
  curl -s -o /dev/null "${U[$p]}"   # warm the cache
  for i in 1 2 3 4 5; do
    CHROME_PATH=/opt/pw-browsers/chromium npx lighthouse "${U[$p]}" --only-categories=performance --output=json --output-path="${TAG}_${p}_$i.json" --chrome-flags="--headless=new --no-sandbox --ignore-certificate-errors" --quiet >/dev/null 2>&1
  done
done
python3 - "$TAG" <<'PY'
import json,statistics,sys
t=sys.argv[1]
for p in ['home','product','category','material']:
  rs=[]
  for i in range(1,6):
    try: rs.append(json.load(open(f'{t}_{p}_{i}.json')))
    except Exception: pass
  if not rs: print(p,'no runs'); continue
  m=lambda k: statistics.median([r['audits'][k]['numericValue'] for r in rs])
  print(f"{p:9} score {statistics.median([r['categories']['performance']['score']*100 for r in rs]):.0f}  FCP {m('first-contentful-paint')/1000:.1f}s  LCP {m('largest-contentful-paint')/1000:.1f}s  TBT {m('total-blocking-time'):.0f}ms  CLS {m('cumulative-layout-shift'):.3f}  runs={len(rs)}")
PY
