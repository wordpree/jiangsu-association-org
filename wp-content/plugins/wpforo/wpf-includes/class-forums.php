<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
 

class wpForoForum{
	
	private $wpforo;
	static $cache = array( 'forums' => array(), 'forum' => array(), 'item' => array() );
	
	public function __construct( $wpForo ){
		if(!isset($this->wpforo)) $this->wpforo = $wpForo;
	}
	
	public function get_cache( $var ){
		if( isset(self::$cache[$var]) ) return self::$cache[$var];
	}
 
 	private function unique_slug($slug, $parentid = 0, $forumid = 0){
		$new_slug = wpforo_text($slug, 250, false);
		$forumid = intval($forumid);
		$i = 2;
		while( $this->wpforo->db->get_var("SELECT `forumid` FROM ".$this->wpforo->db->prefix."wpforo_forums WHERE `slug` = '" . esc_sql($new_slug) . "'" . ($forumid ? ' AND `forumid` != '. intval($forumid) : '')) ){
			if( !isset($parent_slug) && $parentid = intval($parentid) ){
				$parent_slug = $this->wpforo->db->get_var("SELECT `slug` FROM ".$this->wpforo->db->prefix."wpforo_forums WHERE `forumid` = " . intval($parentid) );
				$new_slug = $parent_slug . "-" . wpforo_text($slug, 250, false);
			}else{
				$new_slug = wpforo_text($slug, 250, false) . '-' . $i;
			}
			$i++;
		}
		return $new_slug;
	}
 	
 	public function add( $args = array(), $checkperm = TRUE ){
 		if( $checkperm && !$this->wpforo->perm->usergroup_can('cf') ){
			$this->wpforo->notice->add('Permission denied for add forum', 'error');
			return FALSE;
		}
 		
		if( empty($args) && empty($_REQUEST['forum']) ) return FALSE;
		if( empty($args) && !empty($_REQUEST['forum']) ) $args = $_REQUEST['forum'];
		
		extract($args, EXTR_OVERWRITE);
		
		if( !isset($title) || !$title ){
			$this->wpforo->notice->add('Please insert required fields!', 'error');
			return FALSE;
		}
		
		$title = sanitize_text_field($title);
		$title = wpforo_text($title, 250, false);
		$description = (isset($description) ? wpforo_kses($description, 'post') : '');
		$permission =  (isset($permission) && is_array($permission)) ? serialize(array_map('sanitize_text_field', $permission)) : 'a:5:{i:1;s:4:"full";i:2;s:9:"moderator";i:3;s:8:"standard";i:4;s:9:"read_only";i:5;s:8:"standard";}';
		$meta_key = (isset($meta_key)) ? sanitize_text_field($meta_key) : '';
		$meta_desc = (isset($meta_desc)) ? sanitize_text_field($meta_desc) : '';
		$parentid = (isset($parentid)) ? intval($parentid) : 0;
		$slug = (isset($slug) && $slug) ? sanitize_title($slug) : ((isset($title)) ? sanitize_title($title) : md5(time()));
		$slug = $this->unique_slug($slug, $parentid);
		$icon = (isset($icon)) ? sanitize_text_field($icon) : '';
		$topics = (isset($topics)) ? intval($topics) : 0;
		$posts = (isset($posts)) ? intval($posts) : 0;
		$order = (isset($order)) ? intval($order) : 0;
		$cat_layout = (isset($cat_layout)) ? intval($cat_layout) : 1;
		$status = (isset($status)) ? intval($status) : 1;
		$is_cat = (isset($is_cat)) ? intval($is_cat) : 0;
		if(!$parentid) $is_cat = 1;
		
		if($parentid) {
			$cat_layout = $this->wpforo->db->get_var("SELECT `cat_layout` FROM `".$this->wpforo->db->prefix ."wpforo_forums` WHERE `forumid` = " . intval($parentid) );
			$cat_layout = intval($cat_layout);
		}
		
		if( $this->wpforo->db->insert( 
				$this->wpforo->db->prefix . 'wpforo_forums', 
				array( 
					'title' => stripslashes($title), 
					'slug' => $slug, 
					'description' => stripslashes($description), 
					'parentid' => $parentid, 
					'icon' => $icon,
					'topics' => $topics,
					'posts' => $posts,
					'permissions' => $permission,
					'meta_key' => $meta_key, 
					'meta_desc' => $meta_desc, 
					'status' => $status,
					'is_cat' => $is_cat, 
					'cat_layout' => $cat_layout, 
					'order' => $order 
				), 
				array('%s','%s','%s','%d','%s','%d','%d','%s','%s','%s','%d','%d','%d','%d') 
			) 
		){
			$forumid = $this->wpforo->db->insert_id;
			$this->delete_tree_cache();
			wpforo_clean_cache($forumid, 'forum');
			$this->wpforo->notice->add('Your forum successfully added', 'success');
			return $forumid;
		}
		
		$this->wpforo->notice->add('Can\'t add forum', 'error');
		return FALSE;
	}
 
