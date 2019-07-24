<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Charity Review
 */

get_header();
// Boxed or Fullwidth
$boxedornot = charity_review_boxedornot();
?>

	<?php charity_review_breadcrumb(); ?>
	<!-- End the breadcrumb -->

	<?php if ($boxedornot == 'fullwidth') {?>
		<!-- Start the container. If full width layout is selected in the Customizer.-->
        <div class="container full-width-container">
    <?php }?>

	<div id="primary" class="content-area">

		<main id="main" class="site-main" role="main">

			<?php
				if( have_posts() ) :
					while ( have_posts() ) : the_post();
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

					<?php
				endwhile;

			endif; ?>

		</main>
		<!-- End the #main -->
	</div>
	<!-- End the #primary -->


	<div id="secondary" class="widget-area clearfix right-sidebar" role="complementary">
		<?php get_sidebar(); ?>
	</div>


	<?php if ($boxedornot == 'fullwidth') {?>
        </div>
		<!-- End the container -->
    <?php }?>

<?php get_footer(); ?>