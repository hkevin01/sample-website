<?php
/**
 * Café Dashboard: a single wp-admin screen that surfaces live status, content
 * counts, and quick links to every screen staff actually need day-to-day.
 */
defined( 'ABSPATH' ) || exit;

function rangefinder_add_dashboard_page() {
	add_menu_page(
		'Café Dashboard',
		'Café Dashboard',
		'manage_options',
		'rangefinder-dashboard',
		'rangefinder_render_dashboard_page',
		'dashicons-coffee',
		2
	);
}
add_action( 'admin_menu', 'rangefinder_add_dashboard_page' );

function rangefinder_dashboard_count( $post_type ) {
	$counts = wp_count_posts( $post_type );
	return isset( $counts->publish ) ? (int) $counts->publish : 0;
}

function rangefinder_render_dashboard_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$status  = rangefinder_get_status_data();
	$stripe  = rangefinder_get_stripe_options();
	$stripe_configured = ! empty( $stripe['secret_key'] ) && ! empty( $stripe['publishable_key'] );

	$sections = array(
		array(
			'icon'  => '☕',
			'title' => 'Menu Items',
			'desc'  => 'Add, edit, or remove top menu items and prices shown on the homepage.',
			'count' => rangefinder_dashboard_count( 'cafe_menu_item' ),
			'edit'  => admin_url( 'edit.php?post_type=cafe_menu_item' ),
			'add'   => admin_url( 'post-new.php?post_type=cafe_menu_item' ),
		),
		array(
			'icon'  => '🖼️',
			'title' => 'Gallery Images',
			'desc'  => 'Manage the homepage picture slider (set a featured image per slide).',
			'count' => rangefinder_dashboard_count( 'gallery_image' ),
			'edit'  => admin_url( 'edit.php?post_type=gallery_image' ),
			'add'   => admin_url( 'post-new.php?post_type=gallery_image' ),
		),
		array(
			'icon'  => '📰',
			'title' => 'News / Events',
			'desc'  => 'Post announcements and events shown in the homepage news feed.',
			'count' => rangefinder_dashboard_count( 'news_post' ),
			'edit'  => admin_url( 'edit.php?post_type=news_post' ),
			'add'   => admin_url( 'post-new.php?post_type=news_post' ),
		),
		array(
			'icon'  => '🛒',
			'title' => 'Merchandise',
			'desc'  => 'Manage items sold via Stripe Checkout on the homepage.',
			'count' => rangefinder_dashboard_count( 'merch_item' ),
			'edit'  => admin_url( 'edit.php?post_type=merch_item' ),
			'add'   => admin_url( 'post-new.php?post_type=merch_item' ),
		),
	);
	?>
	<div class="wrap">
		<h1>Café Dashboard</h1>

		<h2 class="title">Live Status</h2>
		<table class="widefat striped" style="max-width:700px;">
			<tbody>
				<tr>
					<td><strong>Current Badge</strong></td>
					<td><?php echo esc_html( $status['label'] ); ?></td>
				</tr>
				<tr>
					<td><strong>Stripe Checkout</strong></td>
					<td>
						<?php if ( $stripe_configured ) : ?>
							✅ Configured
						<?php else : ?>
							⚠️ Not configured — <a href="<?php echo esc_url( admin_url( 'options-general.php?page=rangefinder-stripe-settings' ) ); ?>">add API keys</a>
						<?php endif; ?>
					</td>
				</tr>
			</tbody>
		</table>
		<p>
			<a href="<?php echo esc_url( admin_url( 'options-general.php?page=rangefinder-settings' ) ); ?>" class="button button-primary">Edit Hours &amp; Status</a>
			<a href="<?php echo esc_url( admin_url( 'options-general.php?page=rangefinder-stripe-settings' ) ); ?>" class="button">Edit Stripe &amp; Merchandise Settings</a>
			<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button">Edit Hero / Story / Contact Info</a>
		</p>

		<h2 class="title">Content</h2>
		<table class="widefat striped" style="max-width:900px;">
			<thead>
				<tr>
					<th>Section</th>
					<th>What You Can Do</th>
					<th>Published</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $sections as $section ) : ?>
					<tr>
						<td><?php echo esc_html( $section['icon'] ); ?> <strong><?php echo esc_html( $section['title'] ); ?></strong></td>
						<td><?php echo esc_html( $section['desc'] ); ?></td>
						<td><?php echo (int) $section['count']; ?></td>
						<td>
							<a href="<?php echo esc_url( $section['edit'] ); ?>">Manage</a>
							&middot;
							<a href="<?php echo esc_url( $section['add'] ); ?>">Add New</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}
