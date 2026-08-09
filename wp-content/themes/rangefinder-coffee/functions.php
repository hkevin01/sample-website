<?php
/**
 * Range Finder Coffee theme bootstrap.
 */
defined( 'ABSPATH' ) || exit;

function rangefinder_setup() {
	load_theme_textdomain( 'rangefinder-coffee', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

	register_nav_menus( array(
		'primary' => 'Main Business Navigation',
	) );
}
add_action( 'after_setup_theme', 'rangefinder_setup' );

function rangefinder_widgets_init() {
	$footer_sections = array(
		'footer-1' => 'Footer: Contact Us',
		'footer-2' => 'Footer: Career Hub',
		'footer-3' => 'Footer: Internal Node',
		'footer-4' => 'Footer: Legal / Policy',
	);
	foreach ( $footer_sections as $id => $name ) {
		register_sidebar( array(
			'name'          => $name,
			'id'            => $id,
			'before_widget' => '<li class="footer-links-widget">',
			'after_widget'  => '</li>',
			'before_title'  => '<h4>',
			'after_title'   => '</h4>',
		) );
	}
}
add_action( 'widgets_init', 'rangefinder_widgets_init' );

function rangefinder_enqueue_assets() {
	// CDN webfont for headings; allowed via the CSP style-src/font-src rules in inc/security-headers.php.
	wp_enqueue_style( 'rangefinder-font-poppins', 'https://fonts.cdnfonts.com/css/poppins', array(), null );
	wp_enqueue_style( 'rangefinder-style', get_stylesheet_uri(), array( 'rangefinder-font-poppins' ), '1.0.0' );
	wp_enqueue_script( 'rangefinder-main', get_template_directory_uri() . '/js/main.js', array(), '1.0.0', true );
	wp_localize_script( 'rangefinder-main', 'rangefinderData', array(
		'statusEndpoint'   => esc_url_raw( rest_url( 'rangefinder/v1/status' ) ),
		'checkoutEndpoint' => esc_url_raw( rest_url( 'rangefinder/v1/checkout' ) ),
	) );
}
add_action( 'wp_enqueue_scripts', 'rangefinder_enqueue_assets' );

require get_template_directory() . '/inc/custom-post-types.php';
require get_template_directory() . '/inc/settings-page.php';
require get_template_directory() . '/inc/status-helper.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/stripe-merch.php';
require get_template_directory() . '/inc/security-headers.php';
require get_template_directory() . '/inc/admin-dashboard.php';
