# Vu Legals

Cookie consent for WordPress, built for Vu Digital client sites. No licence, no "powered by", styled from your own CSS variables.

**What it does**

- Two-layer consent UI (banner + preferences) with four categories: strictly necessary, functional, analytics, marketing. Reject is as easy as accept, nothing pre-ticked (ICO/PECR).
- Cookie registry (Vu Legals → Cookies) with one-click presets for GA4, Google Ads, Meta Pixel, LinkedIn, Hotjar, Clarity, HubSpot, YouTube, Vimeo, Maps, reCAPTCHA, X, TikTok, WordPress, Gravity Forms, WooCommerce. A homepage scanner tells you which ones you need.
- Generated Cookie Policy page (block or `[vu_cookie_policy]` shortcode) that stays in sync with the registry and company details, with an inline "Change your cookie settings" panel (switches + save) at the bottom so the policy page is also the place to change your mind. Available anywhere as `[vu_cookie_settings]`.
- Script gating: known trackers are detected anywhere in the page output (Oxygen code blocks, WPCode, theme) and held as `type="text/plain"` until consent. Enqueued handles and custom URL patterns can be mapped manually. Paste-in snippets under Vu Legals → Scripts.
- Embed blocking: YouTube / Vimeo / Maps iframes in content become a placeholder until consent.
- Google Consent Mode v2 defaults in `<head>` before GTM, `update` on every decision, `vul_consent` dataLayer event. Optionally loads GTM itself.
- Gravity Forms: fills Consent field labels with your standard text + privacy link; `{vu_legals:*}` merge tags.
- Geo: show everywhere, or UK/EU/EEA/CH only, or a custom list. Country from CDN/host headers (Cloudflare, WP Engine GeoTarget, CloudFront, Vercel) with a cache-safe browser-timezone fallback. No external lookups.
- Plays nicely with others: blocks Google Site Kit's own tags and Consent Mode snippet so they can't race the banner; registers with the WP Consent API and publishes decisions to it; excludes itself from LiteSpeed / WP Rocket / Perfmatters JS combining, deferral and delay.
- Consent log (anonymous ID, hashed IP, choices, policy version) with CSV export and automatic pruning. Bump the policy version to re-ask everyone.

## Install

Upload the `vu-legals` folder to `wp-content/plugins/`, activate, then:

1. **Vu Legals → Settings → General**: company details, pick or create the Cookie Policy page, set the Privacy Policy page.
2. **Scan & presets**: scan the homepage, add cookies for what it finds.
3. **Style**: colours and radius, or switch to *Theme* mode and define `--vul-*` in your stylesheet.
4. **Integrations**: GTM ID if you want the plugin to load it.
5. Put `[vu_consent_link text="Cookie preferences"]` (or any element with `data-vul-open`) in the footer.

## Styling

Everything is a custom property. In *Theme* mode the plugin emits nothing, so map them to your tokens:

```css
:root {
  --vul-bg: var(--surface);
  --vul-fg: var(--ink);
  --vul-accent: var(--color-primary);
  --vul-accent-fg: #fff;
  --vul-border: var(--line);
  --vul-radius: var(--radius-lg);
  --vul-font: var(--font-body);
}
```

Full list: `--vul-bg --vul-fg --vul-muted --vul-accent --vul-accent-fg --vul-border --vul-radius --vul-btn-radius --vul-font --vul-text-size --vul-shadow --vul-switch-on --vul-switch-off --vul-overlay --vul-z --vul-gap --vul-max`.

Override the markup by copying `templates/banner.php` to `yourtheme/vu-legals/banner.php`.

## Developer API

PHP

```php
vul_has_consent( 'analytics' );                 // bool (server-side, from cookie)
echo vul_gate( '<script>…</script>', 'marketing' );
echo vul_preferences_link( 'Cookie preferences' );
add_filter( 'vul_country', fn() => 'GB' );      // override geo
add_filter( 'vul_providers', fn( $p ) => $p + [ 'crisp' => [ 'name' => 'Crisp', 'category' => 'functional', 'match' => [ 'client.crisp.chat' ], 'cookies' => [ … ] ] ] );
add_filter( 'vul_policy_sections', … );          // edit the generated policy
add_filter( 'vul_handle_map', … );               // enqueued handle → category
add_filter( 'vul_css_vars', … );
```

JS

```js
vul.has('marketing');           // bool
vul.open();                     // preferences modal
vul.accept(); vul.reject(); vul.set({ analytics: true });
vul.on(({ categories, source }) => …);
document.addEventListener('vul:consent', e => …);
// <html data-vul-consent="necessary analytics"> is kept in sync
```

