<?php
/**
 * Custom post types: News/Events, Gallery Images, Café Menu Items, Holiday Closures.
 */
defined( 'ABSPATH' ) || exit;

function rangefinder_register_post_types() {

	register_post_type( 'news_post', array(
		'labels' => array(
			'name'          => 'News / Events',
			'singular_name' => 'News Post',
			'add_new_item'  => 'Add New News Post',
		),
		'public'       => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-megaphone',
		'supports'     => array( 'title', 'editor', 'excerpt' ),
		'has_archive'  => false,
		'rewrite'      => array( 'slug' => 'news' ),
	) );

	register_post_type( 'gallery_image', array(
		'labels' => array(
			'name'          => 'Gallery Images',
			'singular_name' => 'Gallery Image',
			'add_new_item'  => 'Add New Gallery Image',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-format-gallery',
		'supports'     => array( 'title', 'thumbnail' ),
	) );

	register_post_type( 'cafe_menu_item', array(
		'labels' => array(
			'name'          => 'Menu Items',
			'singular_name' => 'Menu Item',
			'add_new_item'  => 'Add New Menu Item',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-coffee',
		'supports'     => array( 'title', 'editor' ),
	) );

	register_post_type( 'holiday', array(
		'labels' => array(
			'name'          => 'Holiday Closures',
			'singular_name' => 'Holiday Closure',
			'add_new_item'  => 'Add New Holiday Closure',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-calendar-alt',
		'supports'     => array( 'title', 'editor' ),
	) );
}
add_action( 'init', 'rangefinder_register_post_types' );

/**
 * Price meta box for café menu items.
 */
function rangefinder_menu_item_price_box() {
	add_meta_box(
		'rangefinder_menu_item_price',
		'Price',
		function ( $post ) {
			wp_nonce_field( 'rangefinder_menu_item_price_save', 'rangefinder_menu_item_price_nonce' );
			$price = get_post_meta( $post->ID, '_menu_item_price', true );
			echo '<label for="rangefinder_menu_item_price_field">Displayed price (e.g. $3.50 or "Prices Vary")</label><br />';
			echo '<input type="text" id="rangefinder_menu_item_price_field" name="rangefinder_menu_item_price" value="' . esc_attr( $price ) . '" style="width:100%;" />';
		},
		'cafe_menu_item',
		'side'
	);
}
add_action( 'add_meta_boxes', 'rangefinder_menu_item_price_box' );

function rangefinder_menu_item_price_save( $post_id ) {
	if ( ! isset( $_POST['rangefinder_menu_item_price_nonce'] ) ||
		! wp_verify_nonce( $_POST['rangefinder_menu_item_price_nonce'], 'rangefinder_menu_item_price_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['rangefinder_menu_item_price'] ) ) {
		update_post_meta( $post_id, '_menu_item_price', sanitize_text_field( $_POST['rangefinder_menu_item_price'] ) );
	}
}
add_action( 'save_post_cafe_menu_item', 'rangefinder_menu_item_price_save' );

/**
 * "Closed" checkbox meta box for holiday closures.
 */
function rangefinder_holiday_closed_box() {
	add_meta_box(
		'rangefinder_holiday_closed',
		'Closure Details',
		function ( $post ) {
			wp_nonce_field( 'rangefinder_holiday_closed_save', 'rangefinder_holiday_closed_nonce' );
			$is_closed = get_post_meta( $post->ID, '_is_closed', true );
			echo '<label for="rangefinder_holiday_closed_field">';
			echo '<input type="checkbox" id="rangefinder_holiday_closed_field" name="rangefinder_holiday_closed" value="1"' . checked( $is_closed, '1', false ) . ' /> ';
			echo 'Business is closed on this date';
			echo '</label>';
		},
		'holiday',
		'side'
	);
}
add_action( 'add_meta_boxes', 'rangefinder_holiday_closed_box' );

function rangefinder_holiday_closed_save( $post_id ) {
	if ( ! isset( $_POST['rangefinder_holiday_closed_nonce'] ) ||
		! wp_verify_nonce( $_POST['rangefinder_holiday_closed_nonce'], 'rangefinder_holiday_closed_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	update_post_meta( $post_id, '_is_closed', isset( $_POST['rangefinder_holiday_closed'] ) ? '1' : '' );
}
add_action( 'save_post_holiday', 'rangefinder_holiday_closed_save' );
