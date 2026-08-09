=== Range Finder Coffee ===
Contributors: rangefindercoffee
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Custom accessible theme for Range Finder Coffee (Fayetteville, WV). Zero build tools, CSS-variable-driven contrast/font scaling, custom post types for menu items/gallery/news/merchandise, and Stripe Checkout for merch sales.

== Description ==

Three-column layout: real-time hours & top menu items, gallery slider/story/news, and location map & contact. Staff manage hours/status, gallery, news, menu items, and merchandise from the standard WordPress admin — no separate backend needed.

== Required Setup ==

1. Activate the theme under Appearance > Themes.
2. Set a static front page under Settings > Reading so `front-page.php` is used.
3. Create a menu under Appearance > Menus and assign it to the "Main Business Navigation" location (optional — a flat fallback menu renders automatically otherwise).
4. Configure hours/status under Settings > Café Hours & Status.
5. Configure Stripe keys under Settings > Stripe & Merchandise before adding Merchandise items.
6. Add content under the Menu Items, Gallery Images, News/Events, and Merchandise post types.

== Changelog ==

= 1.0.0 =
* Initial release: accessibility toolbar, hero, flat nav, three-column layout, live status REST endpoint, Stripe Checkout for merchandise.
