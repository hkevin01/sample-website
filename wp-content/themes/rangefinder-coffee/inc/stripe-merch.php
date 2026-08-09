<?php
/**
 * Stripe Merchandise: custom post type, settings page, checkout REST routes.
 */
defined( 'ABSPATH' ) || exit;

/** ---------- Merch custom post type ---------- */

function rangefinder_register_merch_post_type() {
	register_post_type( 'merch_item', array(
		'labels' => array(
			'name'          => 'Merchandise',
			'singular_name' => 'Merch Item',
			'add_new_item'  => 'Add New Merch Item',
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-cart',
		'supports'     => array( 'title', 'editor', 'thumbnail' ),
	) );
}
add_action( 'init', 'rangefinder_register_merch_post_type' );

function rangefinder_merch_price_box() {
	add_meta_box(
		'rangefinder_merch_price',
		'Price',
		function ( $post ) {
			wp_nonce_field( 'rangefinder_merch_price_save', 'rangefinder_merch_price_nonce' );
			$price_cents = get_post_meta( $post->ID, '_merch_price_cents', true );
			$price_display = $price_cents ? number_format( $price_cents / 100, 2 ) : '';
			echo '<label for="rangefinder_merch_price_field">Price in USD (e.g. 24.00)</label><br />';
			echo '<input type="number" step="0.01" min="0" id="rangefinder_merch_price_field" name="rangefinder_merch_price" value="' . esc_attr( $price_display ) . '" style="width:100%;" />';
		},
		'merch_item',
		'side'
	);
}
add_action( 'add_meta_boxes', 'rangefinder_merch_price_box' );

function rangefinder_merch_price_save( $post_id ) {
	if ( ! isset( $_POST['rangefinder_merch_price_nonce'] ) ||
		! wp_verify_nonce( $_POST['rangefinder_merch_price_nonce'], 'rangefinder_merch_price_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['rangefinder_merch_price'] ) ) {
		$dollars = (float) $_POST['rangefinder_merch_price'];
		update_post_meta( $post_id, '_merch_price_cents', (int) round( $dollars * 100 ) );
	}
}
add_action( 'save_post_merch_item', 'rangefinder_merch_price_save' );

/** ---------- Stripe settings (Settings API, admin-only) ---------- */

function rangefinder_stripe_default_options() {
	return array(
		'secret_key'      => '',
		'publishable_key' => '',
		'currency'        => 'usd',
		'success_url'     => home_url( '/?checkout=success' ),
		'cancel_url'      => home_url( '/?checkout=cancelled' ),
	);
}

function rangefinder_get_stripe_options() {
	$stored = get_option( 'rangefinder_stripe_options', array() );
	return wp_parse_args( $stored, rangefinder_stripe_default_options() );
}

function rangefinder_register_stripe_settings() {
	register_setting( 'rangefinder_stripe_settings_group', 'rangefinder_stripe_options', 'rangefinder_sanitize_stripe_options' );

	add_settings_section( 'rangefinder_stripe_main', 'Stripe API Keys', function () {
		echo '<p>Create keys in your <a href="https://dashboard.stripe.com/apikeys" target="_blank" rel="noopener noreferrer">Stripe Dashboard</a>. The secret key is never sent to the browser.</p>';
	}, 'rangefinder-stripe-settings' );

	add_settings_field( 'secret_key', 'Secret Key', 'rangefinder_field_stripe_secret_key', 'rangefinder-stripe-settings', 'rangefinder_stripe_main' );
	add_settings_field( 'publishable_key', 'Publishable Key', 'rangefinder_field_stripe_publishable_key', 'rangefinder-stripe-settings', 'rangefinder_stripe_main' );
	add_settings_field( 'currency', 'Currency Code', 'rangefinder_field_stripe_currency', 'rangefinder-stripe-settings', 'rangefinder_stripe_main' );

	add_settings_section( 'rangefinder_stripe_urls', 'Checkout Redirects', '__return_false', 'rangefinder-stripe-settings' );

	add_settings_field( 'success_url', 'Success URL', 'rangefinder_field_stripe_success_url', 'rangefinder-stripe-settings', 'rangefinder_stripe_urls' );
	add_settings_field( 'cancel_url', 'Cancel URL', 'rangefinder_field_stripe_cancel_url', 'rangefinder-stripe-settings', 'rangefinder_stripe_urls' );
}
add_action( 'admin_init', 'rangefinder_register_stripe_settings' );

function rangefinder_sanitize_stripe_options( $input ) {
	$output = rangefinder_stripe_default_options();

	$output['secret_key']      = sanitize_text_field( $input['secret_key'] ?? '' );
	$output['publishable_key'] = sanitize_text_field( $input['publishable_key'] ?? '' );
	$output['currency']        = strtolower( preg_replace( '/[^a-zA-Z]/', '', $input['currency'] ?? 'usd' ) ) ?: 'usd';
	$output['success_url']     = esc_url_raw( $input['success_url'] ?? $output['success_url'] );
	$output['cancel_url']      = esc_url_raw( $input['cancel_url'] ?? $output['cancel_url'] );

	return $output;
}

function rangefinder_field_stripe_secret_key() {
	$options = rangefinder_get_stripe_options();
	echo '<input type="password" autocomplete="off" style="width:100%;max-width:420px;" name="rangefinder_stripe_options[secret_key]" value="' . esc_attr( $options['secret_key'] ) . '" placeholder="sk_live_... or sk_test_..." />';
}

function rangefinder_field_stripe_publishable_key() {
	$options = rangefinder_get_stripe_options();
	echo '<input type="text" style="width:100%;max-width:420px;" name="rangefinder_stripe_options[publishable_key]" value="' . esc_attr( $options['publishable_key'] ) . '" placeholder="pk_live_... or pk_test_..." />';
}

function rangefinder_field_stripe_currency() {
	$options = rangefinder_get_stripe_options();
	echo '<input type="text" maxlength="3" style="width:80px;" name="rangefinder_stripe_options[currency]" value="' . esc_attr( $options['currency'] ) . '" />';
}

function rangefinder_field_stripe_success_url() {
	$options = rangefinder_get_stripe_options();
	echo '<input type="url" style="width:100%;max-width:420px;" name="rangefinder_stripe_options[success_url]" value="' . esc_attr( $options['success_url'] ) . '" />';
}

function rangefinder_field_stripe_cancel_url() {
	$options = rangefinder_get_stripe_options();
	echo '<input type="url" style="width:100%;max-width:420px;" name="rangefinder_stripe_options[cancel_url]" value="' . esc_attr( $options['cancel_url'] ) . '" />';
}

function rangefinder_add_stripe_settings_page() {
	add_options_page(
		'Stripe & Merchandise',
		'Stripe & Merchandise',
		'manage_options',
		'rangefinder-stripe-settings',
		'rangefinder_render_stripe_settings_page'
	);
}
add_action( 'admin_menu', 'rangefinder_add_stripe_settings_page' );

function rangefinder_render_stripe_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1>Stripe &amp; Merchandise</h1>
		<p>Staff-only controls. Keys are stored in the WordPress database and are only read server-side.</p>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'rangefinder_stripe_settings_group' );
			do_settings_sections( 'rangefinder-stripe-settings' );
			submit_button( 'Save Changes' );
			?>
		</form>
	</div>
	<?php
}

