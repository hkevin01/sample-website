<?php
/**
 * Range Finder Coffee — Core Theme Functions (WordPress Native)
 */
defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------
 * 1. THEME SETUP
 * ------------------------------------------------------------- */
function rangefinder_setup() {
    load_theme_textdomain( 'rangefinder-coffee', get_template_directory() . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

    register_nav_menus( array(
        'primary' => __( 'Main Business Navigation', 'rangefinder-coffee' ),
    ) );
}
add_action( 'after_setup_theme', 'rangefinder_setup' );


/* -------------------------------------------------------------
 * 2. FOOTER WIDGETS
 * ------------------------------------------------------------- */
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


/* -------------------------------------------------------------
 * 3. ENQUEUE ASSETS (NO BOOTSTRAP)
 * ------------------------------------------------------------- */
function rangefinder_enqueue_assets() {
    wp_enqueue_style( 'rangefinder-font-poppins', 'https://fonts.cdnfonts.com/css/poppins', array(), null );
    wp_enqueue_style( 'rangefinder-style', get_stylesheet_uri(), array( 'rangefinder-font-poppins' ), '1.0.0' );

    wp_enqueue_script( 'rangefinder-main', get_template_directory_uri() . '/js/main.js', array(), '1.0.0', true );

    wp_localize_script( 'rangefinder-main', 'rangefinderData', array(
        'statusEndpoint'   => esc_url_raw( rest_url( 'rangefinder/v1/status' ) ),
        'checkoutEndpoint' => esc_url_raw( rest_url( 'rangefinder/v1/checkout' ) ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'rangefinder_enqueue_assets' );


/* -------------------------------------------------------------
 * 4. IMAGE UPLOAD VALIDATION
 * ------------------------------------------------------------- */
function rangefinder_image_upload_prefilter( $file ) {
    $allowed_mimes = array(
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif'
    );

    if ( ! in_array( $file['type'], array_keys( $allowed_mimes ) ) ) {
        return new WP_Error(
            'invalid_file_type',
            __( 'Sorry, this file type is not allowed.', 'rangefinder-coffee' )
        );
    }

    $max_size = 5 * 1024 * 1024; // 5MB
    if ( $file['size'] > $max_size ) {
        return new WP_Error(
            'invalid_file_size',
            __( 'Sorry, this file is too large. Maximum size is 5MB.', 'rangefinder-coffee' )
        );
    }

    return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'rangefinder_image_upload_prefilter' );


/* -------------------------------------------------------------
 * 5. RATE LIMITING — LOGIN ATTEMPTS
 * ------------------------------------------------------------- */
function rangefinder_rate_limit_login( $user, $username, $password ) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = 'rf_login_attempts_' . $ip;

    $attempts = (int) get_transient( $key );

    if ( $attempts >= 5 ) {
        return new WP_Error(
            'too_many_attempts',
            __( 'Too many login attempts. Please wait 10 minutes and try again.', 'rangefinder-coffee' )
        );
    }

    set_transient( $key, $attempts + 1, 10 * MINUTE_IN_SECONDS );
    return $user;
}
add_filter( 'authenticate', 'rangefinder_rate_limit_login', 30, 3 );


/* -------------------------------------------------------------
 * 6. SOFT DELETE SUPPORT
 * ------------------------------------------------------------- */
function rangefinder_soft_delete( $post_id ) {
    if ( get_post_status( $post_id ) !== 'trash' ) {
        wp_trash_post( $post_id );
    }
}
add_action( 'rangefinder_soft_delete', 'rangefinder_soft_delete' );


/* -------------------------------------------------------------
 * 7. AUDIT LOGGING
 * ------------------------------------------------------------- */
function rangefinder_audit_log( $post_id, $post, $update ) {
    $user = wp_get_current_user();
    $log_entry = sprintf(
        "[%s] User %s (%d) modified post %d (%s)",
        current_time( 'mysql' ),
        $user->user_login,
        $user->ID,
        $post_id,
        $post->post_status
    );

    error_log( $log_entry );
}
add_action( 'wp_insert_post', 'rangefinder_audit_log', 10, 3 );


/* -------------------------------------------------------------
 * 8. HOLIDAY CLOSURES — CUSTOM POST TYPE
 * ------------------------------------------------------------- */
function rangefinder_register_holidays_cpt() {
    register_post_type( 'rf_holiday', array(
        'label' => __( 'Holiday Closures', 'rangefinder-coffee' ),
        'public' => false,
        'show_ui' => true,
        'supports' => array( 'title', 'editor' ),
        'menu_icon' => 'dashicons-calendar-alt',
    ) );
}
add_action( 'init', 'rangefinder_register_holidays_cpt' );


/* -------------------------------------------------------------
 * 9. REQUIRED THEME MODULES
 * ------------------------------------------------------------- */
require get_template_directory() . '/inc/custom-post-types.php';
require get_template_directory() . '/inc/settings-page.php';
require get_template_directory() . '/inc/status-helper.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/stripe-merch.php';
require get_template_directory() . '/inc/security-headers.php';
require get_template_directory() . '/inc/admin-dashboard.php';
require get_template_directory() . '/inc/announcements-api.php';
