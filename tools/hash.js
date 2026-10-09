const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  for (const u of process.argv.slice(2)) {
    const p = await (await b.newContext({viewport:{width:390,height:844}, isMobile:true, hasTouch:true})).newPage();
    await p.goto(u, {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
    const r = await p.evaluate(() => {
      const id = location.hash.slice(1); const els = id ? [...document.querySelectorAll('[id="' + id + '"]')] : [];
      return { url: location.pathname + location.hash, matches: els.map(e => e.tagName + '.' + e.className.toString().slice(0,60) + ' left=' + Math.round(e.getBoundingClientRect().left)), body: document.body.scrollLeft };
    });
    console.log(u.includes('staging') ? 'STAGING' : 'LIVE   ', JSON.stringify(r));
  }
  await b.close();
})();
