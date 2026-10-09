<?php
/**
 * Mobile menu: menu location, assets and Elementor widget.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'after_setup_theme',
	function () {
		register_nav_menus( array( 'mf-mobile' => __( 'Mobile Menu', 'masterfold' ) ) );
	},
	20
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_register_style( 'mf-mobile-menu', MF_CORE_URL . 'assets/css/mobile-menu.css', array(), MF_CORE_VERSION );
		wp_register_script( 'mf-mobile-menu', MF_CORE_URL . 'assets/js/mobile-menu.js', array(), MF_CORE_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}
);

add_action(
	'elementor/widgets/register',
	function ( $widgets_manager ) {
		require_once __DIR__ . '/class-mobile-menu-widget.php';
		$widgets_manager->register( new MF_Mobile_Menu_Widget() );
	}
);
