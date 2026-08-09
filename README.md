# Range Finder Coffee — WordPress Theme

Custom, zero-build-tool WordPress theme for Range Finder Coffee (Fayetteville, WV). No Bootstrap, no bundler — plain PHP templates, one CSS file with native custom properties for instant contrast/font scaling, and one vanilla JS file.

## Project layout

```
wp-content/
  themes/rangefinder-coffee/   Theme (this is the main project)
  plugins/                     Drop-in third-party plugins here (empty by default)
  mu-plugins/                  Must-use plugins (empty by default)
  uploads/                     Media uploads (empty by default, gitignored contents)
docker-compose.yml             Local WordPress + MariaDB + phpMyAdmin stack
docker/php/uploads.ini         PHP upload-size overrides for the wordpress container
Makefile                       Shortcuts for common docker compose / wp-cli commands
```

This repo intentionally does not include WordPress core (`wp-admin/`, `wp-includes/`, `wp-load.php`, etc.) or `wp-config.php`. Docker Compose runs the official `wordpress` image, which generates `wp-config.php` itself from environment variables and stores core files in a named volume; only `wp-content/` is bind-mounted from this repo.

## Local setup (Docker)

1. Copy the env template and adjust credentials if you want: `cp .env.example .env`
2. Start the stack: `make up` (or `docker compose up -d`)
3. Install WordPress (creates the `admin`/`admin` account — change the password immediately): `make install`
4. Activate the theme: `make activate-theme`
5. Visit the site at `http://localhost:8080` and wp-admin at `http://localhost:8080/wp-admin`. phpMyAdmin is at `http://localhost:8081`.
6. Stop the stack with `make down` (data persists in Docker volumes; `docker compose down -v` to wipe it).

Other handy commands: `make logs`, `make shell` (bash inside the wordpress container), `make db-shell`, `make cli ARGS="plugin list"` (runs any `wp-cli` command).

## Local setup (without Docker)

1. Get WordPress core running locally (`wp-env`, LocalWP, an existing install, etc.), with its `wp-content` directory replaced by/symlinked to this project's `wp-content/`.
2. In wp-admin, activate **Range Finder Coffee** under Appearance > Themes.
3. Set a static front page under Settings > Reading (so `front-page.php` is used).
4. Configure:
   - **Settings > Café Hours & Status** — live open/closed override, holiday hours, displayed hours text.
   - **Settings > Stripe & Merchandise** — Stripe secret/publishable keys, currency, checkout redirect URLs.
   - **Appearance > Customize** — hero tagline/image, "Our Story" text, contact info, footer links.
5. Add content via the **Menu Items**, **Gallery Images**, **News/Events**, and **Merchandise** post types in wp-admin.

Once activated, staff should start at **Café Dashboard** (top of the wp-admin menu) — see the Admin Dashboard section below.

## Admin Dashboard

Activating the theme adds a **Café Dashboard** page (`inc/admin-dashboard.php`) to the top of the wp-admin sidebar. It's a single screen with live status, a Stripe-configured check, and published-item counts/quick-links for every content type staff manage — no separate custom backend needed since WordPress admin already covers it.

| Section | Icon | What You Can Do |
|---|---|---|
| Live Status | 🟢/🔴 | See the current computed open/closed/holiday badge at a glance |
| Stripe Checkout | 💳 | See whether Stripe keys are configured, with a direct link to add them if not |
| Menu Items | ☕ | Add/edit/delete top menu items and prices shown on the homepage |
| Gallery Images | 🖼️ | Manage homepage picture-slider images (set a featured image per slide) |
| News / Events | 📰 | Post announcements/events shown in the homepage news feed |
| Merchandise | 🛒 | Manage items sold via Stripe Checkout on the homepage |

Each content row links straight to **Manage** (the WordPress list table) and **Add New** (the editor) for that post type, and the page header links to **Settings > Café Hours & Status**, **Settings > Stripe & Merchandise**, and **Appearance > Customize**.

## Notable theme internals

