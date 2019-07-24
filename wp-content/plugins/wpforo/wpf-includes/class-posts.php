<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;

class wpForoPost{
	
	private $wpforo;
	public static $cache = array( 'posts' => array(), 'post' => array(), 'item' => array(), 'topic_slug' => array(), 'forum_slug' => array(), 'post_url' => array() );
	
	function __construct( $wpForo ){
		if(!isset($this->wpforo)) $this->wpforo = $wpForo;
	}
	
	public function get_cache( $var ){
		if( isset(self::$cache[$var]) ) return self::$cache[$var];
	}
	
	public function add( $args = array() ){
		
		//This variable will be based on according CAN of guest usergroup once Guest Posing is ready
		$guestposting = false;
		
		if( empty($args) && empty($_REQUEST['post']) ){ $this->wpforo->notice->add('Reply request error', 'error'); return FALSE; }
		if( empty($args) && !empty($_REQUEST['post']) ){ $args = $_REQUEST['post']; $args['body'] = $_REQUEST['postbody']; }
		if( !isset($args['body']) || !$args['body'] ){ $this->wpforo->notice->add('Post is empty', 'error'); return FALSE; }
		$args['name'] = (isset($args['name']) ? $args['name'] : '' );
		$args['email'] = (isset($args['email']) ? $args['email'] : '' );
		if( isset($args['userid']) && $args['userid'] == 0 && $args['name'] && $args['email'] ) $guestposting = true;
		
		extract($args, EXTR_OVERWRITE);
		
		if( !isset($topicid) || !$topicid ){ $this->wpforo->notice->add('Error: No topic selected', 'error'); return FALSE; }
		if( !$topic = $this->wpforo->topic->get_topic(intval($topicid)) ){ $this->wpforo->notice->add('Error: Topic is not found', 'error'); return FALSE; }
		if( !$forum = $this->wpforo->forum->get_forum(intval($topic['forumid'])) ){ $this->wpforo->notice->add('Error: Forum is not found', 'error'); return FALSE; }
		
		if( $topic['closed'] ){
			$this->wpforo->notice->add('Can\'t write a post: This topic is closed', 'error');
			return FALSE;
		}
		
		if( !$guestposting && !$this->wpforo->perm->forum_can('cr', $topic['forumid']) ){
			$this->wpforo->notice->add('You haven\'t permission to create post into this forum', 'error');
			return FALSE;
		}
		
		do_action( 'wpforo_start_add_post', $args );
		
		$post = $args;
		$post['forumid'] = $forumid = (isset($topic['forumid']) ? intval($topic['forumid']) : 0);
		$post['parentid'] = $parentid = (isset($parentid) ? intval($parentid) : 0);
		$post['title'] = $title = (isset($title) ? wpforo_text( trim($title), 250, false ) : '');
		$post['body'] = $body = ( isset($body) ? preg_replace('#</pre>[\r\n\t\s\0]*<pre>#isu', "\r\n", $body) : '' );
		$post['created'] = $created = ( isset($created) ? $created : current_time( 'mysql', 1 ) );
		$post['userid'] = $userid = ( isset($userid) ? intval($userid) : $this->wpforo->current_userid );
		
		$post = apply_filters('wpforo_add_post_data_filter', $post);
		
		if(empty($post)) return FALSE;
		
		extract($post, EXTR_OVERWRITE);
		
		if(isset($forumid)) $forumid = intval($forumid);
		if(isset($topicid)) $topicid = intval($topicid);
		if(isset($parentid)) $parentid = intval($parentid);
		if(isset($title)) $title = sanitize_text_field(trim($title));
		if(isset($created)) $created = sanitize_text_field($created);
		if(isset($userid)) $userid = intval($userid);
		if(isset($body)) $body = wpforo_kses(trim($body), 'post');
		if(isset($status)) $status = intval($status);
		if(isset($name)) $name = strip_tags(trim($name));
		if(isset($email)) $email = strip_tags(trim($email));
		
		do_action( 'wpforo_before_add_post', $post );
		
		if(
			$this->wpforo->db->insert( 
				$this->wpforo->db->prefix . 'wpforo_posts', 
				array( 
					'forumid'	=> $forumid, 
					'topicid'	=> $topicid, 
					'parentid'	=> $parentid,
					'userid' 	=> $userid,
					'title'     => stripslashes($title), 
					'body'      => stripslashes($body), 
					'created'	=> $created,
					'modified'	=> $created,
					'status'	=> (isset($status) ? $status : 0),
					'name' 		=> $name, 
					'email' 	=> $email
				), 
				array('%d','%d','%d','%d','%s','%s','%s','%s','%d','%s','%s')
			)
		){
			$postid = $this->wpforo->db->insert_id;
			
			$answ_incr = '';
			$comm_incr = '';
			if( isset($forum['cat_layout']) && $forum['cat_layout'] == 3 ){
				if($parentid){
					$comm_incr = ', `comments` = `comments` + 1 ';
				}else{
					$answ_incr = ', `answers` = `answers` + 1 ';
				}
			}
			
			$this->wpforo->db->query( "UPDATE `"  . $this->wpforo->db->prefix . "wpforo_forums` SET `last_topicid` = ". intval($topicid) .", `last_postid` = ". intval($postid) .", `last_post_date` = '".esc_sql($created)."', `last_userid` =  " . intval($userid) . ", `posts` = `posts` + 1 WHERE `forumid` = " . intval($topic['forumid']) );
			$this->wpforo->db->query( "UPDATE `"  . $this->wpforo->db->prefix . "wpforo_topics` SET `modified` = '" . esc_sql($created) . "', `last_post` = ". intval($postid) .", `posts` = `posts` + 1 $answ_incr WHERE `topicid` = " . intval($topicid) );
			$this->wpforo->db->query( "UPDATE `"  . $this->wpforo->db->prefix . "wpforo_profiles` SET `posts` = `posts` + 1 $answ_incr $comm_incr WHERE `userid` = " . intval($userid)  );
			
			$post['postid'] = $postid;
			$post['posturl'] = $this->get_post_url($postid);
			
			do_action( 'wpforo_after_add_post', $post, $topic );
			
			wpforo_clean_cache($postid, 'post');
			$this->wpforo->member->reset($userid);
			$this->wpforo->notice->add('You successfully replied', 'success');
			return $postid;
		}
		
		$this->wpforo->notice->add('Reply request error', 'error');
		return FALSE;
	}
	
