const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  for (const host of ['masterfold.com','staging.masterfold.com']) {
    const p = await (await b.newContext({viewport:{width:1440,height:900}})).newPage();
    await p.goto('https://'+host+'/product/shiny-slim-z-key-holder-sani/', {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
    await p.waitForTimeout(1000);
    const res = await p.evaluate(() => { const out=[]; let e=document.querySelector('[data-id="29e115b1"]'); while (e && e!==document.documentElement) { const cs=getComputedStyle(e); out.push((e.dataset.id||e.tagName+'.'+e.className.slice(0,30))+' pos='+cs.position+' z='+cs.zIndex+' op='+cs.opacity+' tf='+(cs.transform!=='none')+' anim='+cs.animationName+' will='+cs.willChange+' filt='+cs.filter+' iso='+cs.isolation+' disp='+cs.display); e=e.parentElement; } return out; });
    console.log(host+'\n '+res.join('\n '));
  }
  await b.close();
})();
