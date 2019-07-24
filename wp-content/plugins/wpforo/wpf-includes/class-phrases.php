<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
 
class wpForoPhrase{
	
	private $wpforo;
	
	function __construct( $wpForo ){
		if(!isset($this->wpforo)) $this->wpforo = $wpForo;
	}
	
	function add( $args = array() ){
		if( empty($args) && empty($_REQUEST['phrase']) ) return FALSE;
		if( empty($args) && !empty($_REQUEST['phrase']) ) $args = $_REQUEST['phrase'];
		extract($args);
		
		if( empty($package) ) $package = 'wpforo';
		$sql = $this->wpforo->db->prepare( "INSERT IGNORE INTO `".$this->wpforo->db->prefix."wpforo_phrases` 
												(`langid`, `phrase_key`, `phrase_value`, `package`) 
													VALUES (%d, %s, %s, %s)", 
														$this->wpforo->general_options['lang'], 
														stripslashes(esc_html($key)), 
														stripslashes(esc_html($value)), 
														stripslashes(esc_html($package)) );
		if($this->wpforo->db->query( $sql )){
			$this->wpforo->notice->add('Phrase successfully added', 'success');
			$this->clear_cache();
			return $this->wpforo->db->insert_id;
		}
		$this->wpforo->notice->add('Phrase add error', 'error');
		return FALSE;
	}
	
	function edit(){
		if( !empty($_POST['phrase']['data']) && is_array($_POST['phrase']['data']) ){
			foreach($_POST['phrase']['data'] as $key => $phrase){
				$this->wpforo->db->update( 
					$this->wpforo->db->prefix . 'wpforo_phrases',
					array( 'phrase_value' => sanitize_text_field(stripslashes($phrase['title']))), 
					array( 'phraseid' => intval($key) ), 
					array( '%s' ),
					array( '%d' ) 
				);
				
			}
			$this->clear_cache();
			$this->wpforo->notice->add('Phrase successfully updates', 'success');
			return TRUE;
		}
		
		$this->wpforo->notice->add('Phrase update error', 'error');
		return FALSE;
	}
	
	function get_wpforo_phrase($phraseid){
		$sql = 'SELECT * FROM '.$this->wpforo->db->prefix.'wpforo_phrases WHERE `phraseid` ='.intval($phraseid);
		return $this->wpforo->db->get_row($sql, ARRAY_A);
	}
	
	function get_phrases($args = array(), &$items_count = 0){
		$default = array( 
		  'include' => array(), 		// array( 2, 10, 25 )
		  'exclude' => array(),  		// array( 2, 10, 25 )
		  'langid' => $this->wpforo->general_options['lang'],
		  'package' => array(),
		  
		  'orderby'		=> 'phraseid', 
		  'order'		=> 'ASC', 		// ASC DESC
		  'offset' 		=> '',			// this use when you give row_count
		  'row_count'	=> '' 	
		);
		
		$args = wpforo_parse_args( $args, $default );
		if(is_array($args) && !empty($args)){
			
			$key = substr(md5(serialize($args)), 0, 10);
			
			extract($args, EXTR_OVERWRITE);
			
			$package = wpforo_parse_args( $package );
			$include = wpforo_parse_args( $include );
			$exclude = wpforo_parse_args( $exclude );
			
			$wheres = array();
			
			if(!empty($package))        $wheres[] = "`package` IN('" . implode("','", array_map('esc_sql', array_map('sanitize_text_field', $package))  ) . "')";
			if(!empty($include))        $wheres[] = "`phraseid` IN(" . implode(', ', array_map('intval', $include)) . ")";
			if(!empty($exclude))        $wheres[] = "`phraseid` NOT IN(" . implode(', ', array_map('intval', $exclude)) . ")";
			if($langid != NULL) $wheres[] = "`langid` = " . intval($langid);
			
			$sql = "SELECT * FROM `".$this->wpforo->db->prefix."wpforo_phrases`";
			if(!empty($wheres)){
				$sql .= " WHERE " . implode($wheres, " AND ");
			}
			
			$item_count_sql = preg_replace('#SELECT.+?FROM#isu', 'SELECT count(*) FROM', $sql);
			if( $item_count_sql ) $items_count = $this->wpforo->db->get_var($item_count_sql);
			
			$sql .= esc_sql(" ORDER BY `$orderby` " . $order);
			
			if($row_count != '' && $offset == ''){  // If you give only row_count this if fixed problam
				$offset = $row_count;
				$row_count  = '';
			}
			$sql .= $offset != '' ? esc_sql(' LIMIT '.$offset) : '';
			$sql .= $row_count != '' ? esc_sql(', '.$row_count) : '';
			
			if ( false === ( $phrases = get_transient( 'wpforo_get_phrases_' . $key ) ) ) {
				$phrases = $this->wpforo->db->get_results($sql, ARRAY_A);
				set_transient( 'wpforo_get_phrases_' . $key , $phrases, 60*60*24 );
			}
			return get_transient( 'wpforo_get_phrases_' . $key );
		}
	}
	