	public function edit( $args = array() ){
		
		//This variable will be based on according CAN of guest usergroup once Guest Posing is ready
		$guestposting = false;
		
		if( empty($args) && (!isset($_REQUEST['post']) || empty($_REQUEST['post'])) ) return FALSE;
		if( empty($args) && !empty($_REQUEST['post']) ){ $args = $_REQUEST['post']; $args['body'] = $_REQUEST['postbody']; }
		
		do_action( 'wpforo_start_edit_post', $args );
		
		if( !isset($args['postid']) || !$args['postid'] || !is_numeric($args['postid']) ){
			$this->wpforo->notice->add('Cannot update post data', 'error');
			return FALSE;
		}
		$args['postid'] = intval($args['postid']);
		if( !$post = $this->get_post($args['postid']) ){ $this->wpforo->notice->add('No Posts found for update', 'error'); return FALSE; }
		
		$args['userid'] = $post['userid'];
		$args['status'] = $post['status'];
	
		if( isset($args['userid']) && $args['userid'] == 0 && isset($args['name']) && isset($args['email']) ) $guestposting = true;
		
		$args = apply_filters('wpforo_edit_post_data_filter', $args);
		if(empty($args)) return FALSE;
		
		extract($args, EXTR_OVERWRITE);
		
		if( !$guestposting ){
			$diff = current_time( 'timestamp', 1 ) - strtotime($post['created']);
			if( !($this->wpforo->perm->forum_can('er', $post['forumid']) || 
					($this->wpforo->current_userid == $post['userid'] && $this->wpforo->perm->forum_can('eor', $post['forumid']) && 
						$diff < $this->wpforo->post_options['eor_durr'])) ){
				$this->wpforo->notice->add('You haven\'t permission to edit post from this forum', 'error');
				return FALSE;
			}
		}
		
		$title = (isset($title) ? wpforo_text( trim($title), 250, false ) : '');
		$body = ( isset($body) ? preg_replace('#</pre>[\r\n\t\s\0]*<pre>#isu', "\r\n", $body) : '' );
		
		if(isset($forumid)) $forumid = intval($forumid);
		if(isset($topicid)) $topicid = intval($topicid);
		if(isset($parentid)) $parentid = intval($parentid);
		if(isset($title)) $title = sanitize_text_field(trim($title));
		if(isset($slug)) $slug = sanitize_title($slug);
		if(isset($created)) $created = sanitize_text_field($created);
		if(isset($userid)) $userid = intval($userid);
		if(isset($body)) $body = wpforo_kses(trim($body), 'post');
		if(isset($status)) $status = intval($status);
		if(isset($name)) $name = strip_tags(trim($name));
		if(isset($email)) $email = strip_tags(trim($email));
		
		$title  = ( isset($title) ? stripslashes($title) : stripslashes($post['title']) );
		$body = ( (isset($body) && $body) ? stripslashes($body) : stripslashes($post['body']) );
		$status = ( isset($status) ? $status : intval($post['status']) );
		$name = ( isset($name) ? stripslashes($name) : stripslashes($post['name']) );
		$email = ( isset($email) ? stripslashes($email) : stripslashes($post['email']) );
		
		if( FALSE !== $this->wpforo->db->update(
				$this->wpforo->db->prefix."wpforo_posts",
				array( 
					'title'     => $title,
					'body'      => $body,
					'modified'	=> current_time( 'mysql', 1 ),
					'status'  => $status,
					'name' => $name,
					'email' => $email,
				), 
				array('postid' => $postid),
				array('%s','%s','%s','%d','%s','%s'), 
				array('%d') 
			)
		){
			do_action( 'wpforo_after_edit_post', array( 'postid' => $postid, 'topicid' => $topicid, 'title' => $title, 'body' => $body, 'status' => $status, 'name' => $name, 'email' => $email) );
			
			wpforo_clean_cache($postid, 'post');
			$this->wpforo->notice->add('This post successfully edited', 'success');
			return $postid;
		}
		
		$this->wpforo->notice->add('Reply request error', 'error');
		return FALSE;
	}
	