 	public function edit( $args = array() ){
 		if( !$this->wpforo->perm->usergroup_can('ef') ){
			$this->wpforo->notice->add('Permission denied for edit forum', 'error');
			return FALSE;
		}
 		
		if( empty($args) && empty($_REQUEST['forum']) ) return FALSE;
		if( empty($args) && !empty($_REQUEST['forum']) ) $args = $_REQUEST['forum'];
		if( !isset($args['forumid']) && isset($_GET['id']) ) $args['forumid'] = $_GET['id'];
		
		extract($args, EXTR_OVERWRITE);
		
		if( !isset($forumid) || !$forumid ){
			$this->wpforo->notice->add('Forum update error', 'error');
			return FALSE;
		}
		
		if( !isset($title) || !$title ){
			$this->wpforo->notice->add('Please insert required fields!', 'error');
			return FALSE;
		}
		
		$forumid = intval($forumid);
		$title = sanitize_text_field($title);
		$title = wpforo_text($title, 250, false);
		$description = wpforo_kses($description, 'post');
		$permission = (isset($permission) && is_array($permission)) ? serialize(array_map('sanitize_text_field', $permission)) : 'a:5:{i:1;s:4:"full";i:2;s:9:"moderator";i:3;s:8:"standard";i:4;s:9:"read_only";i:5;s:8:"standard";}';
		$meta_key = (isset($meta_key)) ? sanitize_text_field($meta_key) : '';
		$meta_desc = (isset($meta_desc)) ? sanitize_text_field($meta_desc) : '';
		$parentid = (isset($parentid)) ? intval($parentid) : 0;
		$slug = (isset($slug)) ? sanitize_title($slug) : ((isset($title)) ? sanitize_title($title) : md5(time()));
		$slug = $this->unique_slug($slug, $parentid, $forumid);
		$icon = (isset($icon)) ? sanitize_text_field($icon) : '';
		$topics = (isset($topics)) ? intval($topics) : 0;
		$posts = (isset($posts)) ? intval($posts) : 0;
		$order = (isset($order)) ? intval($order) : 0;
		$cat_layout = (isset($cat_layout)) ? intval($cat_layout) : 1;
		$status = (isset($status)) ? intval($status) : 1;
		$is_cat = (isset($is_cat)) ? intval($is_cat) : 0;
		if(!$parentid) $is_cat = 1;
		
		if($parentid) {
			$cat_layout = $this->wpforo->db->get_var("SELECT `cat_layout` FROM `".$this->wpforo->db->prefix ."wpforo_forums` WHERE `forumid` = " . intval($parentid) );
			$cat_layout = intval($cat_layout);
		}
		
		if( FALSE !== $this->wpforo->db->update( 
				$this->wpforo->db->prefix . 'wpforo_forums', 
				array( 
					'title' => stripslashes($title),
					'slug' => $slug, 
					'description' => stripslashes($description), 
					'parentid' => $parentid, 
					'icon' => $icon, 
					'permissions' => $permission,
					'meta_key' => $meta_key, 
					'meta_desc' => $meta_desc, 
					'status' => $status,
					'is_cat' => $is_cat,
					'cat_layout' => $cat_layout 
				),
				array('forumid' => $forumid),
				array('%s','%s','%s','%d','%s','%s','%s','%s','%d','%d','%d'),
				array('%d')
			)
		){
			if( isset($cat_layout) ){
				$childs = array();
				$this->get_childs($forumid, $childs);
				$sql = "UPDATE `".$this->wpforo->db->prefix . "wpforo_forums` SET `cat_layout` = ".intval($cat_layout)." WHERE `forumid` IN(". implode(',', array_map('intval', $childs)).")";
				$this->wpforo->db->query($sql);
			}
			$this->delete_tree_cache();
			wpforo_clean_cache($forumid, 'forum');
			$this->wpforo->notice->add('Forum successfully updated', 'success');
			return $forumid;
		}
		
		$this->wpforo->notice->add('Forum update error', 'error');
		return FALSE;
	}
	
	public function delete($forumid = 0){
		$forumid = intval($forumid);
		if(!$forumid && isset( $_REQUEST['id'] ) ) $forumid = intval($_REQUEST['id']);

		if( !$this->wpforo->perm->usergroup_can('df') ){
			$this->wpforo->notice->add('Permission denied for delete forum', 'error');
			return FALSE;
		}
		
		$childs = array();
		$this->get_childs($forumid, $childs);
		$forumids = implode(',', array_map('intval', $childs));

		// START delete topic posts include first post
			if( $topicids = $this->wpforo->db->get_col( "SELECT `topicid` FROM ".$this->wpforo->db->prefix."wpforo_topics WHERE `forumid` IN(". esc_sql($forumids) .")" ) ){
				foreach($topicids as $topicid){
					$this->wpforo->topic->delete($topicid, false);
				}
			}
		// END delete topic posts include first post
		
		if($this->wpforo->db->query( "DELETE FROM ".$this->wpforo->db->prefix."wpforo_forums WHERE `forumid` IN(". esc_sql($forumids) .")" )){
			$this->delete_tree_cache();
			wpforo_clean_cache();
			$this->wpforo->notice->add('Your forum successfully deleted', 'success');
			return TRUE;
		}

		$this->wpforo->notice->add('Forum deleting error', 'error');
		return FALSE;
	}

