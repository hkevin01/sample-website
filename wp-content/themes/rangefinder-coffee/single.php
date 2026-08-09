<?php
/**
 * Generic single post template (fallback for post types without a dedicated template).
 */
get_header();
?>
<main class="main-layout" id="main-content" style="grid-template-columns: 1fr;">
	<section class="card" style="grid-column: 1 / -1;">
		<?php
		while ( have_posts() ) :
			the_post();
			the_title( '<h2>', '</h2>' );
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'large' );
			}
			the_content();
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		endwhile;
		?>
	</section>
</main>
<?php get_footer(); ?>