	#################################################################################
	/**
	 * Delete post from DB
	 * 
	 * Returns true if successfully deleted or false.
	 *
	 * @since 1.0.0
	 *
	 * @return	bool
	 */
	 
	function delete($postid, $delete_cache = true){
		$postid = intval($postid);
		
		if( !$post = $this->get_post($postid) ) return true;

		do_action('wpforo_before_delete_post', $post);

		$diff = current_time( 'timestamp', 1 ) - strtotime($post['created']);
		if( !($this->wpforo->perm->forum_can('dr', $post['forumid']) || ($this->wpforo->current_userid == $post['userid'] && $this->wpforo->perm->forum_can('dor', $post['forumid']) && $diff < $this->wpforo->post_options['dor_durr'])) ){
			$this->wpforo->notice->add('You haven\'t permission to delete post from this forum', 'error');
			return FALSE;
		}
		
		//Find and delete default atatchments before deleting post
		$this->delete_attachments( $postid );
		
		//Delete post
		if( $this->wpforo->db->delete($this->wpforo->db->prefix . 'wpforo_posts',  array( 'postid' => intval($postid) ), array( '%d' )) ){
			$last_post = $this->get_posts( array('topicid' => intval($post['topicid']), 'order' => 'DESC', 'row_count' => 1) );
			if(is_array($last_post) && !empty($last_post)){
				$last_post = $last_post[0];
			}else{
				$last_post = array( 'created' => '0000-00-00 00:00:00', 'userid' => 0, 'postid' => 0 );
			}
			
			$this->wpforo->db->delete(
				$this->wpforo->db->prefix.'wpforo_likes', array( 'postid' => $postid ), array( '%d' )
			);
			$this->wpforo->db->delete(
				$this->wpforo->db->prefix.'wpforo_votes', array( 'postid' => $postid ), array( '%d' )
			);
			
			$answ_incr = '';
			$comm_incr = '';
			$forum = $this->wpforo->forum->get_forum($post['forumid']);
			if( isset($forum['cat_layout']) && $forum['cat_layout'] == 3 ){
				if($post['parentid']){
					$comm_incr = ', `comments` = IF( (`comments` - 1) < 0, 0, `comments` - 1 ) ';
				}else{
					$answ_incr = ', `answers` = IF( (`answers` - 1) < 0, 0, `answers` - 1 ) ';
				}
			}
			
			if($this->wpforo->db->query( "UPDATE IGNORE " . $this->wpforo->db->prefix . "wpforo_topics SET `last_post` = " . intval($last_post['postid']) . ", `posts` = IF( (`posts` - 1) < 0, 0, `posts` - 1 ) $answ_incr WHERE `topicid` = " . intval( $post['topicid'] ))){
				if( $this->wpforo->db->query( "UPDATE IGNORE `" . $this->wpforo->db->prefix . "wpforo_forums` SET `last_post_date` = '" . esc_sql($last_post['created']) . "', `last_userid` = " . intval($last_post['userid']) . ", `last_postid` = " . intval($last_post['postid']) . ", `posts` = IF( (`posts` - 1) < 0, 0, `posts` - 1 ) WHERE `forumid` = " . intval( $post['forumid'] ))){
					if( $this->wpforo->db->query( "UPDATE IGNORE `"  . $this->wpforo->db->prefix . "wpforo_profiles` SET `posts` = IF( (`posts` - 1) < 0, 0, `posts` - 1 ) $answ_incr $comm_incr WHERE `userid` = " . intval($post['userid']) ) ){
						$this->wpforo->member->reset($post['userid']);
						$this->wpforo->notice->add('This post successfully deleted', 'success');
					}
				}
			}
			
			do_action('wpforo_after_delete_post', $post);
			
			if( $post['is_first_post'] ) return $this->wpforo->topic->delete($post['topicid']);
			if( $delete_cache ) wpforo_clean_cache(0, 'post');
			return TRUE;
		}
		
		$this->wpforo->notice->add('Post delete error', 'error');
		return FALSE;
	}
	
