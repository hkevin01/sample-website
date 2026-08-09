<?php
/**
 * Main three-column layout: status/menu, gallery/story/news, map/contact.
 */
get_header();

$status  = rangefinder_get_status_data();
$options = rangefinder_get_options();

$checkout_state = isset( $_GET['checkout'] ) ? sanitize_key( wp_unslash( $_GET['checkout'] ) ) : '';
?>

<?php if ( 'success' === $checkout_state ) : ?>
	<p class="card" style="max-width:1400px;margin:20px auto 0;border-left:4px solid var(--status-open);">Thank you! Your order was received &mdash; a receipt has been emailed to you by Stripe.</p>
<?php elseif ( 'cancelled' === $checkout_state ) : ?>
	<p class="card" style="max-width:1400px;margin:20px auto 0;border-left:4px solid var(--status-closed);">Checkout was cancelled. Your card was not charged.</p>
<?php endif; ?>

<main class="main-layout" id="main-content">

	<!-- LEFT BOX: CURRENT STATUS & TOP MENU ITEMS -->
	<section class="left-box" aria-label="Business Status and Highlights">
		<div class="card">
			<h2>Operational Status</h2>
			<div id="live-status" class="status-badge <?php echo esc_attr( $status['class'] ); ?>"><?php echo esc_html( $status['label'] ); ?></div>
			<ul class="hours-list">
				<li><span>Mon - Fri:</span> <strong><?php echo esc_html( $options['hours_weekday_label'] ); ?></strong></li>
				<li><span>Sat - Sun:</span> <strong><?php echo esc_html( $options['hours_weekend_label'] ); ?></strong></li>
			</ul>
		</div>

		<div class="card" id="menu-highlights">
			<h2>Top Menu Items</h2>
			<ul class="flat-menu-list">
				<?php
				$menu_items = new WP_Query( array(
					'post_type'      => 'cafe_menu_item',
					'posts_per_page' => 6,
					'orderby'        => 'menu_order date',
					'order'          => 'ASC',
				) );
				if ( $menu_items->have_posts() ) :
					while ( $menu_items->have_posts() ) : $menu_items->the_post();
						$price = get_post_meta( get_the_ID(), '_menu_item_price', true );
						?>
						<li class="flat-menu-item">
							<h3><span><?php the_title(); ?></span> <span><?php echo esc_html( $price ); ?></span></h3>
							<p><?php the_excerpt(); ?></p>
						</li>
						<?php
					endwhile;
					wp_reset_postdata();
				else :
					?>
					<li class="flat-menu-item">
						<h3><span>Espresso / Cortado</span> <span>$3.50</span></h3>
						<p>Precisely dialed-in single-origin beans sourced directly from seasonal roasters.</p>
					</li>
					<li class="flat-menu-item">
						<h3><span>Nitro Cold Brew</span> <span>$4.50</span></h3>
						<p>Infused with nitrogen for a rich, creamy head. Poured straight from our bar taps.</p>
					</li>
					<li class="flat-menu-item">
						<h3><span>Seasonal Cardamom Latte</span> <span>$5.25</span></h3>
						<p>House-made organic cardamom syrup paired with fresh steamed local milk.</p>
					</li>
					<li class="flat-menu-item">
						<h3><span>Local Artisan Pastries</span> <span>Prices Vary</span></h3>
						<p>Handcrafted baked goods delivered fresh every morning by regional bakers.</p>
					</li>
					<?php
				endif;
				?>
			</ul>
			<p style="margin-top:10px;"><em>Manage these under <strong>Menu Items</strong> in the WordPress admin.</em></p>
		</div>

		<div class="card" id="services">
			<h2>Services</h2>
			<ul class="flat-menu-list">
				<li class="flat-menu-item"><h3><span>Free Wi-Fi</span></h3></li>
				<li class="flat-menu-item"><h3><span>Local Art Wall</span></h3></li>
				<li class="flat-menu-item"><h3><span>Catering &amp; Events</span></h3></li>
			</ul>
		</div>

		<div class="card" id="shop">
			<h2>Merchandise</h2>
			<ul class="flat-menu-list">
				<?php
				$merch = new WP_Query( array(
					'post_type'      => 'merch_item',
					'posts_per_page' => 10,
					'orderby'        => 'menu_order date',
					'order'          => 'ASC',
				) );
				if ( $merch->have_posts() ) :
					while ( $merch->have_posts() ) : $merch->the_post();
						$price_cents = (int) get_post_meta( get_the_ID(), '_merch_price_cents', true );
						if ( $price_cents <= 0 ) {
							continue;
						}
						?>
						<li class="flat-menu-item merch-item">
							<h3><span><?php the_title(); ?></span> <span>$<?php echo esc_html( number_format( $price_cents / 100, 2 ) ); ?></span></h3>
							<p><?php the_excerpt(); ?></p>
							<button type="button" class="toolbar-btn buy-now-btn" data-item-id="<?php echo esc_attr( get_the_ID() ); ?>">Buy Now</button>
						</li>
						<?php
					endwhile;
					wp_reset_postdata();
				else :
					?>
					<li class="flat-menu-item"><p><em>No merchandise listed yet. Add items under <strong>Merchandise</strong> in the WordPress admin and configure Stripe under Settings &gt; Stripe &amp; Merchandise.</em></p></li>
					<?php
				endif;
				?>
			</ul>
			<p id="checkout-error" role="alert" style="color:var(--status-closed);display:none;margin-top:10px;"></p>
		</div>
	</section>

	<!-- MIDDLE BOX: GALLERY SLIDER, STORY, NEWS -->
	<section class="middle-box" aria-label="Gallery, Story and News">
		<div class="card" id="gallery">
			<h2>Gallery</h2>
			<div class="slider-container">
				<div class="slider-track" id="slider-track">
					<?php
					$gallery = new WP_Query( array(
						'post_type'      => 'gallery_image',
						'posts_per_page' => 10,
						'orderby'        => 'menu_order date',
						'order'          => 'ASC',
					) );
					if ( $gallery->have_posts() ) :
						while ( $gallery->have_posts() ) : $gallery->the_post();
							$img = get_the_post_thumbnail_url( get_the_ID(), 'large' );
							?>
							<div class="slide" style="background-image:url('<?php echo esc_url( $img ); ?>');">
								<span><?php the_title(); ?></span>
							</div>
							<?php
						endwhile;
						wp_reset_postdata();
					else :
						?>
						<div class="slide" style="background-color:#5c4033;"><span>Cozy Fayetteville storefront</span></div>
						<div class="slide" style="background-color:#4a3728;"><span>Fresh espresso pull</span></div>
						<div class="slide" style="background-color:#6f4e37;"><span>Weekend crowd on the patio</span></div>
						<?php
					endif;
					?>
				</div>
				<button class="slider-btn slider-prev" id="slide-prev" aria-label="Previous slide">&#10094;</button>
				<button class="slider-btn slider-next" id="slide-next" aria-label="Next slide">&#10095;</button>
			</div>
			<p style="margin-top:10px;"><em>Manage slides under <strong>Gallery Images</strong> in the WordPress admin (set a featured image per slide).</em></p>
		</div>

		<div class="card">
			<h2>Our Story</h2>
			<p class="cozy-summary"><?php echo esc_html( get_theme_mod( 'cozy_summary' ) ); ?></p>
		</div>

		<div class="card" id="news">
			<h2>News &amp; Events</h2>
			<?php
			$news = new WP_Query( array(
				'post_type'      => 'news_post',
				'posts_per_page' => 5,
				'orderby'        => 'date',
				'order'          => 'DESC',
			) );
			if ( $news->have_posts() ) :
				while ( $news->have_posts() ) : $news->the_post();
					?>
					<div class="news-post">
						<span class="news-date"><?php echo esc_html( get_the_date() ); ?></span>
						<p><?php the_excerpt(); ?></p>
					</div>
					<?php
				endwhile;
				wp_reset_postdata();
			else :
				?>
				<div class="news-post">
					<span class="news-date">August 2, 2026</span>
					<p>Live acoustic music returns every Friday evening on the patio starting this month.</p>
				</div>
				<div class="news-post">
					<span class="news-date">July 18, 2026</span>
					<p>We're now roasting a rotating single-origin guest bean sourced from Central America.</p>
				</div>
				<?php
			endif;
			?>
		</div>
	</section>

	<!-- RIGHT BOX: MAP & CONTACT -->
	<section class="right-box" aria-label="Location and Contact">
		<div class="card">
			<h2>Find Us</h2>
			<iframe class="map-wrapper" title="Range Finder Coffee location map"
				src="https://www.openstreetmap.org/export/embed.html?bbox=-81.105%2C38.055%2C-81.095%2C38.065&amp;layer=mapnik"
				loading="lazy"></iframe>
		</div>
		<div class="card">
			<h2>Contact</h2>
			<ul class="contact-list">
				<li>Direct Line: <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', get_theme_mod( 'contact_phone', '' ) ) ); ?>"><?php echo esc_html( get_theme_mod( 'contact_phone', '(304) 555-0199' ) ); ?></a></li>
				<li>Inbox: <a href="mailto:<?php echo esc_attr( get_theme_mod( 'contact_email', 'rangefindercoffee@gmail.com' ) ); ?>"><?php echo esc_html( get_theme_mod( 'contact_email', 'rangefindercoffee@gmail.com' ) ); ?></a></li>
				<li><?php echo esc_html( get_theme_mod( 'contact_address', 'Fayetteville, WV, 25840' ) ); ?></li>
			</ul>
		</div>
	</section>

</main>

<?php get_footer(); ?>
