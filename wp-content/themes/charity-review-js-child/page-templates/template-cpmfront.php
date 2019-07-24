<?php
/**
 *
 * Template Name: Frontpage
 * Description: A page template that displays the Homepage or a Front page as in theme main page with slider and some other contents of the
 * post.
 *js department gose here .create by Hai @see line
 * @package Charity Review
 */

get_header();
// Boxed or Fullwidth
$boxedornot = charity_review_boxedornot();

	// Get Slider Posts from the customizer
	if ( get_theme_mod( 'featured_post' ) != "" ) {
		$slider_posts_id =  get_theme_mod( 'featured_post' ) ; // can't be escaped as it returns value in array.
		$slider_posts_args = array(
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'post__in'       => (array)$slider_posts_id,
		);
		$slider_variable = get_posts( $slider_posts_args );
	?>

	<!-- Home page pro Slider -->
	<div id="home-slider" class="featured-slider slick-slider" >

		<?php
			foreach ( $slider_variable as $key => $slider_value ) {
				$image = wp_get_attachment_url( get_post_thumbnail_id( $slider_value->ID ) );
			?>
				<div class="slide-item">

					<?php
					if($image){?>
						<div class="slider-image" style="background-image: url('<?php echo esc_url( $image ); ?>');"></div>
					<?php } else { ?>
						<div class="no-image-slider slider-image">
						</div>
					<?php } ?>

					<div class="slider-desc-wrapper">

						<div class="slider-desc-text">

							<div class="slider-desc">
								<h1><a href="<?php echo esc_url( get_permalink( $slider_value->ID ) ); ?>"><?php echo wp_trim_words( $slider_value->post_title, 16 ) ?></a></h1>
								<p><?php echo charity_review_strip_url_content($slider_value, 20); ?></p>
								<a href="<?php echo esc_url( get_permalink( $slider_value->ID ) ); ?>" class="pillbtn promo-btn btn" role="button">
									<?php echo _e('Read More <i class="fa fa-long-arrow-right"></i>','charity-review'); ?>
								</a>
							</div>

						</div>

					</div>
					<!-- Slide Desc Wrapper -->

				</div>
				<!-- Slide Item -->
		<?php } ?>

	</div>

	<?php }
		// If no post is assigned in the customizer. Revert to the fallback slider.
		// which is in functions.php
		elseif ( get_theme_mod( 'featured_post' ) == "none" ) {
			charity_review_fallback_slider();
		}
		else{
			charity_review_fallback_slider();
		}
	?>
	<!-- End of Home page slider -->

	<!-- The introduction section starts here -->
	<?php
	$cat_id = ( esc_attr(get_theme_mod( 'first_post' ) ) ) ? esc_attr( get_theme_mod( 'first_post' ) ) : 1;

	$args = array(
		'post_type'     => 'post',
		'tax_query'     => array(
				array(
					'taxonomy'    => 'category',
					'field'       => 'id',
					'terms'       => $cat_id,
				)
			),
		'posts_per_page' => 2,
		);
	$events = new WP_Query( $args );

	if ( $events->have_posts() ) :
		$counter=0;
		while( $events->have_posts() ) : $events->the_post();
			?>
			<section class="section <?php if( $counter%2 ==0 ){ echo "aboutbox"; } else{ echo 'graybg'; } ?>">

				<?php if ($boxedornot == 'fullwidth') {?>
			        <div class="container">
			    <?php }?>

				<div class="row">

					<?php if ( $counter%2 != 0 ) { ?>

					<article <?php post_class();?>>

						<div class="col-md-5 col-sm-12 pull-left mob-center">

							<div class="media-wrapper">
								<div class="media-wrap">
	                      						<?php get_template_part('template-parts/content', get_post_format($post->ID)); ?>
								</div>
							</div>

						</div>

						<div class="col-md-7 col-sm-12 <?php if ( $counter%2 !=0 ) { echo "text-right "; } ?> mob-center">
							<h3 class="line-animation"><a href="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>"> <?php the_title(); ?></a></h3>
							<p><?php echo  charity_review_strip_url_content($post, 30); ?></p>
						</div>

					</article>

					<?php } else { ?>

					<article <?php post_class();?>>

						<div class="col-md-5 col-sm-12 pull-right text-right mob-center">

							<div class="media-wrapper">
								<div class="media-wrap ">
									<?php get_template_part('template-parts/content', get_post_format($post->ID)); ?>
								</div>
							</div>

						</div>

						<div class="col-md-7 col-sm-12 mob-center" >
							<h3 class="line-animation"><a href="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>"> <?php the_title(); ?></a></h3>
							<p><?php echo charity_review_strip_url_content($post, 30); ?></p>
						</div>

					</article>

					<?php } ?>

				</div>

				<div class="row">
					<div class="col-lg-12 text-right">
						<?php if ( ( $counter == 1 ) || ( $counter == 3 ) ) {
							echo '<a href="' .esc_url ( get_category_link( $cat_id )) . '" class="btn">'.__('View All', 'charity-review').' '.esc_attr( get_cat_name( $cat_id ) ).''.__(' Posts', 'charity-review').'</a>';
						} ?>
					</div>
				</div>

				<?php if ($boxedornot == 'fullwidth') {?>
			        </div>
			    <?php }?>

			</section>
			<?php $counter++;
		endwhile;
	endif;
	wp_reset_postdata();
	?>
   <!-- The department section starts here -->
    <?php 
    /*js department gose here .create by Hai*/
	$bg_image = esc_attr( get_theme_mod( 'js_depart_bg' ) ) ;
	$cat_id = ( esc_attr(get_theme_mod( 'js_depart_post' ) ) ) ? esc_attr( get_theme_mod( 'js_depart_post' ) ) : 1;

	$args = array(
		'post_type'     => 'post',
		'tax_query'     => array(
				array(
					'taxonomy'    => 'category',
					'field'       => 'id',
					'terms'       => $cat_id,
				)
			),
		'posts_per_page' => 3,
		);
	$department = new WP_Query( $args );
    if($department->have_posts()){?>
		<section id="js-depart" class="js-depart">
			<div class="section-title" style='background-image: url("<?php echo $bg_image?>");'>
				<h2><?php _e('关于我们');?></h2>
			</div>
		
		<div class="section-content">
		  <div class='content-row-wrapper'>
		  <?php while($department->have_posts()){ 
		     echo '<div class="content-row">';
		     echo '<i class="icomoon-fonts"></i>';
			 $department->the_post();?>
		     <a href='<?php echo get_the_permalink();?>'><?php the_title('<h3>','</h3>');?></a>
		     <?php the_content('');?>
		     </div>
		  <?php }
	}
    wp_reset_postdata();
    ?>
    </div>
    </div>
    </section>
    <!-- The Events section starts here -->
	<?php
    /*events display area,query event plugin tax,modified by Hai*/
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$from_the_blog_cat_id =  ( esc_attr( get_theme_mod( 'from_the_blog' ) ) ) ? esc_attr( get_theme_mod( 'from_the_blog' ) ) : 1 ;

	$args = array(
		'post_type' => is_plugin_active('events-manager/events-manager.php') ? EM_POST_TYPE_EVENT:'post',
		'orderby' => 'DATE',
		'order'		=> 'DESC',
		'tax_query' => array(
				array(
					'taxonomy'    => is_plugin_active('events-manager/events-manager.php') ?                	
		                             EM_TAXONOMY_CATEGORY:'category',
					'field'       => 'id',
					'terms'       => $from_the_blog_cat_id,
				)
			),
		'posts_per_page' => 6,
		);
	$featured = new WP_Query( $args );
 
	if ( $featured->have_posts() ) :
 
		?>
	<section id="about" class="section blogroll aboutbox" >
		<?php if ($boxedornot == 'fullwidth') {?>
	        <div class="container">
	    <?php }?>
		<div class="row">

			<h2 class="section-title line-effect text-center"><?php _e('江苏协会活动一览','charity-review');?></h2>

			<?php while( $featured->have_posts() ): $featured->the_post();?>
				<div class="col-md-4 col-sm-12 text-center">
					<div <?php post_class(); ?>>

						<div class="blog-content clearfix">

							<div class="blog-content-head">
								<h4><a href="<?php echo esc_url( get_permalink() );?>"><?php the_title(); ?></a></h4>
							</div>

							<div class="blog-content-image effect-thumb">
	                      						<?php get_template_part('template-parts/content', get_post_format($post->ID)); ?>
							</div>

							<div class="blog-content-wrap">
							<p><?php the_excerpt(); ?></p>
								<?php
									$blog_button = esc_attr( get_theme_mod( 'from_the_blog_button' ) ) ;
									if(!empty($blog_button)){
								?>
									<a href="<?php echo esc_url( get_permalink( get_the_ID() ) ); ?>" class="btn"><?php echo esc_attr($blog_button ) ; ?></a>
								<?php } ?>
							</div>

						</div>
						<!-- End Blog Content -->

					</div>
					<!-- End the Blog wrap -->
				</div>

			<?php endwhile; ?>

			<?php wp_reset_postdata(); ?>

		</div>
		<!-- End Row -->

		<?php if ($boxedornot == 'fullwidth') {?>
	        </div>
	    <?php }?>

	</section>
	<!-- End the blogroll -->

