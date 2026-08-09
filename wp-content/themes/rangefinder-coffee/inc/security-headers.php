<?php
/**
 * Security response headers (WordPress-native equivalent of an Express "helmet" middleware).
 */
defined( 'ABSPATH' ) || exit;

function rangefinder_security_headers() {
	if ( is_admin() ) {
		rangefinder_admin_security_headers();
		return;
	}

	$font_cdn = 'https://fonts.cdnfonts.com';

	$csp = implode(
		'; ',
		array(
			"default-src 'self'",
			"script-src 'self'",
			"style-src 'self' 'unsafe-inline' {$font_cdn}",
			"font-src 'self' {$font_cdn}",
			"img-src 'self' data: https://www.openstreetmap.org",
			"frame-src https://www.openstreetmap.org https://checkout.stripe.com",
			"connect-src 'self' https://api.stripe.com",
			"object-src 'none'",
			"base-uri 'self'",
			"frame-ancestors 'self'",
		)
	);

	header( "Content-Security-Policy: {$csp}" );
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );

	if ( is_ssl() ) {
		header( 'Strict-Transport-Security: max-age=63072000; includeSubDomains; preload' );
	}

	// Best-effort removal; fully hiding the PHP signature also requires expose_php=Off in php.ini.
	header_remove( 'X-Powered-By' );
}
add_action( 'send_headers', 'rangefinder_security_headers' );

/**
 * Stricter header set for wp-admin/wp-login: no external CDNs, clickjacking
 * fully denied (rather than same-origin), longer-lived HSTS.
 */
function rangefinder_admin_security_headers() {
	$csp = implode(
		'; ',
		array(
			"default-src 'self'",
			"script-src 'self' 'unsafe-inline'",
			"style-src 'self' 'unsafe-inline'",
			"font-src 'self'",
			"img-src 'self' data:",
			"connect-src 'self'",
			"object-src 'none'",
			"base-uri 'self'",
			"frame-ancestors 'none'",
		)
	);

	header( "Content-Security-Policy: {$csp}" );
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: DENY' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: geolocation=(), microphone=(), camera=()' );

	if ( is_ssl() ) {
		header( 'Strict-Transport-Security: max-age=31536000; includeSubDomains' );
	}

	header_remove( 'X-Powered-By' );
}
add_action( 'login_init', 'rangefinder_admin_security_headers' );