	function search( $needle = '', $fields = array( 'phrase_key', 'phrase_value' )){
		if( !$needle ) return array();
		$phreseids = array();
		if(!is_array($fields)) $fields = array($fields);
		$needle = substr(sanitize_text_field($needle), 0, 60);
		foreach($fields as $field){
			$field = sanitize_text_field($field);
			$matches = $this->wpforo->db->get_col( "SELECT `phraseid` FROM ".$this->wpforo->db->prefix."wpforo_phrases WHERE `".esc_sql($field)."` LIKE '%".esc_sql($needle)."%'" );	
			$phreseids = array_merge( $phreseids, $matches );
		}
		return array_unique($phreseids);
	}
	
	public function xml_import($xmlfile, $type = 'import'){
		$file = WPFORO_DIR . '/wpf-admin/xml/' . $xmlfile;
		if( file_exists( $file ) ) {
			$xr = xml_parser_create();
			$fp = fopen($file, "r");
			$xml = fread($fp, filesize($file));
			
			xml_parser_set_option( $xr, XML_OPTION_CASE_FOLDING, 1 );
			xml_parse_into_struct( $xr, $xml, $vals, $index );
			xml_parser_free( $xr );
			
			delete_transient( 'wpforo_get_phrases' );
			
			if(!empty($vals)){
				
				if( isset($vals[0]['tag']) && $vals[0]['tag'] == 'LANGUAGE' && isset($vals[0]['attributes']['LANGUAGE']) && $vals[0]['attributes']['LANGUAGE'] ){
					
					$sql = "SELECT `langid` FROM `".$this->wpforo->db->prefix."wpforo_languages` WHERE `name` LIKE '". esc_sql(sanitize_text_field($vals[0]['attributes']['LANGUAGE'])) ."'";
					$langid = $this->wpforo->db->get_var( $sql );
					
					if( !$langid ){
						$sql = "INSERT INTO `".$this->wpforo->db->prefix."wpforo_languages` (`name`) VALUES ( '".esc_sql(sanitize_text_field($vals[0]['attributes']['LANGUAGE']))."' )";
						if( $this->wpforo->db->query($sql) ){
							$langid = $this->wpforo->db->insert_id;
						}
					}
					
					if( $langid ){
						foreach($vals as $val){
							if( isset($val['tag']) && $val['tag'] == 'PHRASE' && isset($val['attributes']['NAME']) && trim($val['attributes']['NAME']) && isset($val['value']) && trim($val['value']) ){
								$sql = "INSERT IGNORE INTO `".$this->wpforo->db->prefix."wpforo_phrases` 
									(`phraseid`, `langid`, `phrase_key`, `phrase_value`)
									VALUES( NULL, 
									  '".esc_sql(trim($langid))."', 
									  '".esc_sql(trim($val['attributes']['NAME']))."', 
									  '".esc_sql(trim($val['value']))."')";
								$this->wpforo->db->query($sql);
							}
						}
						if( !isset($this->wpforo->general_options['lang']) ){
							$blogname = get_option('blogname');
							$general_options = array(
								'title' => $blogname .  __(' Forum', 'wpforo'),
								'description' => $blogname . __(' Discussion Board', 'wpforo'),
								'lang' => sanitize_text_field($langid),
							);
						}else{
							$general_options = $this->wpforo->general_options;
							$general_options['lang'] = sanitize_text_field($langid);
						}
						if( $type == 'install' ){
							add_option('wpforo_general_options', $general_options);
						}
						else{
							update_option('wpforo_general_options', $general_options);
						}
						return $langid;
					}
				}
			}
		}
		
		return FALSE;
	}
	
	function add_lang(){
		if( is_array($_FILES['add_lang']['name']) && !empty($_FILES['add_lang']['name']) && isset($_FILES['add_lang']['name']['xml']) ){
			if(!is_dir( WPFORO_DIR . '/wpf-admin/xml' )) wp_mkdir_p( WPFORO_DIR . '/wpf-admin/xml' );
			
			$error = $_FILES['add_lang']['error']['xml'];
			
			if( $error ){
				$error = wpforo_file_upload_error($error);
				$this->wpforo->notice->add($error, 'error');
				return FALSE;
			}
			
			$xmlfile = strtolower(sanitize_file_name($_FILES['add_lang']['name']['xml']));
			if( move_uploaded_file(sanitize_text_field($_FILES['add_lang']['tmp_name']['xml']),  WPFORO_DIR . '/wpf-admin/xml/' . $xmlfile) ){
				if($langid = $this->xml_import($xmlfile) ){
					delete_transient( 'wpforo_get_phrases' );
					$this->wpforo->notice->add('New language successfully added and changed  wpforo language to new language', 'success');
					return $langid;
				}
			}
		}
		
		$this->wpforo->notice->add('Can\'t add new language', 'error');
		return FALSE;
	}
	
	function get_languages(){
		return $this->wpforo->db->get_results( "SELECT * FROM `".$this->wpforo->db->prefix."wpforo_languages`", ARRAY_A );
	}
	
	function show_lang_list(){
		$langs = $this->get_languages();
		if(!empty($langs)){
			foreach($langs as $lang) : 
				extract($lang, EXTR_OVERWRITE); ?>
				<option value="<?php echo esc_attr($langid) ?>"<?php if($langid == $this->wpforo->general_options['lang']) echo ' selected' ?>><?php echo esc_html($name) ?></option>
				<?php 
			endforeach;
		}
	}
	
	function clear_cache(){
		$this->wpforo->db->query("DELETE FROM " . $this->wpforo->db->prefix . "options WHERE `option_name` LIKE '%_wpforo_get_phrases_%'");
	}
	
}

?>