<?php
/*
* Plugin Name: Forums - wpForo
* Plugin URI: https://wpforo.com
* Description: Forums wpForo is a new generation of forum plugins. It's full-fledged forum solution for your community. Comes with multiple modern forum layouts.
* Author: gVectors Team (A. Chakhoyan, R. Hovhannisyan)
* Author URI: https://gvectors.com/
* Version: 1.3.1
* Text Domain: wpforo
* Domain Path: /wpf-languages
*/

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;
if( !defined( 'WPFORO_VERSION' ) ) define('WPFORO_VERSION', '1.3.1');

function wpforo_load_plugin_textdomain() { load_plugin_textdomain( 'wpforo', FALSE, basename( dirname( __FILE__ ) ) . '/wpf-languages/' ); }
add_action( 'plugins_loaded', 'wpforo_load_plugin_textdomain' );

if( !class_exists( 'wpForo' ) ) {

	define('WPFORO_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ));
	define('WPFORO_URL', rtrim( plugins_url( '', __FILE__ ), '/' ));
	define('WPFORO_FOLDER', rtrim( plugin_basename(dirname(__FILE__)), '/'));
	define('WPFORO_BASENAME', plugin_basename(__FILE__)); //wpforo/wpforo.php

	define('WPFORO_THEME_DIR', WPFORO_DIR . '/wpf-themes' );
	define('WPFORO_THEME_URL', WPFORO_URL . '/wpf-themes' );
	
	include( WPFORO_DIR . '/wpf-includes/wpf-hooks.php' );
	include( WPFORO_DIR . '/wpf-includes/wpf-actions.php');
	include( WPFORO_DIR . '/wpf-includes/functions.php' );
	if(wpforo_is_admin()) {
		include( WPFORO_DIR . '/wpf-includes/functions-installation.php' );
	}
	include( WPFORO_DIR . '/wpf-includes/functions-integration.php' );
	include( WPFORO_DIR . '/wpf-includes/functions-template.php' );
	include( WPFORO_DIR . '/wpf-includes/class-cache.php' );
	include( WPFORO_DIR . '/wpf-includes/class-forums.php' );
	include( WPFORO_DIR . '/wpf-includes/class-topics.php' );
	include( WPFORO_DIR . '/wpf-includes/class-posts.php' );
	include( WPFORO_DIR . '/wpf-includes/class-usergroups.php' );
	include( WPFORO_DIR . '/wpf-includes/class-members.php' );
	include( WPFORO_DIR . '/wpf-includes/class-permissions.php' );
	include( WPFORO_DIR . '/wpf-includes/class-phrases.php');
	include( WPFORO_DIR . '/wpf-includes/class-subscribes.php' );
	include( WPFORO_DIR . '/wpf-includes/class-template.php' );
	include( WPFORO_DIR . '/wpf-includes/class-notices.php' );
	include( WPFORO_DIR . '/wpf-includes/class-feed.php' );
	include( WPFORO_DIR . '/wpf-includes/class-moderation.php' );

	class wpForo{
	
		public $options = array();
		public $db;
		public $phrases;
		public $access;
		public $usergroup;
		public $theme;
		public $addons = array();
		public $current_object;
		public $menu = array();
		
		public	function __construct(){
			$this->options();
            $this->setup();
        }
		
		public	function init(){
			$this->cache->init();
			$this->member->init_current_user();
			$this->init_current_object();
			$this->moderation->init();
			$this->tpl->init_member_templates();
			$this->tpl->init_nav_menu();
			add_action( 'wp_loaded', 'wpforo_actions');
		}
		
		private function options(){
			global $wpdb;
			$this->db = $wpdb;
			$this->file = __FILE__;
			$this->error = NULL;
			$this->basename = plugin_basename( $this->file );

			$permalink_structure = get_option('permalink_structure');
			
			$this->use_trailing_slashes = ( '/' == substr($permalink_structure, -1, 1) );

			//OPTIONS
			$this->use_home_url = get_wpf_option('wpforo_use_home_url');
			$this->excld_urls = get_wpf_option('wpforo_excld_urls');

			$this->permastruct = trim( get_wpf_option('wpforo_permastruct'), '/' );
			$this->permastruct = preg_replace('#^/?index\.php/?#isu', '', $this->permastruct);
			$this->permastruct = trim($this->permastruct, '/');

			$this->base_permastruct = (!$this->use_home_url ? $this->permastruct : '');
			$this->base_permastruct = rtrim( ( strpos($permalink_structure, 'index.php') !== FALSE ? '/index.php/' . $this->base_permastruct : '/' . $this->base_permastruct ), '/\\' );
			$this->url = esc_url( home_url( $this->user_trailingslashit($this->base_permastruct) ) );
			$this->general_options = get_wpf_option( 'wpforo_general_options');
			$this->pageid = get_wpf_option( 'wpforo_pageid');
			$this->default_groupid = get_wpf_option('wpforo_default_groupid') ? get_option('wpforo_default_groupid') : 3;
			$this->usergroup_cans = get_wpf_option('wpforo_usergroup_cans');
			$this->forum_options = get_wpf_option('wpforo_forum_options');
			$this->forum_cans = get_wpf_option('wpforo_forum_cans');
			$this->post_options = get_wpf_option('wpforo_post_options');
			$this->member_options = get_wpf_option('wpforo_member_options');
			$this->subscribe_options = get_wpf_option('wpforo_subscribe_options');
			$this->countries = get_wpf_option('wpforo_countries');
			$this->features = get_wpf_option('wpforo_features');
			$this->tools_antispam = get_wpf_option('wpforo_tools_antispam');
			$this->tools_cleanup = get_wpf_option('wpforo_tools_cleanup');
			$this->style_options = get_wpf_option('wpforo_style_options');
			$this->theme_options = get_wpf_option('wpforo_theme_options');
			$this->theme = $this->theme_options['folder'];
			//CONSTANTS
			define('WPFORO_BASE_PERMASTRUCT', $this->base_permastruct );
			define('WPFORO_BASE_URL', $this->url );
			define('WPFORO_THEME', $this->theme );
			define('WPFORO_TEMPLATE_DIR', WPFORO_THEME_DIR . '/' . $this->theme );
			define('WPFORO_TEMPLATE_URL', WPFORO_THEME_URL . '/' . $this->theme );
		}
		
		private function setup(){
			if( wpforo_is_admin() ){ 
				register_activation_hook($this->basename, 'do_wpforo_activation');
				if( !wpforo_feature('user-synch') && get_option('wpforo_version') ){
					$users = $this->db->get_var("SELECT COUNT(*) FROM `" . $this->db->prefix . "users`");
					$profiles = $this->db->get_var("SELECT COUNT(*) FROM `" . $this->db->prefix . "wpforo_profiles`");
					$delta = $users - $profiles; 
					if( $users > 100 && $delta > 2 ){ add_action( 'admin_notices', 'wpforo_profile_notice' ); }
				}
				register_deactivation_hook($this->basename, 'do_wpforo_deactivation');
			}
		}
		
		public function phrases(){
			if($this->general_options){
				$phrases = $this->phrase->get_phrases( array( 'langid' => $this->general_options['lang'] ) );
				foreach($phrases as $phrase){
					$this->phrases[addslashes(strtolower($phrase['phrase_key']))] = $phrase['phrase_value'];
				}
			}
		}

		public function user_trailingslashit($url) {
			$rtrimed_url = '';
			$url_append_vars = '';
			if( preg_match('#(^.+?)(/?\?.*$|$)#isu', $url, $match) ){
				if(isset($match[1]) && $match[1]) $rtrimed_url = rtrim($match[1], '/\\');
				if(isset($match[2]) && $match[2]) $url_append_vars = trim($match[2], '/\\');
				if( $rtrimed_url ) {
					$home_url = rtrim( preg_replace('#/?\?.*$#isu', '', home_url()), '/\\' );
					if( $rtrimed_url == $home_url ){
						$url = $rtrimed_url . '/';
					}else{
						$url = ( $this->use_trailing_slashes ? $rtrimed_url . '/' : $rtrimed_url );
					}
				}
			}
			return $url . $url_append_vars;
		}
		
		public function get_statistic(){
			return $this->statistic();
		}
		
		public function statistic( $mode = 'get' ){
			
			if( $mode == 'get' ){
				$cached_stat = get_option( 'wpforo_stat' );
				if( !empty($cached_stat) ){
					if( isset($cached_stat['members']) && $cached_stat['members'] ) return $cached_stat;
				}
			}
			
			$stats['forums'] = $this->forum->get_count();
			$stats['topics'] = $this->topic->get_count();
			$stats['posts'] = $this->post->get_count();
			$stats['members'] = $this->member->get_count();
			$stats['online_members_count'] = $this->member->online_members_count();
			$stats['last_post_title'] = '';
			$stats['last_post_url'] = '';
			$posts = $this->topic->get_topics( array( 'orderby' => 'modified', 'order' => 'DESC', 'row_count' => 1 ) );
			if(isset($posts[0]) && !empty($posts[0])){
				if( $this->perm->forum_can( 'vf', $posts[0]['forumid'] ) ){
					$stats['last_post_title'] = $posts[0]['title'];
					$stats['last_post_url'] = $this->post->get_post_url($posts[0]['last_post']);
				}
				else{
					$stats['last_post_title'] = '';
					$stats['last_post_url'] = '';
				}
			}
			$stats['newest_member_dname'] = '';
			$stats['newest_member_profile_url'] = '';
			$members = $this->member->get_members( array( 'orderby' => 'userid', 'order' => 'DESC', 'row_count' => 1 ) );
			if(isset($members[0]) && !empty($members[0])){
				$stats['newest_member_dname'] = $members[0]['display_name'] ? $members[0]['display_name'] : urldecode($members[0]['user_nicename']);
				$stats['newest_member_profile_url'] = $this->member->get_profile_url($members[0]['ID']);
			}
			if( !empty($stats) && $stats['members'] ) update_option( 'wpforo_stat', $stats );
			return apply_filters('wpforo_get_statistic_array_filter', $stats);
		}
		
		public function init_current_object($url = ''){
			$this->current_object = array('template' => '', 'paged' => 1, 'is_404' => false);
			if(!$url) $url = wpforo_get_request_uri();
			
			if( !is_wpforo_page($url) ) return;
			
			$current_url = wpforo_get_url_query_vars_str($url);
			
			if( $this->use_home_url ) $this->permastruct = '';
			
			$current_object = array();
			$current_object['template'] = '';
			$current_object['is_404'] = false;
			
			if(isset($_GET['wpfs'])) $current_object['template'] = 'search';
			if( isset($_GET['wpforo']) ){
				switch($_GET['wpforo']){
					case 'signup':
						if(!is_user_logged_in()) $current_object['template'] = 'register';
					break;
					case 'signin':
						if(!is_user_logged_in()) $current_object['template'] = 'login';
					break;
					case 'logout':
						wp_logout();
						wp_redirect( wpforo_home_url( preg_replace('#\?.*$#is', '', wpforo_get_request_uri()) ) );
						exit();
					break;
				}
			}
			
			$wpf_url = preg_replace( '#^/?'.preg_quote($this->permastruct).'#isu', '' , $current_url, 1 );
			$wpf_url = preg_replace('#/?\?.*$#isu', '', $wpf_url);
			$wpf_url_parse = array_filter( explode('/', trim($wpf_url, '/')) );
			$wpf_url_parse = array_reverse($wpf_url_parse);

			if(in_array('paged', $wpf_url_parse)){
				foreach($wpf_url_parse as $key => $value){
					if( $value == 'paged'){
						unset($wpf_url_parse[$key]);
						break;
					}
					if(is_numeric($value)) $paged = intval($value);
					
					unset($wpf_url_parse[$key]);
				}
			}
			if(isset($_GET['wpfpaged']) && intval($_GET['wpfpaged'])) $paged = intval($_GET['wpfpaged']);
			$current_object['paged'] = (isset($paged) && $paged) ? $paged : 1;
			
			$wpf_url_parse = array_values($wpf_url_parse);
			
			if( !isset($current_object['template']) || !$current_object['template'] )
				$current_object = apply_filters('wpforo_before_init_current_object', $current_object, $wpf_url_parse);
			
			if( !isset($current_object['template']) || !$current_object['template'] ) {
				if(in_array('members', $wpf_url_parse) && $wpf_url_parse[0] == 'members'){
					$current_object['template'] = 'members';
				}elseif(in_array('profile', $wpf_url_parse)){
					$current_object['template'] = 'profile';
					foreach($wpf_url_parse as $value){
						if( $value == 'profile') break;
						if(is_numeric($value)) $current_object['userid'] = $value; else $current_object['username'] = $value;
					}
				}elseif(in_array('account', $wpf_url_parse)){
					$current_object['template'] = 'account';
					foreach($wpf_url_parse as $value){
						if( $value == 'account') break;
						if(is_numeric($value)) $current_object['userid'] = $value; else $current_object['username'] = $value;
					}
				}elseif(in_array('activity', $wpf_url_parse)){
					$current_object['template'] = 'activity';
					foreach($wpf_url_parse as $value){
						if( $value == 'activity') break;
						if(is_numeric($value)) $current_object['userid'] = $value; else $current_object['username'] = $value;
					}
				}elseif(in_array('subscriptions', $wpf_url_parse)){
					$current_object['template'] = 'subscriptions';
					foreach($wpf_url_parse as $value){
						if( $value == 'subscriptions') break;
						if(is_numeric($value)) $current_object['userid'] = $value; else $current_object['username'] = $value;
					}
				}else{
					$current_object['template'] = 'forum';
					if( isset($wpf_url_parse[0]) ){
						if( isset($wpf_url_parse[1]) ){
							$current_object['topic_slug'] = $wpf_url_parse[0];
							$current_object['forum_slug'] = $wpf_url_parse[1];
							$current_object['template'] = 'post';
						}else{
							$current_object['forum_slug'] = $wpf_url_parse[0];
							$current_object['template'] = 'topic';
						}
					}
				}
			}
			
			if( isset($current_object['userid']) || isset($current_object['username']) ){
				$args = array();
				if(isset($current_object['userid'])) $args['userid'] = $current_object['userid'];
				if(isset($current_object['username'])) $args['username'] = $current_object['username'];
				$selected_user = $this->member->get_member($args);
				if(isset($current_object['userid']) && empty($selected_user)) $selected_user = $this->member->get_member(array('username' => $current_object['userid']));
				if(!empty($selected_user)){
					$current_object['user'] = $selected_user;
					$current_object['userid'] = $selected_user['ID'];
					$current_object['username'] = $selected_user['user_nicename'];
					
					switch($current_object['template']){
						case 'activity':
							$args = array(
								'offset' => ($current_object['paged'] - 1) * $this->post_options['posts_per_page'],
								'row_count' => $this->post_options['posts_per_page'],
								'userid' => $current_object['userid'],
								'order' => 'DESC',
								'check_private' => true
							);
							$current_object['items_count'] = 0;
							$current_object['activities'] = $this->post->get_posts( $args, $current_object['items_count']);
						break;
						case 'subscriptions':
							$args = array(
								'offset' => ($current_object['paged'] - 1) * $this->post_options['posts_per_page'],
								'row_count' => $this->post_options['posts_per_page'],
								'userid' => $current_object['userid'],
								'order' => 'DESC'
							);
							$current_object['items_count'] = 0;
							$current_object['subscribes'] = $this->sbscrb->get_subscribes( $args, $current_object['items_count']);
						break;
					}
					
				}else{
					$current_object['is_404'] = true;
					$current_object['user'] = array();
					$current_object['userid'] = 0;
					$current_object['username'] = '';
				}
			}
			
			if(isset($current_object['forum_slug']) && $current_object['forum_slug']){
				$forum = $this->forum->get_forum(array('slug' => $current_object['forum_slug']), TRUE);
				if(!empty($forum)){
					$current_object['forum'] = $forum;
					$current_object['forumid'] = $forum['forumid'];
					$current_object['forum_desc'] = $forum['description'];
					$current_object['forum_meta_key'] = $forum['meta_key'];
					$current_object['forum_meta_desc'] = $forum['meta_desc'];
				}else{
					$current_object['is_404'] = true;
					$current_object['forum'] = array();
					$current_object['forumid'] = 0;
					$current_object['forum_desc'] = '';
					$current_object['forum_meta_key'] = '';
					$current_object['forum_meta_desc'] = '';
				}
			}
			
			if(isset($current_object['topic_slug']) && $current_object['topic_slug']){
				$topic = $this->topic->get_topic(array('slug' => $current_object['topic_slug']));
				if(!empty($topic)){
					$current_object['topic'] = $topic;
					$current_object['topicid'] = $topic['topicid'];
				}else{
					$current_object['is_404'] = true;
					$current_object['topic'] = array();
					$current_object['topicid'] = 0;
				}
			}
			
			$this->current_object = apply_filters('wpforo_after_init_current_object', $current_object, $wpf_url_parse);
		}
	}
	
	$wpforo = new wpForo();
	$wpforo->cache = new wpForoCache( $wpforo );
	$wpforo->phrase = new wpForoPhrase( $wpforo ); $wpforo->phrases();
	$wpforo->forum = new wpForoForum( $wpforo );
	$wpforo->topic = new wpForoTopic( $wpforo );
	$wpforo->post = new wpForoPost( $wpforo );
	$wpforo->usergroup = new wpForoUsergroup( $wpforo );
	$wpforo->member = new wpForoMember( $wpforo );
	$wpforo->perm = new wpForoPermissions( $wpforo );
	$wpforo->sbscrb = new wpForoSubscribe( $wpforo );
	$wpforo->tpl = new wpForoTemplate( $wpforo );
	$wpforo->notice = new wpForoNotices( $wpforo );
	$wpforo->feed = new wpForoFeed( $wpforo );
    $wpforo->moderation = new wpForoModeration( $wpforo );
	if(wpforo_is_admin()) include( WPFORO_DIR .'/wpf-admin/admin.php' );
	$GLOBALS['wpforo'] = $wpforo;
	add_action('init', array($wpforo, 'init'));
	
	//ADDONS/////////////////////////////////////////////////////
	$wpforo->addons = array(
		'pm' => array('version' => '1.0.0', 'requires' => '1.1.2', 'class' => 'wpForoPMs', 'title' => 'Private Messages', 'thumb' => WPFORO_URL . '/wpf-assets/addons/' . 'pm' . '/header.png', 'desc' => __('Provides a safe way to communicate directly with other members. Messages are private and can only be viewed by conversation participants.', 'wpforo'), 'url' => 'https://gvectors.com/product/wpforo-private-messages/'),
		'cross' => array('version' => '1.0.0', 'requires' => '1.3.1', 'class' => 'wpForoCrossPosting', 'title' => '"Forum - Blog" Cross Posting', 'thumb' => WPFORO_URL . '/wpf-assets/addons/' . 'cross' . '/header.png', 'desc' => __('Blog to Forum and Forum to Blog content synchronization. Blog posts with Forum topics and Blog comments with Forum replies.', 'wpforo'), 'url' => 'https://gvectors.com/product/wpforo-cross-posting/'),
		'attachments' => array('version' => '1.0.0', 'requires' => '1.1.0', 'class' => 'wpForoAttachments', 'title' => 'Advanced Attachments', 'thumb' => WPFORO_URL . '/wpf-assets/addons/' . 'attachments' . '/header.png', 'desc' => __('Adds an advanced file attachment system to forum topics and posts. AJAX powered media uploading and displaying system with user specific library.', 'wpforo'), 'url' => 'https://gvectors.com/product/wpforo-advanced-attachments/'),
		'embeds' => array('version' => '1.0.2', 'requires' => '1.1.0', 'class' => 'wpForoEmbeds', 'title' => 'Embeds', 'thumb' => WPFORO_URL . '/wpf-assets/addons/' . 'embeds' . '/header.png', 'desc' => __('Allows to embed hundreds of video, social network, audio and photo content providers in forum topics and posts.', 'wpforo'), 'url' => 'https://gvectors.com/product/wpforo-embeds/'),
		'ad-manager' => array('version' => '1.0.0', 'requires' => '1.2.0', 'class' => 'wpForoAD', 'title' => 'Ads Manager', 'thumb' => WPFORO_URL . '/wpf-assets/addons/' . 'ad-manager' . '/header.png', 'desc' => __('Ads Manager is a powerful yet simple advertisement management system, that allows you to add adverting banners between forums, topics and posts.', 'wpforo'), 'url' => 'https://gvectors.com/product/wpforo-ad-manager/'),
    );
	$wp_version = get_bloginfo('version'); if (version_compare($wp_version, '4.2.0', '>=')) { add_action('wp_ajax_dismiss_wpforo_addon_note', array($wpforo->notice, 'dismissAddonNote')); add_action('admin_notices', array($wpforo->notice, 'addonNote'));}
	/////////////////////////////////////////////////////////////
}