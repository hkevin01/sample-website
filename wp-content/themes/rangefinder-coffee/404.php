<?php
/**
 * 404 error template.
 */
get_header();
?>
<main class="main-layout" id="main-content" style="grid-template-columns: 1fr;">
	<section class="card" style="grid-column: 1 / -1; text-align:center;">
		<h2>Page Not Found</h2>
		<p>We couldn't find what you were looking for. Try the navigation above, or head back <a href="<?php echo esc_url( home_url( '/' ) ); ?>">home</a>.</p>
	</section>
</main>
<?php get_footer(); ?>
