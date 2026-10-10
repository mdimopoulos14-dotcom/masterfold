const { chromium } = require('playwright');
(async () => { const b = await chromium.launch();
  for (const [vn,o] of [['d',{viewport:{width:1440,height:900}}],['m',{viewport:{width:412,height:823},isMobile:true,hasTouch:true}]]) {
    for (const u of process.argv.slice(2)) { const p = await (await b.newContext(o)).newPage();
      await p.goto(u,{waitUntil:'networkidle',timeout:90000}).catch(()=>{}); await p.waitForTimeout(2500);
      console.log(vn, u.slice(30), JSON.stringify(await p.evaluate(()=>['32268fb','5eab4120'].map(id=>{const e=document.querySelector('.elementor-location-header [data-id="'+id+'"]'); return e?[id,getComputedStyle(e).display,e.textContent.trim().length, e.querySelectorAll('img,svg,video,iframe,a,button').length]:[id,'absent'];})))); } }
  await b.close(); })();
