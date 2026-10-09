import re, json, sys, urllib.request, html
paths = ['/', '/product/ocean-fabric-wine-list-sani/', '/products/', '/materials/standard-materials/leather/', '/about-us/', '/contact/', '/blog/', '/print-methods/']
def fetch(url):
    r = urllib.request.urlopen(urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) Chrome/124 Safari/537.36'}), timeout=60)
    return r.read().decode('utf-8', 'replace')
def norm(s):
    return s.replace('https://staging.masterfold.com', 'https://masterfold.com').replace('staging.masterfold.com', 'masterfold.com')
def seo(h):
    head = h[:h.lower().find('</head>')]
    out = {}
    m = re.search(r'<title>(.*?)</title>', head, re.S); out['title'] = html.unescape(m.group(1).strip()) if m else None
    for m in re.finditer(r'<meta\b[^>]*>', head):
        t = m.group(0)
        k = re.search(r'(?:name|property)=["\']([^"\']+)', t); c = re.search(r'content=["\']([^"\']*)', t)
        if k and c and re.match(r'(description|robots|og:|twitter:|article:|google-site-verification|p:domain_verify|facebook-domain-verification|generator)', k.group(1)):
            out.setdefault('meta:' + k.group(1), []).append(html.unescape(c.group(1)))
    for m in re.finditer(r'<link\b[^>]*rel=["\'](canonical|alternate|next|prev|shortlink)["\'][^>]*>', head):
        href = re.search(r'href=["\']([^"\']+)', m.group(0)); out.setdefault('link:' + m.group(1), []).append(href.group(1) if href else '')
    ld = re.findall(r'<script[^>]*type=["\']application/ld\+json["\'][^>]*>(.*?)</script>', h, re.S)
    out['jsonld'] = [json.loads(x) for x in ld if x.strip()]
    out['gtag_ids'] = sorted(set(re.findall(r'(G-[A-Z0-9]{6,}|GT-[A-Z0-9]{5,}|AW-\d+|UA-\d+-\d+|GTM-[A-Z0-9]+)', h)))
    out['fb_pixel_ids'] = sorted(set(re.findall(r"fbq\(\s*['\"]init['\"]\s*,\s*['\"](\d+)", h)) | set(re.findall(r'"pixelId":"(\d+)"', h)))
    out['pinterest'] = sorted(set(re.findall(r'p:domain_verify[^>]*content=["\']([^"\']+)', h)))
    return out
diffs = 0
for p in paths:
    a = seo(fetch('https://masterfold.com' + p)); b = json.loads(norm(json.dumps(seo(fetch('https://staging.masterfold.com' + p)))))
    a = json.loads(norm(json.dumps(a)))
    keys = sorted(set(a) | set(b)); bad = []
    for k in keys:
        if k == 'meta:generator': continue
        if a.get(k) != b.get(k): bad.append(k)
    print(f"{p:42} {'IDENTICAL' if not bad else 'DIFF: ' + ', '.join(bad)}")
    for k in bad[:6]:
        diffs += 1
        print('     live   :', json.dumps(a.get(k), ensure_ascii=False)[:260])
        print('     staging:', json.dumps(b.get(k), ensure_ascii=False)[:260])
print('total differing fields:', diffs)
