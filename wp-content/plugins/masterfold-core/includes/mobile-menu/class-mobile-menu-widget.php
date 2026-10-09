<?php
/**
 * Elementor widget: "Masterfold Mobile Menu".
 *
 * The sliding, multi-level mobile menu. Items come from Appearance → Menus
 * (default: "Mobile Menu"); every item that has children opens its own panel.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MF_Mobile_Menu_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'mf-mobile-menu';
	}

	public function get_title() {
		return __( 'Masterfold Mobile Menu', 'masterfold' );
	}

	public function get_icon() {
		return 'eicon-menu-bar';
	}

	public function get_categories() {
		return array( 'general' );
	}

	public function get_keywords() {
		return array( 'masterfold', 'menu', 'mobile', 'navigation', 'slide' );
	}

	public function get_style_depends(): array {
		return array( 'mf-mobile-menu' );
	}

	public function get_script_depends(): array {
		return array( 'mf-mobile-menu' );
	}

	protected function register_controls() {
		$menus = array();
		foreach ( wp_get_nav_menus() as $menu ) {
			$menus[ $menu->slug ] = $menu->name;
		}

		$this->start_controls_section( 'content', array( 'label' => __( 'Menu', 'masterfold' ) ) );

		$this->add_control(
			'menu',
			array(
				'label'       => __( 'Menu', 'masterfold' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => $menus,
				'default'     => 'mobile-menu',
				'description' => __( 'Edit the items in Appearance → Menus. Items with children open their own panel.', 'masterfold' ),
			)
		);

		$this->add_control(
			'back_label',
			array(
				'label'   => __( 'Back label on first-level panels', 'masterfold' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => 'ALL',
			)
		);

		$this->add_control(
			'open_icon',
			array(
				'label'   => __( 'Open icon', 'masterfold' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array( 'url' => content_url( 'uploads/menu-svgrepo-com.svg' ) ),
			)
		);

		$this->add_control(
			'close_icon',
			array(
				'label'   => __( 'Close icon', 'masterfold' ),
				'type'    => \Elementor\Controls_Manager::MEDIA,
				'default' => array( 'url' => content_url( 'uploads/cross-svgrepo-com.svg' ) ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Items of the chosen menu grouped by parent id.
	 */
	private function get_children_map( $menu_slug ) {
		$items = wp_get_nav_menu_items( $menu_slug );
		$map   = array();
		if ( $items ) {
			_wp_menu_item_classes_by_context( $items );
			foreach ( $items as $item ) {
				$map[ (int) $item->menu_item_parent ][] = $item;
			}
		}
		return $map;
	}

	private function arrow( $direction = 'right' ) {
		$d = 'right' === $direction ? 'M8.474 18.966L15.44 12 8.474 5.033' : 'M15.526 5.034L8.56 12l6.966 6.966';
		return '<svg viewBox="0 0 24 24" class="arrow-icon" aria-hidden="true" focusable="false"><path d="' . $d . '" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>';
	}

	private function close_button( $icon ) {
		return '<button class="menu-close" aria-label="' . esc_attr__( 'Close menu', 'masterfold' ) . '"><img src="' . esc_url( $icon ) . '" alt="" width="32" height="32" style="transition: opacity 0.2s ease;"></button>';
	}

	/**
	 * One panel (nav.menu-slide) per item that has children.
	 */
	private function render_panel( $panel_id, $parent_id, $map, $back, $settings, $is_main = false ) {
		$close = $this->close_button( $settings['close_icon']['url'] ?? '' );

		echo '<nav class="menu-slide ' . ( $is_main ? 'active' : 'hidden' ) . '" id="' . esc_attr( $panel_id ) . '"' . ( $is_main ? '' : ' aria-hidden="true"' ) . '>';
		echo '<div class="menu-header">';
		if ( $back ) {
			echo '<div class="menu-back" data-back="' . esc_attr( $back['id'] ) . '" tabindex="0" role="button" aria-label="' . esc_attr__( 'Go back', 'masterfold' ) . '">' . $this->arrow( 'left' ) . ' ' . esc_html( $back['label'] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			echo '<div></div>';
		}
		echo $close; // phpcs:ignore WordPress.Security.EscapeOutput
		echo '</div><ul>';

		$sub_panels = array();
		foreach ( $map[ $parent_id ] ?? array() as $item ) {
			$classes   = array_filter( (array) $item->classes, fn( $c ) => $c && 0 !== strpos( $c, 'menu-item' ) && 0 !== strpos( $c, 'current' ) );
			$has_child = ! empty( $map[ (int) $item->ID ] );
			if ( $has_child ) {
				$classes[]    = 'has-submenu';
				$sub_panels[] = $item;
			}
			$target = $item->target ? ' target="' . esc_attr( $item->target ) . '"' : '';

			echo '<li' . ( $classes ? ' class="' . esc_attr( implode( ' ', $classes ) ) . '"' : '' ) . '>';
			if ( $has_child ) {
				echo '<a href="#" data-submenu="' . esc_attr( $this->panel_id( $item ) ) . '">' . esc_html( $item->title ) . ' ' . $this->arrow() . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
			} else {
				$arrow = '_blank' === $item->target ? ' ' . $this->arrow() : '';
				echo '<a href="' . esc_url( $item->url ) . '"' . $target . '>' . esc_html( $item->title ) . $arrow . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput
			}
			echo '</li>';
		}
		echo '</ul></nav>';

		foreach ( $sub_panels as $item ) {
			$this->render_panel(
				$this->panel_id( $item ),
				(int) $item->ID,
				$map,
				array(
					'id'    => $panel_id,
					'label' => $is_main ? $settings['back_label'] : $this->panel_titles[ $panel_id ],
				),
				$settings
			);
		}
	}

	private $panel_titles = array();

	private $panel_ids = array();

	/**
	 * Panel ID for a menu item: its title as a slug (e.g. "reception"), like the
	 * original hand-coded menu. Links such as /products/#reception rely on the
	 * first element with that ID being a hidden menu panel, so the page itself
	 * does not jump when they open.
	 */
	private function panel_id( $item ) {
		if ( ! isset( $this->panel_ids[ $item->ID ] ) ) {
			$base = sanitize_title( $item->title );
			$id   = $base;
			$n    = 2;
			while ( in_array( $id, $this->panel_ids, true ) || 'menu-main' === $id ) {
				$id = $base . '-' . $n++;
			}
			$this->panel_ids[ $item->ID ] = $id;
		}
		return $this->panel_ids[ $item->ID ];
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$map      = $this->get_children_map( $settings['menu'] );
		if ( empty( $map[0] ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo esc_html__( 'Choose a menu with items.', 'masterfold' );
			}
			return;
		}

		$this->panel_ids = array();
		foreach ( $map as $items ) {
			foreach ( $items as $item ) {
				if ( ! empty( $map[ (int) $item->ID ] ) ) {
					$this->panel_titles[ $this->panel_id( $item ) ] = $item->title;
				}
			}
		}

		$main = 'menu-main';
		?>
		<div class="mf-mobile-menu-root" data-main="<?php echo esc_attr( $main ); ?>">
			<div class="custom-mobile-header">
				<div class="mobile-menu-toggle" tabindex="0" role="button" aria-label="<?php esc_attr_e( 'Open menu', 'masterfold' ); ?>" aria-expanded="false">
					<img src="<?php echo esc_url( $settings['open_icon']['url'] ?? '' ); ?>" alt="" width="32" height="32" style="transition: opacity 0.2s ease;">
				</div>
			</div>
			<div class="mobile-menu-overlay"></div>
			<div class="mobile-menu" aria-hidden="true" role="navigation" aria-label="<?php esc_attr_e( 'Mobile menu', 'masterfold' ); ?>">
				<?php $this->render_panel( $main, 0, $map, null, $settings, true ); ?>
			</div>
		</div>
		<?php
	}
}
