<?php
/**
 * Charity Theme Customizer
 *
 * /*create CMT category dropdown for events plugin displaying it front page, added by Hai 
 *	 @see charity_review_CMT_Category_dropdown_control line 351
 *
 * add new js associtation department ,before from the blog section
 * @package Charity
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
if( !function_exists( 'charity_review_customize_register' ) ) :
	function charity_review_customize_register( $wp_customize ) {
		$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
	}
	add_action( 'customize_register', 'charity_review_customize_register' );
endif;

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
if( !function_exists( 'charity_review_customize_preview_js' ) ) :
	function charity_review_customize_preview_js() {
		wp_enqueue_script( 'charity_review_customizer', esc_url_raw( get_template_directory_uri() ) . '/js/customizer.js', array( 'customize-preview' ), '20130508', false );
	}
	add_action( 'customize_preview_init', 'charity_review_customize_preview_js' );
endif;
/**
*
* Panel for customizers
*
**/
get_template_part('/inc/charity-review-customize-control');

if( !function_exists( 'charity_review_customizer_panels' ) ) :
	function charity_review_customizer_panels( $wp_customize ) {

		$wp_customize->add_panel( 'charity_theme_panel', array(
	        'priority'       => 25,
	        'capability'     => 'edit_theme_options',
	        'theme_supports' => '',
	        'title'          =>  __('Theme Options','charity-review'),
	        'description'    => '',
	    ) );
	}
	add_action( 'customize_register', 'charity_review_customizer_panels');
endif;

/************************************************/
/*           Section For Header Logo           */
/***********************************************/
if( !function_exists( 'charity_review_header_section' ) ) :
	function charity_review_header_section( $wp_customize ) {

		// New Layout and Design

		$wp_customize->add_section(	'section_layout_design', array(
				'title'       => __( 'Layout and design','charity-review' ),
				'label' => __( 'Layout and design. ','charity-review' ),
				'panel'       => 'charity_theme_panel',
				'priority'    => 1,
			)
		);

		$wp_customize->add_setting(	'layout_control', array(
				'default'           => 'boxed',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'charity_review_sanitize_select',
			)
		);
		$wp_customize->add_control( 'layout_control', array(
				'label'    => __( 'Boxed or Fullwidth','charity-review' ),
				'section'  => 'section_layout_design',
				'type'     => 'radio',
				'choices' => array(
		            'boxed' => __('Boxed', 'charity-review'),
		            'fullwidth' =>__('Full Width', 'charity-review'),
		        ),
				'priority' => 5,
			)
		);

		$wp_customize->add_setting('max_width', array(
				'default'           => '',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
			)
		);
		$wp_customize->add_control( new charity_review_Custom_Text_Control( $wp_customize, 'max_width', array(
				'label'    => __( 'Site max width (px)','charity-review' ),
				'description'	=> __('Set the max width of the site', 'charity-review'),
				'section'  => 'section_layout_design',
				'priority' => 4,
				'type' => 'customtext',
				'extra' => __( 'Site min width (px): 1170 ','charity-review' ),
			))
		);

		$wp_customize->add_setting( 'layout_picker', array(
				'default'           => "3",
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
			)
		);

		$wp_customize->add_control( new charity_review_Layout_Picker_Custom_Control( $wp_customize, 'layout_picker', array(
		            'label'    => __( 'Layout picker','charity-review' ),
		            'section'  => 'section_layout_design',
		            'settings' => 'layout_picker',
		            'priority' => 6,
		        )
		    )
		);

		$wp_customize->add_setting( 'logo_background_color', array(
	            'capability'        => 'edit_theme_options',
	            'sanitize_callback' => 'charity_review_sanitize_hex_color',
	        )
	    );

	    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'logo_background_color', array(
	                'label'    => __( 'Logo Background Color','charity-review' ),
	                'description' => __( 'Info : Logo color settings: background and hover color. ','charity-review' ),
	                'section'  => 'section_layout_design',
	                'settings' => 'logo_background_color',
	                'priority' => 1
	            )
	        )
	    );

	    $wp_customize->add_setting( 'hover_background_color', array(
	            'capability'        => 'edit_theme_options',
	            'sanitize_callback' => 'charity_review_sanitize_hex_color',
	        )
	    );

	    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'hover_background_color', array(
	                'label'    => __( 'Logo Hover Background Color','charity-review' ),
	                'section'  => 'section_layout_design',
	                'settings' => 'hover_background_color',
	                'priority' => 2
	            )
	        )
	    );

		$wp_customize->add_setting( 'font_description', array(
	            'capability'        => 'edit_theme_options',
	            'sanitize_callback' => 'esc_attr',
	        )
	    );

	   $wp_customize->add_control( new charity_review_Custom_Text_Control( $wp_customize, 'font_description', array(
	   			'label' => __('Font settings', 'charity-review'),
				'description' => __( 'Font settings available in Pro version.','charity-review' ),
				'priority' => 3,
				'section'  => 'section_layout_design',
				'type' => 'customtext',
			))
		);
	}
	add_action( 'customize_register', 'charity_review_header_section' );
