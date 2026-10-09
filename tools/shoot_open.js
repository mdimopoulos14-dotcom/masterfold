const { chromium } = require('playwright'); const fs=require('fs');
const [,, outDir] = process.argv; const urls = JSON.parse(fs.readFileSync('urls.json'));
(async () => {
  const b = await chromium.launch();
  for (const [vn, vp] of [['d',{width:1440,height:900}],['m',{width:390,height:844,isMobile:true,hasTouch:true}]]) {
    const ctx = await b.newContext({viewport:{width:vp.width,height:vp.height}, isMobile:!!vp.isMobile, hasTouch:!!vp.hasTouch});
    const p = await ctx.newPage();
    for (const k of Object.keys(urls).filter(k=>k.startsWith('prod_') && (!process.argv[3] || process.argv.slice(3).includes(k)))) {
      await p.goto(urls[k], {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
      await p.addStyleTag({content:'*{animation:none!important;transition:none!important} .elementor-popup-modal,.dialog-widget,.cky-consent-container,.cky-overlay,.cky-btn-revisit-wrapper{display:none!important}'}).catch(()=>{});
      // open every closed accordion in the visible product column
      const n = await p.evaluate(() => { let c=0; document.querySelectorAll('[class*="selected-"][class*="-column"] details.e-n-accordion-item:not([open]) > summary').forEach(s=>{ if(s.offsetParent){ s.click(); c++; } }); return c; });
      await p.waitForTimeout(1200);
      await p.evaluate(() => { document.querySelectorAll('.e-con, .elementor-widget').forEach(el => { const cs=getComputedStyle(el); if ((cs.overflowY==='scroll'||cs.overflowY==='auto') && el.scrollHeight>el.clientHeight+5) { el.style.setProperty('height','auto','important'); el.style.setProperty('max-height','none','important'); el.style.setProperty('overflow','visible','important'); } }); });
      await p.waitForTimeout(500);
      await p.evaluate(async () => { for (let y=0;y<document.body.scrollHeight;y+=600){window.scrollTo(0,y);await new Promise(r=>setTimeout(r,100));} window.scrollTo(0,0); });
      await p.waitForTimeout(1000);
      await p.screenshot({path:`${outDir}/${k}_${vn}.png`, fullPage:true});
      console.log(k, vn, 'opened', n);
    }
    await ctx.close();
  }
  await b.close();
})();
