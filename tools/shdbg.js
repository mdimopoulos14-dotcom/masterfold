const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  for (const host of ['masterfold.com','staging.masterfold.com']) {
    const p = await (await b.newContext({viewport:{width:1440,height:900}})).newPage();
    await p.goto('https://'+host+'/product/shiny-slim-z-key-holder-sani/', {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
    await p.waitForTimeout(1000);
    const res = await p.evaluate(() => {
      const keep = e => { const cs=getComputedStyle(e); const r=e.getBoundingClientRect(); return cs.boxShadow!=='none' && r.width>500 && r.top<200 && r.bottom>50 && cs.display!=='none' && cs.visibility!=='hidden'; };
      const info = e => { const cs=getComputedStyle(e); const r=e.getBoundingClientRect(); return [e.dataset.id||e.className.slice(0,50), Math.round(r.top), Math.round(r.height), cs.position, cs.zIndex, cs.boxShadow.slice(0,60), e.className.match(/elementor-sticky\S*/g)]; };
      return [...document.querySelectorAll('body *')].filter(keep).map(info);
    });
    console.log(host, JSON.stringify(res, null, 1));
  }
  await b.close();
})();
