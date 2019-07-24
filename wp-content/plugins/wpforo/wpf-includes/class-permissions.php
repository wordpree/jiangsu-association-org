<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
 

class wpForoPermissions{
	
	private $wpforo;
	static $cache = array();
	
	function __construct( $wpForo ){
		if(!isset($this->wpforo)) $this->wpforo = $wpForo;
		if( isset( $this->wpforo->general_options['lang'] ) && $this->wpforo->general_options['lang'] ){
			$accesses = $this->get_accesses();
			if(!empty($accesses)){
				foreach( $accesses as $access ){
					$this->wpforo->access[$access['access']] = $access;
				}
			}
		}
	}
 	
 	/**
	 * 
	 * @param string $access
	 * 
	 * @return array access row by access key
	 */
 	function get_access($access){
		$access = sanitize_text_field($access);
		if( isset($this->wpforo->access[$access]) && !empty($this->wpforo->access[$access]) ){
			return $this->wpforo->access[$access];
		}
		else{
			$sql = "SELECT * FROM `".$this->wpforo->db->prefix."wpforo_accesses` WHERE `access` = '" . esc_sql($access) . "'";
			return $this->wpforo->db->get_row($sql, ARRAY_A);
		}
	}
	
	
 	/**
	* get all accesses from accesses table
	* 
	* @return assoc array with accesses
	*/
 	function get_accesses(){
		$sql = "SELECT * FROM ".$this->wpforo->db->prefix."wpforo_accesses";
		return $this->wpforo->db->get_results($sql, ARRAY_A);
	}
 	
 	function usergroup_cans_form( $groupid = FALSE ){
		
		$can_data = array();
		$cans = $this->wpforo->usergroup_cans;
		
		if( $groupid == FALSE ){
			foreach($cans as $can => $name){ 
				@$can_data[$can]['value'] = 0;
				@$can_data[$can]['name'] = $name;
			}
		}else{
			$usegroup = $this->wpforo->usergroup->get_usergroup( $groupid );
			$ug_cans = unserialize($usegroup['cans']);
			foreach($cans as $can => $name){ 
				@$can_data[$can]['value'] = $ug_cans[$can];
				@$can_data[$can]['name'] = $name;
			}
		}
		
		return $can_data;
	}
	
	function forum_cans_form( $access = FALSE ){
		
		$can_data = array();
		$cans = $this->wpforo->forum_cans;
		
		if( !$access ){
			foreach($cans as $can => $name){ 
				@$can_data[$can]['value'] = 0;
				@$can_data[$can]['name'] = $name;
			}
		}else{
			$access = $this->get_access( $access );
			$access_cans = unserialize($access['cans']);
			foreach($cans as $can => $name){ 
				@$can_data[$can]['value'] = $access_cans[$can];
				@$can_data[$can]['name'] = $name;
			}
		}
		
		return $can_data;
	}
	
	
	/**
	* 
	* @param  string (required)
	* @param  array
	* @param  int 
	* 
	* @return affected rows count or false
	*/
	function add( $title, $cans = array(), $key = '' ){
		$default = array_map('intval', $this->wpforo->forum_cans);
		$cans = wpforo_parse_args($cans, $default);
		if(!$key) $key = $title;
		
		$i = 2;
		while( $this->wpforo->db->get_var("SELECT `access` FROM ".$this->wpforo->db->prefix."wpforo_accesses WHERE `access` = '". esc_sql(sanitize_text_field($key)) . "'") ){
			$key = $key . '-' . $i;
			$i++;
		}
		
		if( $this->wpforo->db->insert( 
			$this->wpforo->db->prefix . 'wpforo_accesses', 
				array( 
					'title'		=> sanitize_text_field($title), 
					'access' 	=> sanitize_text_field($key), 
					'cans'		=> serialize($cans)
				), 
				array( 
					'%s',
					'%s',
					'%s'
				)
			)
		){
			$this->wpforo->notice->add( sprintf( __('%s access successfully added', 'wpforo') , esc_html($title)) , 'success');
			return $this->wpforo->db->insert_id;
		}
		
		$this->wpforo->notice->add('Access add error', 'error');
		return FALSE;
	}
	
	function edit( $title, $cans, $key ){
		$default = array_map('intval', $this->wpforo->forum_cans);
		$cans = wpforo_parse_args($cans, $default);
		
		if( FALSE !== $this->wpforo->db->update( 
			$this->wpforo->db->prefix . 'wpforo_accesses', 
			array( 
				'title' =>  sanitize_text_field($title), 
				'cans' => serialize( $cans ), 
			),
			array( 'access' => sanitize_text_field($key) ),
			array( 
				'%s',
				'%s'
			),
			array( '%s' ))
		){
			$this->wpforo->notice->add( sprintf( __('%s access successfully edited', 'wpforo'), esc_html($title)) , 'success');
			return $key;
		}
		
		$this->wpforo->notice->add('Access edit error', 'error');
		return FALSE;
	}
	
	function delete($accessid){
		
		$accessid = intval($accessid);
		
		if(!$accessid){
			$this->wpforo->notice->add('Access delete error', 'error');
			return FALSE;
		}
		
		if( FALSE !== $this->wpforo->db->delete( $this->wpforo->db->prefix.'wpforo_accesses', array( 'accessid' => $accessid ), array( '%d' ) ) ){
			$this->wpforo->notice->add('Access successfully deleted', 'success');
			return $accessid;
		}
		
		$this->wpforo->notice->add('Access delete error', 'error');
		return FALSE;
	}
	
