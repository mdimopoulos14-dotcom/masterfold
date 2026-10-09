// Collect JS errors (pageerror + console.error) for every URL in urls.json, desktop and mobile.
const { chromium } = require('playwright'); const fs=require('fs');
const urls = JSON.parse(fs.readFileSync('urls.json'));
const pick = Object.entries(urls).filter(([k,v]) => typeof v === 'string');
(async () => {
  const b = await chromium.launch(); const out={};
  for (const [vn, opts] of [['d',{viewport:{width:1440,height:900}}],['m',{viewport:{width:390,height:844},isMobile:true,hasTouch:true}]]) {
    const ctx = await b.newContext(opts);
    for (const [k,u] of pick) {
      const p = await ctx.newPage(); const errs=[];
      p.on('pageerror', e => errs.push('PAGEERROR ' + e.message.slice(0,160)));
      p.on('console', m => { if (m.type()==='error') errs.push('CONSOLE ' + m.text().slice(0,160)); });
      await p.goto(u, {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
      await p.evaluate(async () => { for (let y=0;y<document.body.scrollHeight;y+=700){window.scrollTo(0,y);await new Promise(r=>setTimeout(r,80));} });
      await p.mouse.move(700,60).catch(()=>{}); await p.waitForTimeout(800);
      out[k+'_'+vn]=[...new Set(errs)]; await p.close();
    }
    await ctx.close();
  }
  fs.writeFileSync(process.argv[2], JSON.stringify(out,null,1)); await b.close();
})();