endif;
/****************************************************************************/
/*                Section For Footer Testimonial                            */
/****************************************************************************/
if( !function_exists( 'charity_review_footer_testimonial_customizer' ) ) :
	function charity_review_footer_testimonial_customizer( $wp_customize ) {
		$wp_customize->add_section( 'testimonial_section', array(
				'title'       => __( 'Client Testimonial in Footer','charity-review' ),
				'description' => __( 'This is a section for Testimonial of Clients','charity-review' ),
				'panel'       => 'charity_theme_panel',
				'priority'    => 6,
			)
		);

		$wp_customize->add_setting( 'author_name', array(
				'default'           => __('Charity','charity-review'),
				'capability'        => 'edit_theme_options',
	        			'sanitize_callback' => 'esc_attr'
			)
		);

		$wp_customize->add_control( 'author_name', array(
				'label'    => __( 'Name of The Person','charity-review' ),
				'section'  => 'testimonial_section',
				'type'     => 'text',
				'priority' => 1,
			)
		);


		$wp_customize->add_setting( 'testimonial_content', array(
				'capability'        => 'edit_theme_options',
	        			'sanitize_callback' => 'esc_attr'
				//'sanitize_callback' => 'esc_html',
			) );

		$wp_customize->add_control( 'testimonial_content', array(
		            'label'    => __( 'The Content for the Testimonial','charity-review' ),
		            'section'  => 'testimonial_section',
		            'settings' => 'testimonial_content',
		            'type' => 'textarea',
		            'priority' => 2,
		        )

		);
	}
	add_action( 'customize_register', 'charity_review_footer_testimonial_customizer' );
endif;

