const { chromium } = require('playwright');
(async () => { const b = await chromium.launch();
  for (const [vn,o] of [['d',{viewport:{width:1440,height:900}}],['m',{viewport:{width:390,height:844},isMobile:true,hasTouch:true}]]) {
    const p = await (await b.newContext(o)).newPage();
    await p.goto(process.argv[2],{waitUntil:'networkidle',timeout:90000}).catch(()=>{});
    const st = async (lbl) => console.log(vn, lbl, JSON.stringify(await p.evaluate(()=>{const e=document.querySelector('[data-id="32268fb"]'); return [getComputedStyle(e).display, Math.round(e.getBoundingClientRect().height), e.classList.contains('e-lazyloaded'), document.body.scrollHeight];})));
    await st('networkidle');
    await p.waitForTimeout(4000); await st('+4s');
    await p.mouse.move(200,300); await p.evaluate(()=>scrollTo(0,400)); await p.waitForTimeout(1500); await st('after scroll');
  }
  await b.close(); })();
