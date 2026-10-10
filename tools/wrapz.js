const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch(); const p = await (await b.newContext({viewport:{width:1440,height:900}})).newPage();
  await p.goto('https://masterfold.com/product/shiny-slim-z-key-holder-sani/', {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
  console.log(await p.evaluate(() => { const h=document.querySelector('.elementor-location-header'); const cs=getComputedStyle(h); return JSON.stringify({pos:cs.position, z:cs.zIndex, tf:cs.transform, op:cs.opacity, iso:cs.isolation, cont:cs.contain, will:cs.willChange}); }));
  console.log(await p.evaluate(() => { const el=document.querySelector('.elementor-location-header'); const out=[];
    for (const sh of document.styleSheets) { let rules; try { rules=sh.cssRules; } catch(e){ continue; }
      const walk=(rs,media)=>{ for (const r of rs) { if (r.cssRules && !r.selectorText) { walk(r.cssRules, (r.conditionText||'')); continue; } if (r.selectorText) { try { if (el.matches(r.selectorText)) out.push(media+' | '+r.cssText.slice(0,160)); } catch(e){} } } };
      walk(rules,''); }
    return out.join('\n'); }));
  await b.close();
})();