	public function merge($forumid = 0, $mergeid = 0){
		$forumid = intval($forumid);
		$mergeid = intval($mergeid);
		
		if(!$forumid && isset( $_REQUEST['id'] ) ) $forumid = intval($_REQUEST['id']);
		if(!$mergeid && isset( $_REQUEST['forum']['mergeid'] ) ) $mergeid = intval($_REQUEST['forum']['mergeid']);

		if( !$forumid || !$mergeid ) return false;

		if( $child_forumids = $this->get_child_forums( $forumid ) ){
			$forumids = trim( implode(',', array_map('intval', $child_forumids)) );
			if( $forumids ){
				$merge_layout = $this->wpforo->db->get_var("SELECT `cat_layout` FROM `".$this->wpforo->db->prefix."wpforo_forums` WHERE `forumid` = " . intval($mergeid) );
				
				if(!$this->wpforo->db->query( "UPDATE ".$this->wpforo->db->prefix."wpforo_forums SET `parentid` = " . intval($mergeid) . ", `cat_layout` = " . intval($merge_layout) . " WHERE `forumid` IN(". esc_sql($forumids) .")" )){
					$this->wpforo->notice->add('Forum merging error', 'error');
					return FALSE;
				}
			}
		}
		
		$this->wpforo->db->update( 
			$this->wpforo->db->prefix . 'wpforo_topics', 
			array( 'forumid' => $mergeid ),
			array( 'forumid' => $forumid ),
			array( '%d' ),
			array( '%d' )
		);
		$this->wpforo->db->update( 
			$this->wpforo->db->prefix . 'wpforo_posts', 
			array( 'forumid' => $mergeid ),
			array( 'forumid' => $forumid ),
			array( '%d' ),
			array( '%d' )
		);

		$this->rebuild_last_infos($mergeid);
		$this->rebuild_stats($mergeid);
		
		if($this->wpforo->db->delete( $this->wpforo->db->prefix.'wpforo_forums', array( 'forumid' => $forumid ), array( '%d' ) )){
			$this->delete_tree_cache();
			wpforo_clean_cache(0, 'forum');
			$this->wpforo->notice->add('Forum is successfully merged', 'success');
			return TRUE;
		}

		$this->wpforo->notice->add('Forum merging error', 'error');
		return FALSE;
	}
	
