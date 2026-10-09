const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  for (const u of process.argv.slice(2)) {
    const p = await (await b.newContext({viewport:{width:1440,height:900}})).newPage();
    const errs = []; p.on('pageerror', e => errs.push(e.message.slice(0,100)));
    await p.goto(u, {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
    await p.addStyleTag({content:'.elementor-popup-modal,.dialog-widget,.cky-consent-container,.cky-overlay{display:none!important}'}).catch(()=>{});
    const sw = p.locator('img.swatch-trigger:visible, .custom-lightbox-attribute img:visible').first();
    await sw.scrollIntoViewIfNeeded().catch(()=>{});
    await sw.click({timeout:10000}).catch(e=>errs.push('click: '+e.message.slice(0,80)));
    await p.waitForTimeout(800);
    const st = await p.evaluate(() => { const lb = document.querySelector('#customLightbox, .custom-lightbox, [id*="ustomLightbox"]'); return lb ? { id: lb.id, display: getComputedStyle(lb).display, caption: (document.querySelector('.custom-lightbox-attribute-caption')||{}).textContent } : 'no lightbox element'; });
    console.log(u.replace('https://staging.masterfold.com',''), JSON.stringify(st), errs.length ? errs : '');
  }
  await b.close();
})();
