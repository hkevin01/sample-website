<?php
/**
 * Generic static page template.
 */
get_header();
?>
<main class="main-layout" id="main-content" style="grid-template-columns: 1fr;">
	<section class="card" style="grid-column: 1 / -1;">
		<?php
		while ( have_posts() ) : the_post();
			the_title( '<h2>', '</h2>' );
			the_content();
		endwhile;
		?>
	</section>
</main>
<?php get_footer(); ?>
