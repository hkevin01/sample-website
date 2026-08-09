<?php
/**
 * Café Hours & Status admin settings page (Settings API, admin-only capability).
 */
defined( 'ABSPATH' ) || exit;

function rangefinder_default_options() {
	return array(
		'status_mode'         => 'auto', // auto | force_open | force_closed
		'holiday_active'      => '0',
		'holiday_message'     => '',
		'hours_weekday_label' => '7:00 AM - 4:00 PM',
		'hours_weekend_label' => '8:00 AM - 4:00 PM',
		'weekday_open_hour'   => 7,
		'weekday_close_hour'  => 16,
		'weekend_open_hour'   => 8,
		'weekend_close_hour'  => 16,
	);
}

function rangefinder_get_options() {
	$stored = get_option( 'rangefinder_options', array() );
	return wp_parse_args( $stored, rangefinder_default_options() );
}

function rangefinder_register_settings() {
	register_setting( 'rangefinder_settings_group', 'rangefinder_options', 'rangefinder_sanitize_options' );

	add_settings_section( 'rangefinder_main', 'Live Status Override', '__return_false', 'rangefinder-settings' );

	add_settings_field( 'status_mode', 'Status Mode', 'rangefinder_field_status_mode', 'rangefinder-settings', 'rangefinder_main' );
	add_settings_field( 'holiday_active', 'Holiday Hours Active', 'rangefinder_field_holiday_active', 'rangefinder-settings', 'rangefinder_main' );
	add_settings_field( 'holiday_message', 'Holiday Message', 'rangefinder_field_holiday_message', 'rangefinder-settings', 'rangefinder_main' );

	add_settings_section( 'rangefinder_hours', 'Regular Hours', '__return_false', 'rangefinder-settings' );

	add_settings_field( 'hours_weekday_label', 'Mon - Fri Display Text', 'rangefinder_field_hours_weekday_label', 'rangefinder-settings', 'rangefinder_hours' );
	add_settings_field( 'hours_weekend_label', 'Sat - Sun Display Text', 'rangefinder_field_hours_weekend_label', 'rangefinder-settings', 'rangefinder_hours' );
	add_settings_field( 'weekday_open_hour', 'Mon - Fri Opening Hour (24h)', 'rangefinder_field_weekday_open_hour', 'rangefinder-settings', 'rangefinder_hours' );
	add_settings_field( 'weekday_close_hour', 'Mon - Fri Closing Hour (24h)', 'rangefinder_field_weekday_close_hour', 'rangefinder-settings', 'rangefinder_hours' );
	add_settings_field( 'weekend_open_hour', 'Sat - Sun Opening Hour (24h)', 'rangefinder_field_weekend_open_hour', 'rangefinder-settings', 'rangefinder_hours' );
	add_settings_field( 'weekend_close_hour', 'Sat - Sun Closing Hour (24h)', 'rangefinder_field_weekend_close_hour', 'rangefinder-settings', 'rangefinder_hours' );
}
add_action( 'admin_init', 'rangefinder_register_settings' );

function rangefinder_sanitize_options( $input ) {
	$output = rangefinder_default_options();

	$output['status_mode']    = in_array( $input['status_mode'] ?? '', array( 'auto', 'force_open', 'force_closed' ), true ) ? $input['status_mode'] : 'auto';
	$output['holiday_active'] = ! empty( $input['holiday_active'] ) ? '1' : '0';
	$output['holiday_message'] = sanitize_text_field( $input['holiday_message'] ?? '' );

	$output['hours_weekday_label'] = sanitize_text_field( $input['hours_weekday_label'] ?? '' );
	$output['hours_weekend_label'] = sanitize_text_field( $input['hours_weekend_label'] ?? '' );

	foreach ( array( 'weekday_open_hour', 'weekday_close_hour', 'weekend_open_hour', 'weekend_close_hour' ) as $key ) {
		$val = isset( $input[ $key ] ) ? (int) $input[ $key ] : $output[ $key ];
		$output[ $key ] = max( 0, min( 23, $val ) );
	}

	return $output;
}

function rangefinder_field_status_mode() {
	$options = rangefinder_get_options();
	?>
	<select name="rangefinder_options[status_mode]">
		<option value="auto" <?php selected( $options['status_mode'], 'auto' ); ?>>Automatic (based on hours below)</option>
		<option value="force_open" <?php selected( $options['status_mode'], 'force_open' ); ?>>Force Open</option>
		<option value="force_closed" <?php selected( $options['status_mode'], 'force_closed' ); ?>>Force Closed</option>
	</select>
	<?php
}

function rangefinder_field_holiday_active() {
	$options = rangefinder_get_options();
	?>
	<label>
		<input type="checkbox" name="rangefinder_options[holiday_active]" value="1" <?php checked( $options['holiday_active'], '1' ); ?> />
		Show holiday message instead of normal hours
	</label>
	<?php
}

function rangefinder_field_holiday_message() {
	$options = rangefinder_get_options();
	echo '<input type="text" style="width:100%;max-width:400px;" name="rangefinder_options[holiday_message]" value="' . esc_attr( $options['holiday_message'] ) . '" placeholder="Closed for Labor Day, back tomorrow at 7 AM" />';
}

function rangefinder_field_hours_weekday_label() {
	$options = rangefinder_get_options();
	echo '<input type="text" name="rangefinder_options[hours_weekday_label]" value="' . esc_attr( $options['hours_weekday_label'] ) . '" />';
}

function rangefinder_field_hours_weekend_label() {
	$options = rangefinder_get_options();
	echo '<input type="text" name="rangefinder_options[hours_weekend_label]" value="' . esc_attr( $options['hours_weekend_label'] ) . '" />';
}

function rangefinder_field_weekday_open_hour() {
	$options = rangefinder_get_options();
	echo '<input type="number" min="0" max="23" name="rangefinder_options[weekday_open_hour]" value="' . esc_attr( $options['weekday_open_hour'] ) . '" />';
}

function rangefinder_field_weekday_close_hour() {
	$options = rangefinder_get_options();
	echo '<input type="number" min="0" max="23" name="rangefinder_options[weekday_close_hour]" value="' . esc_attr( $options['weekday_close_hour'] ) . '" />';
}

function rangefinder_field_weekend_open_hour() {
	$options = rangefinder_get_options();
	echo '<input type="number" min="0" max="23" name="rangefinder_options[weekend_open_hour]" value="' . esc_attr( $options['weekend_open_hour'] ) . '" />';
}

function rangefinder_field_weekend_close_hour() {
	$options = rangefinder_get_options();
	echo '<input type="number" min="0" max="23" name="rangefinder_options[weekend_close_hour]" value="' . esc_attr( $options['weekend_close_hour'] ) . '" />';
}

function rangefinder_add_settings_page() {
	add_options_page(
		'Café Hours & Status',
		'Café Hours & Status',
		'manage_options',
		'rangefinder-settings',
		'rangefinder_render_settings_page'
	);
}
add_action( 'admin_menu', 'rangefinder_add_settings_page' );

function rangefinder_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1>Café Hours & Status</h1>
		<p>Staff-only controls. Changes take effect immediately on the front-end status badge.</p>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'rangefinder_settings_group' );
			do_settings_sections( 'rangefinder-settings' );
			submit_button( 'Save Changes' );
			?>
		</form>
	</div>
	<?php
}
