const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  const ctx = await b.newContext({viewport:{width:390,height:844}, isMobile:true, hasTouch:true, userAgent:'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1'});
  const p = await ctx.newPage();
  await p.goto(process.argv[2], {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
  await p.evaluate(async () => { for (let y=0; y<document.body.scrollHeight; y+=600) { window.scrollTo(0,y); await new Promise(r=>setTimeout(r,120)); } window.scrollTo(0,0); });
  await p.waitForTimeout(1500);
  const y0=+process.argv[3], y1=+process.argv[4];
  console.log(JSON.stringify(await p.evaluate(([y0,y1]) => [...document.querySelectorAll('img')].filter(i=>{const r=i.getBoundingClientRect(); const t=r.top+scrollY; return t>y0-200 && t<y1;}).map(i=>{const r=i.getBoundingClientRect(); return {top:Math.round(r.top+scrollY), w:Math.round(r.width), h:Math.round(r.height), complete:i.complete, nw:i.naturalWidth, src:(i.currentSrc||i.src).slice(-60), cls:i.className.slice(0,60), op:getComputedStyle(i).opacity, vis:getComputedStyle(i).visibility, pcls:i.parentElement.className.slice(0,80)};}), [y0,y1]), null, 0));
  await b.close();
})();
