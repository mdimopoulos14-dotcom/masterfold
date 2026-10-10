// Real browser load: DCL/load and the slowest requests, cold and warm cache.
const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  const ctx = await b.newContext({viewport:{width:1440,height:900}});
  for (const run of ['cold','warm']) {
    const p = await ctx.newPage(); const reqs=[];
    p.on('requestfinished', async r => { const t=r.timing(); reqs.push([Math.round(t.responseEnd), r.resourceType(), r.url().slice(0,110), r.url().startsWith('https://staging')?'':'EXT']); });
    p.on('requestfailed', r => reqs.push([-1,'FAILED',r.url().slice(0,110), r.failure()&&r.failure().errorText]));
    const t0=Date.now();
    await p.goto(process.argv[2], {waitUntil:'load', timeout:120000}).catch(e=>console.log('ERR',e.message));
    const nav = await p.evaluate(() => { const n=performance.getEntriesByType('navigation')[0]; return {ttfb:Math.round(n.responseStart), dcl:Math.round(n.domContentLoadedEventEnd), load:Math.round(n.loadEventEnd), fcp: Math.round((performance.getEntriesByName('first-contentful-paint')[0]||{}).startTime||0), requests: performance.getEntriesByType('resource').length+1}; });
    console.log(run, JSON.stringify(nav));
    reqs.sort((a,b)=>b[0]-a[0]); reqs.slice(0,8).forEach(r=>console.log('   ',r.join(' | ')));
    await p.close();
  }
  await b.close();
})();
