<?php
/**
 * Archive template (news/events archive, category/tag/date archives).
 */
get_header();
?>
<main class="main-layout" id="main-content" style="grid-template-columns: 1fr;">
	<section class="card" style="grid-column: 1 / -1;">
		<h2><?php the_archive_title(); ?></h2>
		<?php if ( have_posts() ) : ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<div class="news-post">
					<span class="news-date"><?php echo esc_html( get_the_date() ); ?></span>
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<?php the_excerpt(); ?>
				</div>
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
