<?php
/**
 * WP_Rig\WP_Rig\Nav_Menus\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Nav_Menus;

use WP_Rig\WP_Rig\Nav_Menus\Single_Page_Walker_Nav_Menu;
use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Post;
use WP_Query;
use function add_action;
use function add_filter;
use function register_nav_menus;
use function esc_html__;
use function has_nav_menu;
use function wp_nav_menu;

/**
 * Class for managing navigation menus.
 *
 * Exposes template tags:
 * * `wp_rig()->is_primary_nav_menu_active()`
 * * `wp_rig()->display_primary_nav_menu( array $args = array() )`
 * * `wp_rig()->display_login_logout_nav_menu( array $args = array() )`
 */
class Component implements Component_Interface, Templating_Component_Interface {

	const PRIMARY_NAV_MENU_SLUG      = 'primary';
	const LOGIN_LOGOUT_NAV_MENU_SLUG = 'login-logout';

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'nav_menus';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'after_setup_theme', array( $this, 'action_register_nav_menus' ) );
		add_filter( 'walker_nav_menu_start_el', array( $this, 'filter_primary_nav_menu_dropdown_symbol' ), 10, 4 );

		add_filter( 'wp_nav_menu_objects', array( $this, 'add_single_page_sections_to_menue' ), 10, 2 );
	}

	/**
	 * Add child pages as menu items for single page menue.
	 *
	 * @param array    $items .
	 * @param stdClass $args An object containing wp_nav_menu() arguments.
	 * @return array  Modified nav menu HTML.
	 */
	public function add_single_page_sections_to_menue( $items, $args ) {
		if ( ! ( isset( $args->single_page ) && $args->single_page ) ) {
			return $items;
		}

		global $wp;
		$url = home_url( $wp->request );

		global $post;
		$query_args = array(
			'post_type'      => 'page',
			'posts_per_page' => -1,
			'post_parent'    => $post->ID,
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
		);

		$parent = new WP_Query( $query_args );
		$single_page_sections = array( $post );
		foreach ($parent->posts as $k => $v) {
			array_push($single_page_sections, $v);
		}

		foreach ( $single_page_sections as &$current_post ) {
			$current_post->title = $current_post->post_title;
			$current_post->url   = $url . '#' . $current_post->post_name;
		}

		$items = array_merge( $single_page_sections, $items );
		return $items;
	}

	/**
	 * Apply bootstrap class to the <a> nav element.
	 *
	 * @param array $atts The HTML attributes applied to the menu item's <a> element.
	 * @return array  Modified nav menu HTML.
	 */
	public function add_class_to_all_menu_anchors( $atts ) {
		$atts['class'] = 'nav-link';
		return $atts;
	}

	/**
	 * Apply bootstrap class to the <li> nav element.
	 *
	 * @return array  Modified nav menu HTML.
	 */
	public function filter_handler() : array {
		return array( 'nav-item' );
	}
	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `wp_rig()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return array(
			'is_primary_nav_menu_active'    => array( $this, 'is_primary_nav_menu_active' ),
			'display_primary_nav_menu'      => array( $this, 'display_primary_nav_menu' ),
			'display_login_logout_nav_menu' => array( $this, 'display_login_logout_nav_menu' ),
		);
	}

	/**
	 * Registers the navigation menus.
	 */
	public function action_register_nav_menus() {
		register_nav_menus(
			array(
				static::PRIMARY_NAV_MENU_SLUG      => esc_html__( 'Primary', 'wp-rig' ),
				static::LOGIN_LOGOUT_NAV_MENU_SLUG => esc_html__( 'LoginLogout', 'wp-rig' ),
			)
		);
	}

	/**
	 * Adds a dropdown symbol to nav menu items with children.
	 *
	 * Adds the dropdown markup after the menu link element,
	 * before the submenu.
	 *
	 * Javascript converts the symbol to a toggle button.
	 *
	 * @TODO:
	 * - This doesn't work for the page menu because it
	 *   doesn't have a similar filter. So the dropdown symbol
	 *   is only being added for page menus if JS is enabled.
	 *   Create a ticket to add to core?
	 *
	 * @param string  $item_output The menu item's starting HTML output.
	 * @param WP_Post $item        Menu item data object.
	 * @param int     $depth       Depth of menu item. Used for padding.
	 * @param object  $args        An object of wp_nav_menu() arguments.
	 * @return string Modified nav menu HTML.
	 */
	public function filter_primary_nav_menu_dropdown_symbol( string $item_output, WP_Post $item, int $depth, $args ) : string {

		// Only for our primary menu location.
		if ( empty( $args->theme_location ) || static::PRIMARY_NAV_MENU_SLUG !== $args->theme_location ) {
			return $item_output;
		}

		// Add the dropdown for items that have children.
		if ( ! empty( $item->classes ) && in_array( 'menu-item-has-children', $item->classes ) ) {
			return $item_output . '<span class="dropdown"><i class="dropdown-symbol"></i></span>';
		}

		return $item_output;
	}

	/**
	 * Checks whether the primary navigation menu is active.
	 *
	 * @return bool True if the primary navigation menu is active, false otherwise.
	 */
	public function is_primary_nav_menu_active() : bool {
		return (bool) has_nav_menu( static::PRIMARY_NAV_MENU_SLUG );
	}

	/**
	 * Displays the primary navigation menu.
	 *
	 * @param array $args Optional. Array of arguments. See `wp_nav_menu()` documentation for a list of supported
	 *                    arguments.
	 */
	public function display_primary_nav_menu( array $args = array() ) {
		if ( ! isset( $args['container'] ) ) {
			$args['container'] = '';
		}

		$args['walker'] = new Single_Page_Walker_Nav_Menu();

		$args['theme_location'] = static::PRIMARY_NAV_MENU_SLUG;

		wp_nav_menu( $args );
	}

	/**
	 * Displays the login logout navigation menu.
	 *
	 * @param array $args Optional. Array of arguments. See `wp_nav_menu()` documentation for a list of supported
	 *                    arguments.
	 */
	public function display_login_logout_nav_menu( array $args = array() ) {
		if ( ! isset( $args['container'] ) ) {
			$args['container'] = '';
		}

		$args['walker'] = new Single_Page_Walker_Nav_Menu();
		$args['child_items_wrap'] = '<div id="%1$s" class="dropdown-menu %2$s">%3$s</ul>';

		$args['theme_location'] = static::LOGIN_LOGOUT_NAV_MENU_SLUG;

		wp_nav_menu( $args );
	}
}