	#################################################################################
	/**
	 * array get_post(id(num)) 
	 * 
	 * Returns array from defined and default arguments.
	 *
	 * @since 1.0.0
	 *
	 * @return	array	
	 */
	function get_post($postid){
		
		$post = array();
		$cache = $this->wpforo->cache->on('memory_cashe');
		
		if( $cache && isset(self::$cache['post'][$postid]) ){
			return self::$cache['post'][$postid];
		}
		
		$sql = "SELECT * FROM `".$this->wpforo->db->prefix."wpforo_posts` WHERE `postid` = " . intval($postid);
		$post = $this->wpforo->db->get_row($sql, ARRAY_A);
		if(!empty($post)) $post['userid'] = intval($post['userid']);
		
		if( isset($post['forumid']) && $post['forumid'] && !$this->wpforo->perm->forum_can('vf', $post['forumid']) ){
			return array();
		}
		
		if( isset($post['status']) && $post['status'] && !wpforo_is_owner($post['userid'])){
			if( isset($post['forumid']) && $post['forumid'] && !$this->wpforo->perm->forum_can('au', $post['forumid']) ){
				return array();
			}
		}
		
		if($cache && isset($postid)){
			self::$cache['post'][$postid] = $post;
		}
		
		$post = apply_filters('wpforo_get_post', $post);
		return $post;
	}
	
	/**
	 * Returns merged arguments array from defined and default arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param	array		
	 *
	 * @return 	array
	 */
	function get_posts($args = array(), &$items_count = 0){
		
		$cache = $this->wpforo->cache->on('object_cashe');
		
		$default = array( 
		  'include' => array(), 		// array( 2, 10, 25 )
		  'exclude' => array(),  		// array( 2, 10, 25 )
		  
		  'topicid'		=> NULL,		// topic id in DB
		  'forumid'		=> NULL,		// forum id in DB
		  'parentid'	=> -1,			// parent post id
		  'userid'		=> NULL,		// user id in DB
		  'orderby'		=> 'postid', 	// forumid, order, parentid
		  'order'		=> 'ASC', 		// ASC DESC
		  'offset' 		=> NULL,		// this use when you give row_count
		  'row_count'	=> NULL, 		// 4 or 1 ...
		  'status'		=> NULL, 		// 0 or 1 ...
		  'email'		=> NULL, 		// example@example.com ...
		  		  
		  'check_private' => FALSE
		);
		
		$args = wpforo_parse_args( $args, $default );
		
		if(is_array($args) && !empty($args)){
			
			extract($args, EXTR_OVERWRITE);
			
			if( $row_count === 0 ) return array();
			
			$include = wpforo_parse_args( $include );
			$exclude = wpforo_parse_args( $exclude );
			
			$wheres = array();
			$table_as_prefix = '`'.$this->wpforo->db->prefix.'wpforo_posts`.';
			
			if(!empty($include)) $wheres[] = $table_as_prefix . "`postid` IN(" . implode(', ', array_map('intval', $include)) . ")";
			if(!empty($exclude)) $wheres[] = $table_as_prefix . "`postid` NOT IN(" . implode(', ', array_map('intval', $exclude)) . ")";
			
			if(!is_null($topicid)) $wheres[] = $table_as_prefix . "`topicid` = " . intval($topicid);
			if($parentid != -1) $wheres[]  = $table_as_prefix . "`parentid` = " . intval($parentid);
			if(!is_null($userid)) $wheres[]  = $table_as_prefix . "`userid` = " . intval($userid);
			if(!is_null($status)) $wheres[]  = $table_as_prefix . "`status` = " . intval($status);
			if(!is_null($email)) $wheres[]  = $table_as_prefix . "`email` = '" . esc_sql($email) . "' ";
			
			if( isset($forumid) && $forumid ){
				if( $this->wpforo->perm->forum_can('au', $forumid) ){
					if(!is_null($status)) $wheres[] = $table_as_prefix . " `status` = " . intval($status);
				}
				elseif( isset($this->wpforo->current_userid) && $this->wpforo->current_userid ){
					$wheres[] = " ( " . $table_as_prefix .  "`status` = 0 OR (" . $table_as_prefix .  "`status` = 1 AND " . $table_as_prefix .  "`userid` = " .intval($this->wpforo->current_userid). ") )";
				}
				else{
					$wheres[] = " " . $table_as_prefix .  "`status` = 0";
				}
			}
			
			if( $check_private ){
				$sql = "SELECT DISTINCT `".$this->wpforo->db->prefix."wpforo_posts`.*, `".$this->wpforo->db->prefix."wpforo_topics`.`private` FROM `".$this->wpforo->db->prefix."wpforo_posts`, `".$this->wpforo->db->prefix."wpforo_topics`";
				if(!empty($wheres)){
					$sql .= " WHERE `".$this->wpforo->db->prefix."wpforo_posts`.`topicid` = `".$this->wpforo->db->prefix."wpforo_topics`.`topicid` " . ((!empty($wheres)) ? 'AND' : '') . " " . implode(" AND ", $wheres);
				}
				else{
					$sql .= " WHERE `".$this->wpforo->db->prefix."wpforo_posts`.`topicid` = `".$this->wpforo->db->prefix."wpforo_topics`.`topicid` ";
				}
				$sql .= " ORDER BY " .$table_as_prefix . "`$orderby` " . $order;
				$item_count_sql = preg_replace('#SELECT.+?FROM#isu', 'SELECT count(*) FROM', $sql);
			}
			else{
				$sql = "SELECT * FROM `".$this->wpforo->db->prefix."wpforo_posts`";
				if(!empty($wheres)){
					$sql .= " WHERE " . implode(" AND ", $wheres);
				}
				$sql .= " ORDER BY `$orderby` " . $order;
				$item_count_sql = preg_replace('#SELECT.+?FROM#isu', 'SELECT count(*) FROM', $sql);
			}
			
			if( $item_count_sql ) $items_count = $this->wpforo->db->get_var($item_count_sql);
			
			if($row_count != NULL){
				if($offset != NULL){
					$sql .= esc_sql(" LIMIT $offset,$row_count");
				}else{
					$sql .= esc_sql(" LIMIT $row_count");
				}
			}
			
			if( $cache ){ $object_key = md5( $sql . $this->wpforo->current_user_groupid ); $object_cache = $this->wpforo->cache->get($object_key); if(!empty($object_cache)){ $items_count = $object_cache['items_count']; return $object_cache['items']; }}
			
			$posts = $this->wpforo->db->get_results($sql, ARRAY_A);
			$posts = apply_filters('wpforo_get_posts', $posts);
			
			if( $check_private ){
				foreach($posts as $key => $post){
					if( isset($post['forumid']) && !$this->wpforo->perm->forum_can('vf', $post['forumid']) ){
						unset($posts[$key]);
					}
					if( isset($posts[$key]) && isset($post['forumid']) && isset($post['private']) && $post['private'] && !wpforo_is_owner($post['userid']) ){
						if( !$this->wpforo->perm->forum_can('vp', $post['forumid']) ){
							unset($posts[$key]);
						}
					}
					if( isset($posts[$key]) && isset($post['forumid']) && isset($post['status']) && $post['status'] && !wpforo_is_owner($post['userid']) ){
						if( !$this->wpforo->perm->forum_can('au', $post['forumid']) ){
							unset($posts[$key]);
						}
					}
				}
			}
			
			if($cache && isset($object_key) && !empty($posts)){ 
				self::$cache['posts'][$object_key]['items'] = $posts; 
				self::$cache['posts'][$object_key]['items_count'] = $items_count;
			}
			return $posts;
		}
	}
	