/**
*
* Customizer for the footer page
*
**/
if (! function_exists('charity_review_front_page_customize')) {
function charity_review_front_page_customize( $wp_customize ) {

/****************************************************************************/
				/* General Setting for Footer Content  */
/****************************************************************************/

	$wp_customize->add_section(	'footer_section', array(
			'title'       => __( 'Mission Content for the Site','charity-review' ),
			'description' => __( 'This is a section for Footer Content or General Mission of the site above the testimonial.','charity-review' ),
			'panel'       => 'charity_theme_panel',
			'priority'    => 5,
		)
	);

	$wp_customize->add_setting(	'mission_heading', array(
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'esc_html',
		)
	);
	$wp_customize->add_control( 'mission_heading', array(
			'label'    => __( 'Title for the Mission Section','charity-review' ),
			'section'  => 'footer_section',
			'type'     => 'text',
			'priority' => 1,
		)
	);

	$wp_customize->add_setting( 'mission_content_text', array(
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'esc_html',
		) );

	$wp_customize->add_control(  'mission_content_text', array(
	            'label'    => __( 'The Content for the Mission Section','charity-review' ),
	            'section'  => 'footer_section',
	            'settings' => 'mission_content_text',
	            'priority' => 2,
	            'type' => 'textarea',

	    )
	);

	$wp_customize->add_setting(	'mission_link_url', array(
			'default'           => '#',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(	'mission_link_url', array(
			'label'    => __( 'URL for the Section','charity-review' ),
			'section'  => 'footer_section',
			'type'     => 'text',
			'priority' => 3,
		)
	);
	$wp_customize->add_setting(	'mission_link_text', array(
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'esc_html',
		)
	);
	$wp_customize->add_control( 'mission_link_text',	array(
			'label'    => __( 'Text To show in URL','charity-review' ),
			'section'  => 'footer_section',
			'type'     => 'text',
			'priority' => 4,
		)
	);
	$wp_customize->add_setting( 'section_background', array(
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw',
		) );
	$wp_customize->add_control(	new WP_Customize_Image_Control(	$wp_customize, 'section_background', array(
				'label'    => __( 'Background Image','charity-review' ),
				'section'  => 'footer_section',
				'settings' => 'section_background',
				'priority' => 5,
			)
		)
	);

/***********************************/
	/*** Slider *****/
/**********************************/

 	$wp_customize->add_section( 'charity_front_page', array(
	        'title'       => __( 'Front Page','charity-review' ),
	        'panel'       => 'charity_theme_panel',
	        'priority'    => 2,
    ) );

    $wp_customize->add_setting( 'featured_post', array(
		    'default'           => '',
		    'capability'        => 'edit_theme_options',
		    'sanitize_callback' => 'charity_review_text_sanitize',
	) );

    $wp_customize->add_control( new charity_review_Post_Dropdown_control( $wp_customize, 'featured_post', array(
		    'label'       => __( 'Select a Post for slider','charity-review' ),
		    'section'     => 'charity_front_page',
		    'priority'    => 1,
	) ) );

/******************************/
/***** Posts below slider *****/
/******************************/

	$wp_customize->add_setting( 'first_post', array(
			'default'			=> '',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'charity_review_text_sanitize',
		) );
	$wp_customize->add_control( new charity_review_Category_dropdown_control( $wp_customize, 'first_post', array(
			'label'	      => __( 'Post Below Slider','charity-review' ),
			'description' => __( 'Select a category to display post below the slider','charity-review' ),
			'section'     => 'charity_front_page',
			'priority'    => 3,

		) ) );

/**********************************************/
		/*** js associtation department ***/
	   
/********************* create by Hai**********************/
	$wp_customize->add_setting('js_depart_post',array(
		'default'    =>'',
		'transport'  =>'postMessage',
	));
	$wp_customize->add_control( new js_department_category_dropdown_control($wp_customize,'js_depart_post',array(
	    'label'       => 'Js Department Posts',
		'description' =>'Select a category to display three posts',
		'section'     =>'charity_front_page',
		'priority'    => 5,
	    )
	  )
	);
	$wp_customize->add_setting('js_depart_bg',array(
		'default'    =>'',
		'transport'  =>'postMessage',
	));
	$wp_customize->add_control(new WP_Customize_Image_Control(
           $wp_customize,
           'js_depart_bg',
           array(
               'label'      => 'Js Department Background' ,
		       'description' =>'Upload a background image for js department',
               'section'    => 'charity_front_page',
               'priority'   => 4,
           )
       ));
	
	/**********************************************/
		/*** From the blog ***/
	/*******************************************/


	$wp_customize->add_setting( 'from_the_blog', array(
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'charity_review_text_sanitize',
		) );
   /*create CMT category dropdown for events displaying it front page, added by Hai*/  
	$wp_customize->add_control( new charity_review_CMT_Category_dropdown_control( $wp_customize, 'from_the_blog', array(
			'label'	      => __( 'From The Blog Section','charity-review' ),
			'description' => __( 'Select a category to display three posts.','charity-review' ),
			'section'     => 'charity_front_page',
			'priority'    => 5,
			)
		)
	);

	$wp_customize->add_setting( 'title_color', array(
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'charity_review_sanitize_hex_color',
		) );

	$wp_customize->add_control(new WP_Customize_Color_Control ( $wp_customize, 'title_color', array(
			'label'       => __( 'From The Blog Section Title Color', 'charity-review' ),
			'section'     => 'charity_front_page',
			'priority'    => 6,)
		) );

	$wp_customize->add_setting( 'from_the_blog_background', array(
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control( new WP_Customize_Image_Control ( $wp_customize, 'from_the_blog_background', array(
			'label'       => __( 'From The Blog background image','charity-review' ),
			'description' => __( 'Upload an background image for "From The Blog" section','charity-review' ),
			'section'     => 'charity_front_page',
			'priority'    => 7,
			)
		)
	);

	$wp_customize->add_setting( 'from_the_blog_button', array(
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'esc_html',
		) );

	$wp_customize->add_control( 'from_the_blog_button', array(
			'label'       => __( 'Button Text','charity-review' ),
			'description' => __( 'Change the button text.','charity-review' ),
			'section'     => 'charity_front_page',
			'priority'    => 6,
		) );
}
}
add_action( 'customize_register', 'charity_review_front_page_customize');

/******************************************************************/
/*              Social Media Section                              */
/******************************************************************/
if (! function_exists('charity_review_social_media_section')) :
	function charity_review_social_media_section( $wp_customize ) {

		$wp_customize->add_section(	'social_media_section',	array(
				'title'       => __( 'Social Media Options','charity-review' ),
				'panel'       => 'charity_theme_panel',
				'priority'    => 8,
			)

		);

		$wp_customize->add_setting(	'site_facebook_link', array(
				'default'           => '',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(	'site_facebook_link', array(
				'label'    => __( 'Facebook Link','charity-review' ),
				'section'  => 'social_media_section',
				'type'     => 'text',
				'priority' => 3,
			)
		);

		$wp_customize->add_setting(	'site_twitter_link', array(
				'default'           => '',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(	'site_twitter_link', array(
				'label'    => __( 'Twitter Link','charity-review' ),
				'section'  => 'social_media_section',
				'type'     => 'text',
				'priority' => 4,
			)
		);

		$wp_customize->add_setting( 'site_gplus_link', array(
				'default'           => '',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(	'site_gplus_link', array(
				'label'    => __( 'Google Plus Link','charity-review' ),
				'section'  => 'social_media_section',
				'type'     => 'text',
				'priority' => 5,
			)
		);

		$wp_customize->add_setting( 'site_youtube_link', array(
				'default'           => '',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(	'site_youtube_link', array(
				'label'    => __( 'YouTube Link','charity-review' ),
				'section'  => 'social_media_section',
				'type'     => 'text',
				'priority' => 6,
			)
		);

		$wp_customize->add_setting(	'site_instagram_url', array(
				'default'           => '',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control( 'site_instagram_url', array(
				'label'    => __( 'Instagram Link','charity-review' ),
				'section'  => 'social_media_section',
				'type'     => 'text',
				'priority' => 7,
			)
		);

		$wp_customize->add_setting(	'linkedin_url',	array(
				'default'           => '',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(	'linkedin_url',	array(
				'label'    => __( 'Linkedin URL','charity-review' ),
				'section'  => 'social_media_section',
				'type'     => 'text',
				'priority' => 8,
			)
		);

		$wp_customize->add_setting( 'site_pinterest_link',	array(
				'default'           => '',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control( 'site_pinterest_link', array(
				'label'    => __( 'Pinterest Link','charity-review' ),
				'section'  => 'social_media_section',
				'type'     => 'text',
				'priority' => 9,
			)
		);

		$wp_customize->add_setting(	'site_dribble_link', array(
				'default'           => '',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(	'site_dribble_link', array(
				'label'    => __( 'Dribble Link','charity-review' ),
				'section'  => 'social_media_section',
				'type'     => 'text',
				'priority' => 10,
			)
		);

		$wp_customize->add_setting(	'site_email_address', array(
				'default'           => '',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'charity_review_text_sanitize',
			)
		);

		$wp_customize->add_control(	'site_email_address', array(
				'label'    => __( 'Email Address','charity-review' ),
				'section'  => 'social_media_section',
				'type'     => 'text',
				'priority' => 11,
			)
		);

		$wp_customize->add_setting(	'site_skype_address', array(
				'default'           => '',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'charity_review_text_sanitize',
			)
		);

		$wp_customize->add_control(	'site_skype_address', array(
				'label'    => __( 'Skype/Phone','charity-review' ),
				'section'  => 'social_media_section',
				'type'     => 'text',
				'priority' => 12,
			)
		);



		/***************************************/
				/* Social Media Background */
		/**************************************/

		$wp_customize->add_setting( 'social_media_title', array(
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_html',
			)
		);

		$wp_customize->add_control( 'social_media_title', array(
			            'label'    => __( 'Title for social media section.','charity-review' ),
			            'section'  => 'social_media_section',
			            'settings' => 'social_media_title',
			            'priority' => 1,
			        )
				);

		$wp_customize->add_setting( 'social_media_background', array(
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control( new WP_Customize_Image_Control ( $wp_customize, 'social_media_background', array(
				'label'       => __( 'Social Media Background Image','charity-review' ),
				'description' => __( 'Upload an background image for "Social Media" section','charity-review' ),
				'section'     => 'social_media_section',
				'priority'    => 2,
				)
			)
		);

		 //ADD/CHANGE CSS
		$version_wp = get_bloginfo('version');
	    if($version_wp < 4.7){
		    $wp_customize->add_section(
		    'change_css',
		    array(
		        'title' => __( 'Custom CSS','charity-review' ),
		        'description' => __( 'Here you can customize Your theme\'s css' , 'charity-review' ),
		        'panel' => 'charity_theme_panel',
		        'capability'=>'edit_theme_options',
		        'priority' => 40,
		    )
		    );
		    $wp_customize->add_setting(
		        'css_change',
		        array(
		            'default'=>'',
		            'sanitize_callback'=>'esc_html',
		            'capability'        => 'edit_theme_options',
		        )
		    );
		    $wp_customize->add_control( 'charity-review_css_change', array(
		        'label'        => __( 'Add CSS', 'charity-review' ),
		        'type'=>'textarea',
		        'section'    => 'change_css',
		        'settings'   => 'css_change',
		    ) );
		}

	     $wp_customize->add_section(
	    'documentation',
	    array(
	        'title' => __( 'Documentation and Support','charity-review' ),
	        'capability'=>'edit_theme_options',
	        'priority' => 150,
	    )
	    );
	    $wp_customize->add_setting(
	        'doc_supp',
	        array(
	            'default'=>'',
	            'sanitize_callback'=>'esc_html',
	            'capability'        => 'edit_theme_options',
	        )
	    );

	      $wp_customize->add_control( new charity_review_documentation_Custom_Text_Control( $wp_customize, 'doc_supp', array(
				'section'  => 'documentation',
				'type' => 'customtext',
				'extra' => __( 'Font settings available in Pro version. Buy Pro Version','charity-review' ),
			))
		);


	}
	add_action( 'customize_register', 'charity_review_social_media_section' );
endif;

get_template_part('inc/customizer', 'sanitization');