	function forum_can( $do, $forumid = NULL, $groupid = NULL ){
		
		$can = 0;
		if( !$this->wpforo->current_user_groupid ) return 0;
		
		if( is_null($forumid) && isset($this->wpforo->current_object['forumid']) ) {
			$forumid = $this->wpforo->current_object['forumid'];
		}
		$forumid = intval($forumid);
		
		if( is_null($groupid) ) {
			$groupid = $this->wpforo->current_user_groupid;
		}
		
		if( $forum = wpforo_forum($forumid) ){
			$permissions = unserialize($forum['permissions']);
			if( isset($permissions[$groupid]) ){
				$access = $permissions[$groupid];
				$access_arr = $this->get_access($access);
				$cans = unserialize($access_arr['cans']);
				$can = ( isset($cans[$do]) ? $cans[$do] : 0 );
			}
		}
		return $can;
	}
	
	function usergroup_can( $do, $usergroupid = NULL ){
		if( is_null($usergroupid) ) $usergroupid = $this->wpforo->current_user_groupid;
		$usergroupid = intval($usergroupid);
		$usergroup = $this->wpforo->usergroup->get_usergroup( $usergroupid );
		$cans = unserialize($usergroup['cans']);
		return ( isset($cans[$do]) ? $cans[$do] : 0 );
	}
	
	function user_can_manage_user( $user_id, $managing_user_id ){
		
		if( !$user_id || !$managing_user_id ) return false;
		if( $user_id == $managing_user_id ) return true;
		
		$user = new WP_User( $user_id ); 
		$user_level = $this->user_wp_level( $user );
		if( !empty($user->roles) && is_array($user->roles) ) $user_role = array_shift($user->roles);
		
		$managing_user = new WP_User( $managing_user_id );  
		$managing_user_level = $this->user_wp_level( $managing_user );
		if( !empty($managing_user->roles) && is_array($managing_user->roles) ) $managing_user_role = array_shift($managing_user->roles);
		
		if( (int)$user_level > (int)$managing_user_level ){
			return true;
		}
		elseif( $user_id == 1 && $user_role == 'administrator' ){
			return true;
		}
		elseif( (int)$user_level == (int)$managing_user_level ){
			$member = $this->wpforo->member->get_member( $user_id );
			$managing_member = $this->wpforo->member->get_member( $managing_user_id );
			$user_wpforo_can = $this->usergroup_can( 'em', $member['groupid'] );
			$managing_user_wpforo_can = $this->usergroup_can( 'em', $managing_member['groupid'] );
			if( $user_wpforo_can && !$managing_user_wpforo_can ){
				return true;
			}
			else{
				return false;
			}
		}
		elseif( $user_id != 1 && $managing_user_id == 1 && $managing_user_role == 'administrator' ){
			return false;
		}
		else{
			return false;
		}
	}
	
	function user_wp_level( $user_object ){
		$level = 0;
		$levels = array();
		if( is_int($user_object) ){
			$user_object = new WP_User( $user_object );
		}
		if( isset($user_object->allcaps) && is_array($user_object->allcaps) && !empty($user_object->allcaps) ){
			foreach($user_object->allcaps as $level_key => $level_value){
				if( strpos($level_key, 'level_') !== FALSE && $level_value == 1 ){
					$levels[] = intval(str_replace('level_', '', $level_key));
				}	
			}
			if(!empty($levels)){
				$level = max($levels);
			}
		}
		return $level;
	}
	
	
	
	public function can_link(){
		if( !$this->wpforo->perm->usergroup_can( 'em' ) ){
			$posts = $this->wpforo->member->member_approved_posts( $this->wpforo->current_userid );
			$posts = intval($posts);
			if( isset($this->wpforo->tools_antispam['min_number_post_to_link']) ){
				$min_posts = intval($this->wpforo->tools_antispam['min_number_post_to_link']);
				if( $min_posts != 0 ){
					if ( $posts <= $min_posts ) {
						return false;
					}
				}
			}
		}
		return true;
	}
	
	public function can_attach(){
		if( !$this->wpforo->perm->usergroup_can( 'em' ) ){
			$posts = $this->wpforo->member->member_approved_posts( $this->wpforo->current_userid );
			$posts = intval($posts);
			if( isset($this->wpforo->tools_antispam['min_number_post_to_attach']) ){
				$min_posts = intval($this->wpforo->tools_antispam['min_number_post_to_attach']);
				if( $min_posts != 0 ){
					if ( $posts <= $min_posts  ) {
						return false;
					}
				}
			}
		}
		return true;
	}
	
	public function can_attach_file_type( $ext = '' ){
		if( !$this->wpforo->perm->usergroup_can( 'em' ) ){
			if( isset($this->wpforo->tools_antispam['limited_file_ext']) && $this->wpforo->member->current_user_is_new() ){
				$expld = explode('|', $this->wpforo->tools_antispam['limited_file_ext'] );
				if( in_array($ext, $expld) ){
					return false;
				}
			}
		}
		return true;
	}
	
}

?>