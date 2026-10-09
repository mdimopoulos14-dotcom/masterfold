const { chromium } = require('playwright'); const fs = require('fs');
(async () => {
  const b = await chromium.launch(); const ctx = await b.newContext(); const p = await ctx.newPage();
  await p.goto('https://staging.masterfold.com/login/', { waitUntil: 'networkidle', timeout: 90000 }).catch(()=>{});
  await p.locator('input[name="username"]:visible').first().fill('mf-test-customer@example.invalid');
  await p.locator('input[name="password"]:visible').first().fill(fs.readFileSync(process.env.HOME + '/nm/.testuser_pw', 'utf8'));
  await Promise.all([p.waitForNavigation({ timeout: 90000 }).catch(()=>{}), p.locator('button[name="login"]:visible').first().click()]);
  await p.waitForTimeout(2000);
  console.log('after login:', p.url());
  const names = (await ctx.cookies()).map(c => c.name).filter(n => /wordpress_logged_in/.test(n));
  console.log('logged-in cookie:', names.length ? 'present' : 'MISSING');
  const msg = await p.locator('.user-registration-error:visible, .ur-error:visible, .woocommerce-error:visible').allTextContents().catch(()=>[]);
  if (msg.length) console.log('messages:', msg.join(' | ').slice(0,200));
  await ctx.storageState({ path: process.env.HOME + '/nm/.loggedin.json' });
  await b.close();
})();