	function get_posts_filtered( $args = array() ){
		$posts = array();
		$posts = $this->get_posts( $args );
		if( !empty($posts) ){
			foreach($posts as $key => $post){
				if( isset($post['forumid']) && !$this->wpforo->perm->forum_can('vf', $post['forumid']) ){
					unset($posts[$key]);
				}
				if( isset($posts[$key]) && isset($post['forumid']) && isset($post['private']) && $post['private'] && !wpforo_is_owner($post['userid']) ){
					if( !$this->wpforo->perm->forum_can('vp', $post['forumid']) ){
						unset($posts[$key]);
					}
				}
				if( isset($posts[$key]) && isset($post['forumid']) && isset($post['status']) && $post['status'] && !wpforo_is_owner($post['userid']) ){
					if( !$this->wpforo->perm->forum_can('au', $post['forumid']) ){
						unset($posts[$key]);
					}
				}
			}
		}
		return $posts;
	}
	
	
	function search( $args = array(), &$items_count = 0 ){
		if(!is_array($args)) $args = array('needle' => $args);
		
		$default = array( 
		  'needle'		=> '', 		 		// search needle
		  'forumids' 	=> array(), 		// array( 2, 10, 25 )
		  'date_period'	=> 0,				// topic id in DB
		  'type'		=> 'entire-posts',	// search type ( entire-posts | titles-only | user-posts | user-topics )
		  'orderby'		=> 'relevancy', 	// Sort Search Results by ( relevancy | date | user | forum )
		  'order'		=> 'DESC', 			// Sort Search Results ( ASC | DESC )
		  'offset' 		=> NULL,			// this use when you give row_count
		  'row_count'	=> NULL 			// 4 or 1 ...
		);
		
		$args = wpforo_parse_args( $args, $default );
		
		if( !empty($args) ){
			extract($args, EXTR_OVERWRITE);
			
			$date_period = intval($date_period);
			
			$selects = array('p.`postid`', 't.`topicid`', 't.`private`', 't.`status`', 't.`forumid`', 'p.`userid`', 't.`title`', 'p.`created`', 'p.`body`' );
			$innerjoins = array('INNER JOIN `'.$this->wpforo->db->prefix.'wpforo_topics` t ON t.`topicid` = p.`topicid`');
			$wheres = array();
			$orders = array();
			
			if(!empty($forumids)) $wheres[] = "t.`forumid` IN(" . implode(', ', array_map('intval', $forumids)) . ")";
			if( $date_period != 0 ){
				$date = date( 'Y-m-d H:i:s', current_time( 'timestamp', 1 ) - ($date_period * 24 * 60 * 60) );
				if($date) $wheres[] = "p.`created` > '".esc_sql($date)."'";
			}
			
			if($needle){
				
				$needle = trim( trim( str_replace(' ', '* ', $needle) ), '*' ) . "*";
				$needle = esc_sql(substr(sanitize_text_field($needle), 0, 60));
				
				if($type == 'entire-posts'){
					$selects[] = "MATCH(t.`title`) AGAINST('$needle' IN BOOLEAN MODE) + MATCH(p.`title`) AGAINST('$needle' IN BOOLEAN MODE) + MATCH(p.`body`) AGAINST('$needle' IN BOOLEAN MODE) AS matches";
					$wheres[] = "( MATCH(t.`title`) AGAINST('$needle' IN BOOLEAN MODE) OR MATCH(p.`title`, p.`body`) AGAINST('$needle' IN BOOLEAN MODE) )";
					$orders[] = "MATCH(t.`title`) AGAINST('$needle') + MATCH(p.`title`) AGAINST('$needle') + MATCH(p.`body`) AGAINST('$needle')";
					$orders[] = "MATCH(t.`title`) AGAINST('$needle' IN BOOLEAN MODE) + MATCH(p.`title`) AGAINST('$needle' IN BOOLEAN MODE) + MATCH(p.`body`) AGAINST('$needle' IN BOOLEAN MODE)";
				}elseif($type == 'titles-only'){
					$selects[] = "MATCH(t.`title`) AGAINST('$needle' IN BOOLEAN MODE) + MATCH(p.`title`) AGAINST('$needle' IN BOOLEAN MODE) AS matches";
					$wheres[] = "( MATCH(t.`title`) AGAINST('$needle' IN BOOLEAN MODE) OR MATCH(p.`title`) AGAINST('$needle' IN BOOLEAN MODE) )";
					$orders[] = "MATCH(t.`title`) AGAINST('$needle') + MATCH(p.`title`) AGAINST('$needle')";
					$orders[] = "MATCH(t.`title`) AGAINST('$needle' IN BOOLEAN MODE) + MATCH(p.`title`) AGAINST('$needle' IN BOOLEAN MODE)";
				}elseif($type == 'user-posts' || $type == 'user-topics'){
					$innerjoins[] = "INNER JOIN `".$this->wpforo->db->base_prefix."users` u ON u.`ID` = p.`userid`";
					$wheres[] = "( u.`user_login` LIKE '$needle' OR u.`user_email` LIKE '$needle' OR u.`display_name` LIKE '$needle' )";
					if($type == 'user-topics') $wheres[] = "`is_first_post` = 1";
				}
			}
			
			if($orderby == 'date'){
				$orders = array('p.`created`');
			}elseif($orderby == 'user'){
				$orders = array('p.`userid`');
			}elseif($orderby == 'forum'){
				$orders = array('t.`forumid`');
			}
			
			$sql = "SELECT COUNT(p.`postid`) FROM `".$this->wpforo->db->prefix."wpforo_posts` p ".implode(' ', $innerjoins);
			if(!empty($wheres)) $sql .= " WHERE " . implode( " AND ", $wheres );
			$items_count = $this->wpforo->db->get_var($sql);
			
			$sql = "SELECT ".implode(', ', $selects)." FROM `".$this->wpforo->db->prefix."wpforo_posts` p ".implode(' ', $innerjoins);
			if(!empty($wheres)) $sql .= " WHERE " . implode( " AND ", $wheres );
			if(!empty($orders)) $sql .= " ORDER BY ".implode(' '.strtoupper($order).', ', $orders)." ".strtoupper($order);
			
			if($row_count != NULL){
				if($offset != NULL){
					$sql .= esc_sql(" LIMIT $offset,$row_count");
				}else{
					$sql .= esc_sql(" LIMIT $row_count");
				}
			}

			$posts = $this->wpforo->db->get_results($sql, ARRAY_A);
			foreach($posts as $key => $post){
				if( !$this->wpforo->perm->forum_can( 'vf', $post['forumid'] ) ) unset($posts[$key]);
				if( $post['private'] && !$this->wpforo->perm->forum_can( 'vp', $post['forumid'] ) ) unset($posts[$key]);
				if( $post['status'] && !$this->wpforo->perm->forum_can( 'au', $post['forumid'] ) ) unset($posts[$key]);
			}
			return $posts;
		}else{
			return array();
		}
	}
	
