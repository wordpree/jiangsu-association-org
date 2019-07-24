<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;

function wpforo_actions(){	

	global $wpforo;

	do_action( 'wpforo_actions' );
	
	if( isset($_POST['wpfreg']) && !empty($_POST['wpfreg']) && $userid = $wpforo->member->create($_POST['wpfreg'])){
		wpforo_verify_form('ref');
		wpforo_clean_cache( $userid, 'user' );
		wp_redirect( $wpforo->member->get_profile_url( $userid, ( is_user_logged_in() ? 'account' : 'profile' ) ) );
		exit();
	}
	
	if(isset($_POST['wpforologin']) && isset($_POST['log']) && isset($_POST['pwd'])){
		wpforo_verify_form('ref');
		if ( !is_wp_error( $user = wp_signon() ) ) {
			$wpf_login_times = get_user_meta($user->ID, '_wpf_login_times', true);
			if( isset($user->ID) && $wpf_login_times >= 1) {
				$name = ( isset($user->data->display_name) ) ? $user->data->display_name : '';
				$args = array( 0 => $name );
				$wpforo->notice->add( 'Welcome back %s!', 'success', $args);
			}
			else{
				$wpforo->notice->add('Welcome to our Community!', 'success');
			}
			(int)$wpf_login_times++;
			update_user_meta( $user->ID, '_wpf_login_times', $wpf_login_times );
			wp_redirect( wpforo_home_url( preg_replace('#\?.*$#is', '', wpforo_get_request_uri()) ) );
			exit();
		}else{
			$args = array();
			foreach($user->errors as $u_err) $args[] = $u_err[0];
			$wpforo->notice->add($args, 'error');
			wp_redirect( wpforo_get_request_uri() );
			exit();
		}
	}
	
	extract($wpforo->current_object, EXTR_OVERWRITE);

	if( $template == 'profile' && !isset($username) && !isset($userid) ){
		wp_redirect( wpforo_home_url() );
		exit();
	}
	
	if(isset($_POST['wpforo_member_submit'])){
		if(isset($_POST['member']['userid']) && $_POST['member']['userid']){
			wpforo_verify_form();
			
			if( !( intval($_POST['member']['userid']) == $wpforo->current_userid || 
				( $wpforo->perm->usergroup_can('em') && $wpforo->perm->user_can_manage_user( $wpforo->current_userid, intval($_POST['member']['userid']) )) ) ){
				$wpforo->notice->clear();
				$wpforo->notice->add('Permission denied', 'error');
				wp_redirect(wpforo_get_request_uri());
				exit();
			}
			
			$edit_response = $wpforo->member->edit();
			if( isset($_POST['member']['avatar_type']) && $_POST['member']['avatar_type'] == 'custom' ) $wpforo->member->upload_avatar();
			
			if( isset($_POST['member']['old_pass']) 
				&& isset($_POST['member']['new_pass']) 
					&& isset($_POST['member']['re_new_pass']) 
						&&  $_POST['member']['new_pass'] && $_POST['member']['re_new_pass'] && $_POST['member']['old_pass'] ){
				if( $_POST['member']['new_pass'] == $_POST['member']['re_new_pass'] ){
					$old_pass = $_POST['member']['old_pass'];
					$user_pass = $_POST['member']['user_pass'];
					$userid = intval($_POST['member']['userid']);
					$new_pass = $_POST['member']['new_pass'];
					if ( wp_check_password( $old_pass, $user_pass, $userid) ){
						wp_set_password( $new_pass, $userid );
					}else{
						$wpforo->notice->clear();
						$wpforo->notice->add('Old password is wrong', 'error');
					}
				}else{
					$wpforo->notice->clear();
					$wpforo->notice->add('New Passwords do not match', 'error');
				}
			}
			
			$wpforo->member->reset(intval($_POST['member']['userid']));
			if( $edit_response && $profile_url = $wpforo->member->get_profile_url( sanitize_title($_POST['member']['user_nickname']), 'account') ){
				wp_redirect($profile_url);
				exit();
			}
		}
		wp_redirect(wpforo_get_request_uri());
		exit();
	}

	if( isset($_POST['topic']['save']) && isset($_REQUEST['topic']['action']) ){
		if( $_REQUEST['topic']['action'] == 'add' ){
			wpforo_verify_form();
			if( $topicid = $wpforo->topic->add() ){
				wp_redirect( $wpforo->topic->get_topic_url($topicid) );
				exit();
			}
		}elseif( $_REQUEST['topic']['action'] == 'edit' ){
			wpforo_verify_form();
			if( $topicid = $wpforo->topic->edit() ){
				wp_redirect( $wpforo->topic->get_topic_url($topicid) );
				exit();
			}
		}
		wp_redirect( wpforo_get_request_uri() );
		exit();
	}
	
	if( isset($_POST['post']['save']) ){
		if( $_POST['post']['save'] != 'move' && isset($_REQUEST['post']['action']) ){
			if($_REQUEST['post']['action'] == 'add'){
				wpforo_verify_form();
				if( $postid = $wpforo->post->add() ){
					wp_redirect( $wpforo->post->get_post_url( $postid ) );
					exit();
				}
			}elseif($_REQUEST['post']['action'] == 'edit'){
				wpforo_verify_form();
				if( $postid = $wpforo->post->edit() ){
					wp_redirect( $wpforo->post->get_post_url( $postid ) );
					exit();
				}
			}
		}
		
		if($_POST['post']['save'] == 'move' && isset($_POST['movetopicid']) && isset($_POST['topic']['forumid'])){
			wpforo_verify_form();
			$move_topicid = intval($_POST['movetopicid']);
			$move_forumid = intval($_POST['topic']['forumid']);
			$wpforo->topic->move( $move_topicid, $move_forumid );
			wp_redirect( wpforo_get_request_uri() );
			exit();
		}
		
		wp_redirect( wpforo_get_request_uri() );
		exit();
	}
	
	## Subscriptions
	if( isset($_GET['wpforo']) && ($_GET['wpforo'] == 'sbscrbconfirm' || $_GET['wpforo'] == 'unsbscrb') && isset($_GET['key']) && $_GET['key'] ){
		$sbs_key = sanitize_text_field($_GET['key']);
		if( $_GET['wpforo'] == 'sbscrbconfirm' ){
			$wpforo->sbscrb->edit($sbs_key);
		}else{
			$wpforo->sbscrb->delete($sbs_key);
		}
		wp_redirect( wpforo_home_url( preg_replace('#\?.*$#is', '', wpforo_get_request_uri()) ) );
		exit();
	}
	
	## Resolved
	if( isset($_GET['wpforo']) && $_GET['wpforo'] == 'solved' && $_GET['tid'] ){
		$topicid = intval($_GET['tid']);
		wpforo_clean_cache( $topicid, 'topic' );
		wp_redirect( wpforo_home_url( preg_replace('#\?.*$#is', '', wpforo_get_request_uri()) ) );
		exit();
	}
	
	## Private
	if( isset($_GET['wpforo']) && $_GET['wpforo'] == 'private' && $_GET['tid'] ){
		$topicid = intval($_GET['tid']);
		wpforo_clean_cache($topicid, 'topic');
		wp_redirect( wpforo_home_url( preg_replace('#\?.*$#is', '', wpforo_get_request_uri()) ) );
		exit();
	}
	
	###############################################################
	/**
	* 
	* BACK-END
	* 
	*/
	
	##Settings action
	if( wpforo_is_admin() && isset($_POST['wpforo_screen_option']['value']) ){
		if(!current_user_can('administrator')) return;
		update_option('wpforo_count_per_page', $_POST['wpforo_screen_option']['value']);
	}
	
	if( wpforo_is_admin() && isset($_GET['page']) && $_GET['page'] == 'wpforo-community' && isset($_GET['action']) && $_GET['action'] ){
		if(!current_user_can('administrator')){ 
			$wpforo->notice->add('Permission denied', 'error');
			wp_redirect(admin_url());
			exit();
		}
		if( $_GET['action'] == 'synch' ){
			if( function_exists('set_time_limit') ) set_time_limit( 3600 ); $wpforo->member->synchronize_users();
			wp_redirect(admin_url('admin.php?page=wpforo-community'));
			exit();
		}
		if( $_GET['action'] == 'reset_fstat' && check_admin_referer( 'wpforo_reset_forums_stat' ) ){
			$forums = $wpforo->db->get_results("SELECT `forumid` FROM " . $wpforo->db->prefix . "wpforo_forums ORDER BY `forumid` ASC", ARRAY_A);
			if(!empty($forums)){
				foreach($forums as $forum){
					$topics = $wpforo->db->get_var( "SELECT COUNT(*) as count FROM `" . $wpforo->db->prefix . "wpforo_topics` WHERE `forumid` = " . intval($forum['forumid']) );
					$posts = $wpforo->db->get_var( "SELECT COUNT(*) as count FROM `" . $wpforo->db->prefix . "wpforo_posts` WHERE `forumid` = " . intval($forum['forumid']) );
					$wpforo->db->query("UPDATE `" . $wpforo->db->prefix . "wpforo_forums` SET `topics` = " . intval($topics) . ", `posts` = " . intval($posts) . " WHERE `forumid` = " . intval($forum['forumid']) );
				}
				$wpforo->notice->add('Updated Successfully!', 'success');
			}
		}
		if( $_GET['action'] == 'reset_ustat' && check_admin_referer( 'wpforo_reset_users_stat' ) ){
			$users = $wpforo->db->get_results("SELECT `userid` FROM " . $wpforo->db->prefix . "wpforo_profiles ORDER BY `posts` DESC", ARRAY_A);
			if(!empty($users)){
				foreach($users as $user){
					$questions = $wpforo->member->get_questions_count( $user['userid'] );
					$answers = $wpforo->member->get_answers_count( $user['userid'] );
					$posts = $wpforo->member->get_replies_count( $user['userid'] );
					$question_comments = $wpforo->member->get_question_comments_count( $user['userid'] );
					$wpforo->db->query("UPDATE `" . $wpforo->db->prefix . "wpforo_profiles` 
											SET `posts` = " . intval($posts) . ", `answers` = " . intval($answers) . ", `comments` = " . intval($question_comments) . ", `questions` = " . intval($questions) . " 
																WHERE `userid` = " . intval( $user['userid'] ) );
				}
				$wpforo->notice->add('Updated Successfully!', 'success');
			}
		}
		if( $_GET['action'] == 'reset_phrase_cache' && check_admin_referer( 'wpforo_reset_phrase_cache' ) ){
			$wpforo->phrase->clear_cache();
			$wpforo->notice->add('Deleted Successfully!', 'success');
		}
		if( $_GET['action'] == 'reset_user_cache' && check_admin_referer( 'wpforo_reset_user_cache' ) ){
			$wpforo->member->clear_db_cache();
			$wpforo->notice->add('Deleted Successfully!', 'success');
		}
		if( $_GET['action'] == 'reset_cache' && check_admin_referer( 'wpforo_reset_cache' ) ){
			$wpforo->phrase->clear_cache();
			$wpforo->member->clear_db_cache();
			wpforo_clean_cache(0);
			$wpforo->notice->add('Deleted Successfully!', 'success');
		}
	}
	
	if( wpforo_is_admin() && isset($_GET['page']) && $_GET['page'] == 'wpforo-settings' ){
		
		if(!current_user_can('administrator')){ 
			$wpforo->notice->add('Permission denied', 'error');
			wp_redirect(admin_url());
			exit();
		}
		
		##General options
		if( isset($_POST['wpforo_general_options']) ){
			check_admin_referer( 'wpforo-settings-general' );
			
			if( isset($_POST['wpforo_use_home_url']) && $_POST['wpforo_use_home_url'] ){
				$wpforo_use_home_url = 1;
				if( isset($_POST['wpforo_excld_urls']) && $_POST['wpforo_excld_urls'] )
					update_option('wpforo_excld_urls', trim($_POST['wpforo_excld_urls']));
			}else{
				$wpforo_use_home_url = 0;
			}
			update_option('wpforo_use_home_url', $wpforo_use_home_url);

			if( isset($_POST['wpforo_url']) && $permastruct = utf8_uri_encode( $_POST['wpforo_url'] ) ){
				$permastruct = preg_replace('#^/?index\.php/?#isu', '', $permastruct);
				$permastruct = trim($permastruct, '/');
				
				if( update_option('wpforo_url', esc_url( home_url($permastruct) ) )
					&& update_option('wpforo_permastruct', $permastruct) ){
					$wpforo->notice->add('Forum Base URL successfully updated', 'success');
				}else{
					$wpforo->notice->add('Successfully updated', 'success');
				}

				$wpforo->permastruct = $permastruct;
				flush_rewrite_rules(FALSE);
				nocache_headers();
			}

			if( $wpforo_use_home_url == 0 && !isset($_POST['wpforo_url']) ){
				$wpforo->permastruct = trim( get_wpf_option('wpforo_permastruct'), '/\\' );
				$wpforo->permastruct = preg_replace('#^/?index\.php/?#isu', '', $wpforo->permastruct);
				$wpforo->permastruct = trim($wpforo->permastruct, '/\\');
				$wpforo->pageid = get_wpf_option( 'wpforo_pageid');
				flush_rewrite_rules(FALSE);
				nocache_headers();
			}

			if( update_option('wpforo_general_options', $_POST['wpforo_general_options']) ){
				$wpforo->notice->add('General options successfully updated', 'success');
			}else{
				$wpforo->notice->add('Successfully updated', 'success');
			}

			$wpforo->member->clear_db_cache();
			wpforo_clean_cache();
			wp_redirect( admin_url( 'admin.php?page=wpforo-settings&tab=general' ) );
			exit();
		}
		
		##add new lang action 
		if( isset($_FILES['add_lang']) ){
			check_admin_referer( 'wpforo-settings-language' );
			$wpforo->phrase->add_lang();
			wpforo_clean_cache();
			wp_redirect( admin_url( 'admin.php?page=wpforo-settings&tab=general' ) );
			exit();
		}
		
		##Forums
		if( isset($_POST['wpforo_forum_options']) ){
			check_admin_referer( 'wpforo-settings-forums' );
			if( update_option('wpforo_forum_options', $_POST['wpforo_forum_options']) ){
				$wpforo->notice->add('Forum options successfully updated', 'success');
			}else{
				$wpforo->notice->add('Forum options successfully updated, but previous value not changed', 'success');
			}
			wpforo_clean_cache();
			wp_redirect( admin_url( 'admin.php?page=wpforo-settings&tab=forums' ) );
			exit();
		}
		
		##Posts
		if( isset($_POST['wpforo_post_options']) ){
			check_admin_referer( 'wpforo-settings-posts' );
			$_POST['wpforo_post_options']['eot_durr'] = intval($_POST['wpforo_post_options']['eot_durr']) * 60;
			$_POST['wpforo_post_options']['dot_durr'] = intval($_POST['wpforo_post_options']['dot_durr']) * 60;
			$_POST['wpforo_post_options']['eor_durr'] = intval($_POST['wpforo_post_options']['eor_durr']) * 60;
			$_POST['wpforo_post_options']['dor_durr'] = intval($_POST['wpforo_post_options']['dor_durr']) * 60;
			$_POST['wpforo_post_options']['max_upload_size'] = intval(wpforo_human_size_to_bytes($_POST['wpforo_post_options']['max_upload_size'].'M')); 
			if( update_option('wpforo_post_options', $_POST['wpforo_post_options']) ){
				$wpforo->notice->add('Post options successfully updated', 'success');
			}else{
				$wpforo->notice->add('Post options successfully updated, but previous value not changed', 'success');
			}
			wpforo_clean_cache();
			wp_redirect( admin_url( 'admin.php?page=wpforo-settings&tab=posts' ) );
			exit();
		}
		
		##Members
		if( isset($_POST['wpforo_member_options']) ){
			check_admin_referer( 'wpforo-settings-members' );
			$_POST['wpforo_member_options']['online_status_timeout'] = intval($_POST['wpforo_member_options']['online_status_timeout']) * 60;
			if( update_option('wpforo_member_options', $_POST['wpforo_member_options']) ){
				$wpforo->notice->add('Member options successfully updated', 'success');
			}else{
				$wpforo->notice->add('Member options successfully updated, but previous value not changed', 'success');
			}
			wpforo_clean_cache();
			wp_redirect( admin_url( 'admin.php?page=wpforo-settings&tab=members' ) );
			exit();
		}
		
		##Features
		if( isset($_POST['wpforo_features']) ){
			check_admin_referer( 'wpforo-features' );
			if( update_option('wpforo_features', $_POST['wpforo_features']) ){
				$wpforo->notice->add('Features successfully updated', 'success');
			}else{
				$wpforo->notice->add('Features successfully updated, but previous value not changed', 'success');
			}
			wpforo_clean_cache();
			wp_redirect( admin_url( 'admin.php?page=wpforo-settings&tab=features' ) );
			exit();
		}
		
		##Theme options
		if( isset($_POST['wpforo_theme_options']) && isset($_POST['wpforo_style_options']) ){
			check_admin_referer( 'wpforo-settings-styles' );
			$wpforo->theme_options['style'] = sanitize_text_field($_POST['wpforo_theme_options']['style']);
			$wpforo->theme_options['styles'] = $_POST['wpforo_theme_options']['styles'];
			update_option('wpforo_style_options', $_POST['wpforo_style_options']);
			update_option('wpforo_theme_options', $wpforo->theme_options);
			$wpforo->notice->add('Theme options successfully updated', 'success');
			wpforo_clean_cache();
			wp_redirect( admin_url( 'admin.php?page=wpforo-settings&tab=styles' ) );
			exit();
		}
		
		##Subscription
		if( isset($_POST['wpforo_subscribe_options']) ){
			check_admin_referer( 'wpforo-settings-emails' );
			if( update_option('wpforo_subscribe_options', $_POST['wpforo_subscribe_options']) ){
				$wpforo->notice->add('Subscribe options successfully updated', 'success');
			}else{
				$wpforo->notice->add('Subscribe options successfully updated, but previous value not changed', 'success');
			}
			wpforo_clean_cache();
			wp_redirect( admin_url( 'admin.php?page=wpforo-settings&tab=emails' ) );
			exit();
		}
		
	}
	
	### forum action ###
	if( wpforo_is_admin() && isset($_GET['page']) && $_GET['page'] == 'wpforo-forums' ){
		
		if(!current_user_can('administrator')){ 
			$wpforo->notice->add('Permission denied', 'error');
			wp_redirect(admin_url());
			exit();
		}
		
		if( isset($_POST['wpforo_submit']) && isset($_REQUEST['forum']) && isset($_GET['action']) ){
			check_admin_referer( 'wpforo-forum-addedit' );
			if( $_GET['action'] == 'add' ){
				if( $forumid = $wpforo->forum->add() ){
					wp_redirect( admin_url( 'admin.php?page=wpforo-forums' ) );
					exit();
				}
			}elseif( $_GET['action'] == 'edit' && isset($_GET['id']) ){
				$forumid = $wpforo->forum->edit();
			}
			if( isset($forumid) && $forumid ){
				wp_redirect( admin_url( 'admin.php?page=wpforo-forums&id=' . intval($forumid) . '&action=edit' ) );
			}else{
				wp_redirect( wpforo_get_request_uri() );
			}
			exit();
		}
		
		if(isset($_POST['wpforo_delete']) && $_GET['action'] == 'del' && isset($_REQUEST['forum']['delete'])){
			check_admin_referer( 'wpforo-forum-delete' );
			if( intval($_REQUEST['forum']['delete']) == 1 ){
				$wpforo->forum->delete();
			}elseif( intval($_REQUEST['forum']['delete']) == 0 ){
				$wpforo->forum->merge();
			}
			wp_redirect( admin_url( 'admin.php?page=wpforo-forums' ) );
			exit();
		}
		
		if(isset($_POST['forums_hierarchy_submit'])){
			check_admin_referer( 'wpforo-forums-hierarchy' );
			$wpforo->forum->update_hierarchy();
			wpforo_clean_cache(0, 'forum');
			wp_redirect( admin_url( 'admin.php?page=wpforo-forums' ) );
			exit();
		}
	}
	
	##Moderation
	if( wpforo_is_admin() && isset($_GET['page']) && $_GET['page'] == 'wpforo-moderations' ){
		
		if(!$wpforo->perm->usergroup_can('aum')){
			$wpforo->notice->add('Permission denied', 'error');
			wp_redirect(admin_url());
			exit();
		}

        $u_action = '';
        if( !empty($_GET['action']) && $_GET['action'] != '-1' ){
            $u_action = $_GET['action'];
        }elseif( !empty($_GET['action2']) && $_GET['action2'] != '-1' ){
            $u_action = $_GET['action2'];
        }
        $bulk = FALSE;
        $pids = array();
        if( !empty($_GET['id']) && ($pid = wpforo_bigintval($_GET['id'])) ){
            $pids = (array) $pid;
        }elseif( !empty($_GET['ids']) && ($ids = trim($_GET['ids'])) ){
            $bulk = TRUE;
            $ids = explode(',', urldecode($ids));
            $pids = array_map('wpforo_bigintval', array_filter($ids));
        }
        $pids = array_diff($pids, (array) $wpforo->current_userid);

        if( $u_action && !empty($pids) ) {
            if ($u_action == 'del') {
                if( $bulk ){
                    !check_admin_referer( 'bulk_action_moderation' );
                }else{
                    !check_admin_referer( 'wpforo_admin_table_action_delete' );
                }
                foreach ($pids as $pid) $wpforo->post->delete($pid);
                wp_redirect(admin_url('admin.php?page=wpforo-moderations'));
                exit();
            } elseif ($u_action == 'approve') {
                if( $bulk ){
                    !check_admin_referer( 'bulk_action_moderation' );
                }else{
                    !check_admin_referer( 'wpforo_admin_table_action_approve' );
                }
                foreach ($pids as $pid) {
					if( $pid ){
						$wpforo->moderation->post_approve($pid);
						//Email Notification ////////////////////////////////////////////////////////////
						$post = $wpforo->post->get_post($pid);
						if( !empty($post) && isset($post['is_first_post']) && $post['is_first_post'] ){
							if( isset($post['topicid']) && $post['topicid'] ){
								$topic = $wpforo->topic->get_topic($post['topicid']);
								if( !empty($topic) ){
									wpforo_forum_subscribers_mail_sender( $topic );
								}
							}
						}
						/////////////////////////////////////////////////////////////////////////////////
					}
				}
				wpforo_clean_cache(0, 'approve');
				wp_redirect(admin_url('admin.php?page=wpforo-moderations'));
                exit();
            } elseif ($u_action == 'unapprove') {
                if( $bulk ){
                    !check_admin_referer( 'bulk_action_moderation' );
                }else{
                    !check_admin_referer( 'wpforo_admin_table_action_approve' );
                }
                foreach ($pids as $pid) $wpforo->moderation->post_unapprove($pid);
				wpforo_clean_cache(0, 'unapprove');
                wp_redirect(admin_url('admin.php?page=wpforo-moderations'));
                exit();
            }
        }
    }

	##Phrases
	if( wpforo_is_admin() && isset($_GET['page']) && $_GET['page'] == 'wpforo-phrases' ){

		if(!current_user_can('administrator')){
			$wpforo->notice->add('Permission denied', 'error');
			wp_redirect(admin_url());
			exit();
		}

		if(isset($_POST['phrase']['save'])){
			check_admin_referer( 'wpforo-phrases-edit' );
			$wpforo->phrase->edit();
			wp_redirect( admin_url( 'admin.php?page=wpforo-phrases' ) );
			exit();
		}

		if( isset($_POST['phrase']['add']) && !empty($_POST['phrase']['value']) ){
			check_admin_referer( 'wpforo-phrase-add' );
			$wpforo->phrase->add();
			wp_redirect( admin_url( 'admin.php?page=wpforo-phrases' ) );
			exit();
		}
	}
	
	
	##Members
	if( wpforo_is_admin() && isset($_GET['page']) && $_GET['page'] == 'wpforo-members' ){
		$u_action = '';
		if( !empty($_GET['action']) && $_GET['action'] != '-1' ){
			$u_action = $_GET['action'];
		}elseif( !empty($_GET['action2']) && $_GET['action2'] != '-1' ){
			$u_action = $_GET['action2'];
		}
		$bulk = FALSE;
		$uids = array();
		if( !empty($_GET['id']) && ($uid = intval($_GET['id'])) ){
			$uids = (array) $uid;
		}elseif( !empty($_GET['ids']) && ($ids = trim($_GET['ids'])) ){
			$bulk = TRUE;
			$ids = explode(',', urldecode($ids));
			$uids = array_map('intval', array_filter($ids));
		}
		$uids = array_diff($uids, (array) $wpforo->current_userid);
		
		if( $u_action && !empty($uids) ){
			
			if($u_action == 'del'){
				$url = self_admin_url( 'users.php?action=delete&users[]=' . implode( '&users[]=', $uids ) );
				$url = str_replace( '&amp;', '&', wp_nonce_url( $url, 'bulk-users' ) );
				wp_redirect( $url );
				exit();
			}elseif($u_action == 'ban'){
				if( $bulk ){
					!check_admin_referer( 'bulk_action_member' );
				}else{
					!check_admin_referer( 'wpforo_admin_table_action_ban' );
				}
				foreach($uids as $uid) $wpforo->member->ban($uid);
			}elseif($u_action == 'unban'){
				if( $bulk ){
					!check_admin_referer( 'bulk_action_member' );
				}else{
					!check_admin_referer( 'wpforo_admin_table_action_ban' );
				}
				foreach($uids as $uid) $wpforo->member->unban($uid);
			}
			wpforo_clean_cache(0, 'user');
			wp_redirect( admin_url( 'admin.php?page=wpforo-members' ) );
			exit();
		}
	}
	
	
	##Usergroups
	if( wpforo_is_admin() && isset($_GET['page']) && $_GET['page'] == 'wpforo-usergroups' ){
		
		if(!current_user_can('administrator')){ 
			$wpforo->notice->add('Permission denied', 'error');
			wp_redirect(admin_url());
			exit();
		}
		
		if(isset( $_POST['usergroup']['action'] ) && ( $_POST['usergroup']['action'] == 'add' || $_POST['usergroup']['action'] == 'edit' ) ){
			check_admin_referer( 'wpforo-usergroup-addedit' );
			$board_cans = ( isset($_POST['cans']) ? $_POST['cans'] : array() );
			if( $_POST['usergroup']['action'] == 'add' ){
				$insert_usergroup_name = sanitize_text_field($_POST['usergroup']['name']);
				$insert_usergroup_id = $wpforo->usergroup->add( $insert_usergroup_name, $board_cans );
				if(isset($$insert_usergroup_id)) wpforo_clean_cache( $insert_usergroup_id, 'usergroup' );
				wp_redirect( admin_url( 'admin.php?page=wpforo-usergroups' ) );
				exit();
			}elseif( $_POST['usergroup']['action'] == 'edit' ){
				$insert_usergroup_id = intval($_GET['gid']);
				$insert_usergroup_name = sanitize_text_field($_POST['usergroup']['name']);
				$wpforo->usergroup->edit( $insert_usergroup_id, $insert_usergroup_name, $board_cans );
				if(isset($$insert_usergroup_id)) wpforo_clean_cache( $insert_usergroup_id, 'usergroup' );
				wp_redirect( admin_url( 'admin.php?page=wpforo-usergroups' ) );
				exit();
			}
			
		}
		if(isset($_GET['action']) && $_GET['action']=='del' && isset($_POST['usergroup']['submit']) && $_POST['usergroup']['submit'] == 'Delete'){
			check_admin_referer( 'wpforo-usergroup-delete' );
			$wpforo->usergroup->delete();
			wpforo_clean_cache(0, 'user');
			wp_redirect( admin_url( 'admin.php?page=wpforo-usergroups' ) );
			exit();
		}
	}
	
	##### Admin Accesses action ######
	if( wpforo_is_admin() && isset($_GET['page']) && $_GET['page'] == 'wpforo-settings' && isset($_GET['tab']) && $_GET['tab'] == 'accesses' ){
		
		if(!current_user_can('administrator')){ 
			$wpforo->notice->add('Permission denied', 'error');
			wp_redirect(admin_url());
			exit();
		}
		
		if( isset( $_POST['access'] ) && $_POST['access']['action'] == 'add' ){
			check_admin_referer( 'wpforo-access-addedit' );
			$cans = ( isset($_POST['cans'] ) ? $_POST['cans'] : array() );
			$insert_access_name = sanitize_text_field($_POST['access']['name']);
			$wpforo->perm->add( $insert_access_name, $cans );
			wp_redirect( admin_url( 'admin.php?page=wpforo-settings&tab=accesses' ) );
			exit();
		}elseif( isset( $_POST['access'] ) && $_POST['access']['action'] == 'edit' ){
			check_admin_referer( 'wpforo-access-addedit' );
			$cans = ( isset($_POST['cans'] ) ? $_POST['cans'] : array() );
			$insert_access_key = sanitize_text_field($_POST['access']['key']);
			$insert_access_name = sanitize_text_field($_POST['access']['name']);
			$wpforo->perm->edit( $insert_access_name, $cans, $insert_access_key );
			wpforo_clean_cache(0, 'post');
			wp_redirect( wpforo_get_request_uri() );
			exit();
		}elseif( isset($_GET['action']) && $_GET['action'] == 'del' && isset($_GET['accessid']) ){
			
			if( !check_admin_referer( 'wpforo_access_delete' )){ 
				$wpforo->notice->add('Permission denied', 'error');
				wp_redirect(admin_url());
				exit();
			}
			
			$insert_access_id = intval($_GET['accessid']);
			$wpforo->perm->delete( $insert_access_id );
			wpforo_clean_cache(0, 'post');
			wp_redirect( admin_url( 'admin.php?page=wpforo-settings&tab=accesses' ) );
			exit();
		}
	}
	
	##Themes
	if( wpforo_is_admin() && isset($_GET['page']) && $_GET['page'] == 'wpforo-themes' && isset($_GET['theme']) ){
		
		if(!current_user_can('administrator')){ 
			$wpforo->notice->add('Permission denied', 'error');
			wp_redirect(admin_url());
			exit();
		}
		
		$theme = sanitize_text_field( $_GET['theme'] );
		if( $_GET['action'] == 'activate' || $_GET['action'] == 'install' || $_GET['action'] == 'reset' ){
			if( $_GET['action'] == 'activate' ){
				$new_theme = get_option( 'wpforo_theme_archive_' . $theme );
			}
			elseif( $_GET['action'] == 'install' || $_GET['action'] == 'reset' ){
				$new_theme = $wpforo->tpl->find_theme( $theme );
				if( $_GET['action'] == 'reset' ){
					delete_option( 'wpforo_theme_archive_' . $theme );
				}
			}
			$current_theme = $wpforo->theme_options;
			if( !empty($new_theme) ){
				update_option( 'wpforo_theme_options', $new_theme );
				if( $_GET['action'] != 'reset' ){
					update_option( 'wpforo_theme_archive_' . $wpforo->theme, $current_theme );
				}
			}
			wp_redirect( admin_url( 'admin.php?page=wpforo-themes' ) );
			exit();
		}
		if( $_GET['action'] == 'delete' ){
			$remove_dir = WPFORO_THEME_DIR . '/' . $theme;
			if( is_dir($remove_dir) && strlen($theme) > 0 ){
				wpforo_remove_directory( $remove_dir );
			}
			wp_redirect( admin_url( 'admin.php?page=wpforo-themes' ) );
			exit();
		}
	}
	
	
	if( isset($_GET['forum']) && $_GET['forum'] && isset($_GET['type']) && $_GET['type'] == 'rss2' ){
		$forumid = intval($_GET['forum']);
		$forum = $wpforo->forum->get_forum($forumid);
		$forum['forumurl'] = $forum['url'];
		
		if(isset($_GET['topic']) && $_GET['topic']){
			$topicid = intval($_GET['topic']);
			$topic = $wpforo->topic->get_topic($topicid);
			$topic['topicurl'] = $wpforo->topic->get_topic_url($topicid);
			$posts = $wpforo->post->get_posts( array( 'topicid' => $topicid, 'row_count' => 10, 'orderby' => 'created', 'order' => 'DESC', 'check_private' => true ) );
			foreach($posts as $key => $post){
				$member = wpforo_member( $post );
				$posts[$key]['description'] = wpforo_text( trim(strip_tags($post['body'])), 190, false );
				$posts[$key]['content'] = trim($post['body']);
				$posts[$key]['posturl'] = $wpforo->post->get_post_url( $post['postid'] );
				$posts[$key]['author'] = $member['display_name'];
			}
			$wpforo->feed->rss2_topic($forum, $topic, $posts);
		}
		else{
			$topics = $wpforo->topic->get_topics( array( 'forumid' => $forumid, 'row_count' => 10, 'orderby' => 'created', 'order' => 'DESC' ) );
			foreach($topics as $key => $topic){
				$post = $wpforo->post->get_post($topic['first_postid']);
				$member = wpforo_member($topic);
				$topics[$key]['description'] = wpforo_text( trim(strip_tags($post['body'])), 190, false );
				$topics[$key]['content'] = trim($post['body']);
				$topics[$key]['topicurl'] = $wpforo->topic->get_topic_url($topic['topicid']);
				$topics[$key]['author'] = $member['display_name'];
			}
			$wpforo->feed->rss2_forum($forum, $topics);
		}
		exit();
	}
	
	##Tools
	if( wpforo_is_admin() && isset($_GET['page']) && $_GET['page'] == 'wpforo-tools' ){
		
		if(!current_user_can('administrator')){ 
			$wpforo->notice->add('Permission denied', 'error');
			wp_redirect(admin_url());
			exit();
		}
		
		if( isset($_POST['wpforo_tools_antispam']) ){
			check_admin_referer( 'wpforo-tools-antispam' );
			if( update_option('wpforo_tools_antispam', $_POST['wpforo_tools_antispam']) ){
				$wpforo->notice->add('Settings successfully updated', 'success');
			}
			wp_redirect( admin_url( 'admin.php?page=wpforo-tools&tab=antispam' ) );
			exit();
		}
		
		if( isset($_POST['wpforo_tools_cleanup']) ){
			check_admin_referer( 'wpforo-tools-cleanup' );
			if( update_option('wpforo_tools_cleanup', $_POST['wpforo_tools_cleanup']) ){
				$wpforo->notice->add('Settings successfully updated', 'success');
			}
			wp_redirect( admin_url( 'admin.php?page=wpforo-tools&tab=cleanup' ) );
			exit();
		}
		
		if(isset($_GET['action']) && $_GET['action']=='delete-spam-file' && isset($_GET['sfname']) && $_GET['sfname']){
			$filename = sanitize_file_name($_GET['sfname']);
			if(check_admin_referer( 'wpforo_tools_antispam_files')){
				if(!empty($filename)){
					$filename = str_replace( array('../', './', '/'), '', $filename );
					$filename = urldecode( $filename );
					$upload_dir = wp_upload_dir();
                	$default_attachments_dir =  $upload_dir['basedir'] . '/wpforo/default_attachments/';
					$file = $default_attachments_dir . $filename;
					$attachmentid = $wpforo->post->get_attachment_id( '/' . $filename );
					if ( !wp_delete_attachment( $attachmentid ) ){
						@unlink($file); 
					}
					$wpforo->notice->add( 'Deleted', 'success' );
					wp_redirect( admin_url( 'admin.php?page=wpforo-tools&tab=antispam' ) );
					exit();
				}
			}
			wp_redirect( admin_url( 'admin.php?page=wpforo-tools&tab=antispam' ) );
			exit();
		}
		
		if(isset($_GET['action']) && $_GET['action']=='delete-all' && isset($_GET['level']) && $_GET['level']){
			if(check_admin_referer( 'wpforo_tools_antispam_files')){
				$delete_level = intval($_GET['level']);
				$upload_dir = wp_upload_dir();
                $default_attachments_dir =  $upload_dir['basedir'] . '/wpforo/default_attachments/';
				if(is_dir($default_attachments_dir)){
					if ($handle = opendir($default_attachments_dir)){
						while (false !== ($filename = readdir($handle))){
							$level = 0;
							if( $filename == '.' ||  $filename == '..') continue;
							if( !$level = $wpforo->moderation->spam_file($filename) ) continue;
							if( $delete_level == $level ){
								$attachmentid = $wpforo->post->get_attachment_id( '/' . $filename );
								if ( !wp_delete_attachment( $attachmentid ) ){
									$file = $default_attachments_dir . $filename; @unlink($file); 
								}
							}
						}
						closedir($handle);
						$wpforo->notice->add( 'Deleted', 'success' );
						wp_redirect( admin_url( 'admin.php?page=wpforo-tools&tab=antispam' ) );
						exit();
					}
				}
			}
			wp_redirect( admin_url( 'admin.php?page=wpforo-tools&tab=antispam' ) );
			exit();
		}
	}
	
	do_action( 'wpforo_actions_end' );
	
}