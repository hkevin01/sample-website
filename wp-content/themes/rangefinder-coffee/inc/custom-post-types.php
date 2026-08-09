<?php
/**
 * Custom post types: News/Events, Gallery Images, Café Menu Items.
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
