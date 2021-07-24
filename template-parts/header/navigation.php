<?php
/**
 * Template part for displaying the header navigation menu
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

if ( ! wp_rig()->is_primary_nav_menu_active() ) {
	return;
}

?>

<nav id="site-navigation" class="fixed-top navbar navbar-expand-lg navbar-light bg-light" aria-label="<?php esc_attr_e( 'Main menu', 'wp-rig' ); ?>"
	<?php
	if ( wp_rig()->is_amp() ) {
		?>
		[class]=" siteNavigationMenu.expanded ? 'main-navigation nav--toggle-sub nav--toggle-small nav--toggled-on' : 'main-navigation nav--toggle-sub nav--toggle-small' "
		<?php
	}
	?>
>
	<?php
	if ( wp_rig()->is_amp() ) {
		?>
		<amp-state id="siteNavigationMenu">
			<script type="application/json">
				{
					"expanded": false
				}
			</script>
		</amp-state>
		<?php
	}
	?>
	<?php the_custom_logo(); ?>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-label="<?php esc_attr_e( 'Open menu', 'wp-rig' ); ?>"  aria-expanded="false"
		<?php
		if ( wp_rig()->is_amp() ) {
			?>
			on="tap:AMP.setState( { siteNavigationMenu: { expanded: ! siteNavigationMenu.expanded } } )"
			[aria-expanded]="siteNavigationMenu.expanded ? 'true' : 'false'"
			<?php
		}
		?>
	>
		<span class="navbar-toggler-icon"></span>

	</button>

	<div class="collapse navbar-collapse" id="navbarNavDropdown">
		<?php
		wp_rig()->display_primary_nav_menu(
			array(
				'menu_id'         => 'primary-menu',
				'menu_class'      => 'navbar-nav primary',
				'single_page'     => true,
			)
		);
		wp_rig()->display_login_logout_nav_menu(
			array(
				'menu_id'         => 'login-logout-menu',
				'menu_class'      => 'navbar-nav login-logout-nav',
			)
		);
		?>
	</div>
</nav>
