const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  for (const u of process.argv.slice(2)) {
    const p = await (await b.newContext({viewport:{width:390,height:844}, isMobile:true, hasTouch:true})).newPage();
    await p.goto(u, {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
    const r = await p.evaluate(() => {
      const m = document.querySelector('.mf-mobile-menu-root .mobile-menu');
      if (!m) return 'no menu';
      const cs = getComputedStyle(m);
      const anc = []; let e = m.parentElement;
      while (e && e !== document.documentElement) { const c = getComputedStyle(e); if (c.transform !== 'none' || c.filter !== 'none' || c.perspective !== 'none' || c.contain !== 'none' || c.willChange.includes('transform') || c.backdropFilter !== 'none') anc.push((e.className||e.tagName).toString().slice(0,70) + ' transform=' + c.transform + ' filter=' + c.filter + ' contain=' + c.contain + ' will=' + c.willChange); e = e.parentElement; }
      const rc = m.getBoundingClientRect();
      return { transform: cs.transform, position: cs.position, right: cs.right, width: cs.width, rect: [Math.round(rc.left), Math.round(rc.right)], visible: cs.visibility, containingAncestors: anc };
    });
    console.log(u.replace('https://staging.masterfold.com',''), JSON.stringify(r, null, 1));
  }
  await b.close();
})();
