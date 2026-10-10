// Open the header account panel (and wishlist, search) on live and staging and compare.
const { chromium } = require('playwright');
const FIX = process.argv[2] || '';
(async () => {
  const b = await chromium.launch();
  for (const host of ['masterfold.com','staging.masterfold.com']) {
    const p = await (await b.newContext({viewport:{width:1440,height:900}})).newPage();
    await p.goto('https://'+host+'/product/shiny-slim-z-key-holder-sani/', {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
    await p.addStyleTag({content:'*{animation:none!important;transition:none!important} .cky-consent-container,.cky-overlay,.cky-btn-revisit-wrapper{display:none!important}' + (host.startsWith('staging') ? FIX : '')});
    await p.evaluate(()=>scrollTo(0,600)); await p.waitForTimeout(800);
    for (const [name,x] of [['account',1383],['search',1296]]) {
      await p.mouse.click(x,60); await p.waitForTimeout(1500);
      await p.screenshot({path:`shots/oc_${host.split('.')[0]}_${name}.png`});
      await p.keyboard.press('Escape'); await p.mouse.click(700,600).catch(()=>{}); await p.waitForTimeout(800);
      await p.reload({waitUntil:'networkidle'}).catch(()=>{}); await p.addStyleTag({content:'*{animation:none!important;transition:none!important} .cky-consent-container,.cky-overlay,.cky-btn-revisit-wrapper{display:none!important}' + (host.startsWith('staging') ? FIX : '')}); await p.evaluate(()=>scrollTo(0,600)); await p.waitForTimeout(800);
    }
  }
  await b.close();
})();
