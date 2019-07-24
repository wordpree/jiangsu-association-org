<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class wpForoModeration
{
    private $wpforo;
    private $db;
    public $post_statuses;

    public function __construct($wpforo)
    {
        $this->wpforo = $wpforo;
        $this->db = $wpforo->db;
        $this->post_statuses = apply_filters('wpforo_post_statuses', array('approved', 'unapproved'));
    }

	public function init(){
		if( !$this->wpforo->perm->usergroup_can( 'aup' ) ){
			add_filter('wpforo_add_topic_data_filter', array(&$this, 'auto_moderate'));
			add_filter('wpforo_add_post_data_filter', array(&$this, 'auto_moderate'));
		}
		else{
			if( !$this->wpforo->perm->can_link() ){
				add_filter('wpforo_add_topic_data_filter', array(&$this, 'remove_links'), 7);
				add_filter('wpforo_edit_topic_data_filter', array(&$this, 'remove_links'), 7);
				add_filter('wpforo_add_post_data_filter', array(&$this, 'remove_links'), 7);
				add_filter('wpforo_edit_post_data_filter', array(&$this, 'remove_links'), 7);
			}
			if( $this->wpforo->member->current_user_is_new() ){
				if (class_exists('Akismet')) {
					add_filter('wpforo_add_topic_data_filter', array(&$this, 'akismet_topic'), 8);
					add_filter('wpforo_edit_topic_data_filter', array(&$this, 'akismet_topic'), 8);
					add_filter('wpforo_add_post_data_filter', array(&$this, 'akismet_post'), 8);
					add_filter('wpforo_edit_post_data_filter', array(&$this, 'akismet_post'), 8);
				}
				if ( $this->wpforo->tools_antispam['spam_filter'] ) {
					add_filter('wpforo_add_topic_data_filter', array(&$this, 'spam_topic'), 9);
					add_filter('wpforo_edit_topic_data_filter', array(&$this, 'spam_topic'), 9);
					add_filter('wpforo_add_topic_data_filter', array(&$this, 'spam_post'), 9);
					add_filter('wpforo_edit_topic_data_filter', array(&$this, 'spam_post'), 9);
					add_filter('wpforo_add_post_data_filter', array(&$this, 'spam_post'), 9);
					add_filter('wpforo_edit_post_data_filter', array(&$this, 'spam_post'), 9);
				}
			}
			if ( $this->wpforo->tools_antispam['spam_filter'] ) {
				add_filter('wpforo_add_topic_data_filter', array(&$this, 'auto_moderate'), 10);
				add_filter('wpforo_add_post_data_filter', array(&$this, 'auto_moderate'), 10);
			}
		}
	}    
	
	public function get_post_status_dname($status)
    {
        $status = intval($status);
        return (isset($this->post_statuses[$status]) ? $this->post_statuses[$status] : $status);
    }

    public function get_moderations($args, &$items_count = 0)
    {
        if (isset($_GET['filter_by_userid']) && wpforo_bigintval($_GET['filter_by_userid'])) $args['userid'] = wpforo_bigintval($_GET['filter_by_userid']);
        $filter_by_status = intval((isset($_GET['filter_by_status']) ? $_GET['filter_by_status'] : 1));
        $args['status'] = $filter_by_status;
		if( !isset($_GET['order']) ) $args['order'] = 'DESC';
        $posts = $this->wpforo->post->get_posts($args, $items_count);
        return $posts;
    }

    public function search($needle, $fields = array())
    {
        $posts = $this->wpforo->post->search($needle);
        $pids = array();
        foreach ($posts as $post) $pids[] = $post['postid'];
        return $pids;
    }

    public function post_approve($postid)
    {
        return $this->wpforo->post->status($postid, 0);
    }

    public function post_unapprove($postid)
    {
        return $this->wpforo->post->status($postid, 1);
    }

    public function get_view_url($arg)
    {
        return $this->wpforo->post->get_post_url($arg);
    }

    public function akismet_topic($item)
    {
        $post = array();
        $post['user_ip'] = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null);
        $post['user_agent'] = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null);
        $post['referrer'] = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
        $post['blog'] = get_option('home');
        $post['blog_lang'] = get_locale();
        $post['blog_charset'] = get_option('blog_charset');
        $post['comment_type'] = 'forum-post';

        if (empty($item['forumid'])) {
            $topic = $this->wpforo->topic->get_topic($item['topicid']);
            $item['forumid'] = $topic['forumid'];
        }

        $post['comment_author'] = $this->wpforo->current_user['user_nicename'];
        $post['comment_author_email'] = $this->wpforo->current_user['user_email'];
        $post['comment_author_url'] = $this->wpforo->member->get_profile_url($this->wpforo->current_userid);
        $post['comment_post_modified_gmt'] = current_time('mysql', 1);
        $post['comment_content'] = $item['title'] . "  \r\n  " . $item['body'];
        $post['permalink'] = $this->wpforo->forum->get_forum_url($item['forumid']);

       $response = Akismet::http_post(Akismet::build_query($post), 'comment-check');
        if ($response[1] == 'true') {
			$this->ban_for_spam( $this->wpforo->current_userid );
            $item['status'] = 1;
        }

        return $item;
    }

    public function akismet_post($item)
    {
        $post = array();
        $post['user_ip'] = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null);
        $post['user_agent'] = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null);
        $post['referrer'] = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
        $post['blog'] = get_option('home');
        $post['blog_lang'] = get_locale();
        $post['blog_charset'] = get_option('blog_charset');
        $post['comment_type'] = 'forum-post';

        $topic = $this->wpforo->topic->get_topic($item['topicid']);
        
        $post['comment_author'] = $this->wpforo->current_user['user_nicename'];
        $post['comment_author_email'] = $this->wpforo->current_user['user_email'];
        $post['comment_author_url'] = $this->wpforo->member->get_profile_url($this->wpforo->current_userid);
        $post['comment_post_modified_gmt'] = $topic['modified'];
        $post['comment_content'] = $item['body'];
        $post['permalink'] = $this->wpforo->topic->get_topic_url($item['topicid']);

        $response = Akismet::http_post(Akismet::build_query($post), 'comment-check');
        if ($response[1] == 'true') {
			$this->ban_for_spam( $this->wpforo->current_userid );
            $item['status'] = 1;
        }

        return $item;
    }
	
	public function spam_attachment(){
		$upload_dir = wp_upload_dir();
		$default_attachments_dir =  $upload_dir['basedir'] . '/wpforo/default_attachments/';
		if(is_dir($default_attachments_dir)){
			if ($handle = opendir($default_attachments_dir)){
				while (false !== ($filename = readdir($handle))){
					$file = $default_attachments_dir . '/' . $filename;
					if( $filename == '.' ||  $filename == '..') continue;
					$level = $this->spam_file($filename); 
					if( $level > 2 ){
						$link = '<a href="' . admin_url('admin.php?page=wpforo-tools&tab=antispam#spam-files') . '"><strong>&gt;&gt;</strong></a>';
						$phrase = '<strong>SPAM! - </strong>' . sprintf( __('Probably spam file attachments have been detected by wpForo Spam Control. Please moderate suspected files here %s', 'wpforo'), $link); 
						$this->wpforo->notice->add( $phrase, 'error' );
						return true;
					}
				}
			}
		}
		return false;
	}
	
	public function spam_file( $item, $type = 'file' ){
		if( !isset($item) || !$item ) return false;
		$level = 0;
		$item = strtolower($item);
		$spam_file_phrases = array(
			0 => array( 'watch', 'movie'),
			1 => array( 'download', 'free')
		);
		if($type == 'file'){
			$ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
			$ext_risk = array('pdf', 'doc', 'docx', 'txt', 'htm', 'html', 'rtf', 'xml', 'xls', 'xlsx', 'php', 'cgi');
			$ext_high_risk = array('php', 'cgi', 'exe');
			if( in_array($ext, $ext_risk) ){
				$has_post = $this->wpforo->db->get_var( "SELECT `postid` FROM `".$this->wpforo->db->prefix."wpforo_posts` WHERE `body` LIKE '%" . esc_sql( $item ) . "%' LIMIT 1" );
				foreach($spam_file_phrases as $phrases){
					foreach($phrases as $phrase){
						if( strpos($item, $phrase) !== FALSE ){
							if( !$has_post ){
								$level = 4; break 2;
							}
							else{
								$level = 2; break 2;
							}
						}
					}
				}
				if( !$level ){
					if( !$has_post ){
						$level = 3;
					}
					else{
						if( in_array($ext, $ext_high_risk) ){
							$level = 5;
						}
						else{
							$level = 1;
						}
					}
				}
			}
			return $level;
		}
		elseif($type == 'file-open'){
			$ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
			$allow_to_open = array('pdf', 'doc', 'docx', 'txt', 'rtf', 'xls', 'xlsx');
			if( in_array($ext, $allow_to_open) ){
				return true;
			}
			else{
				return false;
			}
		}
		return 0;
	}
	
	public function spam_topic($topic)
	{
		if( empty($topic) ) return $topic;
		if( isset($topic['title']) ){
			$item = $topic['title'];
		}
		else{
			return $topic;
		}
		$len = wpfor_strlen($item);
		if( $len < 10 ) return $topic;
		$item = strip_tags($item);
		$is_similar = false;
		$topic_args = array( 'userid' => $topic['userid'] );
		$topics = $this->wpforo->topic->get_topics($topic_args);
		$sc_level = ( isset($this->wpforo->tools_antispam['spam_filter_level_topic'])) ? intval($this->wpforo->tools_antispam['spam_filter_level_topic']) : 100;
		if( $sc_level > 100 ) $sc_level = 60; $sc_level = (101 - $sc_level);
		if( !empty($topics) ){
			$count = count($topics);
			$keys[0] = array_rand($topics); if( $count > 1) $keys[1] = array_rand($topics);
			$check_1 = (isset($keys[0])) ? strip_tags($topics[$keys[0]]['title']) : '';
			$check_2 = (isset($keys[1])) ? strip_tags($topics[$keys[1]]['title']) : '';
			if($check_1){ similar_text($item, $check_1, $percent); if( $percent > $sc_level ) $is_similar = true; }
			if($check_2 && !$is_similar){ similar_text($item, $check_2, $percent); if( $percent > $sc_level ) $is_similar = true; }
			if( $is_similar ){
				$this->ban_for_spam( $this->wpforo->current_userid );
				$topic['status'] = 1;
			}
		}
		return $topic;
	}
	
	public function spam_post($post)
	{
		if( empty($post) ) return $post;
		if( isset($post['body']) ){
			$item = $post['body'];
		}
		else{
			return $post;
		}
		
		$len = wpfor_strlen($item);
		$item = strip_tags($item);
		$is_similar = false;
		$post_args = array( 'userid' => $post['userid'] );
		$posts = $this->wpforo->post->get_posts($post_args);
		$sc_level = ( isset($this->wpforo->tools_antispam['spam_filter_level_post'])) ? intval($this->wpforo->tools_antispam['spam_filter_level_post']) : 100;
		if( $sc_level > 100 ) $sc_level = 70; $sc_level = (101 - $sc_level);
		if( !empty($posts) ){
			$count = count($posts);
			$keys[0] = array_rand($posts); if( $count > 1) $keys[1] = array_rand($posts);
			$check_1 = (isset($keys[0])) ? strip_tags($posts[$keys[0]]['body']) : '';
			$check_2 = (isset($keys[1])) ? strip_tags($posts[$keys[1]]['body']) : '';
			if($check_1){ similar_text($item, $check_1, $percent); if( isset($percent) && $percent > $sc_level ) $is_similar = true; }
			if($check_2 && !$is_similar){ similar_text($item, $check_2, $percent); if( isset($percent) && $percent > $sc_level ) $is_similar = true; }
			if( $is_similar ){
				$this->ban_for_spam( $this->wpforo->current_userid );
				$post['status'] = 1;
			}
		}
		return $post;
	}
	
	public function auto_moderate($item){
		
		if( empty($item) ) return $item;
		if( $this->wpforo->perm->usergroup_can( 'em' ) ){ 
			$item['status'] = 0;
			return $item;
		}
		if( !$this->wpforo->perm->usergroup_can( 'aup' ) ){
			$item['status'] = 1;
			return $item;
		}
		
		if( $this->wpforo->member->current_user_is_new() ){
			if( ( isset($item['status']) && $item['status'] == 1 ) || $this->has_unapproved( $this->wpforo->current_userid ) ){
				$this->set_all_unapproved( $this->wpforo->current_userid );
				$item['status'] = 1;
			}
			if( isset($item['body']) && isset($item['title']) && ( $this->has_link($item['body']) || $this->has_link($item['title']) ) ){
				$item['status'] = 1;
			}
		}
		else{
			if( !$this->has_approved( $this->wpforo->current_userid ) ){
				$item['status'] = 1;
			}
		}
		
		return $item;
	}
	
	public function has_approved($user){
		if( empty($user) ) return false;
		if( isset($user['ID']) ){
			$userid = intval($user['ID']);
		}
		else{
			$userid = intval($user);
		}
		$has_approved_post = $this->wpforo->db->get_var( "SELECT `postid` FROM `".$this->wpforo->db->prefix."wpforo_posts` WHERE `userid` = '" . intval($userid) . "' AND `status` = 0 LIMIT 1" );
		if( $has_approved_post ){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function has_unapproved($user){
		if( empty($user) ) return false;
		if( isset($user['ID']) ){
			$userid = intval($user['ID']);
		}
		else{
			$userid = intval($user);
		}
		$has_unapproved_post = $this->wpforo->db->get_var( "SELECT `postid` FROM `".$this->wpforo->db->prefix."wpforo_posts` WHERE `userid` = '" . intval($userid) . "' AND `status` = 1 LIMIT 1" );
		if( $has_unapproved_post ){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function ban_for_spam( $userid ){
		if ( isset($userid) && $this->wpforo->tools_antispam['spam_user_ban'] ) {
			if( !$this->has_approved( $this->wpforo->current_userid ) ){
				$this->wpforo->member->autoban( $userid );
			}
		}
	}
	
	public function set_all_unapproved( $userid ){
		if ( isset($userid) ) {
			$this->wpforo->db->update( $this->wpforo->db->prefix."wpforo_topics", array('status' => 1), array('userid' => intval($userid)), array('%d'), array('%d'));
			$this->wpforo->db->update( $this->wpforo->db->prefix."wpforo_posts", array('status' => 1), array('userid' => intval($userid)), array('%d'), array('%d'));
		}
	}
	
	public function remove_links( $item ){
		if( isset($item['body']) && $item['body'] ){
			$item['body'] = preg_replace('/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/is', ' <span style="color:#aaa;">' . wpforo_phrase('removed link', false, false) . '</span> ', $item['body']);
		}
		if( isset($item['title']) && $item['title'] ){
			if(preg_match('/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/is', $item['title'] )){
				$item['title'] = preg_replace('/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/is', ' -' . wpforo_phrase('removed link', false, false) . '- ', $item['title']);
			}
		}
		return $item;
	}

	public function has_link( $content ){
		if( preg_match('/((http|https)\:\/\/)?[a-zA-Z0-9\.\/\?\:@\-_=#]+\.([a-zA-Z0-9\&\.\/\?\:@\-_=#])*/is', $content ) ){
			return true;
		}
		return false;
	}
	
}