const { chromium } = require('playwright'); const fs = require('fs');
const S = '/tmp/claude-0/-home-user-masterfold/7053d1c9-a9b7-5b6c-a5aa-1618651e9354/scratchpad/';
const V = process.env.HOME + '/nm/plugin/masterfold-core/assets/vendor/swiper/';
(async () => {
  const b = await chromium.launch();
  for (const [name, base] of [['live', 'https://masterfold.com'], ['staging', 'https://staging.masterfold.com']]) {
    const p = await (await b.newContext({viewport:{width:390,height:844}, isMobile:true, hasTouch:true, deviceScaleFactor:1})).newPage();
    // Real browsers fetch Swiper 11 from jsdelivr; this test machine can't, so serve the same files.
    await p.route(/cdn\.jsdelivr\.net\/npm\/swiper@11\/swiper-bundle\.min\.js/, r => r.fulfill({ contentType: 'application/javascript', body: fs.readFileSync(V + 'swiper-bundle.min.js') }));
    await p.route(/cdn\.jsdelivr\.net\/npm\/swiper@11\/swiper-bundle\.min\.css/, r => r.fulfill({ contentType: 'text/css', body: fs.readFileSync(V + 'swiper-bundle.min.css') }));
    await p.goto(base + '/product/ocean-fabric-wine-list-sani/', {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
    await p.addStyleTag({content:'*{animation:none!important;transition:none!important} .elementor-popup-modal,.dialog-widget,.cky-consent-container,.cky-overlay,.cky-btn-revisit-wrapper{display:none!important}'}).catch(()=>{});
    await p.waitForTimeout(2500);
    const st = await p.evaluate(() => { const s = document.querySelector('.product-images-slider'); return { init: !!(s && s.swiper), slides: s ? s.querySelectorAll('.swiper-slide').length : 0, firstSrc: (s && s.querySelector('.swiper-slide-active img') || {}).currentSrc?.split('/').pop() }; });
    console.log(name, JSON.stringify(st));
    await p.screenshot({ path: S + 'slider_' + name + '.png' });
  }
  await b.close();
})();