	/**
	 *  return likes count by post id
	 * 
	 * Return likes count 
	 *
	 * @since 1.0.0
	 *
	 * @param	int 
	 *
	 * @return	int
	 */
	function get_post_likes_count($postid){
		return $this->wpforo->db->get_var("SELECT COUNT(l.`likeid`) FROM `".$this->wpforo->db->prefix."wpforo_likes` l, `".$this->wpforo->db->base_prefix."users` u WHERE `l`.`userid` = `u`.ID AND `l`.`postid` = ".intval($postid) );
	}
	
	/**
	 *  return usernames who likes this post
	 * 
	 * Return array with username
	 *
	 * @since 1.0.0
	 *
	 * @param	int
	 *
	 * @return	array
	 */
	function get_likers_usernames($postid){
		return $this->wpforo->db->get_results("SELECT u.ID, u.display_name FROM `".$this->wpforo->db->prefix."wpforo_likes` l, `".$this->wpforo->db->base_prefix."users` u WHERE `l`.`userid` = `u`.ID AND `l`.`postid` = ".intval($postid)." ORDER BY l.`userid` = " . intval($this->wpforo->current_userid) . " DESC, l.`likeid` DESC LIMIT 3", ARRAY_A);
	}
	
	/**
	 *  return like ID or null
	 * 
	 * @since 1.0.0
	 *
	 * @param	int int
	 *
	 * @return null or like id
	 */
	function is_liked($postid, $userid){
		$returned_value = $this->wpforo->db->get_var("SELECT likeid FROM `".$this->wpforo->db->prefix."wpforo_likes` WHERE `postid` = ".intval($postid)." AND `userid` = ".intval($userid) );
		if(is_null($returned_value)){
			return FALSE;	
		}else{
			return $returned_value;
		}
	}
	
