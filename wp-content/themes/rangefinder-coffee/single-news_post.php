<?php
/**
 * Single News/Events post template.
 */
get_header();
?>
<main class="main-layout" id="main-content" style="grid-template-columns: 1fr;">
	<section class="card" style="grid-column: 1 / -1;">
		<?php
		while ( have_posts() ) : the_post();
			?>
			<span class="news-date"><?php echo esc_html( get_the_date() ); ?></span>
			<?php the_title( '<h2>', '</h2>' ); ?>
			<?php the_content(); ?>
			<?php
		endwhile;
		?>
	</section>
</main>
<?php get_footer(); ?>
