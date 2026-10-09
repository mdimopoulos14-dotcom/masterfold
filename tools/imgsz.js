const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  for (const [vn, vp] of [['mobile', {width:390,height:844,isMobile:true}], ['desktop', {width:1440,height:900}]]) {
    const p = await (await b.newContext({viewport:{width:vp.width,height:vp.height}, isMobile:!!vp.isMobile, deviceScaleFactor: vp.isMobile?2:1})).newPage();
    await p.goto(process.argv[2], {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
    await p.evaluate(async () => { for (let y=0;y<document.body.scrollHeight;y+=700){window.scrollTo(0,y);await new Promise(r=>setTimeout(r,80));} });
    const rows = await p.evaluate(() => [...document.images].filter(i=>i.naturalWidth>1200 && i.getBoundingClientRect().width>0).map(i=>{
      const w=i.closest('[data-widget_type]'); const sec=i.closest('[data-elementor-type]');
      return [i.currentSrc.split('/').pop().slice(0,45), i.naturalWidth+'x'+i.naturalHeight, 'shown '+Math.round(i.getBoundingClientRect().width)+'px', w? w.getAttribute('data-widget_type'):'-', w? w.getAttribute('data-id'):'-', sec? (sec.getAttribute('data-elementor-type')+'#'+sec.getAttribute('data-elementor-id')):'-', i.hasAttribute('srcset')?'srcset':'no-srcset', i.loading||'-'];
    }));
    console.log('==', vn); rows.forEach(r=>console.log('  ', r.join(' | ')));
  }
  await b.close();
})();
