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

	<?php charity_review_breadcrumb(); ?>
	<!-- End the breadcrumb -->

	<?php if ($boxedornot == 'fullwidth') {?>
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

	<?php if ($boxedornot == 'fullwidth') {?>
		<!-- Start the container -->
        <div class="container full-width-container">
    <?php }?>
	<?php if(!empty($sidebar_layout)){ ?>
		<div id="primary" class="content-area <?php echo esc_attr( $sidebar_class ); ?>">
	<?php } else { ?>
		<div id="primary" class="content-area">
	<?php } ?>

		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
					charity_review_archive_title( '<h1 class="page-title">', '</h1>' );
					charity_review_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
			</header>
			<!-- End the .page-header -->

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<div class="entry-content clearfix">
						<?php
							get_template_part( 'template-parts/content', get_post_format($post->ID) );
						?>
					</div>

					<footer class="entry-footer clearfix">
				        <?php charity_review_entry_footer(); ?>
				    </footer>
				    <!-- End Entry Footer -->

				</article>

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
		   		 <div id="secondary" class="widget-area clearfix <?php echo esc_attr( $sidebar ); ?>" role="complementary">
					<?php get_sidebar();?>
				</div>
	<?php }  }  else{ ?>
		<div id="secondary" class="widget-area clearfix right-sidebar" role="complementary">
			<?php get_sidebar();?>
		</div>
	<?php } ?>

	<?php if ($boxedornot == 'fullwidth') {?>
        </div>
		<!-- End the container.-->
    <?php }?>

<?php get_footer();