<?php endif; ?>

<?php /*added js association members activities ,by Hai*/?>
<!-- js members activities start here -->
<section id="js-members" class="members-area">
	<div id="products" class="featured-products">
  <div class="products-heading">
	    <h2>协会成员精彩瞬间</h2>
  </div>
  <div class="products-rows">
  <?php
 
	 $args =array(
	  'post_type'     =>'post',
	  'oderby'        =>'rand',
	  'posts_per_page'=>3,
      'tax_query' => array(
          array(
				'taxonomy' => 'category',
			    'field'    => 'slug',
			    'terms'    => 'members_show',
          )
       )
	);
	  
	$query =new WP_Query($args);
	  if($query->have_posts()){
		  while($query->have_posts()){
			  $query->the_post();
			  if(has_post_thumbnail()){
				  echo '<div class=product-item-row>';
				    echo '<div class=product-item>';
				      the_post_thumbnail('shop_catalog','class=featured-image');?>
				        <div class="caption">
						   <div class="item-description">
							 <a href='<?php the_permalink();?>'><?php the_title('<button>','</button>');?></a>
						   </div>
	                    </div>
	                 </div>
				  </div>
		      <?php }
	     }
   	
	 }
	wp_reset_postdata();
    ?>
	</div>
 </div>
</section>


<?php get_footer(); ?>