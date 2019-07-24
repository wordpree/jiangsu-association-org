<?php
/**
 * The template for displaying single content.
 *
 * @package Charity Review
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-content">
		<?php get_template_part( 'template-parts/content', get_post_format() ); ?>
	</div>
    <!-- End Entry-content -->

</article>
<!-- End Article Post -->