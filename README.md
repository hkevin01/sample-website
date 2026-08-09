# Range Finder Coffee — WordPress Theme

Custom, zero-build-tool WordPress theme for Range Finder Coffee (Fayetteville, WV). No Bootstrap, no bundler — plain PHP templates, one CSS file with native custom properties for instant contrast/font scaling, and one vanilla JS file.

## Project layout

```
wp-content/
  themes/rangefinder-coffee/   Theme (this is the main project)
  plugins/                     Drop-in third-party plugins here (empty by default)
  mu-plugins/                  Must-use plugins (empty by default)
  uploads/                     Media uploads (empty by default, gitignored contents)
```

This repo intentionally does not include WordPress core (`wp-admin/`, `wp-includes/`, `wp-load.php`, etc.) or `wp-config.php`. Point a local WordPress install's `wp-content` at this folder, or symlink `wp-content/themes/rangefinder-coffee` into an existing install.

## Local setup

1. Get WordPress core running locally (`wp-env`, LocalWP, Docker `wordpress` image, etc.), with its `wp-content` directory replaced by/symlinked to this project's `wp-content/`.
2. In wp-admin, activate **Range Finder Coffee** under Appearance > Themes.
3. Set a static front page under Settings > Reading (so `front-page.php` is used).
4. Configure:
   - **Settings > Café Hours & Status** — live open/closed override, holiday hours, displayed hours text.
   - **Settings > Stripe & Merchandise** — Stripe secret/publishable keys, currency, checkout redirect URLs.
   - **Appearance > Customize** — hero tagline/image, "Our Story" text, contact info, footer links.
5. Add content via the **Menu Items**, **Gallery Images**, **News/Events**, and **Merchandise** post types in wp-admin.

## Notable theme internals

| File | Purpose |
|---|---|
| `inc/custom-post-types.php` | Registers Menu Items, Gallery Images, News/Events CPTs |
| `inc/stripe-merch.php` | Merchandise CPT, Stripe settings page, `/wp-json/rangefinder/v1/checkout` REST route |
| `inc/status-helper.php` | Computes live open/closed status, `/wp-json/rangefinder/v1/status` REST route |
| `inc/settings-page.php` | Café Hours & Status admin settings |
| `inc/customizer.php` | Theme Customizer fields (hero, story, contact, footer links) |
| `js/main.js` | Font scaling, contrast toggle, slider, live status polling, Stripe checkout |

Staff/admin auth uses WordPress core login (`wp-login.php`) and the `manage_options` capability — no custom auth code.

## Requirements

- PHP 7.4+
- WordPress 6.0+
- A Stripe account (test-mode keys are fine for development)
