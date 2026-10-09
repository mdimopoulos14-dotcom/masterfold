const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  for (const host of ['masterfold.com','staging.masterfold.com']) {
    const p = await (await b.newContext({viewport:{width:1440,height:900}})).newPage();
    await p.goto('https://'+host+process.argv[2], {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
    await p.addStyleTag({content:'*{animation:none!important;transition:none!important} .cky-consent-container,.cky-overlay,.cky-btn-revisit-wrapper{display:none!important}'});
    for (const y of [0, 1500]) {
      await p.evaluate(y=>window.scrollTo(0,y), y); await p.waitForTimeout(1200);
      await p.screenshot({path:`shots/sticky_${host.split('.')[0]}_${y}.png`, clip:{x:0,y:0,width:1440,height:160}});
    }
    // open first mega menu item while scrolled
    const item = await p.$('.e-n-menu-title');
    if (item) { await item.hover(); await p.waitForTimeout(1500); await p.screenshot({path:`shots/sticky_${host.split('.')[0]}_menu.png`}); }
  }
  await b.close();
})();
