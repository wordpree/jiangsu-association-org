<?php
/**
 * The template for displaying Aside post formats
 *
 * @package Charity Review
 */
?>
<?php
    $featured_image = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );
    $front = get_option('show_on_front');
    if(!is_single() && !is_archive() && !is_search() &&  !is_page_template('page-templates/template-blog.php')) {
?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

        <div class="post-content entry-content">

            <?php  if ( $featured_image ) { ?>
                        <div class="featured-image archive-thumb">

                            <a title="<?php the_title(); ?>" href="<?php echo esc_url( get_permalink() ); ?>" class="post-thumbnail">

                                <img src="<?php echo esc_url( $featured_image ); ?>" class="attachment-custom_post_size wp-post-image" alt="<?php the_title(); ?>">

                                <div class="share-mask">
                                    <div class="share-wrap">
                                        <div class="share-content">
                                            <h2><?php _e('Read More','charity-review');?></h2>
                                        </div>
                                    </div>
                                </div>

                            </a>
                        </div>
            <?php   } else {
                        if( $front == 'page' ){ ?>
                            <div class="featured-image archive-thumb">
                                <a title="<?php the_title(); ?>" href="<?php echo esc_url( get_permalink() ); ?>" class="post-thumbnail no-image-fall">
                                    <span><i class="fa fa-picture-o fa-2x"></i></span>
                                </a>
                            </div>
            <?php       }
                    } ?>

            <?php if( is_front_page() &&  $front == 'posts' || is_home() ){ ?>

                <header class="entry-header">
                    <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

                    <div class="entry-meta">
                        <?php charity_review_posted_on(); ?>
                    </div>
                    <!-- End Entry-meta -->

                </header>

                <div class="entry-wrap clearfix">
                    <?php charity_review_post_content(); ?>
                </div>

            <?php } ?>

        </div>
        <!-- End Post Content -->

        <?php if(  is_front_page() && $front == 'posts' || is_home()){ ?>

        <footer class="entry-footer clearfix">
            <?php charity_review_entry_footer(); ?>
        </footer>
        <!-- End Entry Footer -->

        <?php } ?>

    </article>

<?php } else{  ?>

    <!-- If the post is single or archive display this block  -->

    <?php if ( $featured_image ) { ?>

        <?php if (is_single()) { ?>

            <div class="featured-image archive-thumb">

                <a title="<?php the_title(); ?>" href="<?php echo esc_url( get_permalink() ); ?>" class="post-thumbnail">
                    <img src="<?php echo esc_url( $featured_image ); ?>" class="attachment-custom_post_size wp-post-image" alt="<?php the_title(); ?>">
                </a>

            </div>

        <?php } else{ ?>

        <div class="featured-image archive-thumb">

            <a title="<?php the_title(); ?>" href="<?php echo esc_url( get_permalink() ); ?>" class="post-thumbnail">

                <img src="<?php echo esc_url( $featured_image ); ?>" class="attachment-custom_post_size wp-post-image" alt="<?php the_title(); ?>">

                <div class="share-mask">
                    <div class="share-wrap">
                        <div class="share-content">
                            <h2><?php _e('Read More','charity-review');?></h2>
                        </div>
                    </div>
                </div>

            </a>

        </div>

        <?php } ?>
    <?php } ?>

    <header class="entry-header">

        <?php if ( is_single() ) {
                the_title( '<h2 class="entry-title">', '</h2>');
        } else {
            the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
        } ?>

        <div class="entry-meta">
            <?php charity_review_posted_on(); ?>
        </div>

    </header>

    <div class="entry-wrap clearfix">

        <?php
            if(is_search() || is_archive() || is_page_template('page-templates/template-blog.php') ){
                the_excerpt();
            }
            else{
                the_content();
            }
        ?>

    </div>

    <?php if (!is_archive() && !is_search()) { ?>

        <footer class="entry-footer clearfix">
            <?php charity_review_entry_footer(); ?>
        </footer>

    <?php } ?>
    <!-- End Entry Footer -->

<?php   } ?>