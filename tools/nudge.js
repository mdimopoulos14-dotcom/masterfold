const { chromium } = require('playwright');
const S = '/tmp/claude-0/-home-user-masterfold/7053d1c9-a9b7-5b6c-a5aa-1618651e9354/scratchpad/';
(async () => {
  const b = await chromium.launch();
  for (const [vn, vp] of [['d',{width:1440,height:900}],['m',{width:390,height:844,isMobile:true,hasTouch:true}]]) {
    for (const block of [false, true]) {
      const p = await (await b.newContext({viewport:{width:vp.width,height:vp.height}, isMobile:!!vp.isMobile, hasTouch:!!vp.hasTouch, deviceScaleFactor:1})).newPage();
      if (block) await p.addInitScript(() => { window.scrollBy = function () {}; });
      await p.goto(process.argv[2], {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
      await p.addStyleTag({content:'.elementor-popup-modal,.dialog-widget,.cky-consent-container,.cky-overlay,.cky-btn-revisit-wrapper{display:none!important}'}).catch(()=>{});
      await p.waitForTimeout(2000);
      const st = await p.evaluate(() => ({ y: window.scrollY, sticky: [...document.querySelectorAll('.elementor-sticky--active, .elementor-sticky--effects')].length, lazy: [...document.querySelectorAll('.e-con.e-parent:not(.e-lazyloaded)')].length }));
      await p.evaluate(() => window.scrollTo(0, 0)); await p.waitForTimeout(500);
      await p.screenshot({ path: `${S}nudge_${vn}_${block ? 'off' : 'on'}.png` });
      console.log(vn, block ? 'no-nudge' : 'nudge   ', JSON.stringify(st));
    }
  }
  await b.close();
})();
