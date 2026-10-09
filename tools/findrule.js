const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch(); const p = await (await b.newContext({viewport:{width:1440,height:900}})).newPage();
  await p.goto('https://masterfold.com/wishlist/', {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
  console.log(await p.evaluate(() => { const el=document.querySelector('.elementor-location-header'); const out=[];
    for (const sh of document.styleSheets) { let rules; try { rules=sh.cssRules; } catch(e){ continue; }
      const walk=(rs,media)=>{ for (const r of rs) { if (r.cssRules && !r.selectorText) { walk(r.cssRules, (r.conditionText||r.media&&r.media.mediaText||'')); continue; } if (r.selectorText && /margin/.test(r.style.cssText)) { try { if (el.matches(r.selectorText)) out.push((sh.href||sh.ownerNode.id||'inline').slice(-60)+' | '+media+' | '+r.cssText.slice(0,200)); } catch(e){} } } };
      walk(rules,''); }
    return out.join('\n'); }));
  await b.close();
})();
