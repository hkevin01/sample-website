<?php
/**
 * Computes the current open/closed/holiday status from admin-configured options.
 */
defined( 'ABSPATH' ) || exit;

function rangefinder_get_status_data() {
	$options = rangefinder_get_options();

	if ( '1' === $options['holiday_active'] ) {
		return array(
			'state' => 'holiday',
			'class' => 'closed holiday',
			'label' => '● Holiday Hours: ' . ( $options['holiday_message'] ? $options['holiday_message'] : 'See front desk for details' ),
		);
	}

	if ( 'force_open' === $options['status_mode'] ) {
		return array(
			'state' => 'open',
			'class' => 'open',
			'label' => '● Currently Open',
		);
	}

	if ( 'force_closed' === $options['status_mode'] ) {
		return array(
			'state' => 'closed',
			'class' => 'closed',
			'label' => '● Currently Closed',
		);
	}

	// Automatic mode: derive from server time in the site's configured timezone.
	$current_hour = (int) current_time( 'G' );
	$day_of_week  = (int) current_time( 'w' ); // 0 = Sunday ... 6 = Saturday
	$is_weekend   = ( 0 === $day_of_week || 6 === $day_of_week );

	$open_hour  = $is_weekend ? (int) $options['weekend_open_hour'] : (int) $options['weekday_open_hour'];
	$close_hour = $is_weekend ? (int) $options['weekend_close_hour'] : (int) $options['weekday_close_hour'];

	if ( $current_hour >= $open_hour && $current_hour < $close_hour ) {
		return array(
			'state' => 'open',
			'class' => 'open',
			'label' => sprintf( '● Currently Open until %d:00', $close_hour ),
		);
	}

	return array(
		'state' => 'closed',
		'class' => 'closed',
		'label' => sprintf( '● Currently Closed - Opens at %d:00', $open_hour ),
	);
}

/**
 * REST endpoint so the front-end can refresh status without a full page reload.
 */
function rangefinder_register_rest_routes() {
	register_rest_route( 'rangefinder/v1', '/status', array(
		'methods'             => 'GET',
		'callback'            => function () {
			return rest_ensure_response( rangefinder_get_status_data() );
		},
		'permission_callback' => '__return_true',
	) );
}
add_action( 'rest_api_init', 'rangefinder_register_rest_routes' );
