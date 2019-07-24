<?php
/**
 * The template for displaying author pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Charity Review
 */

get_header();
// Boxed or Fullwidth
$boxedornot = charity_review_boxedornot();
?>

	<?php  charity_review_breadcrumb(); ?>
	<!-- End the breadcrumb -->

	<?php if ($boxedornot == 'fullwidth') {?>
	<!-- Start the container. If full width layout is selected in the Customizer.-->
	<div class="container full-width-container">
		<?php }?>

	<div id="primary" class="content-area">

		<main id="main" class="site-main author-page" role="main">

		<?php if ( have_posts() ) : ?>

			<div class="post-author">

				<div class="author-img text-center">
					<?php echo get_avatar( get_the_author_meta( 'ID' ), 60 );?>
				</div>

				<div class="author-desc">

					<h5><?php _e('Article By','charity-review'); ?> <a href="<?php echo esc_url( get_author_posts_url(get_the_author_meta( 'ID' )) ); ?>"><?php the_author_meta('display_name'); ?></a></h5>
					<p><?php the_author_meta('description'); ?></p>

					<div class="author-links">
						<a class="author-link-posts upper" title="<?php _e('Author archive','charity-review'); ?>" href="<?php echo esc_url( get_author_posts_url(get_the_author_meta( 'ID' )) ); ?>"><i class="fa fa-archive"></i> <?php _e('Author archive','charity-review'); ?></a>

								<?php $author_url = get_the_author_meta('user_url');

								$author_url = preg_replace('#^https?://#', '', rtrim($author_url,'/'));

								if (!empty($author_url)) : ?>

									<a class="upper author-link-website" title="<?php _e('Author website','charity-review'); ?>" href="<?php echo esc_url(get_the_author_meta('user_url')); ?>"><i class="fa fa-globe"></i> <?php _e('Author website','charity-review'); ?></a>

								<?php endif;

								$author_mail = get_the_author_meta('email');

								$show_mail = get_the_author_meta('showemail');

								if ( !empty($author_mail) && ($show_mail == "yes") ) : ?>

									<a class="upper author-link-mail" title="<?php echo esc_attr($author_mail); ?>" href="mailto:<?php echo esc_attr($author_mail); ?>"><?php echo esc_attr($author_mail); ?></a>

								<?php endif; ?>
					</div>
					<!-- Author-links -->

				</div>
				<!-- Author Desc -->
			</div>
			<!-- Post Author -->
			<!-- End the Post Author -->

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<div class="entry-content">
						<?php
							get_template_part( 'template-parts/content', get_post_format() );
						?>
					</div>
				    <!-- End Entry-content -->

				    <footer class="entry-footer clearfix">
				        <?php charity_review_entry_footer(); ?>
				    </footer>
				    <!-- End Entry Footer -->

				</article>
				<!-- End Article Post -->

			<?php endwhile; ?>

			<?php charity_review_posts_navigation(); ?>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>

		</main>
		<!-- End the #main -->
	</div>
	<!-- End the #primary -->

	<div id="secondary" class="widget-area clearfix right-sidebar" role="complementary">
		<?php get_sidebar(); ?>
	</div>
	<!-- End the Sidebar -->

	<?php if ($boxedornot == 'fullwidth') {?>
        </div>
		<!-- End the container -->
    <?php }?>

<?php get_footer();