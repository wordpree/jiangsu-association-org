<?php
        /**
 * Customize for Layout picker, extend the WP customizer
 * set the plugin events cmt to display in the frontpage @see charity_review_CMT_Category_dropdown_control
 * create new class js-department content control @see js_department_content_control
 *modified by Hai
 */
if ( ! class_exists( 'WP_Customize_Control' ) )
    return NULL;

/**/
if(!class_exists('js_department_category_dropdown_control') ){
	class js_department_category_dropdown_control extends WP_Customize_Control{
		 public function render_content() {
              $cat_args = array(
                      'taxonomy' => 'category',
                      'orderby' => 'name',
                  );
              $categories = get_categories( $cat_args ); ?>
               <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
               <span><?php echo esc_html( $this->description ); ?></span><br>
                  <select data-customize-setting-link="<?php echo $this->id; ?>">
                      <option value="none" <?php selected( get_theme_mod($this->id), 'none' ); ?>><?php _e( 'None','charity-review' ); ?></option>
                      <?php foreach ( $categories as $post ) { ?>
                              <option value="<?php echo $post->term_id; ?>" <?php selected( $post->term_id); ?>><?php echo $post->name; ?></option>
                      <?php } ?>
                  </select> <br /><br />
              <?php
          }
	}
}
/**
 * Class to create a custom layout control
 */
if ( ! class_exists( 'charity_review_Layout_Picker_Custom_Control' ) ) {
  class charity_review_Layout_Picker_Custom_Control extends WP_Customize_Control
  {
        /**
         * Render the content on the theme customizer page
         */
        public function render_content()
         {
              ?>
                  <label>
                    <span class="customize-layout-control customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                    <ul>
                      <li>
                          <label for="<?php echo $this->id; ?>[full_width]"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/1col.png" alt="<?php _e('Full Width','charity-review');?>" /></label>
                          <input type="radio" name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>[full_width]"  data-customize-setting-link="<?php echo $this->id; ?>" value="1" />
                          </li>
                      <li>
                          <label for="<?php echo $this->id; ?>[left_sidebar]"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/img/2cl.png" alt="<?php _e('Left Sidebar','charity-review');?>" /></label>
                          <input type="radio" name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>[left_sidebar]" data-customize-setting-link="<?php echo $this->id; ?>" value="2" />
                          </li>
                      <li>
                          <label for="<?php echo $this->id; ?>[right_sidebar]"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/img/2cr.png" alt="<?php _e('Right Sidebar','charity-review');?>" /></label>
                          <input type="radio" name="<?php echo $this->id; ?>" id="<?php echo $this->id; ?>[right_sidebar]"  data-customize-setting-link="<?php echo $this->id; ?>" value="3"  />
                          </li>
                    </ul>
                  </label>
              <?php
         }
  }
}