	public function rebuild_last_infos($forumid){
		
		$forumid = intval($forumid);
		
		$last_topicid = 0;
		$last_postid = 0;
		$last_userid = 0;
		$last_post_date = '0000-00-00 00:00:00';
		
		$last_topics = $this->wpforo->topic->get_topics( array('forumid' => $forumid, 'orderby' => 'topicid', 'order' => 'DESC', 'row_count' => 1) );
		if(!empty($last_topics)){
			$last_topic = $last_topics[0];
			$last_topicid = $last_topic['topicid'];
		}
		$last_posts = $this->wpforo->topic->get_topics( array('forumid' => $forumid, 'orderby' => 'modified', 'order' => 'DESC', 'row_count' => 1) );
		if(!empty($last_posts)){
			$last_post = $last_posts[0];
			$last_post_data = $this->wpforo->post->get_post($last_post['last_post']);
			if(!empty($last_post_data)){
				$last_postid = $last_post_data['postid'];
				$last_userid = $last_post_data['userid'];
				$last_post_date = $last_post_data['created'];
			}
		}
		
		$this->wpforo->db->query( "UPDATE `".$this->wpforo->db->prefix."wpforo_forums` 
										SET `last_topicid` = ".intval($last_topicid).", `last_postid` = ".intval($last_postid).", 
												`last_userid` = ".intval($last_userid).", `last_post_date` = '".esc_sql($last_post_date)."'  
														WHERE `forumid` = ".intval($forumid) );
		wpforo_clean_cache(0, 'forum');
	}

	public function rebuild_stats($forumid){
		if( !$forumid = intval($forumid) ) return false;
		$topics = $this->wpforo->topic->get_count( array('forumid' => $forumid) );
		$posts = $this->wpforo->post->get_count( array('forumid' => $forumid) );

		if( false !== $this->wpforo->db->update(
			$this->wpforo->db->prefix . "wpforo_forums",
			array('topics' => $topics, 'posts' => $posts ),
			array('forumid' => $forumid),
			array('%d', '%d'),
			array('%d')
		) ) {
			wpforo_clean_cache(0, 'forum');
			return true;
		}
		return false;
	}
	
	function get_forum( $args){
		
		$cache = $this->wpforo->cache->on('memory_cashe');
		
		if(is_array($args)){
			$default = array(
			  'forumid' => NULL, // forumid
			  'slug' => '', // slug
			  'status' => NULL, // status forum 1 OR 0
			  'cat_layout' => 1, // forum layout
			  'type' => 'all' // category, forum
			);
		}else{
			$default = array(
			  'forumid' => ( is_numeric($args) ? intval($args) : NULL ), // forumid
			  'slug' => ( !is_numeric($args) ? $args : '' ), // slug
			  'cat_layout' => 1, // forum layout
			  'status' => NULL, // status forum 1 OR 0
			  'type' => 'all' // category, forum
			);
		}
		$args = wpforo_parse_args( $args, $default );
		
		if( $cache && !empty($args['forumid']) ){
			if( !empty(self::$cache['forum'][$args['forumid']]) ){
				return self::$cache['forum'][$args['forumid']];
			}
		}
		
		if( $cache && !empty($args['slug']) ){
			if( !empty(self::$cache['forum'][addslashes($args['slug'])]) ){
				return self::$cache['forum'][addslashes($args['slug'])];
			}
		}
		if(!empty($args)){
			extract($args, EXTR_OVERWRITE);
			$sql = "SELECT * FROM `".$this->wpforo->db->prefix."wpforo_forums`";
			$wheres = array();
			if($forumid != NULL)  $wheres[] = "`forumid` = " . intval($forumid);
			if($status != NULL)   $wheres[] = "`status` = " . intval($status);
			if($type == 'category'){
				$wheres[] = "`is_cat` = 1";
			}elseif($type == 'forum'){
				$wheres[] = "`is_cat` = 0";
			}
			if($slug != '') $wheres[] = "`slug` = '" . esc_sql($slug) . "'";
			
			if(!empty($wheres)){
				$sql .= " WHERE " . implode( " AND ", $wheres );
			}
			$forum = $this->wpforo->db->get_row($sql, ARRAY_A);
			if(!empty($forum)) {
				$forum['url'] = $this->get_forum_url( $forum );
			}
			$forum = apply_filters('wpforo_get_forum', $forum);
			if($cache && isset($forumid)){
				self::$cache['forum'][addslashes($forum['slug'])] = $forum;
				return self::$cache['forum'][$forum['forumid']] = $forum;
			}
			else{
				return $forum;
			} 
		}
	}
	
	function get_forums($args = array(), &$items_count = 0 ){
		
		$cache = $this->wpforo->cache->on('object_cashe');
		
		$default = array( 
		  'include' => array(), // array( 2, 10, 25 )
		  'exclude' => array(),  // array( 2, 10, 25 )
		  'parent_include' => array(), // array( 2, 10, 25 )
		  'parent_exclude' => array(),  // array( 2, 10, 25 )
		  'parentid' => NULL,
		  'parent_slug' => '',
		  'status' => NULL,
		  'type' => 'all', // category, forum
		  'orderby' => 'order', // order by `field`
		  'order' => 'ASC', // ASC DESC
		  'offset' => NULL, // OFFSET
		  'row_count' => NULL, // ROW COUNT
		);
		
		$args = wpforo_parse_args( $args, $default );
		if(is_array($args) && !empty($args)){
			
			extract($args, EXTR_OVERWRITE);
			
			$include = wpforo_parse_args( $include );
			$exclude = wpforo_parse_args( $exclude );
			$parent_include = wpforo_parse_args( $parent_include );
			$parent_exclude = wpforo_parse_args( $parent_exclude );
			
			$sql = "SELECT * FROM `".$this->wpforo->db->prefix."wpforo_forums`";
			$wheres = array();
			
			if(!empty($include))        $wheres[] = "`forumid` IN(" . implode(', ', array_map('intval', $include)) . ")";
			if(!empty($exclude))        $wheres[] = "`forumid` NOT IN(" . implode(', ', array_map('intval', $exclude)) . ")";
			if(!empty($parent_include)) $wheres[] = "`parentid` IN(" . implode(', ', array_map('intval', $parent_include)) . ")";
			if(!empty($parent_exclude)) $wheres[] = "`parentid` NOT IN(" . implode(', ', array_map('intval', $parent_exclude)) . ")";
			if($parentid != NULL) $wheres[] = " `parentid` = " . intval($parentid);
			if($status != NULL)   $wheres[] = " `status` = "   . intval($status);
			
			if($type == 'category'){
				$wheres[] = " `is_cat` = 1";
			}elseif($type == 'forum'){
				$wheres[] = " `is_cat` = 0";
			}
			
			if($parent_slug != '') $wheres[] = "`slug` = '" . esc_sql($parent_slug) . "'";
			
			if(!empty($wheres)) $sql .= " WHERE " . implode( " AND ", $wheres );
			
			$item_count_sql = preg_replace('#SELECT.+?FROM#isu', 'SELECT count(*) FROM', $sql);
			if( $item_count_sql ) $items_count = $this->wpforo->db->get_var($item_count_sql);
			
			$sql .= esc_sql(" ORDER BY `$orderby` " . $order);
			
			if($row_count != NULL){
				if($offset != NULL){
					$sql .= esc_sql(" LIMIT $offset,$row_count");
				}else{
					$sql .= esc_sql(" LIMIT $row_count");
				}
			}
			
			if( $cache ){ $object_key = md5( $sql . $this->wpforo->current_user_groupid ); $object_cache = $this->wpforo->cache->get( $object_key ); if(!empty($object_cache)){$items_count = $object_cache['items_count']; return $object_cache['items'];}}
			
			$forums = $this->wpforo->db->get_results($sql, ARRAY_A);
			$forums = apply_filters('wpforo_get_topics', $forums);
			
			if($cache && isset($object_key) && !empty($forums)){ 
				self::$cache['forums'][$object_key]['items'] = $forums; 
				self::$cache['forums'][$object_key]['items_count'] = $items_count;
			}
			return $forums; 
		}
	}
	
	function search($needle, $fields = array()){
	
		if($needle){
			$needle = sanitize_text_field($needle);
			if(empty($fields)){
				$fields = array( 
				  'title',
				  'description',
				  'meta_key',
				  'meta_desc'
				);
			}
			
			$sql = "SELECT `forumid` FROM `".$this->wpforo->db->prefix."wpforo_forums`";
			$wheres = array();
			
			foreach($fields as $field){
				$wheres[] = "`" . esc_sql($field) . "` LIKE '%" . esc_sql($needle) . "%'";
			}
			
			$sql .= " WHERE " . implode(" OR ", $wheres);
			return $this->wpforo->db->get_col($sql);
		} 
		
		return array();
	}
	
	function update_hierarchy(){
		
		$this->delete_tree_cache();
		
		if(is_array($_REQUEST['forum']) && !empty($_REQUEST['forum'])){
			$i = 0;
			foreach($_REQUEST['forum'] as $hierarchy){
				
				extract($hierarchy, EXTR_OVERWRITE);
				
				if(!isset($forumid) || !$forumid = intval($forumid) ) continue;
				
				if(FALSE !== $this->wpforo->db->update( 
					$this->wpforo->db->prefix . 'wpforo_forums', 
					array( 
						'parentid' => (isset($parentid) ? intval($parentid) : 0), 
						'order' => (isset($order) ? intval($order) : 0), 
					),
					array( 'forumid' => intval($forumid) ),
					array( 
						'%d',
						'%d'
					),
					array( '%d' )
				)) $i++;
				
				if(isset($parentid) && $parentid = intval($parentid) ){
					$cat_layout = $this->wpforo->db->get_var("SELECT `cat_layout` FROM `".$this->wpforo->db->prefix."wpforo_forums` WHERE `forumid` = " . intval($parentid));
					$this->wpforo->db->query("UPDATE `".$this->wpforo->db->prefix."wpforo_forums` SET `cat_layout` = " . intval($cat_layout) . " WHERE `forumid` = " . intval($forumid));
				}
				
			}
			
			$this->wpforo->db->query("UPDATE `".$this->wpforo->db->prefix."wpforo_forums` SET `is_cat` = 0");
			$this->wpforo->db->query("UPDATE `".$this->wpforo->db->prefix."wpforo_forums` SET `is_cat` = 1 WHERE `parentid` = 0");
			
			if($i){
				$this->wpforo->notice->add('Forum hierarchy successfully updated', 'success');
			}else{
				$this->wpforo->notice->add('Cannot update forum hierarchy', 'error');
			}
			
		}
	}
	
	function get_childs($forumid, &$data){
		if(empty($data)) $data[] = $forumid;
		$sub_forums = $this->wpforo->db->get_results("SELECT `forumid` FROM ".$this->wpforo->db->prefix."wpforo_forums WHERE `parentid` = ".intval($forumid), ARRAY_A);
		if(!empty($sub_forums)){
			foreach($sub_forums as $sub_forum){
				$data[] = $sub_forum['forumid'];
				$this->get_childs($sub_forum['forumid'], $data);
			}
		}
	}
	
	
	// get forums tree for drop down menu
	
	/**
	 * Returns depth for this item.
	 *
	 * @since 1.0.0
	 *
	 * @param	int		item id 
	 *
	 * @param	int		before calling the function $depth = 0
	 * 
	 * @return	int	    
	 */
	function count_depth($forumid, &$depth){
		$parentid = $this->wpforo->db->get_var("SELECT `parentid` FROM `".$this->wpforo->db->prefix."wpforo_forums` WHERE `forumid` = ".intval($forumid));
		
		if($parentid != 0){
			$depth++;
			$this->count_depth($parentid, $depth);
		}
	}
	
	function get_child_forums($parent){
		$children = $this->wpforo->db->get_results("SELECT `forumid` AS childid FROM `".$this->wpforo->db->prefix."wpforo_forums` WHERE `parentid` = ".intval($parent)." ORDER BY `order`", ARRAY_A);
		if(!empty($children)){
			foreach( $children as $child ){
				$data[] = $child['childid'];
			}
			return $data;
		}else{
			return array();
		}
	}

	function forum_list( $parent, $type , $cats = TRUE, $topicid = 0 ){
		static $old_depth;
		global $wpforo;
		
		foreach ( $parent as $forumid ) {
			
			if ($forumid == 0) continue;
			$depth = 0;
			$this->count_depth($forumid, $depth);
			$name = $this->wpforo->db->get_var("SELECT `title` FROM `".$this->wpforo->db->prefix."wpforo_forums` WHERE `forumid` = ".intval($forumid));
			if($type == 'select_box'){ 
				if( isset($_GET['page']) && $_GET['page'] == 'wpforo-ads' && isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id']) && $_GET['id'] ){
					$ad = $wpforo->ad->get_ad($_GET['id']);
					$ad_forumids = explode(',', $ad['forumids']);
				}
				?><option value="<?php echo intval($forumid) ?>"<?php echo( !$cats && $depth == 0 ? ' disabled ': ''); echo ( $forumid == $this->parentid($topicid) || (isset( $_GET['forumid'] ) && $forumid == $_GET['forumid']) || ( !empty($_GET['wpff']) && in_array($forumid, $_GET['wpff']) ) || ( !empty($ad_forumids) && in_array($forumid, $ad_forumids) ) || ( isset($_GET['parentid']) && $_GET['parentid'] == $forumid ) ? ' selected ' : '' ) ?> > <?php echo esc_html(str_repeat( '— ', $depth ) . trim($name)) ?></option><?php
			}elseif($type == 'drag_menu'){ 
				$cur_forum = $this->wpforo->db->get_row("SELECT `cat_layout`, `topics`, `posts` FROM `".$this->wpforo->db->prefix."wpforo_forums` WHERE `forumid` = ".intval($forumid), ARRAY_A); 
				$cat_layout_name = ( $cur_forum['cat_layout'] == 2 ? 'Simplified Layout' : ( $cur_forum['cat_layout'] == 3 ? 'QA Layout' : 'Extended Layout' ) ); ?>
				
				<li id="menu-item-<?php echo intval($forumid) ?>" class="menu-item menu-item-depth-<?php echo esc_attr($depth) ?>">
					<input id="forumid-<?php echo intval($forumid) ?>" type="hidden" name="forum[<?php echo intval($forumid) ?>][forumid]"/>
					<input id="parentid-<?php echo intval($forumid) ?>" type="hidden" name="forum[<?php echo intval($forumid) ?>][parentid]"/>
					<input id="order-<?php echo intval($forumid) ?>" type="hidden" name="forum[<?php echo intval($forumid) ?>][order]"/>
					<dl class="menu-item-bar">
						<dt class="menu-item-handle forum_width">
							<span class="item-title forumtitle"><span style="font-weight:400; cursor:help;" title="Forum ID"><?php echo $forumid; ?> &nbsp;|&nbsp;</span> <?php echo esc_html($name) ?></span>
							<span class="item-controls">
                            	<span class="wpforo-cat-layout"><?php echo ( $depth != 0 ? __('Topics', 'wpforo') . '&nbsp;(' . intval($cur_forum['topics']) . ')&nbsp;,&nbsp;' . __('Posts', 'wpforo') . '&nbsp;(' . intval($cur_forum['posts']) . ')&nbsp; | &nbsp;' : '' ) ?><?php echo ( $depth == 0 ? '(&nbsp;<i>' . esc_html($cat_layout_name) . '</i>&nbsp;)&nbsp; | &nbsp;' : '' ); ?></span>
								<span class="menu_add"><a href="<?php echo admin_url( 'admin.php?page=wpforo-forums&action=add&parentid=' . intval($forumid) ) ?>" > <img src="<?php echo WPFORO_URL ?>/wpf-assets/images/icons/plus<?php echo ((!$depth) ? '-dark' : ''); ?>.png" title="<?php if( $depth ) : _e('Add a new SubForum', 'wpforo'); else: _e('Add a new Forum in this Category', 'wpforo'); endif; ?>"/></a></span> &nbsp;|&nbsp;
                                <span class="menu_edit"><a href="<?php echo admin_url( 'admin.php?page=wpforo-forums&id=' . intval($forumid) . '&action=edit' ) ?>"><img src="<?php echo WPFORO_URL ?>/wpf-assets/images/icons/pencil<?php echo ((!$depth) ? '-dark' : ''); ?>.png" title="<?php _e('edit', 'wpforo') ?>"/></a></span>&nbsp;|&nbsp;
                                <?php if( $this->wpforo->perm->usergroup_can('df') ): ?>
                                    <span class="menu_delete"><a href="<?php echo admin_url( 'admin.php?page=wpforo-forums&id=' . intval($forumid) . '&action=del' ) ?>"><img src="<?php echo WPFORO_URL ?>/wpf-assets/images/icons/trash<?php echo ((!$depth) ? '-dark' : ''); ?>.png" title="<?php _e('delete', 'wpforo') ?>"/></a></span>&nbsp;|&nbsp;
                                <?php endif; ?>
								<span class="menu_view"><a href="<?php echo esc_url(wpforo_forum($forumid, 'url')); ?>" > <img src="<?php echo WPFORO_URL ?>/wpf-assets/images/icons/eye<?php echo ((!$depth) ? '-dark' : ''); ?>.png" title="<?php _e('View', 'wpforo') ?>"/> </a> </span>
                            
                            </span>	
						</dt>
					</dl>
					<ul class="menu-item-transport"></ul>
				</li>
				
			 <?php
			}elseif($type == 'front_list'){
				$slug = $this->wpforo->db->get_var("SELECT `slug` FROM `".$this->wpforo->db->prefix."wpforo_forums` WHERE `forumid` = ".intval($forumid));
				if(isset($old_depth) && $old_depth == $depth) echo '</dd><dd>';
				if(isset($old_depth) && $old_depth < $depth) echo '<dl><dd>';
				if(isset($old_depth) && $old_depth > $depth) echo '</dd></dl>';
				$old_depth = $depth;
				if(isset($wpforo->current_object) && isset($wpforo->current_object['forumid'])){
					if( $forumid == $wpforo->current_object['forumid'] ){
						echo'<span class="wpf-dl-item wpf-dl-current"><i class="fa fa-comments-o"></i><strong>'.esc_html($name).'</strong><a href="' . esc_url( wpforo_forum($forumid, 'url') ) . '" >&nbsp;&raquo;</a></span>';
					}
					else{
						echo'<span class="wpf-dl-item"><a href="'.esc_url( wpforo_forum($forumid, 'url') ).'" ><i class="fa fa-comments-o"></i>'.esc_html($name).'</a></span>';
					}
				}
				else{
					echo'<span class="wpf-dl-item"><a href="'.esc_url( wpforo_forum($forumid, 'url') ).'" ><i class="fa fa-comments-o"></i>'.esc_html($name).'</a></span>';
				}
			}
			$subforums = $this->get_child_forums($forumid);
			if( !empty($subforums) ){
				$this->forum_list($subforums, $type);
			}
		}
	}

	function tree( $type = 'front_list', $cats = TRUE, $topicid = 0 ){
		$parentids = $this->wpforo->db->get_results("SELECT `forumid` AS parentid FROM `".$this->wpforo->db->prefix."wpforo_forums` WHERE `parentid` = 0 ORDER BY `order`", ARRAY_A);
		if(!empty($parentids)){
			foreach( $parentids as $parentid ){
				$data[] = $parentid['parentid'];
			}
			if( !wpforo_is_admin() ){
				$key = md5( serialize($data) . $type . (int)$cats . $this->wpforo->current_user_groupid );
				$html = get_option( 'wpforo_forum_tree_' . $key );
				if( $html ){
					echo $html;
				}
				elseif( function_exists('ob_start') ){
					ob_start();
					$this->forum_list($data, $type, $cats);
					$html = ob_get_clean();
					if($type != 'drag_menu'){
						update_option( 'wpforo_forum_tree_' . $key, $html );
					}
					echo $html;
				}
			}
			else{
				$this->forum_list($data, $type, $cats);
			}
		}
	}
	// end forums tree for drop down menu
	
	public function delete_tree_cache() {
		$this->wpforo->db->query("DELETE FROM " . $this->wpforo->db->options . " WHERE `option_name` LIKE 'wpforo_forum_tree_%'");
	}
	
	function parentid( $topicid = 0 ){
		if(isset($_GET['page']) && $_GET['page'] == 'wpforo-forums'){
			if( isset($_GET['id'])) return $this->wpforo->db->get_var("SELECT `parentid` FROM `".$this->wpforo->db->prefix."wpforo_forums` WHERE `forumid` = ".intval($_GET['id']));	
		}
		elseif( isset($_GET['page']) && $_GET['page'] == 'wpforo-topics' ){
			if( isset($_GET['id'])) return $this->wpforo->db->get_var( "SELECT `forumid` FROM `".$this->wpforo->db->prefix."wpforo_topics` WHERE `topicid` = ".intval($_GET['id']));	
		}else{
			if( $topicid ) return $this->wpforo->db->get_var( "SELECT `forumid` FROM `".$this->wpforo->db->prefix."wpforo_topics` WHERE `topicid` = ".intval($topicid));	
		}
	}
	
	function permissions(){
		$access_arr = $this->wpforo->perm->get_accesses();
		if(!empty( $access_arr )){
			
			if(isset($_GET['id'])){
				if($permissions_srlz = $this->wpforo->db->get_var("SELECT `permissions` FROM `".$this->wpforo->db->prefix."wpforo_forums` WHERE `forumid` = ".intval($_GET['id']))){
					$permissions_arr = unserialize($permissions_srlz);
				}
			}
			
			if($usergroups = $this->wpforo->db->get_results("SELECT `groupid`, `name` FROM `".$this->wpforo->db->prefix."wpforo_usergroups`", ARRAY_A)){
				foreach($usergroups as $usergroup){
					extract($usergroup, EXTR_OVERWRITE);
					echo '
						<tr>
							<td>'.esc_html($name).'</td>
							<td>
								<select name="forum[permission]['.intval($groupid).']">';
									foreach($access_arr as $value){
										
										echo '<option value="'.esc_attr($value['access']).'" '.
												((isset($permissions_arr[$groupid]) && $value['access'] == $permissions_arr[$groupid]) 
													|| (!isset($permissions_arr[$groupid]) 
															&& (($name == 'Guest' && $value['access'] == 'read_only') 
																	|| ($name == 'Registered' && $value['access'] == 'standard') 
																		|| ($name == 'Customer' && $value['access'] == 'standard') 
																			|| ($name == 'Moderator' && $value['access'] == 'moderator') 
																				|| ($name == 'Admin' && $value['access'] == 'full')
																					|| ($name != 'Guest' && $name != 'Registered' && $name != 'Customer' && $name != 'Moderator' && $name != 'Admin' && $value['access'] == 'standard') )) ? ' selected ' : '').'>'.esc_html( __( $value['title'], 'wpforo') ).'</option>';
									}
									echo'
								</select>
							</td>
						</tr>
					';
				}
				
				
				
			}
		}
	}
	
	/**
	 * array get_counts(array or id(num)) 
	 * 
	 * @since 1.0.0
	 *
	 * @param	array defined arguments array for returning
	 *
	 * @return int count topics
	 */
	function get_counts($forumids){
		
		if( !empty($forumids) ){
			
			$wheres = '';
			
			if(!is_array($forumids)){
				$wheres = "`forumid` = " . intval($forumids);
			}else{
				$wheres = "`forumid` IN(" . implode(', ', array_map('intval', $forumids)) . ")";
			}
			
			$sql = "SELECT SUM(`topics`) as topics, SUM(`posts`) as posts FROM `".$this->wpforo->db->prefix."wpforo_forums` WHERE " . $wheres;
			return $this->wpforo->db->get_row($sql, ARRAY_A);
			
		}
		
		return 0;
	}
	
	/**
	 * array get_layout(array) 
	 * 
	 * @since 1.0.0
	 *
	 * @param	array defined arguments array for returning
	 *
	 * @return int layout id
	 */
	function get_layout($args){
		if(is_array($args)){
			$default = array(
			  'forumid' => NULL, // forum id
			  'topicid' => NULL, // topic id
			  'postid'  => NULL, // post id
			);
		}else{
			$default = array(
			  'forumid' => $args,    // forumid
			  'topicid' => NULL,    // topic id
			  'postid'  => NULL,    // post id
			);
		}
		$args = wpforo_parse_args( $args, $default );
		if(!empty($args)){
			extract($args, EXTR_OVERWRITE);
			
			if( $args['forumid'] ){
				$sql = "SELECT `cat_layout` FROM `".$this->wpforo->db->prefix."wpforo_forums` WHERE `forumid` = " . intval($args['forumid']);
				$cat_layout = $this->wpforo->db->get_var($sql);
				return ( $cat_layout ? $cat_layout : 1 );
			}elseif( $args['topicid'] ){
				$sql = "SELECT `forumid` FROM `".$this->wpforo->db->prefix."wpforo_topics` WHERE `topicid` = " . intval($args['topicid']);
				$forumid = $this->wpforo->db->get_var($sql);
				return $this->get_layout(array( 'forumid' => $forumid ));
			}elseif( $args['postid'] ){
				$sql = "SELECT `forumid` FROM `".$this->wpforo->db->prefix."wpforo_posts` WHERE `postid` = " .  intval($args['postid']);
				$forumid = $this->wpforo->db->get_var($sql);
				return $this->get_layout(array( 'forumid' => $forumid ));
			}
		}
		
	}

	function get_forum_url($forum){
		
		if( !is_array($forum) ){
			if(is_numeric($forum)){
				$forum = $this->get_forum($forum);
			}else{
				$forum = array('slug' => $forum);
			}
		}
		
		if( is_array($forum) && !empty($forum) ){
			return wpforo_home_url( utf8_uri_encode($forum['slug']) );
		}else{
			return wpforo_home_url();
		}
	}
	
	function get_all_relative_ids($forumid, &$relative_ids){
		$forum = $this->wpforo->db->get_row("SELECT `parentid`, `forumid` FROM `".$this->wpforo->db->prefix."wpforo_forums` WHERE `forumid` = ".intval($forumid), ARRAY_A);
		
		if($forum['parentid'] != 0){
			$relative_ids[] = $forum['forumid'];
			$this->get_all_relative_ids($forum['parentid'], $relative_ids);
		}else{
			$relative_ids[] = $forum['forumid'];
			$relative_ids = array_reverse($relative_ids);
		}
	}

	function get_count( $args = array() ){
		$sql = "SELECT COUNT(`forumid`) FROM `".$this->wpforo->db->prefix."wpforo_forums`";
		if( !empty($args) ){
			$wheres = array();
			foreach ($args as $key => $value)  $wheres[] = "`$key` = " . intval($value);
			if($wheres) $sql .= " WHERE " . implode(' AND ', $wheres);
		}
		return $this->wpforo->db->get_var($sql);
	}
	
	function get_lastinfo( $ids = array() ){
		$lastinfo = array();
		if(!empty($ids)){
			$ids = implode(',', array_map('intval', $ids));
			$lastinfo = $this->wpforo->db->get_row( "SELECT `userid` as last_userid, `topicid` as last_topicid, `postid` as last_postid, `created` as last_post_date FROM `" . $this->wpforo->db->prefix . "wpforo_posts` WHERE forumid IN(" . $ids  .") ORDER BY `created` DESC LIMIT 1", ARRAY_A);
		}
		return $lastinfo;
	}
}

?>