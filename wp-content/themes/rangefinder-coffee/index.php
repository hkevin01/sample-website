<?php
/**
 * Fallback template (blog posts index / default loop).
 */
get_header();
?>
<main class="main-layout" id="main-content" style="grid-template-columns: 1fr;">
	<section class="card" style="grid-column: 1 / -1;">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) : the_post();
				?>
				<article <?php post_class(); ?>>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				</article>
				<?php
			endwhile;
			the_posts_navigation();
		else :
			?>
			<p>Nothing found.</p>
			<?php
		endif;
		?>
	</section>
</main>
<?php get_footer(); ?>