Gated markup (what the plugin produces, and what you can write by hand):

```html
<script type="text/plain" data-vul-consent="analytics" data-vul-src="https://…/tracker.js"></script>
<script type="text/plain" data-vul-consent="marketing">fbq('init', '…');</script>
```

## Notes

- The HTML is identical for every visitor, by design, so full-page caches (LiteSpeed, WP Rocket, WP Engine, Cloudflare) are safe with no vary cookie: Consent Mode defaults are always `denied`, gated scripts are always emitted gated, the banner markup is always present but hidden, and geo decisions move to the browser when a page cache is detected. The JS then replays stored consent (`gtag('consent','update')`, script activation, banner hidden) on load.
- Revoking a category deletes the registered first-party cookies for it (wildcards like `_ga_*` supported) and reloads the page, since scripts can't be un-run. Third-party cookies on other domains can't be deleted by any banner; the reload stops them being refreshed.
- The consent log endpoint (`/wp-json/vu-legals/v1/consent`) is public, write-only, sanitised, and rate-limited (20/min/IP) rather than nonce-protected, again because of page caching.
- Uninstalling removes settings, registry entries and the log table. Deactivating doesn't.

## LiteSpeed Cache (and other optimisers)

The plugin registers its own excludes through LiteSpeed's filters, so a default LiteSpeed install needs no manual configuration. If you have tightened settings or the excludes are being ignored, these are the ones that matter, under **LiteSpeed Cache → Page Optimization**:

- **JS Settings → JS Combine**: exclude `vu-legals.js` (JS Excludes). Combining changes load order relative to the inline `VUL_CONFIG` and the consent-mode block.
- **JS Settings → Load JS Deferred**: set to *Deferred* or *Off*, never *Delayed*, or add `vu-legals.js`, `VUL_CONFIG`, `dataLayer` and `gtag('consent'` to **JS Deferred / Delayed Excludes**. Delayed JS waits for user interaction, so the banner would not appear until the visitor moves the mouse, and the Consent Mode defaults would fire after GTM.
- **JS Settings → Inline JS Deferred / Delayed**: the same excludes cover the inline scripts (they are matched on content).
- **CSS Settings → Generate UCSS / CSS Combine**: `.vul-*` selectors are whitelisted via `litespeed_ucss_whitelist`. If UCSS still strips the banner styles, add `.vul` to **UCSS Allowlist**.
- **Cache → Cache Logged-in Users**: no effect on the plugin either way.
- **Guest Mode / Guest Optimization**: safe, because the HTML is visitor-neutral. No vary cookie is needed and none is set.
- **Localization → Localize Resources**: do not localise `googletagmanager.com` or `gtag/js`; a localised copy bypasses the URL-based tracker detection.
- **After changing plugin settings, purge all.** Settings live in the cached HTML.

WP Rocket: the equivalent excludes are registered for *Delay JavaScript execution*, *Load JavaScript deferred*, *Minify/Combine* and *Remove Unused CSS*. Perfmatters: *Delay JS* excludes are registered.

## Legal documents (v1.1)

Vu Legals → Settings → Documents generates three more documents from the company details plus a handful of fields and switches:

- **Website Terms of Use**: about us (entity-aware: limited, LLP, sole trader, partnership, charity), access, IP, linking, third-party links, forms and user content, disclaimers, liability, security and acceptable use, privacy, communications, changes, governing law (England and Wales / Scotland / NI).
- **Privacy Policy**: controller and ICO number, scope, rights, a "what we collect / how / why / lawful basis" table and a retention table both driven by one repeater, marketing, sharing (service providers typed in plus providers pulled from the cookie registry), transfers (UK / UK+EEA / international with current UK safeguards), security, cookies, changes.
- **Accessibility Statement**: modelled on the UK government statement; status (full / partial / non), known issues, testing date and method, feedback route, EASS enforcement.

Each is a **Legal Document** block or `[vu_document type="terms|privacy|accessibility|cookies"]`. Every clause shows its generated text in admin with an override box; type in it and that clause uses your wording (merge fields and `{if:flag}` conditionals still work). Version and updated date per document. "Create page" makes the page with the block in it.

Templates live in `templates/docs/*.php` (ordered arrays of sections) and are filterable via `vul_document_sections`, `vul_document_tokens`, `vul_document_flags`, `vul_document_processors`, `vul_document_html`. The wording is Vu Digital's own, structured to match standard UK solicitor templates; it is a sound baseline for brochure and lead-generation sites and is not a substitute for advice in regulated sectors.

## Roadmap

Terms of Sale (WooCommerce-aware: delivery, returns, cancellation, pricing, payment); footer legal block; WPML/Polylang string registration.
