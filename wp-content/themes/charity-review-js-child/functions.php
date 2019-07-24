<?php 
if(!function_exists('crc_scripts_enqueue')) {
	
	function crc_scripts_enqueue(){
	  wp_enqueue_style('charity-review-css',get_template_directory_uri().'/css/charity.css',array(),'v0831','all');
	  wp_enqueue_style('charity-review-frontpage-css',get_stylesheet_directory_uri().'/css/front-page.css',array(),'v0901','all');
	  wp_enqueue_script('charity-review-js-js',get_stylesheet_directory_uri().'/js/js-slider.js',array('jquery'),'v0901',true);
	  wp_enqueue_script('charity-review-vendor-js-js',get_template_directory_uri().'/js/vendor.js',array('jquery'),'v0901',true);
     wp_enqueue_script('crc_customizer',get_stylesheet_directory_uri().'/inc/customizer.js',array('jquery'),'v1001',true);
	};
	add_action('wp_enqueue_scripts','crc_scripts_enqueue');
}

