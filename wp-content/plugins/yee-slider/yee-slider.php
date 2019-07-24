<?php 
/**
 * Plugin Name: slides your website
 * Plugin URI:  https://yijiang.com.au
 * Description: Easily slides your fonder
 * Version:     0.1.0
 * Author:      Hai
 * Author URI:
 * License:     GPL version 2 or later - 
 *License URI:  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: yee-slider
 * Domain Path:
**/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*register custom post type*/
function yee_custom_post_type_init(){
  $labels =array(
	'name'          => __('Sliders','yee-slider'),
	'singular_name' => __('Slider','yee-slider'),
	'menu_name'     => __('Sliders','yee-slider'),
	'add_new'       => __('Add New','yee-slider'),
	'add_new_item'  => __('Add New Slider','yee-slider'),
	'new_item'      => __('New Slider','yee-slider'),
	'edit_item'     => __('Edit slider','yee-slider'),
	'view_item'     => __('View Slider','yee-slider'),
	'all_items'     => __('All Sliders','yee-slider'),
	'search_items'  => __('Search Sliders','yee-slider'),
	
 );

 $args =array('labels'        => $labels,
			  'public'        => true,
			  'publicly_queryable' => true,
			  'show_ui'            => true,
			  'show_in_menu'       => true,
			  'query_var'          => true,
			  'rewrite'            => array( 'slug' => 'slider' ),
			  'capability_type'    => 'post',
			  'has_archive'        => true,
			  'hierarchical'       => false,
			  'menu_position' => 5,
			  'menu_icon'     => 'dashicons-format-gallery',
			  'supports'      =>array('title','editor','thumbnail','excerpt'),
			 );

 register_post_type('slider',$args);
}
add_action('init','yee_custom_post_type_init');

/*get permalinks to work when you activate the plugin*/
register_activation_hook(__FILE__,'yee_rewrite_flush');
function yee_rewrite_flush(){
	// First, we "add" the custom post type via the above written function.
    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
    // They are only referenced in the post_type column with a post entry, 
    // when you add a post of this CPT.
    // ATTENTION: This is *only* done during plugin activation hook in this example!
    // You should *NEVER EVER* do this on every page load!!
	yee_custom_post_type_init();
	flush_rewrite_rules();	
}

/*including scripts files*/
function yee_enqueue_scripts(){
	wp_enqueue_script('slider-jQuery',plugins_url('/assets/js/jquery-3.2.1.min.js',__FILE__));
	wp_enqueue_script('slider_js',plugins_url('/assets/js/slider.js',__FILE__),array('jquery'),'v0802',true);
	wp_enqueue_style('slider_style',plugins_url('/assets/css/slider.css',__FILE__),array(),'v0802');
}
add_action('wp_enqueue_scripts','yee_enqueue_scripts');

/*query the content of slider*/
function get_slider_contents(){
echo '<div id="yee_slider" class="yee_slider_wrapper">';
$args =array('post_type'=>'slider',
			 'orderby'=>'rand',
			 'posts_per_page'=>'4'
			);
$query =new WP_Query($args);
add_image_size('size1',180,100,true);
add_image_size('size2',600,280,true);
if ($query->have_posts()){
	while($query->have_posts()){
		$query->the_post();
		$featured_image_id =get_post_thumbnail_id();
		$featured_image_src1 =wp_get_attachment_image_src($featured_image_id,size1);
		$featured_image_src2 =wp_get_attachment_image_src($featured_image_id,size2);
		echo '<div class="slider" image-size-1=" '.$featured_image_src1[0].' " 
		image-size-2=" '.$featured_image_src2[0].' " title=" '.get_the_title().' " data-content=" '.get_the_content().' "></div>';
	}
	
}
echo '</div>';
wp_reset_postdata();
}

add_shortcode('yee_slider','get_slider_contents');

/************slider widget ********************/
class yee_widget extends WP_Widget{
	/*add slider widget*/
	public function __construct(){
		parent::__construct('yee_widget','Slider Show',array('description'=>__('Slider Show Your Photos','yee-slider')));
	}
	
	/*font-end dispaly of widget*/
	public function widget($args,$instance){
		 get_slider_contents();
	}
	
	/*back-end widget form*/
	public function form($instance){
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Slider', 'yee-slider' );
		 ?>
		<p>
           <label for="<?php echo $this->get_field_id('title'); ?>"> <?php _e('Title:');?></label>
           <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title);?>">
        </p>
		<?php
	}
	
	/*Sanitize widget form values as they are saved.*/
	public  function update($new_instance, $old_instance){
		$instance = array();
		$instance['title']=empty($new_instance['title'])  ? " " : strip_tags($new_instance['title']);
		return $instance;
	}
	
}

/*initialize widget*/
function yee_widget_init(){
	register_widget('yee_widget');
}
add_action('widgets_init','yee_widget_init');
/************end slider widget ********************/