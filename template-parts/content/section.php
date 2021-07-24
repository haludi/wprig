<?php
/**
 * Template part for displaying a single page session
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<section id="<?php echo get_post()->post_name; ?>" <?php post_class( 'entry' ); ?>>
	<?php
	get_template_part( 'template-parts/content/section_header', get_post_type() );

	get_template_part( 'template-parts/content/entry_content', get_post_type() );
	?>
</section><!-- #post-<?php the_ID(); ?> -->
