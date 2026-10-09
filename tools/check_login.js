const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch(); const ctx = await b.newContext({ storageState: process.env.HOME + '/nm/.loggedin.json' }); const p = await ctx.newPage();
  const r = await p.goto('https://staging.masterfold.com/my-account/', { waitUntil: 'domcontentloaded', timeout: 90000 });
  console.log('my-account status', r.status(), 'url', p.url(), 'cache', r.headers()['x-litespeed-cache'] || '-');
  console.log('logged-in body class:', await p.evaluate(() => document.body.classList.contains('logged-in')));
  await b.close();
})();
