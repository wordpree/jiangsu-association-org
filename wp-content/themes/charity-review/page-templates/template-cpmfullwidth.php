<?php
/*
  Template Name: Full Width Page
 * This is the template that displays the contents in full Width
 *
 * @package Charity
 */

get_header();
// Boxed or Fullwidth
$boxedornot = charity_review_boxedornot();
?>

	<?php charity_review_breadcrumb(); ?>
	<!-- End the Breadcrumb -->

	<?php if ($boxedornot == 'fullwidth') {?>
		<!-- Start the container -->
        <div class="container full-width-container">
    <?php }?>

	<div class="content-area full-width-posts">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>
				<?php
				if ( has_post_thumbnail() ) {
					// check if the post has a Post Thumbnail assigned to it.
				?>
					<div class="featured-image">
			    			<div class="media-wrap ">
							<?php the_post_thumbnail(); ?>
						</div>
					</div>
				<?php } ?>

				<?php get_template_part( 'template-parts/content', 'page' ); ?>

			<?php endwhile; // end of the loop. ?>

		</main>
		<!-- End the #main -->
	</div>
	<!-- End the #primary -->

	<?php if ($boxedornot == 'fullwidth') {?>
        </div>
        <!-- End the Container -->
    <?php }?>

<?php get_footer(); ?>