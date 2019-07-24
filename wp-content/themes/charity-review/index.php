<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Charity Review
 */

get_header();
// Boxed or Fullwidth
$boxedornot = charity_review_boxedornot();
?>
	<?php if ($boxedornot == 'fullwidth') { ?>
		<!-- Start the container. If full width layout is selected in the Customizer.-->
        <div class="container full-width-container">
    <?php }?>

    	<?php
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

	<?php if(!empty($sidebar_layout)){ ?>
	<div id="primary" class="content-area <?php echo $sidebar_class; ?>">
		<?php } else { ?>
		<div id="primary" class="content-area">
			<?php } ?>
					<main id="main" class="site-main hello" role="main">

					<?php if ( have_posts() ) :  ?>

						<?php if( is_home() && is_front_page() ) {  ?>
							<header class="page-header">
								<?php echo sprintf( __( '%1$s Latest Posts %2$s','charity-review' ), '<h1 class="page-title">', '</h1>' ); ?>
							</header>
						<?php } ?>
						<!-- .page-header -->

						<?php /* Start the Loop */
						while ( have_posts() ) : the_post();

								/* Include the Post-Format-specific template for the content.
								 * If you want to override this in a child theme, then include a file
								 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
								 */
							get_template_part( 'template-parts/content', get_post_format() );
						endwhile;

							//previous or next post navigation
								charity_review_posts_navigation();
					else :
						get_template_part( 'template-parts/content', 'none' );

					endif;
					?>

					</main>
					<!-- End the #main -->
				</div>
				<!-- End the #primary -->

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
			<!-- End the Sidebar -->

	<?php   if ($boxedornot == 'fullwidth') {?>
        </div>
		<!-- End the container -->
    <?php }?>

<?php get_footer(); ?>