<?php
/**
 * The template for displaying all single posts.
 *
 * @package Charity Review
 */

get_header();
// Boxed or Fullwidth
$boxedornot = charity_review_boxedornot();
?>

	<?php charity_review_breadcrumb(); ?>
	<!-- End the Breadcrumb -->

	<?php
		// Layout picker to be controlled from Customizer
		$sidebar_layout = get_theme_mod('layout_picker');

			if( $sidebar_layout == 1 ){
				$sidebar_class = 'no-sidebar';
			}
			if( $sidebar_layout == 2){
				$sidebar_class = 'pull-right';
			}
			if( $sidebar_layout == 3){
				$sidebar_class = '';
			}

	?>

	<?php if ($boxedornot == 'fullwidth') {?>
		<!-- Start the container -->
        <div class="container full-width-container">
    <?php }?>
	<?php if(!empty($sidebar_layout)){ ?>
		<div id="primary" class="content-area <?php echo $sidebar_class; ?>">
	<?php } else { ?>
		<div id="primary" class="content-area">
	<?php } ?>
			<main id="main" class="site-main" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'template-parts/content', 'single' ); ?>

					<?php charity_review_post_navigation(); ?>

					<?php
						// If comments are open or we have at least one comment, load up the comment template
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;
					?>

				<?php endwhile; // end of the loop. ?>

			</main>
			<!-- End #main -->
		</div>
		<!-- End #primary -->

	<?php
		if(!empty($sidebar_layout)){
			 if( ($sidebar_layout == 2) || ($sidebar_layout == 3)) {  ?>
				<?php if( $sidebar_layout == 2) { $sidebar = "left-sidebar";}  if( $sidebar_layout == 3) { $sidebar = "right-sidebar";} ?>
		   		 <div id="secondary" class="widget-area clearfix <?php echo esc_attr($sidebar); ?>" role="complementary">
					<?php get_sidebar();?>
				</div>
	<?php }  }  else{ ?>
		<div id="secondary" class="widget-area clearfix right-sidebar" role="complementary">
			<?php get_sidebar();?>
		</div>
	<?php } ?>

	<?php if ($boxedornot == 'fullwidth') {?>
        </div>
        <!-- End the Container -->
    <?php }?>

<?php get_footer(); ?>