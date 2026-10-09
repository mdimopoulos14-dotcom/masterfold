const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  const p = await (await b.newContext({viewport:{width:1440,height:900}})).newPage();
  await p.goto(process.argv[2], {waitUntil:'networkidle', timeout:90000}).catch(()=>{});
  const r = await p.evaluate(() => {
    const hdr = document.querySelector('[data-elementor-type="header"]');
    const out=[];
    const walk=(el,d)=>{ for (const c of el.children){ if(!c.dataset.id){ walk(c,d); continue;} const n=c.querySelectorAll('*').length; if(n<15) continue; out.push(' '.repeat(d*2)+c.dataset.id+' '+(c.dataset.widget_type||c.dataset.element_type)+' nodes='+n+' bytes='+c.outerHTML.length+' vis='+(c.offsetParent!==null)+' cls='+[...c.classList].filter(x=>!x.startsWith('elementor-')&&!x.startsWith('e-')).join('.')); if(d<4) walk(c,d+1);} };
    walk(hdr,0); return out.join('\n');
  });
  console.log(r); await b.close();
})();
