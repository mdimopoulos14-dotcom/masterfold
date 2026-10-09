const { chromium } = require('playwright');
const [,, outDir] = process.argv;
(async () => {
  const b = await chromium.launch();
  const ctx = await b.newContext({viewport:{width:390,height:844}, isMobile:true, hasTouch:true, deviceScaleFactor:1});
  const p = await ctx.newPage();
  await p.goto('https://staging.masterfold.com/?v='+Date.now(), {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
  await p.addStyleTag({content:'.elementor-popup-modal,.dialog-widget,.cky-consent-container,.cky-overlay,.cky-btn-revisit-wrapper{display:none!important}'});
  const shot = async (name) => { await p.waitForTimeout(700); await p.screenshot({path:`${outDir}/${name}.png`}); };
  await shot('0_closed');
  await p.locator('.mobile-menu-toggle:visible').first().tap(); await shot('1_main');
  const go = async (text, name) => { await p.locator('.mobile-menu .menu-slide.active a', {hasText: text}).first().tap(); await shot(name); };
  await go('Products','2_products'); await go('Restaurant','3_restaurant');
  await p.locator('.mobile-menu .menu-slide.active .menu-back').first().tap(); await shot('4_back_products');
  await p.locator('.mobile-menu .menu-slide.active .menu-back').first().tap(); await go('Materials','5_materials'); await go('Standard','6_standard');
  await p.locator('.mobile-menu .menu-slide.active .menu-close').first().tap(); await shot('7_closed_again');
  await b.close();
})();
