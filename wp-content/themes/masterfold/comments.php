<?php
/**
 * Comments list and form. Prints nothing when comments are closed and there
 * are none, so Elementor pages stay clean.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! post_type_supports( get_post_type(), 'comments' ) ) {
	return;
}

if ( ! have_comments() && ! comments_open() ) {
	return;
}

if ( comments_open() && get_option( 'thread_comments' ) ) {
	wp_enqueue_script( 'comment-reply' );
}
?>
<section id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="title-comments">
			<?php
			$masterfold_count = (int) get_comments_number();
			printf(
				/* translators: %s: Number of comments. */
				esc_html( _n( 'One Response', '%s Responses', $masterfold_count, 'masterfold' ) ),
				esc_html( number_format_i18n( $masterfold_count ) )
			);
			?>
		</h2>
		<?php the_comments_navigation(); ?>
		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 42,
				)
			);
			?>
		</ol>
		<?php the_comments_navigation(); ?>
	<?php endif; ?>
	<?php
	comment_form(
		array(
			'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h2>',
		)
	);
	?>
</section>
