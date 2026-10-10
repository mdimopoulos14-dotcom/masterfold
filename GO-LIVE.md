# Masterfold: going live with the staging rebuild

Staging (staging.masterfold.com) runs the rebuilt site. Live (masterfold.com) is
untouched. This lists everything that differs between them, how to move it to
live, what to check afterwards, and how to undo each part.

## 1. Moving it to live

**Recommended:** push staging to live as a whole (files + database) with the
host's staging tool (Plesk "WordPress Toolkit > Copy Data" or equivalent),
choosing "replace" and letting it search-replace `staging.masterfold.com` →
`masterfold.com`. Do it at a quiet hour and take a full live backup first.

Before pushing, check nothing was added on live since the staging copy was made
(new products, orders/enquiries, form entries, users). Anything new on live
would be overwritten by a full database push; either re-enter it on staging
first or push files only and apply section 2 by hand.

**After pushing:**

1. LiteSpeed Cache > Toolbox > Purge All.
2. Elementor > Tools > Regenerate CSS & Data.
3. Run the checks in section 3.

## 2. What changed (and how to undo each part)

### Code (in this repository)

| What | Where | Undo |
|---|---|---|
| Masterfold theme (replaces Hello Elementor; same look) | `wp-content/themes/masterfold` | Appearance > Themes > activate Hello Elementor (its menus and Customizer CSS are still stored) |
| Masterfold Core plugin 1.8.1 | `wp-content/plugins/masterfold-core` | Deactivate it **only together with** re-publishing the WPCode snippets listed below |

Masterfold Core contains:
- the product-page attribute sections (Elementor widget "MF Attribute Section"; catalog mode, no add-to-cart);
- the mobile tabbed menu built from Appearance > Menus > "Mobile Menu";
- the WPCode snippets that ran on every request, moved into files (`includes/snippets`), each loaded only while its WPCode copy stays unpublished;
- Meta pixel / Conversions API compatibility with page caching (unique event IDs per visitor on cached pages);
- per-page loading of plugin CSS/JS (sliders, maps, galleries, Royal Addons);
- locally served Google Fonts;
- the CSS-only sticky desktop header (CSS class `mf-sticky-top`);
- the image alt-text cleaner.

### Database / settings on staging

- **Active theme:** `masterfold`. Menus and Customizer CSS (27 KB) were copied to it.
- **WPCode:** the 41 snippets marked with `_mf_disabled_reason` are set to Draft. Their code now runs from Masterfold Core, apart from three that were removed outright:
  - #41354 (alt text) is replaced by `includes/media/alt-text.php`;
  - #38454 ("scroll 1px") is disabled because it had no visible effect;
  - #40427 is replaced by `performance/images.php`.

  The other drafts were already drafts before this work.
- **Header templates 2368 and 39327:**
  - the mobile menu shortcode was replaced by the "MF Mobile Menu" widget;
  - the desktop header container uses CSS class `mf-sticky-top` instead of Elementor's Sticky setting.

  Originals are saved in post meta `_mf_elementor_data_backup` and `_mf_elementor_data_backup_sticky`.
- **Other Elementor templates:** product templates use the new attribute widgets. Originals are saved in `_mf_elementor_data_backup` (19 templates).
- **Woo Category Slider:** 9 sliders use thumbnail size `medium_large` instead of `full`. The original value is saved in `_mf_original_thumbnail_size`.
- **LiteSpeed Cache:**
  - public cache TTL is 36000s;
  - JS defer stays **off** (tested: it lowered PageSpeed, see `tools/jsdefer_on.php` / `jsdefer_off.php`).
- **Elementor:** element cache TTL is 24 hours.
- **.htaccess:**
  - a "Masterfold WebP" block at the top serves `.webp` copies of JPG/PNG to browsers that accept them;
  - the LiteSpeed block was regenerated.
- **wp-config.php:** `WP_MEMORY_LIMIT` is `1024M`. It was raised while the old snippets exhausted memory. Those snippets are gone, so `512M` is enough; live's original value is also fine.
- **Generated files:** `uploads/mf-fonts/` (local fonts) and `uploads/mf-cache/` (slim stylesheet) are rebuilt automatically if deleted.

## 3. Checks after going live

- [ ] Home, a category, a product, a material page, About, Contact and My Account render normally on desktop and mobile.
- [ ] Product page: attribute sections show, accordions open, swatch lightbox opens, no add-to-cart button.
- [ ] Mobile menu: opens, sub-panels slide, "ALL" goes back, links work; edit it under Appearance > Menus > Mobile Menu.
- [ ] Desktop menu: Products/Materials/About mega menus open, header stays pinned when scrolling.
- [ ] Logged in as a customer: account panel, wishlist and My Account work.
- [ ] Meta Events Manager > Test Events: PageView and ViewContent arrive (browser and server) with matching event IDs.
- [ ] Google Search Console / Site Kit: no new errors after a few days. `tools/seo_cmp.py` compares titles, meta, canonical, schema and tracking IDs between two hosts (it reported 0 differences between live and staging).
- [ ] Response header `x-litespeed-cache: hit` on the second visit to a page.

## 4. Housekeeping

- Test customer `mf-test-customer` (user ID 634), created on staging for logged-in checks, has been deleted.
- Ask the host to raise PHP OPcache memory from 128 MB to 256 MB and
  `max_accelerated_files` to 20000. The site has more PHP files than the
  current cache holds, which slows uncached pages.
- Known pre-existing issue (also on live, not changed): following a link to `/products/#twist` on mobile scrolls the page sideways.

## 5. PageSpeed: where it stands

Lighthouse mobile (simulated slow phone), median of 5 runs. Live and staging
were measured back to back on the same machine, so they compare fairly;
absolute numbers from PageSpeed Insights will differ.

| Page | Live (original) | Staging (rebuild) | First paint | Largest paint | Layout shift |
|---|---|---|---|---|---|
| Home | 39 | 46 | 7.1s → 4.3s | 20.9s → 11.0s | 0 → 0 |
| Product | no score (Lighthouse error NO_LCP) | 50 | – | – → 8.8s | – → 0 |
| Category | 39 | 49 | 6.5s → 4.1s | 11.9s → 7.3s | 0.109 → 0 |
| Material | 50 | 60 | 6.1s → 3.6s | 12.2s → 8.1s | 0 → 0 |

On live, Lighthouse/PageSpeed cannot score product pages at all: the old
"scroll 1px" snippet (#38454) prevents a Largest Contentful Paint from being
recorded. That snippet is disabled on staging.

Beyond the score:
- cached pages are served in about 0.15 s;
- uncached pages that crashed or took 5+ s now render normally;
- the desktop header DOM is 41 % smaller;
- about 20 unused scripts no longer load on most pages;
- on pages without Royal widgets, 400 KB of Royal Addons CSS is replaced by a 44 KB slim copy.

Tried and rejected: LiteSpeed "Load JS Deferred" (lowered every score, so it is
off) and critical-CSS inlining (broke slider layouts).

What still holds the score back is the combined weight of the plugins that
render the pages: about 40 active plugins with their own CSS and JavaScript,
Elementor nested widgets, Royal Addons, Woo Variation Swatches, User
Registration and two wishlist plugins. The simulated slow phone charges all of
it to first paint. Reaching the 80s+ would mean rebuilding pages without some
of those plugins, which changes how they work. That is a decision for the site
owner, not something done silently in an "exact copy".