	/**
	 *  return votes sum by post id
	 * 
	 * Return votes count 
	 *
	 * @since 1.0.0
	 *
	 * @param	int 
	 *
	 * @return	int
	 */
	function get_post_votes_sum($postid){
		$sum = $this->wpforo->db->get_var("SELECT sum(`reaction`) FROM `".$this->wpforo->db->prefix."wpforo_votes` WHERE `postid` = ".intval($postid) );
		if($sum == null){
			$sum = 0;
		}
		return $sum;
	}
	
	
	/**
	 *  return forum slug
	 * 
	 * string (slug)
	 *
	 * @since 1.0.0
	 *
	 * @param	int
	 *
	 * @return	string or false
	 */
	 
	function get_forumslug_byid($postid){
		
		$cache = $this->wpforo->cache->on('memory_cashe');
		
		if( $cache && isset(self::$cache['forum_slug'][$postid]) ){
			return self::$cache['forum_slug'][$postid];
		}
		
		$slug = $this->wpforo->db->get_var("SELECT `slug` FROM ".$this->wpforo->db->prefix."wpforo_forums WHERE `forumid` =(SELECT forumid FROM `".$this->wpforo->db->prefix."wpforo_topics` WHERE `topicid` =(SELECT `topicid` FROM `".$this->wpforo->db->prefix."wpforo_posts` WHERE postid = ".intval($postid)."))");
		
		if($cache && isset($postid)){
			self::$cache['forum_slug'][$postid] = $slug;
		}
		
		if($slug){
			return $slug;
		}else{
			return FALSE;
		}
	}
	
	
	/**
	 *  return topic slug
	 * 
	 * string (slug)
	 *
	 * @since 1.0.0
	 *
	 * @param	int
	 *
	 * @return	string or false
	 */
	 
	function get_topicslug_byid( $postid ){
		
		$cache = $this->wpforo->cache->on('memory_cashe');
		
		if( $cache && isset(self::$cache['topic_slug'][$postid]) ){
			return self::$cache['topic_slug'][$postid];
		}
		
		$slug = $this->wpforo->db->get_var("SELECT `slug` FROM ".$this->wpforo->db->prefix."wpforo_topics WHERE `topicid` =(SELECT `topicid` FROM `".$this->wpforo->db->prefix."wpforo_posts` WHERE postid = ".intval($postid).")");
		
		if($cache && isset($postid)){
			self::$cache['topic_slug'][$postid] = $slug;
		}
		
		if($slug){
			return $slug;
		}else{
			return FALSE;
		}
	}
	