/** ---------- Checkout Session REST route (calls Stripe's HTTP API directly, no SDK required) ---------- */

function rangefinder_register_checkout_route() {
	register_rest_route( 'rangefinder/v1', '/checkout', array(
		'methods'             => 'POST',
		'callback'            => 'rangefinder_create_checkout_session',
		'permission_callback' => '__return_true',
		'args'                => array(
			'item_id' => array(
				'required'          => true,
				'validate_callback' => function ( $value ) {
					return is_numeric( $value );
				},
			),
		),
	) );
}
add_action( 'rest_api_init', 'rangefinder_register_checkout_route' );

function rangefinder_create_checkout_session( WP_REST_Request $request ) {
	$item_id = (int) $request->get_param( 'item_id' );
	$item    = get_post( $item_id );

	if ( ! $item || 'merch_item' !== $item->post_type || 'publish' !== $item->post_status ) {
		return new WP_Error( 'invalid_item', 'That merchandise item is not available.', array( 'status' => 404 ) );
	}

	$price_cents = (int) get_post_meta( $item_id, '_merch_price_cents', true );
	if ( $price_cents <= 0 ) {
		return new WP_Error( 'invalid_price', 'This item does not have a valid price set.', array( 'status' => 400 ) );
	}

	$stripe = rangefinder_get_stripe_options();
	if ( empty( $stripe['secret_key'] ) ) {
		return new WP_Error( 'stripe_not_configured', 'Stripe is not configured yet. Add API keys under Settings > Stripe & Merchandise.', array( 'status' => 500 ) );
	}

	$body = array(
		'mode'                            => 'payment',
		'success_url'                     => add_query_arg( 'session_id', '{CHECKOUT_SESSION_ID}', $stripe['success_url'] ),
		'cancel_url'                      => $stripe['cancel_url'],
		'line_items'                      => array(
			array(
				'quantity'   => 1,
				'price_data' => array(
					'currency'     => $stripe['currency'],
					'unit_amount'  => $price_cents,
					'product_data' => array(
						'name' => get_the_title( $item_id ),
					),
				),
			),
		),
	);

	$response = wp_remote_post( 'https://api.stripe.com/v1/checkout/sessions', array(
		'headers' => array(
			'Authorization' => 'Bearer ' . $stripe['secret_key'],
			'Content-Type'  => 'application/x-www-form-urlencoded',
		),
		'body'    => rangefinder_flatten_stripe_params( $body ),
		'timeout' => 15,
	) );

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'stripe_request_failed', $response->get_error_message(), array( 'status' => 502 ) );
	}

	$code = wp_remote_retrieve_response_code( $response );
	$data = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( $code >= 400 || empty( $data['url'] ) ) {
		$message = $data['error']['message'] ?? 'Stripe checkout session could not be created.';
		return new WP_Error( 'stripe_error', $message, array( 'status' => 502 ) );
	}

	return rest_ensure_response( array( 'checkout_url' => $data['url'] ) );
}

/**
 * Stripe's API expects PHP-style bracketed form fields for nested arrays
 * (e.g. line_items[0][price_data][currency]); this flattens our nested
 * array into that format for a standard x-www-form-urlencoded POST body.
 */
function rangefinder_flatten_stripe_params( $params, $prefix = '' ) {
	$flat = array();
	foreach ( $params as $key => $value ) {
		$field = $prefix ? "{$prefix}[{$key}]" : $key;
		if ( is_array( $value ) ) {
			$flat = array_merge( $flat, rangefinder_flatten_stripe_params( $value, $field ) );
		} else {
			$flat[ $field ] = $value;
		}
	}
	return $flat;
}
