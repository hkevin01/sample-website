	<!-- 7. BOTTOM FOOTER -->
	<footer class="footer" id="footer">
		<div class="footer-container">

			<div class="footer-section">
				<h4>Contact Us</h4>
				<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
					<ul class="footer-links"><?php dynamic_sidebar( 'footer-1' ); ?></ul>
				<?php else : ?>
					<ul class="footer-links">
						<li>Direct Line: <a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', get_theme_mod( 'contact_phone', '' ) ) ); ?>"><?php echo esc_html( get_theme_mod( 'contact_phone', '(304) 555-0199' ) ); ?></a></li>
						<li>Inbox: <a href="mailto:<?php echo esc_attr( get_theme_mod( 'contact_email', 'rangefindercoffee@gmail.com' ) ); ?>"><?php echo esc_html( get_theme_mod( 'contact_email', 'rangefindercoffee@gmail.com' ) ); ?></a></li>
						<li><?php echo esc_html( get_theme_mod( 'contact_address', 'Fayetteville, WV, 25840' ) ); ?></li>
					</ul>
				<?php endif; ?>
			</div>

			<div class="footer-section">
				<h4>Career Hub</h4>
				<?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
					<ul class="footer-links"><?php dynamic_sidebar( 'footer-2' ); ?></ul>
				<?php else : ?>
					<ul class="footer-links">
						<li><a href="<?php echo esc_url( get_theme_mod( 'career_link', '#' ) ); ?>">Barista Openings</a></li>
						<li><a href="<?php echo esc_url( get_theme_mod( 'application_link', '#' ) ); ?>">Application Portal</a></li>
					</ul>
				<?php endif; ?>
			</div>

			<div class="footer-section">
				<h4>Internal Node</h4>
				<?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
					<ul class="footer-links"><?php dynamic_sidebar( 'footer-3' ); ?></ul>
				<?php else : ?>
					<ul class="footer-links">
						<li><a href="<?php echo esc_url( wp_login_url() ); ?>">Staff Sign-In Dashboard</a></li>
						<li><a href="<?php echo esc_url( home_url( '/?nojs=1' ) ); ?>">No-JS Legacy View Mode</a></li>
					</ul>
				<?php endif; ?>
			</div>

			<div class="footer-section">
				<h4>Legal / Policy</h4>
				<?php if ( is_active_sidebar( 'footer-4' ) ) : ?>
					<ul class="footer-links"><?php dynamic_sidebar( 'footer-4' ); ?></ul>
				<?php else : ?>
					<ul class="footer-links">
						<li><a href="<?php echo esc_url( get_theme_mod( 'privacy_link', '#' ) ); ?>">Privacy Practices</a></li>
						<li><a href="<?php echo esc_url( get_theme_mod( 'terms_link', '#' ) ); ?>">Usage Standards</a></li>
					</ul>
				<?php endif; ?>
			</div>

		</div>
	</footer>

	<?php wp_footer(); ?>
</body>
</html>