/**
 * Customize for textarea, extend the WP customizer
 *
 */



  if ( class_exists( 'WP_Customize_Control' ) ) {
      /**
       * Class to create a post control
       */
    if ( ! class_exists( 'charity_review_Post_Dropdown_control' ) ) {
      class charity_review_Post_Dropdown_control extends WP_Customize_Control {
            /**
             * Render the content on the theme customizer page
             */
            public function render_content() { ?>
                  <label>
                      <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                      <select multiple="multiple" data-customize-setting-link="<?php echo $this->id; ?>">
                          <option value="none" <?php selected( get_theme_mod($this->id)[0], 'none' ); ?>><?php _e( 'None','charity-review' ); ?></option>
                              <?php  $posts = get_posts( array( 'posts_per_page'=> -1, 'post_type'=>'post' ) );
                              foreach ( $posts as $post ) { ?>
                                   <option value="<?php echo $post->ID; ?>" <?php selected( $post->ID); ?>><?php echo $post->post_title; ?></option>
  							<?php } ?>
                      </select>
                  </label><br><br>
                  <?php
              }
          }
    }
/**
*
* Class to create custom category dropdown section
*
**/
  if ( ! class_exists( 'charity_review_Category_dropdown_control' ) ) {
      class charity_review_Category_dropdown_control extends WP_Customize_Control {

          public function render_content() {
              $cat_args = array(
                      'taxonomy' => 'category',
                      'orderby' => 'name',
                  );
              $categories = get_categories( $cat_args ); ?>
               <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
               <span><?php echo esc_html( $this->description ); ?></span><br>
                  <select data-customize-setting-link="<?php echo $this->id; ?>">
                      <option value="none" <?php selected( get_theme_mod($this->id), 'none' ); ?>><?php _e( 'None','charity-review' ); ?></option>
                      <?php foreach ( $categories as $post ) { ?>
                              <option value="<?php echo $post->term_id; ?>" <?php selected( $post->term_id); ?>><?php echo $post->name; ?></option>
                      <?php } ?>
                  </select> <br /><br />
              <?php
          }
      }
  }
	  
	/*create CMT category dropdown for events displaying it front page, added by Hai*/  
  if ( ! class_exists( 'charity_review_CMT_Category_dropdown_control' ) ) {
      class charity_review_CMT_Category_dropdown_control extends WP_Customize_Control {

          public function render_content() {
			  if(is_plugin_active('events-manager/events-manager.php')){
				  $cat_args = array(
                      'taxonomy' => EM_TAXONOMY_CATEGORY,
                      'orderby' => 'name',
                  );
			  }else{
				  $cat_args = array(
                      'taxonomy' => 'category',
                      'orderby' => 'name',
                  );
			  }
              
              $categories = get_categories( $cat_args );?>
              
               <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
               <span><?php echo esc_html( $this->description ); ?></span><br>
                  <select data-customize-setting-link="<?php echo $this->id; ?>">
                      <option value="none" <?php selected( get_theme_mod($this->id), 'none' ); ?>><?php _e( 'None','charity-review' ); ?></option>
                      <?php foreach ( $categories as $post ) { ?>
                              <option value="<?php echo $post->term_id; ?>" <?php selected( $post->term_id); ?>><?php echo $post->name; ?></option>
                      <?php } ?>
                  </select> <br /><br />
              <?php
          }
      }
  }
	  
}


if ( ! class_exists( 'charity_review_Custom_Text_Control' ) ) {
  class charity_review_Custom_Text_Control extends WP_Customize_Control {
          public $type = 'customtext';
          public $extra = ''; // we add this for the extra description
          public function render_content() {
          ?>
          <label id="text-box-custom">
              <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
              <span><?php echo esc_html( $this->description ); ?></span>
                <input type="text" id="custom-text-box" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
               <?php  printf( '<a href="https://codethemes.co/product/charity-pro/" target="_blank">' ); ?>
              <?php echo esc_attr_e(' Buy Pro Version', 'charity-review');?></a></span>
          </label>

          <?php
               echo '<style>';
                      echo '#custom-text-box{
                          display: none;
                      }';
                      echo '</style>';
          }
      }
}



if ( ! class_exists( 'charity_review_documentation_Custom_Text_Control' ) ) {
  class charity_review_documentation_Custom_Text_Control extends WP_Customize_Control {
          public $type = 'customtext';
          public $extra = ''; // we add this for the extra description

          public function enqueue() {
           wp_enqueue_style( 'charity-review-customizer-sort-style', trailingslashit( get_template_directory_uri() ) . '/css/customizer.css' );
        }

          public function render_content() {
          ?>
          <p>
                <a class="charity_review_support" target="_blank" href="<?php echo  esc_url('https://codethemes.co/charity-lite-documentation/') ?>"><span class="dashicons dashicons-book-alt"></span><?php echo  __('Documentation', 'charity-review') ?></a>

                <a class="charity_review_support" target="_blank" href="<?php echo  esc_url('https://codethemes.co/my-tickets/') ?>"><span class="dashicons dashicons-edit"></span><?php echo   __('Create a Ticket', 'charity-review') ?></a>

                <a class="charity_review_support" target="_blank" href="<?php echo  esc_url('https://codethemes.co/product/charity-pro/') ?>"><span class="dashicons dashicons-star-filled"></span><?php echo   __('Buy Premium', 'charity-review') ?></a>

                <a class="support-image charity_review_support" target="_blank" href="<?php echo  esc_url('https://codethemes.co/support/#customization_support') ?>"><img src = "<?php echo esc_url(get_template_directory_uri() . '/img/wparmy.png') ?>" /> <?php echo __('Request Customization', 'charity-review'); ?></a>
              </p>

          <?php
          }
      }
}