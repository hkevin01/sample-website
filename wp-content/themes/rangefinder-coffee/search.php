<?php
/**
 * Search results template.
 */
get_header();
?>
<main class="main-layout" id="main-content" style="grid-template-columns: 1fr;">
	<section class="card" style="grid-column: 1 / -1;">
		<h2>
			<?php
			printf(
				/* translators: %s: search query */
				esc_html__( 'Search Results for: %s', 'rangefinder-coffee' ),
				'<span>' . esc_html( get_search_query() ) . '</span>'
			);
			?>
		</h2>
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class(); ?>>
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<?php the_excerpt(); ?>
				</article>
				<?php
			endwhile;
			the_posts_navigation();
		else :
			?>
			<p>No results found. Try a different search term.</p>
			<?php get_search_form(); ?>
			<?php
		endif;
		?>
	</section>
</main>
<?php get_footer(); ?>
