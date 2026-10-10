// Swap Royal Addons' full stylesheet for the slim copy on a live page and report layout/pixel changes.
const { chromium } = require('playwright'); const fs=require('fs');
const slim = fs.readFileSync(process.argv[2],'utf8');
const urls = process.argv.slice(3);
(async () => {
  const b = await chromium.launch();
  for (const [vn, opts] of [['d',{viewport:{width:1440,height:900}}],['m',{viewport:{width:390,height:844},isMobile:true,hasTouch:true}]]) {
    const ctx = await b.newContext(opts);
    for (const u of urls) {
      const p = await ctx.newPage();
      await p.goto(u+(u.includes('?')?'&':'?')+'LSCWP_CTRL=before_optm', {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
      await p.addStyleTag({content:'*{animation:none!important;transition:none!important} .cky-consent-container,.cky-overlay,.cky-btn-revisit-wrapper{display:none!important}'});
      await p.evaluate(async () => { for (let y=0;y<document.body.scrollHeight;y+=600){scrollTo(0,y);await new Promise(r=>setTimeout(r,60));} scrollTo(0,0); });
      await p.waitForTimeout(800);
      const snap = () => p.evaluate(() => [...document.querySelectorAll('body *')].map(e => { const r=e.getBoundingClientRect(); const cs=getComputedStyle(e); return [Math.round(r.top+scrollY),Math.round(r.left),Math.round(r.width),Math.round(r.height),cs.color,cs.backgroundColor,cs.fontSize,cs.display,cs.visibility,cs.opacity].join(','); }));
      const a = await snap(); const s1 = await p.screenshot({fullPage:true});
      const ok = await p.evaluate(css => { const l=document.getElementById('wpr-addons-css-css'); if(!l) return false; const s=document.createElement('style'); s.textContent=css; l.replaceWith(s); return true; }, slim);
      await p.waitForTimeout(800);
      const c = await snap(); const s2 = await p.screenshot({fullPage:true});
      let n=0, ex=[]; for (let i=0;i<Math.min(a.length,c.length);i++) if (a[i]!==c[i]) { n++; if (ex.length<3) ex.push(i+': '+a[i]+' -> '+c[i]); }
      fs.writeFileSync('/tmp/sd_a.png', s1); fs.writeFileSync('/tmp/sd_b.png', s2);
      const px = require('child_process').execSync('python3 -I cmp.py /tmp/sd_a.png /tmp/sd_b.png').toString().trim().replace(/^.*?: /,'');
      console.log(vn, u.replace('https://staging.masterfold.com',''), 'swapped='+ok, 'changed='+n+'/'+a.length, px, ex.join(' | ').slice(0,300));
      await p.close();
    }
    await ctx.close();
  }
  await b.close();
})();
