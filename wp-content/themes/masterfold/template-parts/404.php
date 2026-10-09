<?php
/**
 * Not found, when no Elementor template covers it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<main id="content" class="site-main">
	<?php if ( masterfold_show_page_title() ) : ?>
		<div class="page-header">
			<h1 class="entry-title"><?php esc_html_e( 'The page can&rsquo;t be found.', 'masterfold' ); ?></h1>
		</div>
	<?php endif; ?>
	<div class="page-content">
		<p><?php esc_html_e( 'It looks like nothing was found at this location.', 'masterfold' ); ?></p>
	</div>
</main>
