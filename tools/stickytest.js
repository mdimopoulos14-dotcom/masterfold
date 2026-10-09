const { chromium } = require('playwright');
const CSS = process.argv[2];
(async () => {
  const b = await chromium.launch(); const p = await (await b.newContext({viewport:{width:1440,height:900}})).newPage();
  await p.goto('https://staging.masterfold.com/product-category/room/', {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
  await p.evaluate(css => { const s=document.createElement('style'); s.textContent=css; document.head.appendChild(s); }, CSS);
  await p.waitForTimeout(300);
  const m = async () => p.evaluate(() => { const h=document.querySelector('.elementor-location-header'); const s=document.querySelector('.mf-sticky-top'); return [Math.round(h.nextElementSibling.getBoundingClientRect().top+scrollY), Math.round(s.getBoundingClientRect().top), Math.round(h.getBoundingClientRect().height)]; });
  console.log('top', JSON.stringify(await m()));
  for (const y of [500, 1200]) { await p.evaluate(y=>scrollTo(0,y), y); await p.waitForTimeout(300); console.log('scroll', y, JSON.stringify(await m())); }
  // can we click content under the transparent wrapper area?
  await p.evaluate(()=>scrollTo(0,0)); await p.waitForTimeout(300);
  console.log('hit', await p.evaluate(() => { const e=document.elementFromPoint(300,400); return e.tagName+'.'+e.className.slice(0,50); }));
  await b.close();
})();
