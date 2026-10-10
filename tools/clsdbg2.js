const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  const ctx = await b.newContext({viewport:{width:412,height:823}, isMobile:true, hasTouch:true});
  const p = await ctx.newPage();
  const snap = () => p.evaluate(() => { const h=document.querySelector('.elementor-location-header'); if(!h) return null; return [...h.children].map(c=>{const cs=getComputedStyle(c); return [c.dataset.id||c.className.slice(0,30), Math.round(c.getBoundingClientRect().height), cs.display, cs.position, c.className.match(/elementor-sticky\S*|hide-mb\S*|animate\S*/g)];}).concat([['loc', Math.round(h.getBoundingClientRect().height)]]); });
  await p.goto(process.argv[2], {waitUntil:'commit', timeout:90000});
  await p.waitForSelector('.elementor-location-single', {timeout:60000}).catch(()=>{});
  console.log('early', JSON.stringify(await snap()));
  await p.waitForLoadState('domcontentloaded'); console.log('dcl  ', JSON.stringify(await snap()));
  await p.waitForLoadState('networkidle').catch(()=>{}); await p.waitForTimeout(2500); console.log('late ', JSON.stringify(await snap()));
  await b.close();
})();
