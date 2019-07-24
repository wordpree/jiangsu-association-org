<?php
/*
  Template Name: Left Sidebar Page
 * This is the template that displays the contents in full Width
 *
 * @package Charity Review
 */

get_header();
// Boxed or Fullwidth
$boxedornot = charity_review_boxedornot();
?>

	<?php  charity_review_breadcrumb(); ?>

	<?php if ($boxedornot == 'fullwidth') {?>
		<!-- Start the container. If full width layout is selected in the Customizer.-->
        <div class="container full-width-container">
    <?php }?>

	<div id="primary" class="content-area pull-right">
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

		</main><!-- #main -->
	</div><!-- #primary -->

	<div id="secondary" class="widget-area clearfix left-sidebar" role="complementary">
		<?php get_sidebar(); ?>
	</div>

	<?php if ($boxedornot == 'fullwidth') {?>
        </div>
		<!-- End the container -->
    <?php }?>

<?php get_footer(); ?>