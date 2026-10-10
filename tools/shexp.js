const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  const p = await (await b.newContext({viewport:{width:1440,height:900}})).newPage();
  await p.goto('https://staging.masterfold.com/product/shiny-slim-z-key-holder-sani/', {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
  await p.addStyleTag({content:'*{animation:none!important;transition:none!important}'});
  await p.waitForTimeout(800);
  const exps = {f:'.mf-sticky-top{z-index:inherit!important}'};
  for (const [k,css] of Object.entries(exps)) {
    await p.evaluate(css => { let s=document.getElementById('exp'); if(!s){s=document.createElement('style');s.id='exp';document.head.appendChild(s);} s.textContent=css; }, css);
    await p.waitForTimeout(500);
    await p.screenshot({path:`shots/exp_${k}.png`, clip:{x:0,y:0,width:1440,height:160}});
  }
  const q = await (await b.newContext({viewport:{width:1440,height:900}})).newPage();
  await q.goto('https://masterfold.com/product/shiny-slim-z-key-holder-sani/', {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
  await q.addStyleTag({content:'*{animation:none!important;transition:none!important}'}); await q.waitForTimeout(800);
  await q.screenshot({path:'shots/exp_live.png', clip:{x:0,y:0,width:1440,height:160}});
  await b.close();
})();
