const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  for (const block of [false, true]) {
    const p = await (await b.newContext({viewport:{width:390,height:844}, isMobile:true, hasTouch:true})).newPage();
    await p.addInitScript((block) => {
      window.__lcp = [];
      new PerformanceObserver(l => l.getEntries().forEach(e => window.__lcp.push(Math.round(e.startTime) + 'ms ' + (e.element ? (e.element.tagName + '.' + (e.element.className||'').toString().slice(0,40)) : e.url.split('/').pop())))).observe({ type: 'largest-contentful-paint', buffered: true });
      if (block) { window.scrollBy = function () {}; }
    }, block);
    await p.goto(process.argv[2], {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
    await p.waitForTimeout(1500);
    console.log(block ? 'scrollBy disabled:' : 'normal:           ', JSON.stringify(await p.evaluate(() => window.__lcp)));
  }
  await b.close();
})();
