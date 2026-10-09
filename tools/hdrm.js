const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  for (const host of ['masterfold.com','staging.masterfold.com']) {
    const p = await (await b.newContext({viewport:{width:1440,height:900}})).newPage();
    await p.goto('https://'+host+'/wishlist/', {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
    console.log(host, JSON.stringify(await p.evaluate(() => {
      const h=document.querySelector('.elementor-location-header');
      const kids=[...h.children].map(c=>{const cs=getComputedStyle(c); const r=c.getBoundingClientRect(); return [c.dataset.id||c.className.slice(0,30), Math.round(r.top), Math.round(r.height), cs.marginTop, cs.marginBottom, cs.position, cs.display];});
      const nx=h.nextElementSibling; const nr=nx.getBoundingClientRect();
      return {locm:[getComputedStyle(h).marginTop,getComputedStyle(h).marginBottom,getComputedStyle(h).paddingTop,Math.round(h.getBoundingClientRect().top)], loc:[getComputedStyle(h).display, Math.round(h.getBoundingClientRect().height)], kids, next:[nx.tagName+'.'+nx.className.slice(0,40), Math.round(nr.top), getComputedStyle(nx).marginTop]};
    })));
  }
  await b.close();
})();
