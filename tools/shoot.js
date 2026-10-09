const { chromium } = require('playwright');
const fs = require('fs');
const [,, outDir, ...names] = process.argv;
const urls = JSON.parse(fs.readFileSync('urls.json'));
const pick = Object.entries(urls).filter(([k,v]) => typeof v === 'string' && (!names.length || names.includes(k)));
(async () => {
  const b = await chromium.launch();
  for (const [vpName, vp] of [['d', {width:1440,height:900}], ['m', {width:390,height:844, isMobile:true, hasTouch:true, deviceScaleFactor:1}]]) {
    const ctx = await b.newContext({ viewport: {width:vp.width,height:vp.height}, isMobile: !!vp.isMobile, hasTouch: !!vp.hasTouch, ignoreHTTPSErrors: true,
      userAgent: vp.isMobile ? 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1' : undefined });
    // pre-accept cookie banner so it doesn't cover content
    const page = await ctx.newPage();
    for (const [k, u] of pick) {
      const t0 = Date.now();
      try {
        await page.goto(u, { waitUntil: 'networkidle', timeout: 90000 });
      } catch (e) { console.log('timeout', k, vpName); }
      await page.addStyleTag({ content: '*{animation:none!important;transition:none!important;caret-color:transparent!important} .elementor-popup-modal,.dialog-widget,.cky-consent-container,.cky-overlay,.cky-btn-revisit-wrapper{display:none!important}' }).catch(()=>{});
      await page.evaluate(async () => { for (let y=0; y<document.body.scrollHeight; y+=600) { window.scrollTo(0,y); await new Promise(r=>setTimeout(r,120)); } window.scrollTo(0,0); });
      await page.waitForTimeout(1500);
      await page.screenshot({ path: `${outDir}/${k}_${vpName}.png`, fullPage: true });
      console.log(k, vpName, Date.now()-t0, 'ms');
    }
    await ctx.close();
  }
  await b.close();
})();
