<?php
/**
 * Comments template (list + reply form), only loaded when comments are open.
 */
defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>
<div class="card" id="comments">
	<?php if ( have_comments() ) : ?>
		<h2><?php comments_number( 'No Comments', '1 Comment', '% Comments' ); ?></h2>
		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'      => 'ol',
				'short_ping' => true,
			) );
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php if ( comments_open() ) : ?>
		<?php comment_form(); ?>
	<?php else : ?>
		<p>Comments are closed.</p>
	<?php endif; ?>
</div>
