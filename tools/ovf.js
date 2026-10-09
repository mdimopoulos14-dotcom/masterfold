const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  const p = await (await b.newContext({viewport:{width:390,height:844}, isMobile:true, hasTouch:true, deviceScaleFactor:1})).newPage();
  await p.goto(process.argv[2], {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
  const r = await p.evaluate(() => {
    const W = document.documentElement.clientWidth;
    const wide = [...document.querySelectorAll('body *')].filter(e => { const rc = e.getBoundingClientRect(); return rc.right > W + 2 && rc.width > 0 && getComputedStyle(e).visibility !== 'hidden'; })
      .slice(0, 12).map(e => (e.className && typeof e.className === 'string' ? e.className.slice(0,60) : e.tagName) + ' right=' + Math.round(e.getBoundingClientRect().right) + ' pos=' + getComputedStyle(e).position);
    return { scrollWidth: document.documentElement.scrollWidth, clientWidth: W, bodyOverflowX: getComputedStyle(document.body).overflowX, htmlOverflowX: getComputedStyle(document.documentElement).overflowX, scrollX: window.scrollX, wide };
  });
  console.log(JSON.stringify(r, null, 1));
  await p.screenshot({ path: process.argv[3] });
  await b.close();
})();
