const { chromium } = require('playwright');
const url = process.argv[2];
(async () => {
  const b = await chromium.launch();
  const results = [];
  for (let v = 0; v < 2; v++) {
    const ctx = await b.newContext();
    const p = await ctx.newPage();
    const errs = [];
    p.on('pageerror', e => errs.push(e.message.slice(0,120)));
    let cacheHdr = '';
    p.on('response', r => { if (r.url().split('?')[0] === url.split('?')[0] && r.request().resourceType()==='document') cacheHdr = r.headers()['x-litespeed-cache'] || 'none'; });
    // Stand-in for fbevents.js that records every fbq() call.
    await p.route(/connect\.facebook\.net\/.*fbevents\.js/, r => r.fulfill({ contentType: 'application/javascript', body:
      'window.__fbq=[];(function(){var q=(window.fbq&&window.fbq.queue)||[];var f=function(){window.__fbq.push([].slice.call(arguments));};f.queue=[];f.loaded=true;f.version="2.0";f.callMethod=f;window.fbq=window._fbq=f;q.forEach(function(a){f.apply(null,a);});})();' }));
    await p.goto(url, { waitUntil: 'networkidle', timeout: 90000 }).catch(()=>{});
    await p.waitForTimeout(1500);
    const calls = await p.evaluate(() => (window.__fbq || []).filter(c=>c[0]==="track").map(c => c[1]+" eventID="+((c[3]&&c[3].eventID)||"-")));
    const cookies = (await ctx.cookies()).filter(c => /^_fb[pc]$/.test(c.name)).map(c => c.name);
    results.push({ visitor: v+1, cache: cacheHdr, calls, cookies, errs, hasHelper: await p.evaluate(()=>typeof window.mfEventId) });
    await ctx.close();
  }
  console.log(JSON.stringify(results, null, 1));
  await b.close();
})();
