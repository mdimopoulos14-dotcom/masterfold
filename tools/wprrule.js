const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch(); const p = await (await b.newContext({viewport:{width:390,height:844},isMobile:true,hasTouch:true})).newPage();
  await p.goto('https://staging.masterfold.com/about-us/?LSCWP_CTRL=before_optm', {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
  console.log(await p.evaluate(() => {
    const el=[...document.querySelectorAll('body *')][1134]; const out=['EL '+el.tagName+'.'+el.className];
    const sh=document.getElementById('wpr-addons-css-css').sheet;
    const walk=(rs,media)=>{ for (const r of rs) { if (r.cssRules && !r.selectorText) { walk(r.cssRules, r.conditionText||''); continue; } if (r.selectorText && /background/.test(r.style.cssText)) { try { if (el.matches(r.selectorText)) out.push(media+' | '+r.cssText.slice(0,300)); } catch(e){} } } };
    walk(sh.cssRules,''); return out.join('\n'); }));
  await b.close();
})();
