<?php
/**
 * Archive or search results without an Elementor archive template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<main id="content" class="site-main">
	<?php if ( masterfold_show_page_title() ) : ?>
		<div class="page-header">
			<?php
			if ( is_search() ) {
				echo '<h1 class="entry-title">' . esc_html__( 'Search results for: ', 'masterfold' ) . '<span>' . esc_html( get_search_query() ) . '</span></h1>';
			} else {
				the_archive_title( '<h1 class="entry-title">', '</h1>' );
				the_archive_description( '<p class="archive-description">', '</p>' );
			}
			?>
		</div>
	<?php endif; ?>
	<div class="page-content">
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				echo '<article class="post">';
				printf( '<h2 class="entry-title"><a href="%s">%s</a></h2>', esc_url( get_permalink() ), wp_kses_post( get_the_title() ) );
				if ( has_post_thumbnail() ) {
					printf( '<a href="%s">%s</a>', esc_url( get_permalink() ), get_the_post_thumbnail( null, 'large' ) );
				}
				the_excerpt();
				echo '</article>';
			}
		} elseif ( is_search() ) {
			echo '<p>' . esc_html__( 'It seems we can\'t find what you\'re looking for.', 'masterfold' ) . '</p>';
		}
		?>
	</div>
	<?php
	the_posts_pagination(
		array(
			'prev_text' => '<span class="meta-nav">&larr;</span> ' . esc_html__( 'Previous', 'masterfold' ),
			'next_text' => esc_html__( 'Next', 'masterfold' ) . ' <span class="meta-nav">&rarr;</span>',
		)
	);
	?>
</main>
