<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
 

class wpForoSubscribe{
	
	private $wpforo;
	static $cache = array( 'subscribe' => array() );
	
	function __construct( $wpForo ){
		if(!isset($this->wpforo)) $this->wpforo = $wpForo;
	}
 	
 	function get_confirm_key(){
		return substr(md5(rand().time()), 0, 32);
	}
 	
	function add( $args = array() ){
		if( empty($args) && empty($_REQUEST['sbscrb']) ) return FALSE;
		if( empty($args) && !empty($_REQUEST['sbscrb']) ) $args = $_REQUEST['sbscrb']; 
		if( !isset($args['active']) || !$args['active'] ) $args['active'] = 0;
		
		extract( $args, EXTR_OVERWRITE );
		if( !isset($itemid) || !$itemid || !isset($userid) || !$userid || !isset($type) || !$type ) return FALSE;
		
		if( !isset($confirmkey) || (isset($confirmkey) && !$confirmkey ) ) $confirmkey = $this->get_confirm_key();
		
		if($this->wpforo->db->insert( 
			$this->wpforo->db->prefix . 'wpforo_subscribes', 
			array( 
				'itemid' => intval($itemid),
				'type' => sanitize_text_field($type),
				'confirmkey' => sanitize_text_field($confirmkey), 
				'userid' => intval($userid),
				'active' => $active
			), 
			array( 
				'%d',
				'%s', 
				'%s', 
				'%d',
				'%d'
			)
		)){
			if( isset($active) && $active == 1 ){
				$this->wpforo->notice->add('You have been successfully subscribed', 'success');
			}else{
				$this->wpforo->notice->add('Success! Thank you. Please check your email and click confirmation link below to complete this step.', 'success');
			}
			return $confirmkey;
		}
		
		$this->wpforo->notice->add('Can\'t subscribe to this item', 'error');
		return FALSE;
	}
	
	function edit( $confirmkey = '' ){
		if( !$confirmkey && isset($_REQUEST['key']) && $_REQUEST['key'] ) $confirmkey = $_REQUEST['key']; 
		if( !$confirmkey ){
			$this->wpforo->notice->add('Invalid request!', 'error');
			return FALSE;
		}
		
		if( $this->wpforo->db->update( 
			$this->wpforo->db->prefix . 'wpforo_subscribes', 
			array( 'active' => 1 ), 
			array( 'confirmkey' => sanitize_text_field($confirmkey) ),
			array( '%d' ),
			array( '%s' )
		) ){
			$this->wpforo->notice->add('You have been successfully subscribed', 'success');
			return TRUE;
		}
		
		$this->wpforo->notice->add('Your subscription for this item could not be confirmed', 'error');
		return FALSE;
	}
	
	function delete( $confirmkey = '' ){
		if( !$confirmkey && isset($_REQUEST['confirmkey']) && $_REQUEST['confirmkey'] ) $confirmkey = $_REQUEST['confirmkey'];
		if( !$confirmkey ){
			$this->wpforo->notice->add('Invalid request!', 'error');
			return FALSE;
		}
		if( $this->wpforo->db->delete( $this->wpforo->db->prefix.'wpforo_subscribes', array( 'confirmkey' => sanitize_text_field($confirmkey) ), array( '%s' ) ) ){
			$this->wpforo->notice->add('You have been successfully unsubscribed', 'success');
			return TRUE;
		}
		
		$this->wpforo->notice->add('Could not be unsubscribe from this item', 'error');
		return FALSE;
	}
	
	function get_subscribe( $args = array() ){
		
		$cache = $this->wpforo->cache->on('memory_cashe');
		
		if( is_string($args) ) $args = array("confirmkey" => sanitize_text_field($args));
		if( empty($args) && !empty($_REQUEST['sbscrb']) ) $args = $_REQUEST['sbscrb']; 
		if( empty($args) ) return FALSE;
		extract( $args, EXTR_OVERWRITE );
		if( (!isset($itemid) || !$itemid || !isset($userid) || !$userid || !isset($type) || !$type) && (!isset($confirmkey) || !$confirmkey) ) return FALSE;
		if( isset($confirmkey) && $confirmkey){
			$where = " `confirmkey` = '".esc_sql(sanitize_text_field($confirmkey))."'";
		}elseif( isset($itemid) && $itemid && isset($userid) && $userid && isset($type) && $type ){
			$where = " `itemid` = ".intval($itemid)." AND `userid` = ".intval($userid)." AND `type` = '".esc_sql(sanitize_text_field($type))."'";
		}else{
			return FALSE;
		}
		if( $cache && isset(self::$cache['subscribe'][$itemid][$userid][$type]) ){
			return self::$cache['subscribe'][$itemid][$userid][$type];
		}
		$sql = "SELECT * FROM `".$this->wpforo->db->prefix."wpforo_subscribes` WHERE " . $where;
		$subscribe = $this->wpforo->db->get_row($sql, ARRAY_A);
		if($cache && !empty($subscribe)){
			self::$cache['subscribe'][$itemid][$userid][$type] = $subscribe;
		}
		return $subscribe;
	}
	
	function get_subscribes( $args = array(), &$items_count = 0 ){
		
		$default = array( 
		  'itemid' => NULL,
		  'type' => '',  // topic | forum
		  'userid' => NULL, //
		  'active' => 1,
		  'orderby' => 'subid', // order by `field`
		  'order' => 'DESC', // ASC DESC
		  'offset' => NULL, // OFFSET
		  'row_count' => NULL, // ROW COUNT
		);
		
		$args = wpforo_parse_args( $args, $default );
		if(!empty($args)){
			extract($args, EXTR_OVERWRITE);
			
			$sql = "SELECT * FROM `".$this->wpforo->db->prefix."wpforo_subscribes`";
			$wheres = array();
			
			if( $type ) $wheres[] = " `type` = '" . esc_sql(sanitize_text_field($type)) . "'";
			$wheres[] = " `active` = "   . intval($active);
			if($itemid != NULL)   $wheres[] = " `itemid` = "   . intval($itemid);
			if($userid != NULL)   $wheres[] = " `userid` = "   . intval($userid);
			
			if(!empty($wheres)) $sql .= " WHERE " . implode( " AND ", $wheres );
			
			$item_count_sql = preg_replace('#SELECT.+?FROM#isu', 'SELECT count(*) FROM', $sql);
			if( $item_count_sql ) $items_count = $this->wpforo->db->get_var($item_count_sql);
			
			$sql .= " ORDER BY `$orderby` " . $order;
			
			if($row_count != NULL){
				if($offset != NULL){
					$sql .= esc_sql(" LIMIT $offset,$row_count");
				}else{
					$sql .= esc_sql(" LIMIT $row_count");
				}
			}
			return $this->wpforo->db->get_results($sql, ARRAY_A);
			
		}
	}
	
	function get_confirm_link($args){
		if(is_string($args)) return wpforo_home_url( "?wpforo=sbscrbconfirm&key=" . sanitize_text_field($args) );
		
		if($args['type'] == 'forum'){
			$url = $this->wpforo->forum->get_forum_url($args['itemid']) . '/';
		}elseif($args['type'] == 'topic'){
			$url = $this->wpforo->topic->get_topic_url($args['itemid']) . '/';
		}else{
			$url = wpforo_home_url();
		}
		return wpforo_home_url( $url . "?wpforo=sbscrbconfirm&key=" . sanitize_text_field($args['confirmkey']) );
	}
	
	function get_unsubscribe_link($confirmkey){
		return wpforo_home_url( "?wpforo=unsbscrb&key=" . sanitize_text_field($confirmkey) );
	}
	
}