| File | Purpose |
|---|---|
| `inc/custom-post-types.php` | Registers Menu Items, Gallery Images, News/Events CPTs |
| `inc/stripe-merch.php` | Merchandise CPT, Stripe settings page, `/wp-json/rangefinder/v1/checkout` REST route |
| `inc/status-helper.php` | Computes live open/closed status, `/wp-json/rangefinder/v1/status` REST route |
| `inc/settings-page.php` | Café Hours & Status admin settings |
| `inc/customizer.php` | Theme Customizer fields (hero, story, contact, footer links) |
| `inc/security-headers.php` | Sends CSP/HSTS/X-Frame-Options/etc. response headers (see Security below) |
| `inc/admin-dashboard.php` | Café Dashboard admin page (status, Stripe check, content counts/quick-links) |
| `js/main.js` | Font scaling, contrast toggle, slider, live status polling, Stripe checkout |

Staff/admin auth uses WordPress core login (`wp-login.php`) and the `manage_options` capability — no custom auth code.

## Tech stack

WordPress core handles everything a hand-rolled Node/Express stack would otherwise need to reimplement (auth, sessions, hashing, DB access, an admin UI). This theme only adds what WordPress doesn't provide out of the box:

| Concern | Choice | Why |
|---|---|---|
| CMS / auth / sessions / admin UI | WordPress core | Battle-tested password hashing, session cookies, capability checks — no custom auth code to maintain |
| Database access | `WP_Query` / `$wpdb` (core) | Prepared statements built in; no raw SQL in this theme |
| Payments | Stripe Checkout via `wp_remote_post` to Stripe's HTTP API | No SDK/Composer dependency; server-side only, secret key never reaches the browser |
| Security headers | `inc/security-headers.php` (`send_headers` hook) | WordPress-native equivalent of an Express `helmet` middleware — same header set (CSP, HSTS, X-Frame-Options, etc.) without adding Node to the stack |
| Typography | CDN font via `fonts.cdnfonts.com`, enqueued in `functions.php` | Explicitly allow-listed in the CSP `style-src`/`font-src` directives rather than left wide open |
| Local dev environment | Docker Compose (`wordpress` + `mariadb` + `phpmyadmin`) | Reproducible stack, no local PHP/MySQL install needed |

## Security

`inc/security-headers.php` sends response headers on every front-end request; the table below maps each relevant OWASP Top 10 risk to how it's mitigated in this stack (WordPress-native, not a Node middleware chain):

| OWASP Risk | Mitigation |
|---|---|
| A02 Cryptographic Failures | WordPress core hashes passwords with a salted, iterated hash (bcrypt-backed in modern WordPress/PHP); session auth cookies are signed with `AUTH_KEY`/`AUTH_SALT` from `wp-config.php` |
| A03 Injection | All admin-settings input passes through `sanitize_text_field`/`esc_url_raw`/etc. sanitize callbacks ([inc/settings-page.php](wp-content/themes/rangefinder-coffee/inc/settings-page.php), [inc/stripe-merch.php](wp-content/themes/rangefinder-coffee/inc/stripe-merch.php)); no raw SQL, `eval`, or `exec` in the theme |
| A05 Security Misconfiguration | `inc/security-headers.php` sets CSP, `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, and HSTS (when served over HTTPS); `docker/php/security.ini` sets `expose_php = Off` to drop the `X-Powered-By` signature |
| A06 Vulnerable/Outdated Components | No third-party PHP packages (no Composer deps to fall behind on); keep WordPress core and any installed plugins updated via wp-admin or `make cli ARGS="core update"` |
| A07 Identification & Authentication Failures | Admin-only actions require the `manage_options` capability; WordPress core already rate-limits/locks out repeated failed logins with common security plugins, and applies constant-time password comparison internally |
| A08 Software & Data Integrity Failures | Settings are saved via the WordPress Settings API (`options.php`), which validates nonces and capabilities before any write; no custom partial-file writes in this theme |

## Automatic backups

This theme doesn't implement its own backup code — back up the two things that actually hold state:

1. **Database** (all settings, post types, Stripe/customizer config): `docker compose exec db mariadb-dump -u$MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE > backup-$(date +%F).sql`, scheduled via cron/CI.
2. **`wp-content/uploads/`** (media): sync to off-site storage (S3, Backblaze, etc.) on the same schedule.

For production, use a maintained WordPress backup plugin (e.g. UpdraftPlus) for scheduled, restorable snapshots instead of custom scripts — it already handles atomic writes and partial-failure recovery.

## Requirements

- PHP 7.4+
- WordPress 6.0+
- A Stripe account (test-mode keys are fine for development)
