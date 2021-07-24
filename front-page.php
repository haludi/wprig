<?php
/**
 * Render your site front page, whether the front page displays the blog posts index or a static page.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#front-page-display
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

use WP_Query;

get_header();

// Use grid layout if blog index is displayed.
wp_rig()->print_styles( 'wp-rig-content', 'wp-rig-front-page' );

?>
	<main id="primary" class="site-main">
		<?php

		global $post;

		$query_args = array(
			'post_type'      => 'page',
			'posts_per_page' => -1,
			'post_parent'    => $post->ID,
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
		);

		$parent = new WP_Query( $query_args );

		$items = array( $post );
		foreach ($parent->posts as $k => $v) {
			array_push($items, $v);
		}

		$originalPost = $post;
		foreach ( $items as &$current_post ) {
			$post = $current_post;
			get_template_part( 'template-parts/content/section', get_post_type() );
		}
		$post = $originalPost;

		?>
	</main><!-- #primary -->
<?php
get_footer();
