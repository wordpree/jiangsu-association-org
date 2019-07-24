<?php

	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;

	function wpforo_add_menu(){
		global $wpforo;
		
		$wpforo->moderation->spam_attachment();
		
		$all_count = 0;
		$mod_count = $wpforo->post->unapproved_count(); $mod_count_num = intval($mod_count);
		$mod_count = ( $mod_count ) ? ' <span class="awaiting-mod count-1"><span class="pending-count">' . intval($mod_count) . '</span></span> ' : '' ;
		$ban_count = $wpforo->member->banned_count(); $ban_count_num = intval($ban_count);
		$ban_count = ( $ban_count ) ? ' <span class="awaiting-mod count-1"><span class="pending-count">' . intval($ban_count) . '</span></span> ' : '' ;
		$all_count = $mod_count_num + $ban_count_num;
		$all_count = ( $all_count ) ? ' <span class="awaiting-mod count-1"><span class="pending-count">' . intval($all_count) . '</span></span> ' : '' ;
		
		$position = ( isset($wpforo->general_options['menu_position']) && $wpforo->general_options['menu_position'] > 0 ) ? $wpforo->general_options['menu_position'] : 23;
		if( $wpforo->current_user_groupid == 1 || 
			$wpforo->current_user_groupid == 2 ||
			$wpforo->perm->usergroup_can('vm') ||
			( $wpforo->perm->usergroup_can('cf') && 
				$wpforo->perm->usergroup_can('ef') && 
					$wpforo->perm->usergroup_can('df') )
			) add_menu_page(__('Dashboard', 'wpforo'), __('Forums', 'wpforo') . $all_count , 'read', 'wpforo-community', 'wpforo_toplevel_page', 'dashicons-format-chat', $position);
		if( $wpforo->current_user_groupid == 1 || $wpforo->current_user_groupid == 2 ) add_submenu_page('wpforo-community', __('Dashboard', 'wpforo'), __('Dashboard', 'wpforo'), 'read', 'wpforo-community', 'wpforo_toplevel_page' );
		if( $wpforo->perm->usergroup_can('cf') && $wpforo->perm->usergroup_can('ef') && $wpforo->perm->usergroup_can('df') ) add_submenu_page('wpforo-community', __('Forums', 'wpforo'), __('Forums', 'wpforo'), 'read',   'wpforo-forums', 'wpforo_forum_menu');
		if( $wpforo->current_user_groupid == 1 ) add_submenu_page('wpforo-community', __('Settings', 'wpforo'), __('Settings', 'wpforo'), 'read', 'wpforo-settings', 'wpforo_settings');
		if( $wpforo->current_user_groupid == 1 ) add_submenu_page('wpforo-community', __('Tools', 'wpforo'), __('Tools', 'wpforo'), 'read', 'wpforo-tools', 'wpforo_tools');
		if( $wpforo->perm->usergroup_can('aum') ) add_submenu_page('wpforo-community', __('Moderation', 'wpforo'), __('Moderation' , 'wpforo') . $mod_count, 'read', 'wpforo-moderations', 'wpforo_moderations');
		if( $wpforo->perm->usergroup_can('vm') ) add_submenu_page('wpforo-community', __('Members', 'wpforo'), __('Members', 'wpforo') . $ban_count, 'read', 'wpforo-members', 'wpforo_member_menu');
		if( $wpforo->current_user_groupid == 1 ) add_submenu_page('wpforo-community', __('Usergroups', 'wpforo'), __('Usergroups', 'wpforo'), 'read', 'wpforo-usergroups', 'wpforo_usergroups_menu');
		if( $wpforo->current_user_groupid == 1 ) add_submenu_page('wpforo-community', __('Phrases', 'wpforo'), __('Phrases', 'wpforo'), 'read', 'wpforo-phrases', 'wpforo_phrases');
		if( $wpforo->current_user_groupid == 1 ) add_submenu_page('wpforo-community', __('Themes', 'wpforo'), __('Themes', 'wpforo'), 'read', 'wpforo-themes', 'wpforo_themes');
		if( $wpforo->current_user_groupid == 1 ) add_submenu_page('wpforo-community', __('Addons', 'wpforo'), __('Addons', 'wpforo'), 'read', 'wpforo-addons', 'wpforo_addons');
		//exit();
	}
	add_action('admin_menu', 'wpforo_add_menu', 39);
	
	function wpforo_toplevel_page(){
		global $wpforo; 
		require( WPFORO_DIR . '/wpf-admin/dashboard.php' );
	}
	
	function wpforo_forum_menu(){
		global $wpforo;
		require( WPFORO_DIR . '/wpf-admin/forum.php' );
	}
	
	function wpforo_member_menu(){
		global $wpforo;
		require( WPFORO_DIR . '/wpf-admin/member.php' );
	}
	
	function wpforo_usergroups_menu(){
		global $wpforo;
		require( WPFORO_DIR . '/wpf-admin/usergroup.php' );
	}
	
	function wpforo_settings(){
		global $wpforo, $wpdb;
		require( WPFORO_DIR . '/wpf-admin/options.php' );
	}
	
	function wpforo_themes(){
		global $wpforo;
		require( WPFORO_DIR . '/wpf-admin/themes.php' );
	}
	
	function wpforo_phrases(){
		global $wpforo;
		require( WPFORO_DIR . '/wpf-admin/phrase.php' );
	}
	
	function wpforo_integrations(){
		global $wpforo;
		require( WPFORO_DIR . '/wpf-admin/integration.php' );
	}
	
	function wpforo_addons(){
		global $wpforo;
		require( WPFORO_DIR . '/wpf-admin/addons.php' );
	}
	
	function wpforo_tools(){
		global $wpforo;
		require( WPFORO_DIR . '/wpf-admin/tools.php' );
	}

	function wpforo_moderations(){
		global $wpforo;
		require( WPFORO_DIR . '/wpf-admin/moderation.php' );
	}
?>