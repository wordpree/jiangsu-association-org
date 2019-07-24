<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
 
function do_wpforo_activation($network_wide){
	if ( is_multisite() && $network_wide ) { 
        global $wpdb;
		
        $old_blogid = $wpdb->blogid;
        $blogids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
        foreach ($blogids as $blogid){
            switch_to_blog($blogid);
            wpforo_activation();
        }
        switch_to_blog($old_blogid);
    }else{
        wpforo_activation();
    }
}

function do_wpforo_deactivation($network_wide){
	if ( is_multisite() && $network_wide ) { 
        global $wpdb;
		
        $old_blogid = $wpdb->blogid;
        $blogids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
        foreach ($blogids as $blogid){
            switch_to_blog($blogid);
            wpforo_deactivation();
        }
        switch_to_blog($old_blogid);
    }else{
        wpforo_deactivation();
    }
}

function wpforo_activation(){
	global $wpforo, $wpdb;
	if( ! wpforo_is_admin() ) return;
	if( ! current_user_can( 'activate_plugins' ) ) return;
	add_option('wpforo_default_groupid', 3);
	require( WPFORO_DIR . '/wpf-includes/install-sql.php' );
	foreach( $wpforo_sql as $sql ) if( FALSE === @$wpforo->db->query($sql) ) @$wpforo->db->query( preg_replace('#)[\r\n\t\s]*ENGINE.*$#isu', ')', $sql) );
	$users = $wpforo->db->get_var("SELECT COUNT(*) FROM `" . $wpforo->db->prefix . "users`");
	if( $users <= 100 ) $wpforo->member->synchronize_users();
	$wpforo->member->init_current_user();
	$blogname = get_option('blogname');
	$adminemail = get_option('admin_email');
	add_option( 'wpforo_count_per_page', 10 ); 
	
	###################################################################
	// General Options ////////////////////////////////////////////////
	$general_options = array(
		'title' => $blogname . ' ' . __('Forum', 'wpforo'),
		'description' => $blogname . ' ' . __('Discussion Board', 'wpforo'),
		'lang' => 1,
	);
	wpforo_update_options( 'wpforo_general_options', $general_options );
	
	###################################################################
	// Forums /////////////////////////////////////////////////////////
	$wpforo_forum = array(
		'layout_qa_intro_topics_toggle' => 1,
		'layout_extended_intro_topics_toggle' => 1,
		'layout_qa_intro_topics_count' => 3,
		'layout_extended_intro_topics_count' => 5,
	);
	wpforo_update_options( 'wpforo_forum_options', $wpforo_forum );
	
	##################################################################
	// Topics & Posts ////////////////////////////////////////////////
	$upload_max_filesize = @ini_get('upload_max_filesize');
	$upload_max_filesize = wpforo_human_size_to_bytes($upload_max_filesize); 
	if( !$upload_max_filesize || $upload_max_filesize > 10485760 ) $upload_max_filesize = 10485760;
	$wpforo_post = array(
		'layout_extended_intro_posts_toggle' => 1,
		'layout_extended_intro_posts_count' => 4,
		'topics_per_page' => 10,
		'eot_durr' => 300,
		'dot_durr' => 300,
		'posts_per_page' => 15,
		'eor_durr' => 300,
		'dor_durr' => 300,
		'max_upload_size' => $upload_max_filesize,
		'attach_cant_view_msg' => __("You are not permitted to view this attachment", 'wpforo')
	);
	wpforo_update_options( 'wpforo_post_options', $wpforo_post );
	
	#################################################################
	// Features /////////////////////////////////////////////////////
	$wpforo_features = array(
		'user-admin-bar' => 0,
		'page-title' => 1,
		'top-bar' => 1,
		'top-bar-search' => 1,
		'breadcrumb' => 1,
		'footer-stat' => 1,
		'author-link' => 0,
		'comment-author-link' => 0,
		'user-register' => 1,
		'user-register-email-confirm' => 0,
		'register-url' => 0,
		'login-url' => 0,
		'replace-avatar' => 1,
		'avatars' => 1,
		'custom-avatars' => 1,
		'signature' => 1,
		'rating' => 1,
		'rating_title' => 1,
		'member_cashe' => 1,
		'object_cashe' => 1,
		'html_cashe' => 0,
		'memory_cashe' => 1,
		'seo-title' => 1,
		'seo-meta' => 1,
		'seo-profile' => 1,
		'font-awesome' => 1,
		'user-synch' => 0,
		'output-buffer' => 1,
		'wp-date-format' => 0,
		'subscribe_conf' => 1,
		'subscribe_checkbox_on_post_editor' => 1,
		'subscribe_checkbox_default_status' => 0,
		'attach-media-lib' => 1,
		'debug-mode' => 0,
		'copyright' => 1
	);
	wpforo_update_options( 'wpforo_features', $wpforo_features );
	
	#################################################################
	// Theme & Style ////////////////////////////////////////////////
	$wpforo_style = array(
		'font_size_forum' => 17,
		'font_size_topic' => 16,
		'font_size_post_content' => 14,
		'custom_css' => "#wpforo-wrap {\r\n   font-size: 13px; width: 100%; padding:10px 20px; margin:0px;\r\n}\r\n",
	);
	wpforo_update_options( 'wpforo_style_options', $wpforo_style );
	
	$defaut_theme = 'classic';
	$theme = $wpforo->tpl->find_theme( $defaut_theme );
	$current_theme = get_option( 'wpforo_theme_options' );
	if(!empty($current_theme)) {
		$theme = wpforo_deep_merge($theme, $current_theme);
		update_option( 'wpforo_theme_options', $theme );
	}
	add_option( 'wpforo_theme_options', $theme );
	
	#################################################################
	// Members //////////////////////////////////////////////////////
	$wpforo_member = array(
		'online_status_timeout' => 240,
		'url_structure' => 'nicename',
		'login_url' => '',
		'register_url' => '',
		'lost_password_url' => '',
		'rating_title_ug' => array ( 1 => '0', 5 => '1', 4 => '1', 2 => '0', 3 => '1' ),
		'rating_badge_ug' => array ( 1 => '1', 5 => '1', 4 => '1', 2 => '1', 3 => '1' ),
		'title_usergroup' => array ( 1 => '1', 5 => '1', 4 => '1', 2 => '1', 3 => '0' ),
	);
	$exlude = array('rating_title_ug', 'rating_badge_ug');
	wpforo_update_options( 'wpforo_member_options', $wpforo_member, $exlude);
	
	#################################################################
	// Subscribe Options ////////////////////////////////////////////
	$subscriptions_options = array (
	      'from_name' =>  $blogname . ' - Forum',
	      'from_email' =>  $adminemail,
		  'admin_emails' => $adminemail,
		  'new_topic_notify' => 1,
		  'new_reply_notify' => 0,
	      'confirmation_email_subject' =>  "Please confirm subscription to [entry_title]",
	      'confirmation_email_message' =>  "Hello [member_name]!<br>\r\n Thank you for subscribing.<br>\r\n This is an automated response.<br>\r\n We are glad to inform you that after confirmation you will get updates from - [entry_title].<br>\r\n Please click on link below to complete this step.<br>\r\n [confirm_link]" ,
	      'new_topic_notification_email_subject' =>  "New Topic" ,
	      'new_topic_notification_email_message' =>  "Hello [member_name]!<br>\r\n New topic has been created on your subscribed forum - [forum].\r\n <br><br>\r\n <strong>[topic_title]</strong>\r\n <blockquote>\r\n [topic_desc]\r\n </blockquote>\r\n <br><hr>\r\n If you want to unsubscribe from this forum please use the link below.<br>\r\n [unsubscribe_link]" ,
		  'new_post_notification_email_subject' =>  "New Reply" ,
	      'new_post_notification_email_message' =>  "Hello [member_name]!<br>\r\n New reply has been posted on your subscribed topic - [topic].\r\n <br><br>\r\n <strong>[reply_title]</strong>\r\n <blockquote >\r\n [reply_desc]\r\n </blockquote>\r\n <br><hr>\r\n If you want to unsubscribe from this topic please use the link below.<br>\r\n [unsubscribe_link]" ,
	      'report_email_subject' => "Forum Post Report",
		  'report_email_message' => "<strong>Report details:</strong>\r\n Reporter: [reporter], <br>\r\n Message: [message],<br>\r\n <br>\r\n [post_url]",
		  //'spam_notification_email_subject' => 'New Banned User',
		  //'spam_notification_email_message' => "Hello [member_name]!<br>\r\n Please check this user's topics/posts and consider to Delete or Unban.<br>\r\n User Activity: [profile_activity_url]",
		  'update' =>  '1' 
	 );
	 wpforo_update_options( 'wpforo_subscribe_options', $subscriptions_options );
	
	#################################################################
	// Tool Options - Antispam ///////////////////////////////////////
	$topic_level = mt_rand(30, 60);
	$post_level = mt_rand(30, 60);
	$wpforo_tools_antispam = array(
		'spam_filter' => 1,
		'spam_filter_level_topic' => $topic_level,
		'spam_filter_level_post' => $post_level,
		'spam_user_ban' => 0,
		'new_user_max_posts' => 5,
		'spam_user_ban_notification' => 1,
		'min_number_post_to_attach' => 3,
		'min_number_post_to_link' => 0,
		'limited_file_ext' => 'pdf|doc|docx|txt|htm|html|rtf|xml|xls|xlsx|zip|rar|tar|gz|bzip|7z',
	);
	wpforo_update_options( 'wpforo_tools_antispam', $wpforo_tools_antispam);
	
	#################################################################
	// Tool Options - Cleanup ///////////////////////////////////////
	$wpforo_tools_cleanup = array(
		'user_reg_days_ago' => 7,
		'auto_cleanup_users' => 0,
		'usergroup' => array ( 1 => '0', 5 => '0', 2 => '1', 3 => '0' ),
	);
	wpforo_update_options( 'wpforo_tools_cleanup', $wpforo_tools_cleanup);
	
	
	
	#################################################################
	// Countries ////////////////////////////////////////////////////
	$countries = array( "Afghanistan","Åland Islands","Albania","Algeria","American Samoa","Andorra","Angola","Anguilla","Antarctica",
						"Antigua and Barbuda","Argentina","Armenia","Aruba","Australia","Austria","Azerbaijan","Bahamas","Bahrain",
						"Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bermuda","Bhutan","Bolivia","Bosnia and Herzegovina",
						"Botswana","Bouvet Island","Brazil","British Indian Ocean Territory","Brunei Darussalam","Bulgaria","Burkina Faso",
						"Burundi","Cambodia","Cameroon","Canada","Cape Verde","Cayman Islands","Central African Republic","Chad","Chile",
						"China","Christmas Island","Cocos (Keeling) Islands","Colombia","Comoros","Congo","Congo, The Democratic Republic of The",
						"Cook Islands","Costa Rica","Cote D'ivoire","Croatia","Cuba","Cyprus","Czech Republic","Denmark","Djibouti","Dominica",
						"Dominican Republic","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Ethiopia","Falkland Islands (Malvinas)",
						"Faroe Islands","Fiji","Finland","France","French Guiana","French Polynesia","French Southern Territories","Gabon",
						"Gambia","Georgia","Germany","Ghana","Gibraltar","Greece","Greenland","Grenada","Guadeloupe","Guam","Guatemala",
						"Guernsey","Guinea","Guinea-bissau","Guyana","Haiti","Heard Island and Mcdonald Islands","Holy See (Vatican City State)",
						"Honduras","Hong Kong","Hungary","Iceland","India","Indonesia","Iran, Islamic Republic of","Iraq","Ireland",
						"Isle of Man","Israel","Italy","Jamaica","Japan","Jersey","Jordan","Kazakhstan","Kenya","Kiribati","Korea, Democratic People's Republic of",
						"Korea, Republic of","Kuwait","Kyrgyzstan","Lao People's Democratic Republic","Latvia","Lebanon","Lesotho","Liberia",
						"Libyan Arab Jamahiriya","Liechtenstein","Lithuania","Luxembourg","Macao","Macedonia, The Former Yugoslav Republic of",
						"Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Martinique","Mauritania","Mauritius",
						"Mayotte","Mexico","Micronesia, Federated States of","Moldova, Republic of","Monaco","Mongolia","Montenegro","Montserrat",
						"Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepal","Netherlands","Netherlands Antilles","New Caledonia",
						"New Zealand","Nicaragua","Niger","Nigeria","Niue","Norfolk Island","Northern Mariana Islands","Norway","Oman",
						"Pakistan","Palau","Palestinian Territory, Occupied","Panama","Papua New Guinea","Paraguay","Peru","Philippines",
						"Pitcairn","Poland","Portugal","Puerto Rico","Qatar","Reunion","Romania","Russian Federation","Rwanda","Saint Helena",
						"Saint Kitts and Nevis","Saint Lucia","Saint Pierre and Miquelon","Saint Vincent and The Grenadines","Samoa",
						"San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia","Seychelles","Sierra Leone","Singapore",
						"Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","South Georgia and The South Sandwich Islands",
						"Spain","Sri Lanka","Sudan","Suriname","Svalbard and Jan Mayen","Swaziland","Sweden","Switzerland","Syrian Arab Republic",
						"Taiwan, Province of China","Tajikistan","Tanzania, United Republic of","Thailand","Timor-leste","Togo","Tokelau",
						"Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Turks and Caicos Islands","Tuvalu","Uganda","Ukraine",
						"United Arab Emirates","United Kingdom","United States","United States Minor Outlying Islands","Uruguay","Uzbekistan",
						"Vanuatu","Venezuela","Viet Nam","Virgin Islands, British","Virgin Islands, U.S.","Wallis and Futuna","Western Sahara",
						"Yemen","Zambia","Zimbabwe" );
						
	add_option( 'wpforo_countries', $countries );
	
	#################################################################
	// Forum Navigation and Menu ////////////////////////////////////
	$menu_name = wpforo_phrase('wpForo Navigation', false, 'orig');
	$menu_location = 'wpforo-menu';
	$menu_exists = wp_get_nav_menu_object( $menu_name );
	if(!$menu_exists){
		$id = array();
		$menu_id = wp_create_nav_menu($menu_name);
		$id['wpforo-home'] = wp_update_nav_menu_item($menu_id, 0, array(
			'menu-item-title' =>  wpforo_phrase('Forums', false),
			'menu-item-classes' => 'wpforo-home',
			'menu-item-url' => '/%wpforo-home%/', 
			'menu-item-status' => 'publish',
			'menu-item-parent-id' => 0,
			'menu-item-position' => 0));
	
		 $id['wpforo-members'] = wp_update_nav_menu_item($menu_id, 0, array(
			'menu-item-title' =>  wpforo_phrase('Members', false),
			'menu-item-classes' => 'wpforo-members',
			'menu-item-url' => '/%wpforo-members%/', 
			'menu-item-status' => 'publish',
			'menu-item-parent-id' => 0,
			'menu-item-position' => 0));
			
		 $id['wpforo-profile'] =  wp_update_nav_menu_item($menu_id, 0, array(
			'menu-item-title' =>  wpforo_phrase('My Profile', false),
			'menu-item-classes' => 'wpforo-profile',
			'menu-item-url' => '/%wpforo-profile-home%/', 
			'menu-item-status' => 'publish',
			'menu-item-parent-id' => 0,
			'menu-item-position' => 0));
		
		if(isset($id['wpforo-profile']) && $id['wpforo-profile']){
			$id['wpforo-profile-account'] =  wp_update_nav_menu_item($menu_id, 0, array(
				'menu-item-title' =>  wpforo_phrase('Account', false),
				'menu-item-classes' => 'wpforo-profile-account',
				'menu-item-url' => '/%wpforo-profile-account%/', 
				'menu-item-status' => 'publish',
				'menu-item-parent-id' => $id['wpforo-profile'],
				'menu-item-position' => 1)
			);
			$id['wpforo-profile-activity'] =  wp_update_nav_menu_item($menu_id, 0, array(
				'menu-item-title' =>  wpforo_phrase('Activity', false),
				'menu-item-classes' => 'wpforo-profile-activity',
				'menu-item-url' => '/%wpforo-profile-activity%/', 
				'menu-item-status' => 'publish',
				'menu-item-parent-id' => $id['wpforo-profile'],
				'menu-item-position' => 1)
			);
			$id['wpforo-profile-subscriptions'] =  wp_update_nav_menu_item($menu_id, 0, array(
				'menu-item-title' =>  wpforo_phrase('Subscriptions', false),
				'menu-item-classes' => 'wpforo-profile-subscriptions',
				'menu-item-url' => '/%wpforo-profile-subscriptions%/', 
				'menu-item-status' => 'publish',
				'menu-item-parent-id' => $id['wpforo-profile'],
				'menu-item-position' => 2)
			);
		}
		
		$id['wpforo-register'] =  wp_update_nav_menu_item($menu_id, 0, array(
			'menu-item-title' =>  wpforo_phrase('Register', false),
			'menu-item-classes' => 'wpforo-register',
			'menu-item-url' => '/%wpforo-register%/', 
			'menu-item-status' => 'publish',
			'menu-item-parent-id' => 0,
			'menu-item-position' => 0));
		
		$id['wpforo-login'] =  wp_update_nav_menu_item($menu_id, 0, array(
			'menu-item-title' =>  wpforo_phrase('Login', false),
			'menu-item-classes' => 'wpforo-login',
			'menu-item-url' => '/%wpforo-login%/', 
			'menu-item-status' => 'publish',
			'menu-item-parent-id' => 0,
			'menu-item-position' => 0));
		
		$id['wpforo-logout'] =  wp_update_nav_menu_item($menu_id, 0, array(
			'menu-item-title' =>  wpforo_phrase('Logout', false),
			'menu-item-classes' => 'wpforo-logout',
			'menu-item-url' => '/%wpforo-logout%/', 
			'menu-item-status' => 'publish',
			'menu-item-parent-id' => 0,
			'menu-item-position' => 0));
			
		if( !has_nav_menu( $menu_location ) ){
			$locations = get_theme_mod('nav_menu_locations');
			if(empty($locations)) $locations = array();
			$locations[$menu_location] = $menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}
	
	#################################################################
	// Forum Access Array ///////////////////////////////////////////
	$forum_cans = array (
	  'vf' => 'Can view forum',
	  'ct' => 'Can create topic',
	  'vt' => 'Can view topic',
	  'et' => 'Can edit topic',
	  'dt' => 'Can delete topic',
	  'cr' => 'Can post reply',
	  'vr' => 'Can view replies',
	  'er' => 'Can edit replies',
	  'dr' => 'Can delete replies',
	  'eot' => 'Can edit own topic',
	  'eor' => 'Can edit own reply',
	  'dot' => 'Can delete own topic',
	  'dor' => 'Can delete own reply',
	  'l'   => 'Can like',
	  'r'   => 'Can report',
	  's'   => 'Can set topic sticky',
	  'p'   => 'Can set topic private',
	  'op'  => 'Can set own topic private',
	  'vp'  => 'Can view private topic',
	  'au'  => 'Can approve/unapprove content',
	  'sv'  => 'Can set topic solved',
	  'osv' => 'Can set own topic solved',
	  'v'   => 'Can vote',
	  'a'   => 'Can attach file',
	  'va'  => 'Can view attached files',
	  'at'  => 'Can set topic answered',
	  'oat' => 'Can set own topic answered',
	  'cot' => 'Can close topic',
	  'mt'  => 'Can move topic'
	);
	wpforo_update_options( 'wpforo_forum_cans', $forum_cans);
	
	#################################################################
	// Access Sets //////////////////////////////////////////////////
	$cans_n = array('vf'  => 0, 'ct'  => 0, 'vt'  => 0, 'et'  => 0, 'dt' => 0,
					'cr'  => 0, 'vr'  => 0, 'er'  => 0, 'dr'  => 0,
					'eot' => 0, 'eor' => 0, 'dot' => 0,	'dor' => 0,	
					'l'   => 0, 'r'   => 0, 's'   => 0, 'au'  => 0, 'p'   => 0, 'op' => 0, 'vp' => 0, 'sv'  => 0, 'osv'  => 0, 'v'  => 0, 'a' => 0, 'va' => 0, 
					'at'  => 0, 'oat' => 0, 'cot' => 0, 'mt' => 0);
	$cans_r = array('vf'  => 1, 'ct'  => 0, 'vt'  => 1, 'et'  => 0, 'dt' => 0,
					'cr'  => 0, 'vr'  => 1, 'er'  => 0, 'dr' => 0,
					'eot' => 0, 'eor' => 0, 'dot' => 0,	'dor' => 0,	
					'l'   => 0, 'r'   => 0, 's'   => 0, 'au'  => 0, 'p'   => 0, 'op' => 0, 'vp' => 0, 'sv'  => 0, 'osv' => 0, 'v' => 0, 'a' => 0, 'va' => 1,
					'at'  => 0, 'oat' => 0, 'cot' => 0, 'mt' => 0);
	$cans_s = array('vf'  => 1, 'ct'  => 1, 'vt'  => 1, 'et'  => 0, 'dt' => 0,
					'cr'  => 1, 'vr'  => 1, 'er'  => 0, 'dr' => 0,
					'eot' => 1, 'eor' => 1, 'dot' => 1,	'dor' => 1,	
					'l'   => 1, 'r'   => 1, 's'   => 0, 'au'  => 0, 'p'   => 0, 'op' => 1, 'vp' => 0, 'sv'  => 0, 'osv' => 1, 'v' => 1, 'a' => 1, 'va' => 1,
					'at'  => 0, 'oat' => 1, 'cot' => 0, 'mt' => 0);
	$cans_m =  array('vf'  => 1, 'ct'  => 1, 'vt'  => 1, 'et'  => 1, 'dt' => 1,
					'cr'  => 1, 'vr'  => 1, 'er'  => 1, 'dr' => 1,
					'eot' => 1, 'eor' => 1, 'dot' => 1,	'dor' => 1,	
					'l'   => 1, 'r'   => 1, 's'   => 1, 'au'  => 1, 'p'   => 1, 'op' => 1, 'vp' => 1, 'sv'  => 1, 'osv'  => 1, 'v' => 1, 'a' => 1, 'va' => 1,
					'at'  => 1, 'oat' => 1, 'cot' => 1, 'mt' => 1);
	$cans_a =  array('vf'  => 1, 'ct'  => 1, 'vt'  => 1, 'et'  => 1, 'dt' => 1,
					'cr'   => 1, 'vr'  => 1, 'er'  => 1, 'dr'  => 1,
					'eot'  => 1, 'eor' => 1, 'dot' => 1, 'dor' => 1,	
					'l'    => 1, 'r'   => 1, 's'   => 1, 'au'  => 1, 'p'   => 1, 'op' => 1, 'vp' => 1, 'sv'  => 1, 'osv' => 1, 'v'   => 1, 'a' => 1, 'va' => 1,
					'at'   => 1, 'oat' => 1, 'cot' => 1, 'mt'  => 1);
	
	$sql = "SELECT * FROM `".$wpforo->db->prefix."wpforo_accesses`";
	$accesses = $wpforo->db->get_results($sql, ARRAY_A);
	if( empty($accesses) ){
		
		$cans_n = serialize($cans_n);
		$cans_r = serialize($cans_r);
		$cans_s = serialize($cans_s);
		$cans_m = serialize($cans_m);
		$cans_a = serialize($cans_a);
		
		$sql = "INSERT IGNORE INTO `".$wpforo->db->prefix."wpforo_accesses` 
			(`access`, `title`, cans) VALUES	
			('no_access', 'No access', '". $cans_n ."'),
			('read_only', 'Read only access', '". $cans_r ."'),
			('standard', 'Standard access', '". $cans_s ."'),
			('moderator', 'Moderator access', '".$cans_m."'),
			('full', 'Full access', '".$cans_a."')";
		
		$wpforo->db->query( $sql );
	}
	else{
		foreach($accesses as $access){
			$default = array();
			$data_update = array();
			$current = unserialize($access['cans']);
			if( strtolower($access['access']) == 'no_access' ) $default = $cans_n;
			elseif( strtolower($access['access']) == 'read_only' ) $default = $cans_r;
			elseif( strtolower($access['access']) == 'standard' ) $default = $cans_s;
			elseif( strtolower($access['access']) == 'moderator' ) $default = $cans_m;
			elseif( strtolower($access['access']) == 'full' ) $default = $cans_a;
			if( !empty($default) ){
				$data_update = array_merge($default, $current);
				if( !empty($data_update) ){
					$data_update = serialize($data_update);
					$wpforo->db->query("UPDATE `".$wpforo->db->prefix."wpforo_accesses` SET `cans` = '" . $wpforo->db->_real_escape($data_update) . "' WHERE `accessid` = " . intval($access['accessid']) );
				}
			}
		}
	}
	
	
	#################################################################
	// Usergroup Cans ///////////////////////////////////////////////
	$usergroup_cans = array (
		'cf'   => 'Dashboard - Can create forum',
		'ef'   => 'Dashboard - Can edit forum',
		'df'   => 'Dashboard - Can delete forum',
		'vm'   => 'Dashboard - Members Menu',
		'aum'  => 'Dashboard - Moderation Menu',
		'em'   => 'Dashboard - Can edit member',
	  	'bm'   => 'Dashboard - Can ban member',
	  	'dm'   => 'Dashboard - Can delete member',
		'vmg'  => 'Dashboard - Usergroup Menu',
		'aup'  => 'Front - Can pass moderation',
		'vmem' => 'Front - Can view members',
		'vprf' => 'Front - Can view profiles',
		'upa'  => 'Front - Can upload avatar',
		'ups'  => 'Front - Can have signature', 
		'va'   => 'Front - Can view avatars',
		'vmu'  => 'Front - Can view member username',
		'vmm'  => 'Front - Can view member email',
		'vmt'  => 'Front - Can view member title',
		'vmct' => 'Front - Can view member custom title',
		'vmr'  => 'Front - Can view member reputation',
		'vmw'  => 'Front - Can view member website',
		'vmsn' => 'Front - Can view member social networks',
		'vmrd' => 'Front - Can view member reg. date',
		'vmlad'=> 'Front - Can view member last active date',
		'vip'  => 'Front - Can view member ip address', 
		'vml'  => 'Front - Can view member location', 
		'vmo'  => 'Front - Can view member occupation', 
		'vms'  => 'Front - Can view member signature', 
		'vmam' => 'Front - Can view member about me', 
		'vmpn' => 'Front - Can view member phone number',
		'vwpm' => 'Front - Can write PM'
	);
	wpforo_update_options( 'wpforo_usergroup_cans', $usergroup_cans );
	
	#################################################################
	// Usergroup ////////////////////////////////////////////////////
	$cans_admin = array('cf'    => '1', 'ef'   => '1', 'df'   => '1', 'vm'   => '1', 'aum'   => '1', 'em' => '1', 'vmg' => '1', 'aup' => '1', 'vmem' => '1',  'vprf' => '1',
						'bm'    => '1', 'dm'    => '1', 'upa'  => '1', 'ups'  => '1', 'va'   => '1',
						'vmu'   => '1', 'vmm'  => '1', 'vmt'  => '1', 'vmct' => '1',
						'vmr'   => '1', 'vmw'  => '1', 'vmsn' => '1', 'vmrd' => '1',
						'vmlad' => '1',	'vip'  => '1', 'vml'  => '1', 'vmo'  => '1', 
						'vms'   => '1', 'vmam' => '1', 'vmpn' => '1', 'vwpm' => '1');
	$cans_moder = array('cf'    => '0', 'ef'   => '0', 'df'   => '0', 'vm'   => '0', 'aum'   => '1', 'em' => '0', 'vmg' => '0', 'aup' => '1', 'vmem' => '1',  'vprf' => '1',
						'bm'    => '1', 'dm'    => '1', 'upa'  => '1', 'ups'  => '1', 'va'   => '1',
						'vmu'   => '1', 'vmm'  => '1', 'vmt'  => '1', 'vmct' => '1',
						'vmr'   => '1', 'vmw'  => '1', 'vmsn' => '1', 'vmrd' => '1',
						'vmlad' => '1',	'vip'  => '1', 'vml'  => '1', 'vmo'  => '1', 
						'vms'   => '1', 'vmam' => '1', 'vmpn' => '1', 'vwpm' => '1');
	$cans_reg = array(  'cf'    => '0', 'ef'   => '0', 'df'   => '0', 'vm'   => '0', 'aum'   => '0', 'em' => '0', 'vmg' => '0', 'aup' => '1', 'vmem' => '1',  'vprf' => '1',
						'bm'    => '0', 'dm'    => '0', 'upa'  => '1', 'ups'  => '1', 'va'   => '1',
						'vmu'   => '1', 'vmm'  => '0', 'vmt'  => '1', 'vmct' => '1',
						'vmr'   => '1', 'vmw'  => '1', 'vmsn' => '1', 'vmrd' => '1',
						'vmlad' => '1',	'vip'  => '0', 'vml'  => '1', 'vmo'  => '1', 
						'vms'   => '1', 'vmam' => '1', 'vmpn' => '0', 'vwpm' => '1');
	$cans_guest = array('cf'  => '0', 'ef'     => '0', 'df'   => '0', 'vm'   => '0', 'aum'   => '0', 'em' => '0', 'vmg' => '0', 'aup' => '0', 'vmem' => '1',  'vprf' => '1',
						'bm'    => '0', 'dm'    => '0', 'upa'  => '0', 'ups'  => '0', 'va'   => '0',
						'vmu'   => '0', 'vmm'  => '0', 'vmt'  => '0', 'vmct' => '0',
						'vmr'   => '1', 'vmw'  => '0', 'vmsn' => '1', 'vmrd' => '0',
						'vmlad' => '1',	'vip'  => '0', 'vml'  => '1', 'vmo'  => '1', 
						'vms'   => '0', 'vmam' => '1', 'vmpn' => '0', 'vwpm' => '0');
	$cans_customer = array(  'cf'    => '0', 'ef'   => '0', 'df'   => '0', 'vm'   => '0', 'aum'   => '0', 'em' => '0', 'vmg' => '0', 'aup' => '0', 'vmem' => '1',  'vprf' => '1',
						'bm'    => '0', 'dm'    => '0', 'upa'  => '1', 'ups'  => '1', 'va'   => '1',
						'vmu'   => '1', 'vmm'  => '0', 'vmt'  => '1', 'vmct' => '1',
						'vmr'   => '1', 'vmw'  => '1', 'vmsn' => '1', 'vmrd' => '1',
						'vmlad' => '1',	'vip'  => '0', 'vml'  => '1', 'vmo'  => '1', 
						'vms'   => '1', 'vmam' => '1', 'vmpn' => '0', 'vwpm' => '1');
	
	
	$sql = "SELECT * FROM `".$wpforo->db->prefix."wpforo_usergroups`";
	$usergroups = $wpforo->db->get_results($sql, ARRAY_A);
	if( empty($usergroups) ){
		$cans_admin = serialize( $cans_admin );
		$cans_moder = serialize( $cans_moder );
		$cans_reg = serialize( $cans_reg );
		$cans_guest = serialize( $cans_guest );
		$cans_customer = serialize( $cans_customer );
		$sql = "INSERT IGNORE INTO `".$wpforo->db->prefix."wpforo_usergroups` 
			(`name`, `cans`) VALUES	('Admin', '$cans_admin'),('Moderator', '$cans_moder'),('Registered', '$cans_reg'),('Guest', '$cans_guest'),('Customer', '$cans_customer')";
		$wpforo->db->query($sql);
	}
	else{
		foreach($usergroups as $usergroup){
			$default = array();
			$data_update = array();
			$current = unserialize($usergroup['cans']);
			if( strtolower($usergroup['name']) == 'admin' ) $default = $cans_admin;
			elseif( strtolower($usergroup['name']) == 'moderator' ) $default = $cans_moder;
			elseif( strtolower($usergroup['name']) == 'registered' ) $default = $cans_reg;
			elseif( strtolower($usergroup['name']) == 'guest' ) $default = $cans_guest;
			elseif( strtolower($usergroup['name']) == 'customer' ) $default = $cans_customer;
			if( !empty($default) ){
				$data_update = array_merge($default, $current);
				if( !empty($data_update) ){
					$data_update = serialize($data_update);
					$wpforo->db->query("UPDATE `".$wpforo->db->prefix."wpforo_usergroups` SET `cans` = '" . $wpforo->db->_real_escape($data_update) . "' WHERE `groupid` = " . intval($usergroup['groupid']) );
				}
			}
		}
	}
	$sql = "SELECT COUNT(*) FROM `".$wpforo->db->prefix."wpforo_forums`";
	$count = $wpforo->db->get_var($sql);
	if(!$count){
		if( $parentid = $wpforo->forum->add( array( 'title' => 'Main Category', 'description' => 'This is a simple category / section' ), FALSE ) ){
			$wpforo->forum->add( array( 'title' => 'Main Forum', 'description' => 'This is a simple parent forum', 'parentid' => $parentid, 'icon' => 'fa-comments' ), FALSE );
		}
	}
	
	#################################################################
	// Permalink Settings ///////////////////////////////////////////
	$permalink_structure = get_option( 'permalink_structure' );
	if( !$permalink_structure ){
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
	}
	
	#################################################################
	// Creating Forum Page //////////////////////////////////////////
	if( !$wpforo->pageid || 
		!$wpforo->db->get_var("SELECT `ID` FROM `{$wpforo->db->prefix}posts` WHERE `ID` = '".intval($wpforo->pageid)."' AND ( `post_content` LIKE '%[wpforo]%' OR `post_content` LIKE '%[wpforo-index]%' ) AND `post_status` LIKE 'publish' AND `post_type` IN('post', 'page')") ){
		if( !$page_id = $wpforo->db->get_var("SELECT `ID` FROM `{$wpforo->db->prefix}posts` WHERE `post_content` LIKE '%[wpforo]%' AND `post_status` LIKE 'publish' AND `post_type` IN('post', 'page')") ){
			$wpforo_page = array(
				'post_date' => current_time( 'mysql', 1 ),
				'post_date_gmt' => current_time( 'mysql', 1 ),
				'post_content' => '[wpforo]',
				'post_title' => 'Forum',
				'post_status' => 'publish',
				'comment_status' => 'close',
				'ping_status' => 'close',
				'post_name' => 'community',
				'post_modified' => current_time( 'mysql', 1 ),
				'post_modified_gmt' => current_time( 'mysql', 1 ),
				'post_parent' => 0,
				'menu_order' => 0,
				'post_type' => 'page'
			);
			$page_id = wp_insert_post( $wpforo_page );
		}
		if( $page_id && !is_wp_error($page_id) ){
			update_option( 'wpforo_pageid', $page_id );
			update_option( 'wpforo_use_home_url', '0' );
			$wpforo_url = get_wpf_option('wpforo_url');
			if( !$wpforo_url ){
				update_option( 'wpforo_permastruct', 'community' );
				update_option( 'wpforo_url', esc_url( home_url('/') ) . "community/" );
			}else{
				if( !$wpforo->permastruct ){
					update_option( 'wpforo_permastruct',  basename($wpforo_url) );
					update_option( 'wpforo_url', esc_url( home_url('/') ) . basename($wpforo_url) . "/" );
				}else{
					update_option( 'wpforo_url', esc_url( home_url('/') ) . $wpforo->permastruct . "/" );
				}
			}
		}
	}else{
		if( !$wpforo->use_home_url ) update_option( 'wpforo_use_home_url', '0' );
		if( !$wpforo->permastruct ) update_option( 'wpforo_permastruct', basename( get_wpf_option('wpforo_url') ) );
		$wpforo->db->query("UPDATE `{$wpforo->db->prefix}posts` SET `post_content` = REPLACE(`post_content`, '[wpforo-index]', '[wpforo]') WHERE `ID` = '{$wpforo->pageid}'");
	}

	$wpforo->pageid = get_wpf_option( 'wpforo_pageid');
	$wpforo->permastruct = trim( get_wpf_option('wpforo_permastruct'), '/' );
	flush_rewrite_rules(FALSE);
	nocache_headers();
	
	
	#################################################################
	// Importing Language Packs and Phrases /////////////////////////
	$wpforo->phrase->xml_import('english.xml', 'install');
	
	#################################################################
	// Creating wpforo folders //////////////////////////////////////
	$upload_array = wp_upload_dir();
	$wpforo_upload_dir = $upload_array['basedir'].'/wpforo/';
	if (!is_dir($wpforo_upload_dir)) {
		wp_mkdir_p($wpforo_upload_dir);
	}
	$avatars_upload_dir=$upload_array['basedir'].'/wpforo/avatars/';
	if (!is_dir($avatars_upload_dir)) {
		wp_mkdir_p($avatars_upload_dir);
	}
	
	#################################################################
	// RESET USER CACHE /////////////////////////////////////////////
	$wpforo->member->clear_db_cache();
	
	#################################################################
	// RESET FUNCTIONS //////////////////////////////////////////////
	$sql = "SHOW COLUMNS FROM `".$wpdb->prefix."wpforo_phrases` WHERE `Field` LIKE 'package'";
	if( !$wpdb->get_row($sql, ARRAY_A) ){
		$sql = "ALTER TABLE `".$wpdb->prefix."wpforo_phrases` ADD COLUMN `package` VARCHAR(255) NOT NULL DEFAULT 'wpforo'";
		@$wpdb->query( $sql );
	}
	$wpforo->phrase->clear_cache();
	
	#################################################################
	// ADD `private` field in TOPIC TABLE  ///////////////////////////
	$args = array( 'table' => $wpdb->prefix . 'wpforo_topics', 'col' => 'private', 'check' => 'col_exists' );
	if( !wpforo_db_check( $args ) ){
		$sql = "ALTER TABLE `".$wpdb->prefix."wpforo_topics` ADD `private` TINYINT(1) NOT NULL DEFAULT '0', ADD INDEX `is_private` (`private`);";
		@$wpdb->query( $sql );
		$sql = "ALTER TABLE `".$wpdb->prefix."wpforo_topics` ADD INDEX `own_private` ( `userid`, `private`);";
		@$wpdb->query( $sql );
	}
	// ADD INDEXES in wpforo_views TABLE///////////////////////////
	$args = array( 'table' => $wpdb->prefix . 'wpforo_views', 'col' => 'topicid', 'check' => 'key_exists' );
	if( !wpforo_db_check( $args ) ){
		$sql = "ALTER TABLE `".$wpdb->prefix."wpforo_views` ADD INDEX(`userid`);";
		@$wpdb->query( $sql );
		$sql = "ALTER TABLE `".$wpdb->prefix."wpforo_views` ADD INDEX(`topicid`);";
		@$wpdb->query( $sql );
		$sql = "ALTER TABLE `".$wpdb->prefix."wpforo_views` ADD UNIQUE( `userid`, `topicid`);";
		@$wpdb->query( $sql );
		$sql = "ALTER TABLE `".$wpdb->prefix."wpforo_likes` ADD UNIQUE( `userid`, `postid`);";
		@$wpdb->query( $sql );
	}
	$args = array( 'table' => $wpdb->prefix . 'wpforo_views', 'col' => 'created', 'check' => 'col_type' );
	$col_type = wpforo_db_check( $args );
	if( $col_type != 'int(11)' ){
		$sql = "ALTER TABLE `".$wpdb->prefix."wpforo_views` CHANGE `created` `created` INT(11) NOT NULL;";
		@$wpdb->query( $sql );
	}
	
	// ADD `status` field in TOPICS & POSTS TABLE  ///////////////////////////
	$args = array( 'table' => $wpdb->prefix . 'wpforo_topics', 'col' => 'status', 'check' => 'col_exists' );
	if( !wpforo_db_check( $args ) ){
		$sql = "ALTER TABLE `".$wpdb->prefix."wpforo_topics` ADD `status` TINYINT(1) NOT NULL DEFAULT '0', ADD INDEX `status` (`status`);";
		@$wpdb->query( $sql );
		$sql = "ALTER TABLE `".$wpdb->prefix."wpforo_topics` ADD INDEX `forumid_status` ( `forumid`, `status`);";
		@$wpdb->query( $sql );
		$sql = "ALTER TABLE `".$wpdb->prefix."wpforo_posts` ADD `status` TINYINT(1) NOT NULL DEFAULT '0', ADD INDEX `status` (`status`);";
		@$wpdb->query( $sql );
		$sql = "ALTER TABLE `".$wpdb->prefix."wpforo_posts` ADD INDEX `topicid_status` ( `topicid`, `status`);";
		@$wpdb->query( $sql );
	}
	// ADD `name` and `email` field in TOPIC TABLE  ///////////////////////////
	$args = array( 'table' => $wpdb->prefix . 'wpforo_topics', 'col' => 'name', 'check' => 'col_exists' );
	if( !wpforo_db_check( $args ) ){
		$sql = "ALTER TABLE `" . $wpdb->prefix . "wpforo_topics` ADD `name` VARCHAR(50) NOT NULL  AFTER `status`,  ADD `email` VARCHAR(50) NOT NULL  AFTER `name`";
		@$wpdb->query( $sql );
		$sql = "ALTER TABLE `" . $wpdb->prefix . "wpforo_posts` ADD `name` VARCHAR(50) NOT NULL  AFTER `status`,  ADD `email` VARCHAR(50) NOT NULL  AFTER `name`";
		@$wpdb->query( $sql );
		$sql = "ALTER TABLE `" . $wpdb->prefix . "wpforo_topics` ADD KEY `email` (`email`)";
		@$wpdb->query( $sql );
		$sql = "ALTER TABLE `" . $wpdb->prefix . "wpforo_posts` ADD KEY `email` (`email`)";
		@$wpdb->query( $sql );
	}
	#################################################################
	// UPDATE VERSION - END /////////////////////////////////////////
	update_option('wpforo_version', WPFORO_VERSION);
	$wpforo->notice->clear();
	wpforo_clean_cache();
}


function wpforo_update() {
	if ( get_option('wpforo_version') && WPFORO_VERSION !== get_option('wpforo_version') ) wpforo_activation();
}
add_action('wp_loaded', 'wpforo_update');


function wpforo_update_options( $option_key, $default, $exlude = array() ) {
	
	$option = get_option( $option_key, array() );
	
	if( !empty($option) ){
		if( !empty($exlude) ){
			foreach( $exlude as $key ){
				if( isset($default[$key]) ) unset($default[$key]);
			}
		}
		$option_update = array_merge($default, $option);
	}
	else{
		$option_update = $default;
	}
	
	update_option( $option_key, $option_update );
}


function wpforo_deactivation() {}


function wpforo_uninstall() {	
	
	global $wpforo, $wpdb;
	
	if( ! wpforo_is_admin() ) return;
	if( ! current_user_can( 'activate_plugins' ) ) return;
	$QUERY_STRING = trim(preg_replace('|_wpnonce=[^\&\?\=]*|is', '', $_SERVER['QUERY_STRING']), '&');
	
	if( 'action=wpforo-uninstall' == trim($QUERY_STRING) ){

		$tables = array(    $wpdb->prefix . 'wpforo_accesses',
							$wpdb->prefix . 'wpforo_forums',
							$wpdb->prefix . 'wpforo_languages',
							$wpdb->prefix . 'wpforo_likes',
							$wpdb->prefix . 'wpforo_phrases',
							$wpdb->prefix . 'wpforo_profiles',
							$wpdb->prefix . 'wpforo_posts',
							$wpdb->prefix . 'wpforo_subscribes',
							$wpdb->prefix . 'wpforo_topics',
							$wpdb->prefix . 'wpforo_usergroups',
							$wpdb->prefix . 'wpforo_views',
							$wpdb->prefix . 'wpforo_votes');
		
		foreach($tables as $table){
			if( strpos( $table, '_wpforo_' ) !== FALSE){
				$sql = "DROP TABLE IF EXISTS `$table`;";
				$wpdb->query( $sql );
			}
		}
		
		if( isset($wpforo->pageid) && $wpforo->pageid ){
			wp_delete_post( $wpforo->pageid, true );
		}
		
		$options = array( 'wpforo_version',
						  'wpforo_url',
						  'wpforo_stat',
						  'wpforo_general_options',
						  'wpforo_pageid',
						  'wpforo_count_per_page',
						  'wpforo_default_groupid',
						  'wpforo_usergroup_cans',
						  'wpforo_forum_options',
						  'wpforo_forum_cans',
						  'wpforo_post_options',
						  'wpforo_member_options',
						  'wpforo_subscribe_options',
						  'wpforo_countries',
						  'wpforo_theme_options',
						  'wpforo_features',
						  'wpforo_style_options',
						  'wpforo_permastruct',
						  'wpforo_use_home_url',
						  'wpforo_excld_urls',
						  'wpforo_tools_antispam',
						  'wpforo_tools_cleanup'
		);
		 
		foreach($options as $option){
			if( strpos( $option, 'wpforo_' ) !== FALSE){
				delete_option( $option );
			}
		}
		
		$wpdb->query( "DELETE FROM `" . $wpdb->base_prefix ."usermeta` WHERE `meta_key` = '_wpf_member_obj'" );
		$wpdb->query( "DELETE FROM `" . $wpdb->prefix ."options` WHERE option_name LIKE 'widget_wpforo_widget_%'" );
		
		$menu = wp_get_nav_menu_object( 'wpforo-navigation' );
		wp_delete_nav_menu( $menu->term_id );
		wp_delete_post($wpforo->pageid, TRUE);
		
		deactivate_plugins( WPFORO_BASENAME );
		
	}
	else{
		return;
	}
}

function wpforo_profile_notice(){
	global $wpdb;
	$button = '';
	$users = $wpdb->get_var("SELECT COUNT(*) FROM `" . $wpdb->prefix . "users`");
	$profiles = $wpdb->get_var("SELECT COUNT(*) FROM `" . $wpdb->prefix . "wpforo_profiles`");
	$delta = $users - $profiles; 
	$status = ( $delta > 2 ) ? round((( $profiles * 100 ) / $users ), 1) . '% (' . $profiles . ' / ' . $users . ') ' : '100%';
	$btext = ( $profiles == 0 ) ? __( 'Start Profile Synchronization', 'wpforo') : __( 'Continue Synchronization', 'wpforo');
	$url = admin_url('admin.php?page=wpforo-community&action=synch');
	$class = 'wpforo-mnote notice notice-warning is-dismissible';
	$note = __( 'This process may take a few seconds or dozens of minutes, please be patient and don\'t close this page.', 'wpforo');
	$info = __( 'You can permanently disable this message in Dashboard > Forums > Features admin page.', 'wpforo');
	$button = '<a href="' . $url . '" class="button button-primary button-large" style="font-size:14px;">' . $btext . ' &gt;&gt;</a>';
	$header = __( 'wpForo Forum Installation | ', 'wpforo' );
	$message = __( 'Forum users\' profile data are not synchronized yet, this step is required! Please click the button below to complete installation.', 'wpforo' );
	echo '<div class="' . $class . '" style="padding:15px 20px;"><h2 style="margin:0px;">' . esc_html($header) . $status . ' </h2><p style="font-size:15px;margin:5px 0px;">' . $message . '</p><p style="margin:0px 0px 10px 0px;">' . $button . '</p><hr /><p style="margin:0px;color:#dd0000;">' . $note . '</p><p style="margin:0px;color:#999; font-size:12px;">' . $info . '</p></div>'; 	
}

?>