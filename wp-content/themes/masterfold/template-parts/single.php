<?php
/**
 * A page or post not designed in Elementor (e.g. My Account, Wishlist).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

while ( have_posts() ) :
	the_post();
	?>
	<main id="content" <?php post_class( 'site-main' ); ?>>
		<?php if ( masterfold_show_page_title() ) : ?>
			<div class="page-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			</div>
		<?php endif; ?>
		<div class="page-content">
			<?php
			the_content();
			wp_link_pages();
			if ( has_tag() ) {
				echo '<div class="post-tags">';
				the_tags( '<span class="tag-links">' . esc_html__( 'Tagged ', 'masterfold' ), ', ', '</span>' );
				echo '</div>';
			}
			?>
		</div>
		<?php comments_template(); ?>
	</main>
	<?php
endwhile;
