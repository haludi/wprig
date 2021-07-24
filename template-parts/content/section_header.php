<?php
/**
 * Template part for displaying a single page session's header
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<header class="entry-header">
	<?php
	get_template_part( 'template-parts/content/section_title', get_post_type() );
	?>
</header><!-- .entry-header -->
