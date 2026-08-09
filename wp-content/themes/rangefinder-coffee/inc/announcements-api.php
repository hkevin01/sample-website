<?php
/**
 * Announcements REST API: thin, capability-checked CRUD over the News/Events
 * CPT. Deletes use WordPress's native Trash (Recycle Bin) instead of a custom
 * soft-delete flag, so recovery is already handled by WordPress core.
 */
defined( 'ABSPATH' ) || exit;

function rangefinder_announcement_to_array( WP_Post $post ) {
	return array(
		'id'      => $post->ID,
		'title'   => get_the_title( $post ),
		'excerpt' => get_the_excerpt( $post ),
		'content' => $post->post_content,
		'date'    => get_the_date( 'c', $post ),
		'status'  => $post->post_status,
	);
}

function rangefinder_announcements_permission_check() {
	return current_user_can( 'edit_posts' );
}

function rangefinder_register_announcements_routes() {
	register_rest_route( 'rangefinder/v1', '/announcements', array(
		array(
			'methods'             => 'GET',
			'callback'            => 'rangefinder_list_announcements',
			'permission_callback' => '__return_true',
		),
		array(
			'methods'             => 'POST',
			'callback'            => 'rangefinder_create_announcement',
			'permission_callback' => 'rangefinder_announcements_permission_check',
			'args'                => array(
				'title'   => array( 'required' => true ),
				'content' => array( 'required' => false ),
			),
		),
	) );

	register_rest_route( 'rangefinder/v1', '/announcements/(?P<id>\d+)', array(
		array(
			'methods'             => 'PUT',
			'callback'            => 'rangefinder_update_announcement',
			'permission_callback' => 'rangefinder_announcements_permission_check',
		),
		array(
			'methods'             => 'DELETE',
			'callback'            => 'rangefinder_delete_announcement',
			'permission_callback' => 'rangefinder_announcements_permission_check',
		),
	) );
}
add_action( 'rest_api_init', 'rangefinder_register_announcements_routes' );

function rangefinder_list_announcements() {
	$posts = get_posts( array(
		'post_type'      => 'news_post',
		'posts_per_page' => 20,
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );
	return rest_ensure_response( array_map( 'rangefinder_announcement_to_array', $posts ) );
}

function rangefinder_create_announcement( WP_REST_Request $request ) {
	$post_id = wp_insert_post( array(
		'post_type'    => 'news_post',
		'post_status'  => 'publish',
		'post_title'   => sanitize_text_field( $request->get_param( 'title' ) ),
		'post_content' => wp_kses_post( (string) $request->get_param( 'content' ) ),
	), true );

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	return rest_ensure_response( rangefinder_announcement_to_array( get_post( $post_id ) ) );
}

function rangefinder_update_announcement( WP_REST_Request $request ) {
	$id   = (int) $request->get_param( 'id' );
	$post = get_post( $id );

	if ( ! $post || 'news_post' !== $post->post_type ) {
		return new WP_Error( 'not_found', 'Announcement not found.', array( 'status' => 404 ) );
	}

	$update = array( 'ID' => $id );
	if ( null !== $request->get_param( 'title' ) ) {
		$update['post_title'] = sanitize_text_field( $request->get_param( 'title' ) );
	}
	if ( null !== $request->get_param( 'content' ) ) {
		$update['post_content'] = wp_kses_post( (string) $request->get_param( 'content' ) );
	}

	$result = wp_update_post( $update, true );
	if ( is_wp_error( $result ) ) {
		return $result;
	}

	return rest_ensure_response( rangefinder_announcement_to_array( get_post( $id ) ) );
}

function rangefinder_delete_announcement( WP_REST_Request $request ) {
	$id   = (int) $request->get_param( 'id' );
	$post = get_post( $id );

	if ( ! $post || 'news_post' !== $post->post_type ) {
		return new WP_Error( 'not_found', 'Announcement not found.', array( 'status' => 404 ) );
	}

	// Soft delete: moves to Trash (WordPress's built-in Recycle Bin), recoverable until emptied.
	$trashed = wp_trash_post( $id );
	if ( ! $trashed ) {
		return new WP_Error( 'delete_failed', 'Could not delete announcement.', array( 'status' => 500 ) );
	}

	return rest_ensure_response( array( 'deleted' => true, 'id' => $id ) );
}
