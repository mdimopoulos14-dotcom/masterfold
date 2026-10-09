const { chromium } = require('playwright');
const fs = require('fs');
const raw = fs.readFileSync(process.argv[3], 'utf8');           // untrimmed HTML (all stylesheets)
const handles = process.argv.slice(4);
const hrefOf = h => { const m = raw.match(new RegExp("<link[^>]*id=['\"]" + h + "['\"][^>]*href=['\"]([^'\"]+)")); return m && m[1]; };
(async () => {
  const b = await chromium.launch();
  const measure = async (add) => {
    const p = await (await b.newContext({viewport:{width:1440,height:900}})).newPage();
    await p.goto(process.argv[2], {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
    for (const h of add) { const u = hrefOf(h); if (u) await p.addStyleTag({ url: u }).catch(()=>{}); }
    await p.addStyleTag({content:'*{animation:none!important;transition:none!important}'});
    await p.waitForTimeout(800);
    const r = await p.evaluate(() => document.documentElement.scrollHeight);
    await p.close(); return r;
  };
  console.log('trimmed page height      :', await measure([]));
  console.log('all removed added back   :', await measure(handles));
  for (const h of handles) console.log(('only ' + h).padEnd(25).slice(0,60), ':', await measure([h]));
  await b.close();
})();
