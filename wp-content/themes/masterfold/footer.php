<?php
/**
 * Site footer (Elementor footer template) and page end.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'elementor_theme_do_location' ) ) {
	elementor_theme_do_location( 'footer' );
}
wp_footer();
?>
</body>
</html>
