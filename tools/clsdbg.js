const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  const ctx = await b.newContext({viewport:{width:412,height:823}, isMobile:true, hasTouch:true, deviceScaleFactor:1.75, userAgent:'Mozilla/5.0 (Linux; Android 11; moto g power (2022)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36'});
  await ctx.addInitScript(() => { window.__ls=[]; new PerformanceObserver(l => { for (const e of l.getEntries()) window.__ls.push({t:Math.round(e.startTime), v:+e.value.toFixed(4), src:e.sources.map(s=>{const n=s.node; return (n&&n.nodeType===1?(n.dataset.id||n.tagName+'.'+String(n.className).slice(0,50)):'#text')+' '+JSON.stringify([s.previousRect.y,s.previousRect.height,s.currentRect.y,s.currentRect.height]);})}); }).observe({type:'layout-shift', buffered:true}); });
  const p = await ctx.newPage();
  await p.goto(process.argv[2], {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
  await p.waitForTimeout(3000);
  console.log(JSON.stringify(await p.evaluate(()=>window.__ls), null, 1));
  await b.close();
})();
