// Inject Hello's original stylesheets in place of the theme base CSS and report elements whose box moves.
const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  const p = await (await b.newContext({viewport:{width:1440,height:900}})).newPage();
  await p.goto(process.argv[2], {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
  const snap = () => p.evaluate(() => [...document.querySelectorAll('body *')].map(e => { const r=e.getBoundingClientRect(); return [Math.round(r.top+scrollY), Math.round(r.left), Math.round(r.width), Math.round(r.height)].join(','); }));
  const a = await snap();
  await p.evaluate(async () => {
    const base='https://staging.masterfold.com/wp-content/themes/hello-elementor/assets/css/';
    const css=(await Promise.all(['reset.css','theme.css','header-footer.css'].map(f=>fetch(base+f).then(r=>r.text())))).join('\n');
    const l=document.getElementById('masterfold-base-css'); const s=document.createElement('style'); s.textContent=css; if(l){l.replaceWith(s);} else {document.head.prepend(s); console.log('nobase');}
  });
  await p.waitForTimeout(800);
  const bb = await snap();
  const els = await p.evaluate(() => [...document.querySelectorAll('body *')].map(e => e.tagName.toLowerCase()+(e.id?'#'+e.id:'')+'.'+[...e.classList].slice(0,3).join('.')));
  let n=0; for (let i=0;i<a.length;i++) if (a[i]!==bb[i]) { if (n++<25) console.log(els[i], a[i], '->', bb[i]); }
  console.log('moved', n, 'of', a.length);
  await b.close();
})();
