<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Charity Review
 */
?>

</div><!-- #content -->
<?php
$boxedornot = charity_review_boxedornot();
$section_background = get_theme_mod('section_background');
?>
	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="pre-footer">
			<?php
				if ( is_front_page() ) {
		    		if ( get_theme_mod( 'mission_heading' ) !='' || get_theme_mod('mission_content_text') !='' ) { ?>
						<section id="promo" class="section promo" style="background-image:<?php if(!empty($section_background)) { ?>url(<?php echo esc_url($section_background);?>)" <?php } ?>>
							<?php if ($boxedornot == 'fullwidth') {?>
						    	<div class="container">
						    <?php }?>
								<div class="row">
								        <div class="col-md-12 text-center">
									        <div class="promo-content">
									            <h2><?php echo esc_attr(get_theme_mod( 'mission_heading' )); ?> </h2>
										        <p><?php echo esc_attr(get_theme_mod( 'mission_content_text' )); ?></p>
										    </div>
										    <?php
										    	$mission_text = esc_attr(get_theme_mod('mission_link_text'));
										    	if( !empty($mission_text)){
										    ?>
											    <a href="<?php echo esc_url(get_theme_mod( 'mission_link_url' )); ?>" class="pillbtn promo-btn btn" target="_blank">
											    	<?php echo $mission_text; ?> <i class="fa fa-heart-o fa-btn"></i>
											    </a>
										<?php } ?>
								        </div>
							    	</div>
					    	<?php if ($boxedornot == 'fullwidth') {?>
						    	</div>
						    <?php }?>
						</section>
			<?php   }

					if ( get_theme_mod( 'author_name' ) != '' || get_theme_mod( 'testimonial_content' ) != '' ) { ?>
						<section id="testimonials" class="section ">
							<?php if ($boxedornot == 'fullwidth') {?>
						    	<div class="container">
						    <?php }?>
								<div class="row">
									<div class="col-md-12 text-center">
										<blockquote class="blockquotev1">
							            	<?php echo esc_attr(get_theme_mod( 'testimonial_content' )); ?>
							              <span>- <?php echo esc_attr(get_theme_mod( 'author_name' )); ?></span>
							            </blockquote>
									</div>
								</div>
							<?php if ($boxedornot == 'fullwidth') {?>
						    	</div>
						    <?php }?>
						</section>
			<?php 	}
				}

				$facebook = get_theme_mod('site_facebook_link');
				$twitter = get_theme_mod('site_twitter_link');
				$site_gplus_link = get_theme_mod('site_gplus_link');
				$site_youtube_link = get_theme_mod('site_youtube_link');
				$site_instagram_url = get_theme_mod('site_instagram_url');
				$linkedin_url = get_theme_mod('linkedin_url');
				$site_dribble_link = get_theme_mod('site_dribble_link');
				$site_pinterest_link = get_theme_mod('site_pinterest_link');
				$site_email_address = get_theme_mod('site_email_address');
				$site_skype_address = get_theme_mod('site_skype_address');

			if( $facebook || $twitter || $site_gplus_link || $site_youtube_link || $site_instagram_url || $linkedin_url || $site_dribble_link || $site_pinterest_link || $site_skype_address || $site_email_address){

			?>
				<section class="section social-section">
					<?php if ($boxedornot == 'fullwidth') {?>
					<div class="container">
						<?php }?>
						<div class="row">
							<div class="col-md-12 text-center">
								<div class="social-content">
									<h2><?php echo esc_attr( get_theme_mod( 'social_media_title' ) ) ? get_theme_mod( 'social_media_title' ) : __('SPREAD THE LOVE', 'charity-review'); ?></h2>
									<div class="social-sharing">
										<?php if ( get_theme_mod( 'site_facebook_link' ) != null ) { ?>
										<a href="<?php echo esc_url(get_theme_mod( 'site_facebook_link' )); ?>" class="fb" target="_blank"><i class="fa fa-facebook"></i></a>
										<?php }
										if ( get_theme_mod( 'site_twitter_link' ) != null ) {
											?>
											<a href="<?php echo esc_url(get_theme_mod( 'site_twitter_link' )); ?>" class="tw" target="_blank"><i class="fa fa-twitter"></i></a>
										<?php }
										if ( get_theme_mod( 'site_gplus_link' ) != null ) {
											?>
											<a href="<?php echo esc_url(get_theme_mod( 'site_gplus_link' )); ?>" class="gp" target="_blank"><i class="fa fa-google-plus"></i></a>
											<?php }
										if ( get_theme_mod( 'site_youtube_link' ) != null ) {
											?>
											<a href="<?php echo esc_url(get_theme_mod( 'site_youtube_link' )); ?>" class="yt" target="_blank"><i class="fa fa-youtube-play"></i></a>
											<?php }
										if ( get_theme_mod( 'site_instagram_url' ) != null ) {
											?>
											<a href="<?php echo esc_url(get_theme_mod( 'site_instagram_url' )); ?>" class="in" target="_blank"><i class="fa fa-instagram"></i></a>
											<?php }
										if ( get_theme_mod( 'linkedin_url' ) != null ) {
											?>
											<a href="<?php echo esc_url(get_theme_mod( 'linkedin_url' )); ?>" class="ld" target="_blank"><i class="fa fa-linkedin"></i></a>
											<?php }
										if ( get_theme_mod( 'site_dribble_link' ) != null ) {
											?>
											<a href="<?php echo esc_url(get_theme_mod( 'site_dribble_link' )); ?>" class="dr" target="_blank"><i class="fa fa-dribbble"></i></a>
											<?php }
										if ( get_theme_mod( 'site_pinterest_link' ) != null ) {
											?>
											<a href="<?php echo esc_url(get_theme_mod( 'site_pinterest_link' )); ?>" class="pi" target="_blank"><i class="fa fa-pinterest"></i></a>
											<?php }
											if ( get_theme_mod( 'site_email_address' ) != null ) {
												?>
										<a href="mailto:<?php echo esc_attr(antispambot(get_theme_mod( 'site_email_address' ))); ?>" class="pi"><i class="fa fa-envelope"></i></a>
										<?php }
										if ( get_theme_mod( 'site_skype_address' ) != null ) {
											?>
											<a href="callto:<?php echo esc_attr(get_theme_mod( 'site_skype_address' )); ?>" class="pi"><i class="fa fa-phone"></i></a>
											<?php } ?>
									</div>
								</div>
							</div>
						</div>
				  	<?php if ($boxedornot == 'fullwidth') {?>
				    	</div>
				    <?php }?>
				</section>
			<?php } ?>
		</div>

		<div class="footer-widget">

			<div class="container">
				<div class="row">

					<div class="col-md-4 col-sm-12 pad0 foot-bor">
						<?php
							if ( is_active_sidebar( 'footer-1' ) ) {
								dynamic_sidebar( 'footer-1' );
							}
							else{
								if(is_user_logged_in() && current_user_can('administrator') ){
									echo '<h6 class="text-center"><a href="'.esc_url(admin_url("customize.php")).'"><i class="fa fa-plus-circle"></i>'.__('Assign a Widget', 'charity-review').'</a></h6>';
								}
							}
						?>
					</div>

					<div class="col-md-4 col-sm-12 pad0 foot-bor">
						<?php
							if ( is_active_sidebar( 'footer-2' ) ) {
								dynamic_sidebar( 'footer-2' );
							}
							else{
								if(is_user_logged_in()&& current_user_can('administrator') ){
									echo '<h6 class="text-center"><a href="'.esc_url(admin_url("customize.php")).'"><i class="fa fa-plus-circle"></i>'.__('Assign a Widget', 'charity-review').'</a></h6>';
								}
							}
						?>
					</div>

					<div class="col-md-4 col-sm-12 pad0 foot-bor br0">
						<?php
							if ( is_active_sidebar( 'footer-3' ) ) {
								dynamic_sidebar( 'footer-3' );
							}
							else{
								if(is_user_logged_in()&& current_user_can('administrator')  ){
									echo '<h6 class="text-center"><a href="'.esc_url(admin_url("customize.php")).'"><i class="fa fa-plus-circle"></i>'.__('Assign a Widget', 'charity-review').'</a></h6>';
								}
							}
						?>
					</div>
					<!-- End Footer Widget Columns -->

				</div>
			</div>

        </div>
        <!-- Footer Widgets -->

        <div class="copyright clearfix">

        	<?php if ($boxedornot == 'fullwidth') {?>
		    	<div class="container">
		    <?php }?>

		    <div class="container pad0">

			  	<div class="copyright-content">
			        <p class="text-right">
			        	<?php $copyright_link = esc_url( __( 'https://codethemes.co/','charity-review' ) ); ?>
			        		<?php esc_html_e('Developed by ', 'charity-review');?>
	        				<a href="<?php echo $copyright_link; ?>" target="_blank">
	        					 <?php printf( esc_html__( 'Code Themes','charity-review' ), 'Code Themes' ); ?>
	        				</a>
					</p>
			  	</div>

		  	</div>

		  	<?php if ($boxedornot == 'fullwidth') {?>
		    	</div>
		    <?php }?>

		</div>
		<!-- End the Copyright -->

	</footer>
	<!-- End the Footer -->

</div>
<!-- End the Page -->

<a href="#0" class="cp-top"><?php _e('Top', 'charity-review');?></a>
<!-- End the scroll to top -->

<?php wp_footer(); ?>
</body>
</html>