	/**
	* return post full url by id
	* 
	* @since 1.0.0
	* 
	* @param int $postid
	* 
	* @return string $url
	*/
	function get_post_url( $arg, $absolute = true ){
		
		$position = array();
		
		if( isset($arg) && !is_array($arg) ){
			$postid = intval($arg);
			$post = $this->get_post($postid);
		}
		elseif( !empty($arg) && isset($arg['postid']) ){
			$post = $arg;
			$postid = $post['postid'];
		}
		
		if( is_array($post) && !empty($post) && $postid ){
			$url = $this->get_forumslug_byid($postid) . '/' . $this->get_topicslug_byid($postid);
			if( $post['topicid'] ){
				if( !$position ) $position = $this->wpforo->db->get_var("SELECT COUNT(`postid`) FROM `".$this->wpforo->db->prefix."wpforo_posts` WHERE `topicid` = ".intval($post['topicid'])." AND `postid` <= " . ($post['parentid'] ? intval($post['parentid']) : intval($postid) ) . " ORDER BY `postid`");
				if( $position <= $this->wpforo->post_options['posts_per_page'] ) return wpforo_home_url($url, false, $absolute ) . "#post-" . intval($postid);
				$paged = ceil( $position/$this->wpforo->post_options['posts_per_page'] );
				return wpforo_home_url( $url . "/paged/" . $paged, false, $absolute ) ."#post-" . intval($postid);
			}
		}
		
		return wpforo_home_url();
	}
	
	
	/**
	* return 0 or 1 
	* 
	* @since 1.0.0
	* 
	* @param int $postid
	*/
	function is_answered( $postid ){
		$is_answered =  $this->wpforo->db->get_var( $this->wpforo->db->prepare( 
			" SELECT is_answer 
				FROM `".$this->wpforo->db->prefix."wpforo_posts`
				WHERE postid = %d
			", 
			intval($postid)
		) );
		return $is_answered;
	}
	
	function get_count( $args = array() ){
		$sql = "SELECT COUNT(`postid`) FROM `".$this->wpforo->db->prefix."wpforo_posts`";
		if( !empty($args) ){
			$wheres = array();
			foreach ($args as $key => $value)  $wheres[] = "`$key` = " . intval($value);
			if($wheres) $sql .= " WHERE " . implode(' AND ', $wheres);
		}
		return $this->wpforo->db->get_var($sql);
	}
	
	function unapproved_count(){
		return $this->wpforo->db->get_var( "SELECT COUNT(*) FROM `".$this->wpforo->db->prefix."wpforo_posts` WHERE `status` = 1" );
	}
	
	function get_attachment_id( $filename ){
		$attach_id =  $this->wpforo->db->get_var( "SELECT `post_id` FROM `".$this->wpforo->db->prefix."postmeta` WHERE `meta_key` = '_wp_attached_file' AND `meta_value` LIKE '%" . esc_sql($filename) . "' LIMIT 1");
		return $attach_id;
	}
	
	function delete_attachments( $postid ){
		$post = $this->get_post($postid);
		if( isset($post['body']) && $post['body'] ){
			if( preg_match_all('|\/wpforo\/default_attachments\/([^\s\"\]]+)|is', $post['body'], $attachments, PREG_SET_ORDER) ){
				$upload_dir = wp_upload_dir();
                $default_attachments_dir = $upload_dir['basedir'] . '/wpforo/default_attachments/';
				foreach( $attachments as $attachment ){
					$filename = trim($attachment[1]);
					$file = $default_attachments_dir . $filename;
					if( file_exists($file) ){
						$posts = $this->wpforo->db->get_var( "SELECT COUNT(*) as posts FROM `".$this->wpforo->db->prefix."wpforo_posts` WHERE `body` LIKE '%" . esc_sql( $attachment[0] ) . "%'" );
						if( is_numeric($posts) && $posts == 1 ){
							$attachmentid = $this->get_attachment_id( '/' . $filename  );
							if ( !wp_delete_attachment( $attachmentid ) ){
								@unlink($file); 
							}
						}
					}
				}
			}
		}
	}

	public function status( $postid, $status ){
        if( !$postid = wpforo_bigintval($postid) ) return false;
        if( !$post = $this->get_post($postid) ) return false;

        if( $post['is_first_post'] ) return $this->wpforo->topic->status($post['topicid'], $status);

        if( false !== $this->wpforo->db->update(
            $this->wpforo->db->prefix."wpforo_posts",
            array( 'status' => intval($status) ),
            array( 'postid' => $postid ),
            array( '%d' ),
            array( '%d' )
        )){
            $this->wpforo->notice->add('Done!', 'success');
            return true;
        }

        $this->wpforo->notice->add('error: Change Status action', 'error');
        return false;
    }
}
?>