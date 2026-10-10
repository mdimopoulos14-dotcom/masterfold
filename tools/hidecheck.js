const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  const ctx = await b.newContext({viewport:{width:412,height:823}, isMobile:true, hasTouch:true, javaScriptEnabled: process.argv[3] !== 'nojs'});
  const p = await ctx.newPage();
  await p.goto(process.argv[2], {waitUntil:'domcontentloaded', timeout:90000}).catch(()=>{});
  const r = await p.evaluate(() => {
    const el = document.querySelector('[data-id="68a387c"]') || document.querySelector('main');
    const out=[]; let e=el; while (e) { const cs=getComputedStyle(e); if (cs.opacity!=='1' || cs.visibility!=='visible' || cs.display==='none' || cs.contentVisibility!=='visible') out.push((e.dataset&&e.dataset.id)||e.tagName+'.'+String(e.className).slice(0,60)+' op='+cs.opacity+' vis='+cs.visibility+' disp='+cs.display); e=e.parentElement; }
    return {hidden: out, bodyClass: document.body.className.slice(0,300), htmlClass: document.documentElement.className, htmlStyle: document.documentElement.getAttribute('style'), bodyStyle: document.body.getAttribute('style'), rs: document.readyState};
  });
  console.log(JSON.stringify(r,null,1));
  await b.close();
})();
