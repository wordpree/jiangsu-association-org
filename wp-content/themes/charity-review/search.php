<?php
/**
 * The template for displaying search results pages.
 *
 * @package Charity Review
 */

get_header();
// Boxed or Fullwidth
$boxedornot = charity_review_boxedornot();
?>

	<?php charity_review_breadcrumb(); ?>
	<!-- End the breadcrumb -->

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
		<!-- Start the container. If full width layout is selected in the Customizer.-->
        <div class="container full-width-container">
    <?php }?>

	    <?php if(!empty($sidebar_layout)){ ?>
	    <div id="primary" class="content-area <?php echo $sidebar_class; ?>">
	    	<?php } else {?>
	    	<div id="primary" class="content-area">
	    		<?php } ?>

			<main id="main" class="site-main search-main" role="main">

				<?php if ( have_posts() ) : ?>

					<header class="page-header">
						<h3 class="page-title">
							<?php printf( __( 'Search Results for: %s','charity-review' ), '<span> ' . get_search_query() . ' </span>' ); ?>
						</h3>
					</header>
					<!-- End the .page-header -->

					<section class="searchpage-form">
						<div class="page-content">
							<?php get_search_form(); ?>
						</div>
						<!-- End the .page-content -->
					</section>
					<!-- End .no-results -->

					<?php /* Start the Loop */ ?>
					<?php while ( have_posts() ) : the_post(); ?>

						<?php
						/**
						 * Run the loop for the search to output the results.
						 * If you want to overload this in a child theme then include a file
						 * called content-search.php and that will be used instead.
						 */
						get_template_part( 'template-parts/content', 'search' );
						?>

					<?php endwhile; ?>

					<?php charity_review_posts_navigation(); ?>

				<?php else : ?>

					<?php get_template_part( 'template-parts/content', 'none' ); ?>

				<?php endif; ?>

			</main>
			<!-- End the #main -->
		</div>
		<!-- End the #primary -->

	<?php
		if(!empty($sidebar_layout)){
			 if( ($sidebar_layout == 2) || ($sidebar_layout == 3)) {  ?>
				<?php if( $sidebar_layout == 2) { $sidebar = "left-sidebar";}  if( $sidebar_layout == 3) { $sidebar = "right-sidebar";} ?>
		   		 <div id="secondary" class="widget-area clearfix <?php echo $sidebar; ?>" role="complementary">
					<?php get_sidebar();?>
				</div>
	<?php }  }  else{ ?>
		<div id="secondary" class="widget-area clearfix right-sidebar" role="complementary">
			<?php get_sidebar();?>
		</div>
	<?php } ?>
	<!-- End the Sidebar -->

	<?php if ($boxedornot == 'fullwidth') {?>
        </div>
		<!-- End the container -->
    <?php }?>

<?php get_footer(); ?>