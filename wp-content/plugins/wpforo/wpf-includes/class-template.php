<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;

define('WPFORO_THEME_DIR', WPFORO_DIR . '/wpf-themes' );
define('WPFORO_THEME_URL', WPFORO_URL . '/wpf-themes' );

class wpForoTemplate{
	public $default;
	public $options;
	public $forms;
	public $style;
	public $theme;
	public $slugs;
	
	function __construct(){
		$this->init_defaults();
		$this->init_options();
	}

	public function init(){
        $this->init_hooks();
        $this->init_member_templates();
        $this->init_nav_menu();
    }

	private function init_hooks(){
        if( is_wpforo_page() ){
            add_filter("mce_external_plugins", array($this, 'add_tinymce_buttons'), 15);
            add_filter("tiny_mce_plugins", array($this, 'filter_tinymce_plugins'), 15);
            add_filter("wp_mce_translation", array($this, 'add_tinymce_translations'));
            add_filter("wpforo_editor_settings", array($this, 'editor_settings_required_params'), 999);

            add_action('wpforo_topic_form_extra_fields_after', array($this, 'add_default_attach_input'));
            add_action('wpforo_reply_form_extra_fields_after', array($this, 'add_default_attach_input'), 10, 1);
            add_action('wpforo_portable_form_extra_fields_after', array($this, 'add_default_attach_input'));

	        add_filter( 'wpforo_content_after', array( $this, 'do_spoilers' ) );

            add_action('wp_footer', array($this, 'add_footer_html'), 999999, 0);

            //ajax actions hooks
            add_action('wp_ajax_wpforo_active_tab_content_ajax', array($this, 'ajx_active_tab_content'));
        }
    }

	private function init_defaults(){
        $this->default = new stdClass;

        $this->default->slugs = array(
            'paged' => 'paged',
            'recent' => 'recent',
            'tags' => 'tags',
            'members' => 'members',
            'profile' => 'profile',
            'account' => 'account',
            'activity' => 'activity',
            'subscriptions' => 'subscriptions'
        );

        $this->default->style = array(
            'font_size_forum' => 17,
            'font_size_topic' => 16,
            'font_size_post_content' => 14,
            'custom_css' => "#wpforo-wrap {\r\n   font-size: 13px; width: 100%; padding:10px 0; margin:0px;\r\n}\r\n"
        );

		$this->default->forms = array(
			'qa_comments_rich_editor'    => 0,
			'threaded_reply_rich_editor' => 1,
            'qa_display_answer_editor' => 1
		);

        $theme = get_option('wpforo_theme_options');
        if(empty($theme)) $theme = $this->find_theme( 'classic' );
        $this->default->options = $theme;
    }

    private function init_options(){
        $this->slugs = get_wpf_option('wpforo_tpl_slugs', $this->default->slugs);
        $this->style = get_wpf_option('wpforo_style_options', $this->default->style);

        $this->options = get_wpf_option('wpforo_theme_options', $this->default->options);
        $this->theme = $this->options['folder'];

        $this->forms = get_wpf_option('wpforo_form_options', $this->default->forms);

        $this->init_defines();
    }

    private function init_defines(){
        define('WPFORO_THEME', $this->theme );
        define('WPFORO_TEMPLATE_DIR', wpforo_fix_directory(WPFORO_THEME_DIR . '/' . $this->theme) );
        define('WPFORO_TEMPLATE_URL', WPFORO_THEME_URL . '/' . $this->theme );
    }

	function add_tinymce_buttons($plugin_array) {
	  $plugin_array = array();
	  $plugin_array['wpforo_pre_button'] = WPFORO_URL . '/wpf-assets/js/tinymce-pre.js';
	  $plugin_array['wpforo_link_button'] = WPFORO_URL . '/wpf-assets/js/tinymce-link.js';
	  $plugin_array['wpforo_spoiler_button'] = WPFORO_URL . '/wpf-assets/js/tinymce-spoiler.js';
	  $plugin_array['wpforo_source_code_button'] = WPFORO_URL . '/wpf-assets/js/tinymce-code.js';
	  $plugin_array['emoticons'] = WPFORO_URL . '/wpf-assets/js/tinymce-emoji.js';
	  return $plugin_array;
	}
	
	function filter_tinymce_plugins($plugins){
		return array('hr','lists','textcolor','paste', 'wpautoresize', 'fullscreen');
	}
	
	function add_tinymce_translations($mce_translation){
		$mce_translation['Insert link'] = __( 'Insert link' );
		$mce_translation['Link Text'] = __( 'Link Text' );
		$mce_translation['Open link in a new tab'] = __( 'Open link in a new tab' );
		$mce_translation['Insert Spoiler'] = __( 'Insert Spoiler', 'wpforo' );
		$mce_translation['Spoiler'] = __( 'Spoiler', 'wpforo' );
		return $mce_translation;
	}

	public function add_default_attach_input($forumid = null){
		if( WPF()->perm->can_attach($forumid) ){ ?>
            <div class="wpf-default-attachment">
                <label for="wpf_file"><?php wpforo_phrase('Attach file:') ?> </label> <input id="wpf_file" type="file" name="attachfile" />
                <p><?php wpforo_phrase('Maximum allowed file size is'); echo ' ' . wpforo_print_size(WPF()->post->options['max_upload_size']); ?></p>
                <div class="wpf-clear"></div>
            </div>
          <?php
		}
    }

    public function topic_form_forums_selectbox($forumid = null){
	    if( WPF()->forum->options['layout_threaded_add_topic_button'] && WPF()->perm->forum_can( 'ct', $forumid ) ){
            ?>
            <div class="wpf-topic-form-extra-wrap">
                <div class="wpf-topic-forum-field" style="padding: 0 10px 15px; text-align: center; margin: 0 auto 10px;">
                    <div class="wpf-choose-forum" style="font-size: 15px; padding-bottom: 5px;">
                        <?php wpforo_phrase('Select Forum') ?>
                    </div>
                    <div class="wpf-topic-forum-wrap" style="width: 70%; margin: 0 auto; padding-bottom: 15px; min-width: 222px; border-bottom: 1px dashed #cccccc;">
                        <select class="wpf-topic-form-forumid" style="width: 100%; max-width: 100%;">
                            <option class="wpf-topic-form-no-selected-forum" value="0">-- <?php wpforo_phrase('No forum selected'); ?> --</option>
                            <?php WPF()->forum->tree('select_box', false, array(), true, array(), $forumid); ?>
                        </select>
                    </div>
                </div>
                <div class="wpf-topic-form-ajax-wrap"></div>
            </div>
            <?php
	    }
    }
	
	function topic_form($forumid = null){
	    if( !$forumid ) $forumid = WPF()->current_object['forumid'];
		$forumid = intval($forumid);
		$layout = WPF()->forum->get_layout($forumid);
		$textareaid = uniqid('wpf_topic_body_');
        ?>
		<div class="wpf-topic-create">
			<form name="topic" action="" enctype="multipart/form-data" method="POST" class="wpforoeditor" data-textareaid="<?php echo $textareaid ?>" data-bodyminlength="<?php echo WPF()->post->options['topic_body_min_length'] ?>" data-bodymaxlength="<?php echo WPF()->post->options['topic_body_max_length'] ?>">
				<?php wp_nonce_field( 'wpforo_verify_form', 'wpforo_form' ); ?>
                <input type="hidden" name="topic[action]" value="add"/>
				<?php if(!is_user_logged_in()): ?>
                	<?php $guest = WPF()->member->get_guest_cookies(); ?>
                    <div class="wpf-topic-guest-fields">
                        <div class="wpf-topic-guest-name">
                            <label style="padding-left:8px;"> <?php wpforo_phrase('Author Name') ?> * </label>
                            <input id="wpf_user_name" type="text" placeholder="<?php esc_attr( wpforo_phrase('Your name') ) ?>" name="topic[name]" value="<?php echo esc_attr($guest['name']) ?>" />
                        </div>
                        <div class="wpf-topic-guest-email">
                            <label style="padding-left:8px;"> <?php wpforo_phrase('Author Email') ?> * </label>
                            <input id="wpf_user_email" type="text" placeholder="<?php esc_attr( wpforo_phrase('Your email') ) ?>" name="topic[email]" value="<?php echo esc_attr($guest['email']) ?>" />
                        </div>
                        <div class="wpf-clear"></div>
                    </div>
                <?php endif; ?>

                <input type="hidden" id="wpf_parent" name="topic[forumid]" value="<?php echo $forumid ?>" />

                <div class="wpf-topic-form-wrap">
                    <label class="wpf-subject-label" style="padding-left:8px;"> <?php $layout == 3 ? wpforo_phrase('Your question') : wpforo_phrase('Topic Title') ?> * </label>
                    <input id="wpf_title" class="wpf-subject" type="text" name="topic[title]" autocomplete="off" required autofocus placeholder="<?php esc_attr( wpforo_phrase('Enter title here') ) ?>">
                    <?php
                        $settings = $this->editor_buttons( 'topic' );
                        wp_editor( '', $textareaid, $settings );
                    ?>
                    <div class="wpf-extra-fields">
                       <?php do_action('wpforo_topic_form_extra_fields_before', $forumid) ?>
                       <div class="wpf-main-fields">
                            <?php if(WPF()->perm->forum_can('s', $forumid)) : ?>
                                <input id="wpf_t_sticky" name="topic[type]" type="checkbox" value="1">&nbsp;&nbsp;
                                <i class="fas fa-exclamation wpfsx"></i>&nbsp;&nbsp;<label for="wpf_t_sticky" style="padding-bottom:2px; cursor: pointer;"><?php wpforo_phrase('Set Topic Sticky'); ?>&nbsp;</label>
                                <span class="wpfbs">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                            <?php endif; ?>
                            <?php if(WPF()->perm->forum_can('p', $forumid) || WPF()->perm->forum_can('op', $forumid)) : ?>
                                <input id="wpf_t_private" name="topic[private]" type="checkbox" value="1">&nbsp;&nbsp;
                                <i class="fas fa-eye-slash wpfsx"></i>&nbsp;&nbsp;<label for="wpf_t_private" style="padding-bottom:2px; cursor: pointer;" title="<?php wpforo_phrase('Only Admins and Moderators can see your private topics.'); ?>"><?php wpforo_phrase('Private Topic'); ?>&nbsp;</label>
                            <?php endif; ?>
                            <?php do_action('wpforo_topic_form_buttons_hook', $forumid); ?>
                        </div>
                        <?php do_action('wpforo_topic_form_extra_fields_after', $forumid) ?>
                    </div>
                    <?php if( WPF()->post->options['tags'] && WPF()->perm->forum_can('tag', $forumid) ) : ?>
                        <div class="wpf-topic-tags">
                            <p class="wpf-topic-tags-label"><i class="fas fa-tag"></i> <?php $layout == 3 ? wpforo_phrase('Question Tags') : wpforo_phrase('Topic Tags') ?> <span>(<?php wpforo_phrase('Separate tags using a comma') ?>)</span></p>
                            <input id="wpf_tags" placeholder="<?php echo sprintf(wpforo_phrase('Start typing tags here (maximum %d tags are allowed)...', false), WPF()->post->options['max_tags']) ?>" name="topic[tags]" autocomplete="off" value="" type="text">
                            <style type="text/css">#wpforo-wrap .wpf-ac-loading {background-image: url('<?php echo WPFORO_URL ?>/wpf-assets/images/ajax_loading.gif');background-repeat: no-repeat;background-position: right center;visibility: visible;} </style>
                        </div>
                    <?php endif; ?>
                    <?php if( wpforo_feature('subscribe_checkbox_on_post_editor') && WPF()->perm->forum_can('sb', $forumid) ) : ?>
                        <div class="wpf-topic-sbs"><input id="wpf-topic-sbs" type="checkbox" name="wpforo_topic_subs" value="1" <?php echo ( wpforo_feature('subscribe_checkbox_default_status') ) ? 'checked="true" ' : ''; ?>/>&nbsp;<label for="wpf-topic-sbs"><?php $layout == 3 ? wpforo_phrase('Subscribe to this question') : wpforo_phrase('Subscribe to this topic') ?></label></div>
                    <?php endif; ?>
                    <?php do_action('wpforo_editor_topic_submit_before', $forumid) ?>
                    <input id="wpf_formbutton" type="submit" name="topic[save]" class="button button-primary forum_submit" value="<?php $layout == 3 ? wpforo_phrase('Ask a question') : wpforo_phrase('Add topic') ?>">
                    <?php do_action('wpforo_editor_topic_submit_after', $forumid) ?>
                    <div class="wpf-clear"></div>
                </div>
			</form>
		</div>
		
		<?php
	}

	function topic_form_portable($forumid = null){
		if( !$forumid ) $forumid = WPF()->current_object['forumid'];
		$textareaid = uniqid('wpf_topic_body_');
		$forumid = intval($forumid);
		$layout = WPF()->forum->get_layout($forumid);
		?>
        <div class="wpf-topic-create">
            <form name="topic" action="" enctype="multipart/form-data" method="POST" class="wpforoeditor" data-textareaid="<?php echo $textareaid ?>" data-bodyminlength="<?php echo WPF()->post->options['topic_body_min_length'] ?>" data-bodymaxlength="<?php echo WPF()->post->options['topic_body_max_length'] ?>">
				<?php wp_nonce_field( 'wpforo_verify_form', 'wpforo_form' ); ?>
                <input type="hidden" name="topic[action]" value="add"/>
				<?php if(!is_user_logged_in()): ?>
					<?php $guest = WPF()->member->get_guest_cookies(); ?>
                    <div class="wpf-topic-guest-fields">
                        <div class="wpf-topic-guest-name">
                            <label style="padding-left:8px;"> <?php wpforo_phrase('Author Name') ?> * </label>
                            <input id="wpf_user_name" type="text" placeholder="<?php esc_attr( wpforo_phrase('Your name') ) ?>" name="topic[name]" value="<?php echo esc_attr($guest['name']) ?>" />
                        </div>
                        <div class="wpf-topic-guest-email">
                            <label style="padding-left:8px;"> <?php wpforo_phrase('Author Email') ?> * </label>
                            <input id="wpf_user_email" type="text" placeholder="<?php esc_attr( wpforo_phrase('Your email') ) ?>" name="topic[email]" value="<?php echo esc_attr($guest['email']) ?>" />
                        </div>
                        <div class="wpf-clear"></div>
                    </div>
				<?php endif; ?>

                <input type="hidden" id="wpf_parent" name="topic[forumid]" value="<?php echo $forumid ?>" />

                <div class="wpf-topic-form-wrap">
                    <label class="wpf-subject-label" style="padding-left:8px;"> <?php $layout == 3 ? wpforo_phrase('Your question') : wpforo_phrase('Topic Title') ?> * </label>
                    <input id="wpf_title" class="wpf-subject" type="text" name="topic[title]" autocomplete="off" required autofocus placeholder="<?php esc_attr( wpforo_phrase('Enter title here') ) ?>">
                    <div class="wpf_topic_form_textarea_wrap" data-textareatype="rich_editor">
                        <textarea id="<?php echo $textareaid ?>" class="wpf_topic_body" name="topic[body]" style="resize: vertical; width: 100%;" rows="6"></textarea>
                    </div>
                    <div class="wpf-extra-fields">
						<?php do_action('wpforo_topic_form_extra_fields_before', $forumid) ?>
                        <div class="wpf-main-fields">
							<?php if(WPF()->perm->forum_can('s', $forumid)) : ?>
                                <input id="wpf_t_sticky" name="topic[type]" type="checkbox" value="1">&nbsp;&nbsp;
                                <i class="fas fa-exclamation wpfsx"></i>&nbsp;&nbsp;<label for="wpf_t_sticky" style="padding-bottom:2px; cursor: pointer;"><?php wpforo_phrase('Set Topic Sticky'); ?>&nbsp;</label>
                                <span class="wpfbs">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
							<?php endif; ?>
							<?php if(WPF()->perm->forum_can('p', $forumid) || WPF()->perm->forum_can('op', $forumid)) : ?>
                                <input id="wpf_t_private" name="topic[private]" type="checkbox" value="1">&nbsp;&nbsp;
                                <i class="fas fa-eye-slash wpfsx"></i>&nbsp;&nbsp;<label for="wpf_t_private" style="padding-bottom:2px; cursor: pointer;" title="<?php wpforo_phrase('Only Admins and Moderators can see your private topics.'); ?>"><?php wpforo_phrase('Private Topic'); ?>&nbsp;</label>
							<?php endif; ?>
							<?php do_action('wpforo_topic_form_buttons_hook', $forumid); ?>
                        </div>
						<?php do_action('wpforo_topic_form_extra_fields_after', $forumid) ?>
                    </div>
					<?php if( WPF()->post->options['tags'] && WPF()->perm->forum_can('tag', $forumid) ) : ?>
                        <div class="wpf-topic-tags">
                            <p class="wpf-topic-tags-label"><i class="fas fa-tag"></i> <?php $layout == 3 ? wpforo_phrase('Question Tags') : wpforo_phrase('Topic Tags') ?> <span>(<?php wpforo_phrase('Separate tags using a comma') ?>)</span></p>
                            <input id="wpf_tags" placeholder="<?php echo sprintf(wpforo_phrase('Start typing tags here (maximum %d tags are allowed)...', false), WPF()->post->options['max_tags']) ?>" name="topic[tags]" autocomplete="off" value="" type="text">
                            <style type="text/css">#wpforo-wrap .wpf-ac-loading {background-image: url('<?php echo WPFORO_URL ?>/wpf-assets/images/ajax_loading.gif');background-repeat: no-repeat;background-position: right center;visibility: visible;} </style>
                        </div>
					<?php endif; ?>
					<?php if( wpforo_feature('subscribe_checkbox_on_post_editor') && WPF()->perm->forum_can('sb', $forumid) ) : ?>
                        <div class="wpf-topic-sbs"><input id="wpf-topic-sbs" type="checkbox" name="wpforo_topic_subs" value="1" <?php echo ( wpforo_feature('subscribe_checkbox_default_status') ) ? 'checked="true" ' : ''; ?>/>&nbsp;<label for="wpf-topic-sbs"><?php $layout == 3 ? wpforo_phrase('Subscribe to this question') : wpforo_phrase('Subscribe to this topic') ?></label></div>
					<?php endif; ?>
					<?php do_action('wpforo_editor_topic_submit_before', $forumid) ?>
                    <input id="wpf_formbutton" type="submit" name="topic[save]" class="button button-primary forum_submit" value="<?php $layout == 3 ? wpforo_phrase('Ask a question') : wpforo_phrase('Add topic') ?>">
					<?php do_action('wpforo_editor_topic_submit_after', $forumid) ?>
                    <div class="wpf-clear"></div>
                </div>
            </form>
        </div>

		<?php
	}
	
	/**
	* 
	* @param array $args
	*  
	* Please note that all array elements are required!
	* example of args
	* $default = array(
	*	"topic_closed" => $topic['closed'], 	// is topic closed or opened (values 1 or 0)
	* 	"topicid" => $topic['topicid'],  		// the id of topic
	* 	"forumid" => $forum_data['forumid'],
	* 	"layout" => $cat_layout,
	* 	"topic_title" => $topic['title']		// the title of topic
	* );
	*/
	function reply_form($args){
		extract($args, EXTR_OVERWRITE);

		$this->report_dialog();

		if( $topic_closed ) return;

		$textareaid = uniqid('wpf_post_body_');
		
		$head_html = '<p id="wpf-reply-form-title">'.wpforo_phrase('Leave a reply', false).'</p>';
		$head_html = apply_filters( 'wpforo_reply_form_head', $head_html, $args ); 
		if(!isset(WPF()->post->options['max_upload_size']) || !WPF()->post->options['max_upload_size']){$server_mus = wpforo_human_size_to_bytes(ini_get('upload_max_filesize')); if( !$server_mus || $server_mus > 10485760 ) $server_mus = 10485760; WPF()->post->options['max_upload_size'] = $server_mus;}
		?>
		<div id="wpf-form-wrapper" <?php echo ( WPF()->forum->get_layout() == 3 && !$this->forms['qa_display_answer_editor'] ? 'style="display: none;"' : '' ) ?>>
			<?php echo $head_html; //this is a HTML content ?>
			<div id="wpf-post-create" class="wpf-post-create">
				<form name="post" action="" enctype="multipart/form-data" method="POST" class="wpforoeditor wpforo-main-form" data-textareaid="<?php echo $textareaid ?>" data-bodyminlength="<?php echo WPF()->post->options['post_body_min_length'] ?>" data-bodymaxlength="<?php echo WPF()->post->options['post_body_max_length'] ?>">
					<?php wp_nonce_field( 'wpforo_verify_form', 'wpforo_form' ); ?>
                    <?php $parentid = 0; if( wpfval($args, 'layout') && $args['layout'] == 3 && isset($topicid) && !WPF()->topic->can_answer($topicid) ){ $topic = wpforo_topic( $topicid ); $parentid = ( wpfval($topic, 'first_postid') ) ? $topic['first_postid'] : 0;} ?>
                    <input type="hidden" id="wpf_formaction" name="post[action]" value="add"/>
					<input type="hidden" id="wpf_formtopicid" name="post[topicid]" value="<?php echo intval($topicid) ?>"/>
					<input type="hidden" id="wpf_postparentid" name="post[parentid]" value="<?php echo intval($parentid) ?>"/>
					<input type="hidden" id="wpf_formpostid" name="post[postid]" value=""/>
					<input type="hidden" id="wpf_parent" name="post[forumid]" value="<?php echo intval($forumid) ?>" />
                    <?php if(!is_user_logged_in()): ?>
                		<?php $guest = WPF()->member->get_guest_cookies(); ?>
                        <div class="wpf-post-guest-fields">
                            <div class="wpf-post-guest-name">
                                <label style="padding-left:8px;"> <?php wpforo_phrase('Author Name') ?> * </label>
                                <input id="wpf_user_name" type="text" placeholder="<?php esc_attr( wpforo_phrase('Your name') ) ?>" name="post[name]" value="<?php echo esc_attr($guest['name']) ?>" required />
                            </div>
                            <div class="wpf-post-guest-email">
                                <label style="padding-left:8px;"> <?php wpforo_phrase('Author Email') ?> * </label>
                                <input id="wpf_user_email" type="text" placeholder="<?php esc_attr( wpforo_phrase('Your email') ) ?>" name="post[email]" value="<?php echo esc_attr($guest['email']) ?>" required />
                            </div>
                            <div class="wpf-clear"></div>
                        </div>
                        <label style="padding-left:8px;"> <?php wpforo_phrase('Post Title') ?> * </label>
                	<?php endif; ?>
	                <?php 
					$reply_title = wpforo_phrase('RE', false) . ': '. $topic_title; 
					$reply_title = apply_filters( 'wpforo_reply_form_field_title', $reply_title, $args );
					$reply_title = esc_attr($reply_title);
					?>
					<input id="wpf_title" required="required" type="text" name="post[title]" class="wpf-subject" value="<?php if($reply_title) echo esc_attr($reply_title); ?>" autocomplete="off" placeholder="<?php if($reply_title) echo esc_attr($reply_title); ?>"><br/>
					<?php
                    $settings = $this->editor_buttons( 'post' );
					wp_editor( '', $textareaid, $settings );
					?>
					<div class="wpf-extra-fields">
                        <?php do_action('wpforo_reply_form_extra_fields_before') ?>
						 <?php do_action('wpforo_reply_form_buttons_hook'); ?>
                        <?php do_action('wpforo_reply_form_extra_fields_after') ?>
	                </div>
                    <?php if( WPF()->post->options['tags'] && WPF()->perm->forum_can('tag', $forumid) ) : ?>
                        <div class="wpf-topic-tags" style="display: none;">
                            <p class="wpf-topic-tags-label"><i class="fas fa-tag"></i> <?php wpforo_phrase('Tags') ?> <span>(<?php wpforo_phrase('Separate tags using a comma') ?>)</span></p>
                            <input id="wpf_tags" placeholder="<?php echo sprintf(wpforo_phrase('Start typing tags here (maximum %d tags are allowed)...', false), WPF()->post->options['max_tags']) ?>" name="post[tags]" autocomplete="off" value="" type="text">
                            <style type="text/css">#wpforo-wrap .wpf-ac-loading {background-image: url('<?php echo WPFORO_URL ?>/wpf-assets/images/ajax_loading.gif');background-repeat: no-repeat;background-position: right center;visibility: visible;} </style>
                        </div>
                    <?php endif; ?>
	                <?php if( wpforo_feature('subscribe_checkbox_on_post_editor') && WPF()->perm->forum_can('sb', $forumid) ) :
		                $args = array( "userid" => WPF()->current_userid , "itemid" => intval($topicid), "type" => "topic" );
		                $subscribe = WPF()->sbscrb->get_subscribe( $args );
	                	if( !isset($subscribe['subid']) ) : ?>
	                		<div class="wpf-topic-sbs"><input id="wpf-topic-sbs" type="checkbox" name="wpforo_topic_subs" value="1" <?php echo ( wpforo_feature('subscribe_checkbox_default_status') ) ? 'checked="true" ' : ''; ?> />&nbsp;<label for="wpf-topic-sbs"><?php wpforo_phrase(( WPF()->forum->get_layout() == 3 ? 'Subscribe to this question' : 'Subscribe to this topic' ) ) ?></label></div>
						<?php endif;
					endif; ?>
                    <?php do_action('wpforo_editor_post_submit_before') ?>
                    <input id="wpf_formbutton" type="submit" name="post[save]" class="button button-primary forum_submit" value="<?php wpforo_phrase(( WPF()->forum->get_layout() == 3 ? 'Answer' : 'Add Reply' ) ) ?>">
                    <?php do_action('wpforo_editor_post_submit_after') ?>
                    <div class="wpf-clear"></div>
				</form>
			</div>
		</div>
		<?php
		$layout = WPF()->forum->get_layout();
		if ( $layout == 3 || $layout == 4 ) {
			$form_option = ( $layout == 3 ? 'qa_comments_rich_editor' : 'threaded_reply_rich_editor' );
			if ( $this->forms[ $form_option ] ) {
				$this->post_form();
			} else {
				$this->post_form_simple();
			}
		}
	}

	public function post_form_simple(){
		if( wpfval(WPF()->current_object, 'topic', 'closed') ) return;
		$textareaid = uniqid('wpf_post_body_');
		$layout = WPF()->forum->get_layout();
		$bodyminlength = ( $layout == 3 ? WPF()->post->options['comment_body_min_length'] : WPF()->post->options['post_body_min_length'] );
		$bodymaxlength = ( $layout == 3 ? WPF()->post->options['comment_body_max_length'] : WPF()->post->options['post_body_max_length'] );
		$submit_ico = ( $layout == 3 ? '<i class="far fa-comment"></i>' : '<i class="fas fa-reply"></i>' );
		$submit_value = ( $layout == 3 ? wpforo_phrase('Add a comment', false) : wpforo_phrase('Reply', false) );
        ?>
        <form enctype="multipart/form-data" method="POST" class="wpforo-post-form" style="display: none;" data-textareaid="<?php echo $textareaid ?>" data-bodyminlength="<?php echo $bodyminlength ?>" data-bodymaxlength="<?php echo $bodymaxlength ?>">
	        <?php wp_nonce_field( 'wpforo_verify_form', 'wpforo_form' ); ?>
            <input type="hidden" class="wpf_post_action" name="post[action]" value="add"/>
            <input type="hidden" class="wpf_post_forumid" name="post[forumid]" value="<?php echo wpforo_bigintval( wpfval(WPF()->current_object, 'forumid') ) ?>" />
            <input type="hidden" class="wpf_post_topicid" name="post[topicid]" value="<?php echo wpforo_bigintval( wpfval(WPF()->current_object, 'topicid') ) ?>"/>
            <input type="hidden" class="wpf_post_parentid" name="post[parentid]" value="0">
            <?php if(!is_user_logged_in()): ?>
		        <?php $guest = WPF()->member->get_guest_cookies(); ?>
                <div class="wpf-post-guest-fields" style="display: table;">
                    <div class="wpf-post-guest-name" style="display: table-row;">
                        <label style="padding-left:8px; display: table-cell;"> <?php wpforo_phrase('Author Name') ?> * </label>
                        <input class="wpf_user_name" style="display: table-cell;" type="text" placeholder="<?php esc_attr( wpforo_phrase('Your name') ) ?>" name="post[name]" value="<?php echo esc_attr($guest['name']) ?>" required />
                    </div>
                    <div class="wpf-post-guest-email" style="display: table-row;">
                        <label style="padding-left:8px; display: table-cell;"> <?php wpforo_phrase('Author Email') ?> * </label>
                        <input class="wpf_user_email" style="display: table-cell;" type="text" placeholder="<?php esc_attr( wpforo_phrase('Your email') ) ?>" name="post[email]" value="<?php echo esc_attr($guest['email']) ?>" required />
                    </div>
                    <div class="wpf-clear"></div>
                </div>
	        <?php endif; ?>
            <div class="wpf_post_form_textarea_wrap"  data-textareatype="simple_editor">
                <textarea id="<?php echo $textareaid ?>" required class="wpf_post_body" name="post[body]" placeholder="<?php wpforo_phrase('Write here . . .') ?>"></textarea>
            </div>
            <div class="wpf-extra-fields">
		        <?php do_action('wpforo_portable_form_extra_fields_before') ?>
		        <?php do_action('wpforo_portable_form_buttons_hook'); ?>
		        <?php do_action('wpforo_portable_form_extra_fields_after') ?>
            </div>
	        <?php do_action('wpforo_portable_editor_post_submit_before') ?>
            <button type="submit" name="post[save]" class="wpf-button">
	            <?php echo $submit_ico ?>
                <?php echo $submit_value ?>
            </button>
            <button type="reset" class="wpf-button-secondary wpf-button-close-form">
                <i class="fas fa-times"></i>
                <span class="wpf-button-text"><?php wpforo_phrase('Cancel') ?></span>
            </button>
            <div class="wpf-clear"></div>
	        <?php do_action('wpforo_portable_editor_post_submit_after') ?>
        </form>
        <?php
    }

	public function post_form(){
		if( wpfval(WPF()->current_object, 'topic', 'closed') ) return;
		$textareaid = uniqid('wpf_post_body_');
		$topicid = wpforo_bigintval( wpfval(WPF()->current_object, 'topicid') );
		$layout = WPF()->forum->get_layout();
		$bodyminlength = ( $layout == 3 ? WPF()->post->options['comment_body_min_length'] : WPF()->post->options['post_body_min_length'] );
		$bodymaxlength = ( $layout == 3 ? WPF()->post->options['comment_body_max_length'] : WPF()->post->options['post_body_max_length'] );
		$submit_ico = ( $layout == 3 ? '<i class="far fa-comment"></i>' : '<i class="fas fa-reply"></i>' );
		$submit_value = ( $layout == 3 ? wpforo_phrase('Add a comment', false) : wpforo_phrase('Reply', false) );
		?>
        <form enctype="multipart/form-data" method="POST" class="wpforo-post-form" style="display: none;" data-textareaid="<?php echo $textareaid ?>"  data-bodyminlength="<?php echo $bodyminlength ?>" data-bodymaxlength="<?php echo $bodymaxlength ?>">
			<?php wp_nonce_field( 'wpforo_verify_form', 'wpforo_form' ); ?>
            <input type="hidden" class="wpf_post_action" name="post[action]" value="add"/>
            <input type="hidden" class="wpf_post_forumid" name="post[forumid]" value="<?php echo wpforo_bigintval( wpfval(WPF()->current_object, 'forumid') ) ?>" />
            <input type="hidden" class="wpf_post_topicid" name="post[topicid]" value="<?php echo $topicid ?>"/>
            <input type="hidden" class="wpf_post_parentid" name="post[parentid]" value="0">
	        <?php if(!is_user_logged_in()): ?>
		        <?php $guest = WPF()->member->get_guest_cookies(); ?>
                <div class="wpf-post-guest-fields" style="display: table;">
                    <div class="wpf-post-guest-name" style="display: table-row;">
                        <label style="padding-left:8px; display: table-cell;"> <?php wpforo_phrase('Author Name') ?> * </label>
                        <input class="wpf_user_name" style="display: table-cell;" type="text" placeholder="<?php esc_attr( wpforo_phrase('Your name') ) ?>" name="post[name]" value="<?php echo esc_attr($guest['name']) ?>" required />
                    </div>
                    <div class="wpf-post-guest-email" style="display: table-row;">
                        <label style="padding-left:8px; display: table-cell;"> <?php wpforo_phrase('Author Email') ?> * </label>
                        <input class="wpf_user_email" style="display: table-cell;" type="text" placeholder="<?php esc_attr( wpforo_phrase('Your email') ) ?>" name="post[email]" value="<?php echo esc_attr($guest['email']) ?>" required />
                    </div>
                    <div class="wpf-clear"></div>
                </div>
	        <?php endif; ?>
            <div class="wpf_post_form_textarea_wrap wpf-post-create" data-textareatype="rich_editor">
                <textarea id="<?php echo $textareaid ?>" class="wpf_post_body" name="post[body]"></textarea>
            </div>
            <div class="wpf-extra-fields">
		        <?php do_action('wpforo_portable_form_extra_fields_before') ?>
		        <?php do_action('wpforo_portable_form_buttons_hook'); ?>
		        <?php do_action('wpforo_portable_form_extra_fields_after') ?>
            </div>
	        <?php if( wpforo_feature('subscribe_checkbox_on_post_editor') && WPF()->perm->forum_can('sb') ) :
		        $args = array( "userid" => WPF()->current_userid , "itemid" => $topicid, "type" => "topic" );
		        $subscribe = WPF()->sbscrb->get_subscribe( $args );
		        if( !isset($subscribe['subid']) ) : ?>
                    <div class="wpf-topic-sbs">
                        <input id="wpf-topic-sbs" type="checkbox" name="wpforo_topic_subs" value="1" <?php echo ( wpforo_feature('subscribe_checkbox_default_status') ) ? 'checked="true" ' : ''; ?> />&nbsp;
                        <label for="wpf-topic-sbs"><?php wpforo_phrase(( $layout == 3 ? 'Subscribe to this question' : 'Subscribe to this topic' ) ) ?></label>
                    </div>
		        <?php endif;
	        endif; ?>
	        <?php do_action('wpforo_portable_editor_post_submit_before') ?>
            <button type="submit" name="post[save]" class="wpf-button">
				<?php echo $submit_ico ?>
				<?php echo $submit_value ?>
            </button>
            <button type="reset" class="wpf-button-secondary wpf-button-close-form">
                <i class="fas fa-times"></i>
                <span class="wpf-button-text"><?php wpforo_phrase('Cancel') ?></span>
            </button>
            <div class="wpf-clear"></div>
	        <?php do_action('wpforo_portable_editor_post_submit_after') ?>
            <div class="wpf-clear"></div>
        </form>
		<?php
	}

	public function topic_moderation_tabs($tabs = array()){
        $tabs = apply_filters('wpforo_topic_moderation_tabs', $tabs);
        if(!$tabs) return;
        $default_tab = array('title' => '', 'id' => '', 'class' => '', 'icon' => '', 'active' => false);
        ?>
        <div class="wpf-tool-tabs">
            <?php foreach ($tabs as $tab) : $tab = wpforo_parse_args($tab, $default_tab); ?>
                <div id="<?php echo esc_attr($tab['id']) ?>" class="wpf-tool-tab <?php echo $tab['class']; echo ($tab['active'] ? ' wpf-tt-active' : ''); ?>">
                    <i class="<?php echo $tab['icon'] ?>"></i>&nbsp;
                    <?php echo $tab['title'] ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div id="wpf_tool_tab_content_wrap">
            <i class="fas fa-spinner fa-spin wpf-icon-spinner"></i>
        </div>
        <?php
    }

	private function topic_merge_form(){
        ?>
        <div class="wpf-tool wpf-tool-merge">
            <h3><i class="fas fa-code-branch"></i></h3>
            <div class="wpf-cl"></div>
            <form method="post" enctype="multipart/form-data" action="">
				<?php wp_nonce_field( 'wpforo_verify_form', 'wpforo_form' ); ?>
                <ul>
                    <li class="wpf-target-topic">
                        <label for="target-topic" class="wpf-input-label"><?php wpforo_phrase('Target Topic URL') ?> <sup>*</sup></label>
                        <div class="wpf-tool-desc">
                            <?php wpforo_phrase('Please copy the target topic URL from browser address bar and paste in the field below.') ?><br/>
                        </div>
                        <input id="target-topic" type="text" required name="wpforo[target_topic_url]" value="" placeholder="http://example.com/community/main-forum/target-topic/" />
                        <div class="wpf-tool-desc" style="margin: 15px 1px 0px 1px;">
                            <?php wpforo_phrase('All posts will be merged and displayed (ordered) in target topic according to posts dates. If you want to append merged posts to the end of the target topic you should allow to update posts dates to current date by check the option below.') ?>
                        </div>
                    </li>
                    <li class="wpf-update-date-and-append"><input id="update-date-and-append" type="checkbox" name="wpforo[update_date_and_append]" value="1" /> <label for="update-date-and-append"><?php wpforo_phrase('Update post dates (current date) to allow append posts to the end of the target topic.') ?></label></li>
                    <li class="wpf-update-to-target-title"><input id="update-to-target-title" type="checkbox" name="wpforo[to_target_title]" value="1" checked /> <label for="update-to-target-title"><?php wpforo_phrase('Update post titles with target topic title.') ?></label></li>
                    <li><i class="fas fa-info-circle wpfcl-5" style="font-size: 16px;"></i> &nbsp;<?php wpforo_phrase('Topics once merged cannot be unmerged. This topic URL will no longer be available.') ?></li>
                    <li class="wpf-submit"><input type="submit" name="wpforo[topic_merge]" value="<?php wpforo_phrase('Merge') ?>"></li>
                </ul>
            </form>
        </div>
        <?php
    }

	private function reply_move_form(){
        if( !$posts = WPF()->post->get_posts( array('topicid' => WPF()->current_object['topicid']) ) ) return;
        if( count($posts) < 2 ) return;
        ?>
        <div class="wpf-tool wpf-tool-split">
            <h3><i class="far fa-share-square"></i></h3>
            <div class="wpf-cl"></div>
            <form id="wpforo_split_form" method="post" enctype="multipart/form-data" action="">
				<?php wp_nonce_field( 'wpforo_verify_form', 'wpforo_form' ); ?>
                <ul>
                    <li id="wpf_split_target_url" class="wpf-target-topic">
                        <label for="spl-target-url" class="wpf-input-label"><?php wpforo_phrase('Target Topic URL') ?> <sup>*</sup></label>
                        <div class="wpf-tool-desc">
                            <?php wpforo_phrase('Please copy the target topic URL from browser address bar and paste in the field below.') ?><br/>
                        </div>
                        <input id="spl-target-url" type="text" name="wpforo[target_topic_url]" required placeholder="http://example.com/community/main-forum/target-topic/" />
                        <div class="wpf-tool-desc" style="margin: 15px 1px 0px 1px;">
                            <?php wpforo_phrase('All posts will be merged and displayed (ordered) in target topic according to posts dates. If you want to append merged posts to the end of the target topic you should allow to update posts dates to current date by check the option below.') ?>
                        </div>
                    </li>
                    <li id="wpf_split_append">
                        <input id="spl-update-date-and-append" type="checkbox" name="wpforo[update_date_and_append]" value="1" />
                        <label for="spl-update-date-and-append"><?php wpforo_phrase('Update post dates (current date) to allow append posts to the end of the target topic.') ?></label>
                    </li>
                    <li>
                        <input id="split-update-to-target-title" type="checkbox" name="wpforo[to_target_title]" value="1" checked />
                        <label for="split-update-to-target-title"><?php wpforo_phrase('Update post titles with target topic title.') ?></label>
                    </li>
                    <li>
                        <label class="wpf-input-label"><?php wpforo_phrase('Select Posts to Split') ?> <sup>*</sup></label>
                        <div class="wpf-split-posts">
                            <ul>
                                <?php foreach ($posts as $post) : ?>
                                    <li>
                                        <label title="<?php wpforo_text($post['body'], 200); ?>"><input type="checkbox" name="wpforo[posts][]" value="<?php echo $post['postid'] ?>" /><?php wpforo_text($post['body'], 100); ?></label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </li>
                    <li style="padding-top: 10px;"><i class="fas fa-info-circle wpfcl-5" style="font-size: 16px;"></i> &nbsp;<?php wpforo_phrase('Topic once split cannot be unsplit. The first post of new topic becomes the earliest reply.') ?></li>
                    <li class="wpf-submit"><input type="submit" name="wpforo[topic_split]" value="<?php wpforo_phrase('Move') ?>"></li>
                </ul>
            </form>
        </div>
	    <?php
    }
	
	
    private function topic_split_form(){
        if( !$posts = WPF()->post->get_posts( array('topicid' => WPF()->current_object['topicid']) ) ) return;
        if( count($posts) < 2 ) return;
        ?>
        <div class="wpf-tool wpf-tool-split">
            <h3><i class="fas fa-cut"></i></h3>
            <div class="wpf-cl"></div>
            <form id="wpforo_split_form" method="post" enctype="multipart/form-data" action="">
				<?php wp_nonce_field( 'wpforo_verify_form', 'wpforo_form' ); ?>
                <ul>
                    <li>
                        <input id="wpf_split_create_new" type="checkbox" name="wpforo[create_new]" value="1" checked>
                        <label for="wpf_split_create_new" class="wpf-input-label" style="display: inline-block;"><?php wpforo_phrase('Create New Topic') ?></label>
                        <div class="wpf-tool-desc">
                            <?php wpforo_phrase('Create new topic with split posts. The first post of new topic becomes the earliest reply.') ?><br/>
                        </div>
                    </li>
                    <li id="wpf_split_target_url" class="wpf-target-topic" style="display: none;">
                        <label for="spl-target-url" class="wpf-input-label"><?php wpforo_phrase('Target Topic URL') ?> <sup>*</sup></label>
                        <div class="wpf-tool-desc">
                            <?php wpforo_phrase('Please copy the target topic URL from browser address bar and paste in the field below.') ?><br/>
                        </div>
                        <input id="spl-target-url" type="text" name="wpforo[target_topic_url]" required disabled placeholder="http://example.com/community/main-forum/target-topic/" />
                        <div class="wpf-tool-desc" style="margin: 15px 1px 0px 1px;">
                            <?php wpforo_phrase('All posts will be merged and displayed (ordered) in target topic according to posts dates. If you want to append merged posts to the end of the target topic you should allow to update posts dates to current date by check the option below.') ?>
                        </div>
                    </li>
                    <li id="wpf_split_append" style="display: none;">
                        <input id="spl-update-date-and-append" type="checkbox" name="wpforo[update_date_and_append]" value="1" />
                        <label for="spl-update-date-and-append"><?php wpforo_phrase('Update post dates (current date) to allow append posts to the end of the target topic.') ?></label>
                    </li>
                    <li id="wpf_split_new_title">
                        <label for="spl-topic-title" class="wpf-input-label"><?php wpforo_phrase('New Topic Title') ?> <sup>*</sup></label>
                        <input id="spl-topic-title" type="text" name="wpforo[new_topic_title]" required placeholder="<?php wpforo_phrase('Topic Title') ?>" />
                    </li>
                    <li id="wpf_split_forumid"><label for="spl-topic-forum" class="wpf-input-label"><?php wpforo_phrase('New Topic Forum') ?> <sup>*</sup></label>
                        <select id="spl-topic-forum" name="wpforo[new_topic_forumid]"><?php WPF()->forum->tree('select_box', false, WPF()->current_object['forumid'] ) ?></select></li>
                    <li>
                        <input id="split-update-to-target-title" type="checkbox" name="wpforo[to_target_title]" value="1" checked />
                        <label for="split-update-to-target-title"><?php wpforo_phrase('Update post titles with target topic title.') ?></label>
                    </li>
                    <li>
                        <label class="wpf-input-label"><?php wpforo_phrase('Select Posts to Split') ?> <sup>*</sup></label>
                        <div class="wpf-split-posts">
                            <ul>
                                <?php foreach ($posts as $post) : ?>
                                    <li>
                                        <label title="<?php wpforo_text($post['body'], 200); ?>"><input type="checkbox" name="wpforo[posts][]" value="<?php echo $post['postid'] ?>" /><?php wpforo_text($post['body'], 100); ?></label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </li>
                    <li style="padding-top: 10px;"><i class="fas fa-info-circle wpfcl-5" style="font-size: 16px;"></i> &nbsp;<?php wpforo_phrase('Topic once split cannot be unsplit. The first post of new topic becomes the earliest reply.') ?></li>
                    <li class="wpf-submit"><input type="submit" name="wpforo[topic_split]" value="<?php wpforo_phrase('Split') ?>"></li>
                </ul>
            </form>
        </div>
	    <?php
    }

    private function topic_move_form($topicid = NULL){
        if(!$topicid && empty(WPF()->current_object['topicid'])) return;
        if(!$topicid) $topicid = WPF()->current_object['topicid'];
        ?>
		<div class="wpf-tool wpf-tool-split">
            <h3><i class="far fa-share-square"></i></h3>
			<div class="wpf-cl"></div>
            <form id="wpf_topicmoveform" method="POST" enctype="multipart/form-data" action="">
                <ul>
                    <li>
                        <div id="wpf_movedialog" title="<?php esc_attr(wpforo_phrase('Move topic')) ?>">
                            <div class="form-field">
                                <label for="parent"></label>
                                <label for="spl-target-url" class="wpf-input-label"
                                       style="padding-bottom: 7px;"><?php wpforo_phrase('Choose Target Forum') ?>
                                    <sup>*</sup></label>

                                <?php wp_nonce_field('wpforo_verify_form', 'wpforo_form'); ?>
                                <input type="hidden" name="movetopicid" value="<?php echo intval($topicid) ?>"/>
                                <input type="hidden" name="post[save]" value="move"/>
                                <select id="wpf_parent" name="topic[forumid]" class="postform">
                                    <?php WPF()->forum->tree('select_box', FALSE); ?>
                                </select>

                            </div>
                        </div>
                    </li>
                    <li style="padding-top: 20px;"><i class="fas fa-info-circle wpfcl-5" style="font-size: 16px;"></i>
                        &nbsp;<?php wpforo_phrase('This action changes topic URL. Once the topic is moved to other forum the old URL of this topic will no longer be available.') ?>
                    </li>
                    <li class="wpf-submit"><input type="submit" value="<?php wpforo_phrase('Move') ?>"/></li>
                </ul>
            </form>
		</div>
        <?php
    }
	
	function pagenavi($paged = null, $items_count = null, $items_per_page = null, $permalink = TRUE, $class = ''){
	    if( is_null($paged) ) $paged = WPF()->current_object['paged'];
	    if( is_null($items_count) ) $items_count = WPF()->current_object['items_count'];
	    if( is_null($items_per_page) ) $items_per_page = WPF()->current_object['items_per_page'];

		if($items_count <= $items_per_page) return;
		
		$pages_count = ceil($items_count/$items_per_page);

		$current_url = ( WPF()->current_url ? WPF()->current_url : wpforo_get_request_uri() );
		$sanitized_current_url = trim( WPF()->strip_url_paged_var($current_url), '/' );

		if($permalink){
			$rtrimed_url = '';
			$url_append_vars = '';
			if( preg_match('#^(.+?)(/?[\?\&].*)?$#isu', $sanitized_current_url, $match) ){
				if( wpfval($match, 1) ) $rtrimed_url = rtrim($match[1], '/\\');
				if( wpfval($match, 2) ) $url_append_vars = '?' . trim($match[2], '?&/\\');
			}
			$url = str_replace('%', '%%', $rtrimed_url . '/'. $this->slugs['paged']) .'/%1$d/' . str_replace('%', '%%', $url_append_vars);
		}else{
			$url = str_replace('%', '%%', $sanitized_current_url) . '&wpfpaged=%1$d';
		}
		?>
		
		<div class="wpf-navi <?php echo esc_attr($class) ?>">
            <div class="wpf-navi-wrap">
                <span class="wpf-page-info">
                    <?php wpforo_phrase('Page') ?> <?php echo intval($paged) ?> / <?php echo intval($pages_count) ?>
                </span>
                <?php if( $paged - 1 > 0 ): $prev_url = ( ($paged - 1) == 1 ? $sanitized_current_url : sprintf($url, $paged - 1) ); ?>
                    <a href="<?php echo esc_url( WPF()->user_trailingslashit($prev_url) ) ?>" class="wpf-prev-button" rel="prev">
                        <i class="fas fa-chevron-left fa-sx"></i> <?php wpforo_phrase('prev') ?>
                    </a>
                <?php endif ?>
                <select class="wpf-navi-dropdown" onchange="if (this.value) window.location.assign(this.value)" title="<?php esc_attr( wpforo_phrase('Select Page') ) ?>">
                    <option value="<?php echo esc_url( WPF()->user_trailingslashit($sanitized_current_url) ) ?>" <?php wpfo_check($paged, 1, 'selected') ?>>1</option>
                    <?php for($i = 2; $i <= $pages_count; $i++) : ?>
                        <option value="<?php echo esc_url( WPF()->user_trailingslashit( sprintf($url, $i) ) ) ?>" <?php wpfo_check($paged, $i, 'selected') ?>>
                            <?php echo $i ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <?php if( $paged + 1 <= $pages_count ): ?>
                    <a href="<?php echo esc_url( WPF()->user_trailingslashit(sprintf($url, $paged + 1) ) ) ?>" class="wpf-next-button" rel="next">
                        <?php wpforo_phrase('next') ?> <i class="fas fa-chevron-right fa-sx"></i>
                    </a>
                <?php endif ?>
            </div>
		</div>
		
		<?php 
	} 
	
	function likers($postid){
		if(!$postid) return '';
		
		$l_count = wpforo_post($postid, 'likes_count');
		$l_usernames = wpforo_post($postid, 'likers_usernames');
		$return = '';
		
		if( $l_count ){
			if($l_usernames[0]['ID'] == WPF()->current_userid) $l_usernames[0]['display_name'] = wpforo_phrase('You', FALSE);
			if($l_count == 1){
				$return = sprintf( wpforo_phrase('%s liked', FALSE), '<a href="' . esc_url(WPF()->member->get_profile_url($l_usernames[0]['ID'])) . '">'.esc_html($l_usernames[0]['display_name']).'</a>' );
			}elseif($l_count == 2){
				$return = sprintf( wpforo_phrase('%s and %s liked', FALSE), '<a href="' . esc_url(WPF()->member->get_profile_url($l_usernames[0]['ID'])) . '">'.esc_html($l_usernames[0]['display_name']).'</a>', '<a href="'.esc_url(WPF()->member->get_profile_url($l_usernames[1]['ID'])).'">'.esc_html($l_usernames[1]['display_name']).'</a>' );
			}elseif($l_count == 3){
				$return = sprintf( wpforo_phrase('%s, %s and %s liked', FALSE), '<a href="' . esc_url(WPF()->member->get_profile_url($l_usernames[0]['ID'])) .'">'.esc_html($l_usernames[0]['display_name']).'</a>', '<a href="'.esc_url(WPF()->member->get_profile_url($l_usernames[1]['ID'])).'">'.esc_html($l_usernames[1]['display_name']).'</a>', '<a href="'.esc_url(WPF()->member->get_profile_url($l_usernames[2]['ID'])).'">'.esc_html($l_usernames[2]['display_name']).'</a>' );
			}elseif($l_count >= 4){
				$l_count = $l_count - 3;
				$return = sprintf( wpforo_phrase('%s, %s, %s and %d people liked', FALSE), '<a href="' . esc_url(WPF()->member->get_profile_url($l_usernames[0]['ID'])) .'">'.esc_html($l_usernames[0]['display_name']).'</a>', '<a href="'.esc_url(WPF()->member->get_profile_url($l_usernames[1]['ID'])).'">'.esc_html($l_usernames[1]['display_name']).'</a>', '<a href="'.esc_url(WPF()->member->get_profile_url($l_usernames[2]['ID'])).'">'.esc_html($l_usernames[2]['display_name']).'</a>', $l_count );
			}
		}
		return $return;
	}


	/**
	 * Get actions buttons
	 *
	 * @since 1.0.0
	 *
	 * @param array $buttons names function will return buttons by this array
	 *
	 * @param array $forum required
	 *
	 * @param array $topic required
	 *
	 * @param array $post required
	 *
	 * @param bool $is_topic required this is a first post in the loop
	 *
	 * @param bool $echo
	 *
	 * $buttons = array( 'reply', 'answer', 'comment', 'quote', 'like', 'report', 'sticky', 'close', 'edit', 'delete', 'link' );
	 *
	 * @return string
	 */
	function buttons( $buttons, $forum = array(), $topic = array(), $post = array(), $echo = true ){
        $buttons = (array) $buttons;
		$is_topic = (bool) wpfval($post, 'is_first_post');
		
		$button_html = array(); 
		$login = is_user_logged_in();
		
		$forumid = (isset($forum['forumid'])) ? $forum['forumid'] : 0;
		$topicid = (isset($topic['topicid'])) ? $topic['topicid'] : 0;
		$postid = (isset($post['postid'])) ? $post['postid'] : 0;

		if( $post ){
			$userid = wpforo_bigintval( wpfval($post, 'userid') );
			$is_owner = (int) (bool) ( WPF()->current_userid && wpforo_is_owner( $userid ) );
			$mention = (string) ( !$is_owner ? wpforo_member($post, 'user_nicename') : '' );
		}else{
			$userid = 0;
			$is_owner = 0;
			$mention = '';
        }

		$is_sticky = (isset($topic['type'])) ? $topic['type'] : 0;
		$is_closed = (isset($topic['closed'])) ? $topic['closed'] : 0;
		$is_private = (isset($topic['private'])) ? $topic['private'] : 0;
		$is_solved = (isset($topic['solved'])) ? $topic['solved'] : 0;
        $is_approve = (isset($post['status'])) ? $post['status'] : 0;
		
		foreach($buttons as $button){
			
			switch($button){

				case 'reply': 
					if($is_closed || $is_approve || ($is_topic && !WPF()->post->options['layout_threaded_first_post_reply']) ) break;
					if( WPF()->perm->forum_can('cr', $forumid) ){
					    $layout = WPF()->forum->get_layout($forumid);
					    $layout_class = 'wpforo_layout_' . intval($layout);
			   			$button_html[] = '<span id="parentpostid'.wpforo_bigintval($postid).'" class="wpforo-reply wpf-action '.$layout_class.'" data-mention="'. esc_attr($mention) .'"><i class="fas fa-reply fa-rotate-180"></i><span class="wpf-button-text">' . wpforo_phrase('Reply', false).'</span></span>';
			   		}else{
                        $button_html[] = ( $login ) ? '' : '<span class="wpf-action not_reg_user"><i class="fas fa-reply fa-rotate-180"></i><span class="wpf-button-text">' . wpforo_phrase('Reply', false).'</span></span>';
			   		}
					break; 
				case 'answer':
					if($is_closed || $is_approve) break;
					if( WPF()->perm->forum_can('cr', $forumid) ){
			   			if( WPF()->topic->can_answer($topicid) ){
                            $button_html[] = '<span class="wpforo-answer wpf-button" data-phrase="' . esc_attr( wpforo_phrase('Answer', false) ).'"  data-mention="'. esc_attr($mention) .'"><i class="fas fa-pencil-alt"></i><span class="wpf-button-text">' . wpforo_phrase('Answer', false).'</span></span>';
                        }
			   		} else {
                        $button_html[] = ( $login ) ? '' : '<span class="wpf-button not_reg_user" data-phrase="' . esc_attr( wpforo_phrase('Answer', false) ).'"><i class="fas fa-pencil-alt"></i><span class="wpf-button-text">' . wpforo_phrase('Answer', false).'</span></span>';
			   		}
				 	break; 
				case 'comment':
					if($is_closed || $is_approve || ($is_topic && !WPF()->post->options['layout_qa_first_post_reply'])) break;
					$title = wpforo_phrase('Use comments to ask for more information or suggest improvements. Avoid answering questions in comments.', false);
					if( WPF()->perm->forum_can('cr', $forumid) ) {
						$button_html[] = '<span id="parentpostid'.wpforo_bigintval($postid).'" class="wpforo-qa-comment wpf-button" title="'.esc_attr($title).'" data-phrase="' . esc_attr( wpforo_phrase('Add a comment', false) ) . '"  data-mention="'. esc_attr($mention) .'"><i class="far fa-comment"></i><span class="wpf-button-text">' . wpforo_phrase('Add a comment', false).'</span></span>';
			   		}else{
                        $button_html[] = ( $login ) ? '' : '<span class="not_reg_user wpf-button" title="'.esc_attr($title).'" data-phrase="' . esc_attr( wpforo_phrase('Add a comment', false) ) . '"><i class="far fa-comment"></i><span class="wpf-button-text">' . wpforo_phrase('Add a comment', false).'</span></span>';
			   		}
				 	break;
                case 'comment_raw':
					if($is_closed || $is_approve || ($is_topic && !WPF()->post->options['layout_qa_first_post_reply'])) break;
					$title = wpforo_phrase('Use comments to ask for more information or suggest improvements. Avoid answering questions in comments.', false);
					if( WPF()->perm->forum_can('cr', $forumid) ) {
						$button_html[] = '<a class="wpforo-qa-comment" title="'.esc_attr($title).'" data-phrase="' . esc_attr( wpforo_phrase('Add a comment', false, 'lower') ) . '"  data-mention="'. esc_attr($mention) .'"><i class="far fa-comment"></i><span class="wpf-button-text">' . wpforo_phrase('Add a comment', false, 'lower').'</span></a>';
			   		}else{
                        $button_html[] = ( $login ) ? '' : '<a class="not_reg_user" title="'.esc_attr($title).'" data-phrase="' . esc_attr( wpforo_phrase('Add a comment', false, 'lower') ) . '"><i class="far fa-comment"></i><span class="wpf-button-text">' . wpforo_phrase('Add a comment', false, 'lower').'</span></a>';
			   		}
				 	break;
				case 'quote':
					if($is_closed || $is_approve) break;
					if( WPF()->perm->forum_can('cr', $forumid) ) {
						$button_html[] = '<span wpf-tooltip="'.esc_attr(wpforo_phrase('Quote', false)).'" class="wpf-action wpforo-quote" data-postid="'.wpforo_bigintval($postid).'"  data-mention="'. esc_attr($mention) .'" data-userid="'. esc_attr($userid) .'" data-isowner="'. esc_attr($is_owner) .'"><i class="fas fa-quote-left wpfsx"></i><span class="wpf-button-text">' . wpforo_phrase('Quote', false).'</span></span>';
			   		}else{
                        $button_html[] = ( $login ) ? '' : '<span wpf-tooltip="'.esc_attr(wpforo_phrase('Quote', false)).'" class="wpf-action not_reg_user"><i class="fas fa-quote-left wpfsx"></i><span class="wpf-button-text">' . wpforo_phrase('Quote', false).'</span></span>';
			   		}	
					 break; 
				case 'like':
					if( WPF()->perm->forum_can('l', $forumid) && $login && WPF()->current_userid != $post['userid'] ) {
						$like_status = ( WPF()->post->is_liked( $postid, WPF()->current_userid ) === FALSE ? 'wpforo-like' : 'wpforo-unlike' );
						$like_icon = ( $like_status == 'wpforo-like') ? 'up' : 'down';
						$button_html[] = '<span class="wpf-action '. $like_status .'" data-postid="'. wpforo_bigintval($postid) .'"><i class="fas fa-thumbs-'. esc_attr($like_icon) .' wpfsx wpforo-like-ico"></i><span class="wpforo-like-txt">' . wpforo_phrase( str_replace('wpforo-', '', $like_status), false) . '</span></span>';
					}	
				 	break; 
				case 'report':
					if( WPF()->perm->forum_can('r', $forumid) && $login ) {
						$button_html[] = '<span wpf-tooltip="'.esc_attr(wpforo_phrase('Report', false)).'" class="wpf-action wpforo-report" data-postid="'. wpforo_bigintval($postid) .'"><i class="fas fa-exclamation-triangle"></i><span class="wpf-button-text">' . wpforo_phrase('Report', false).'</span></span>';
					}	
				 	break;
				case 'sticky':
					$sticky_status = ( $is_sticky ? 'wpforo-unsticky' : 'wpforo-sticky');
					if( WPF()->perm->forum_can('s', $forumid) ) {
						$button_html[] = '<span wpf-tooltip="'.esc_attr(wpforo_phrase( str_replace('wpforo-', '', $sticky_status), false)).'" class="wpf-action '. $sticky_status .'" data-topicid="'. wpforo_bigintval($topicid) .'"><i class="fas fa-thumbtack wpfsx"></i><span class="wpforo-sticky-txt">' . wpforo_phrase( str_replace('wpforo-', '', $sticky_status), false).'</span></span>';
					}
				 	break; 
				case 'private':
					if( $login ){
						if( WPF()->perm->forum_can('p', $forumid) || (WPF()->current_userid == $post['userid'] && WPF()->perm->forum_can('op', $forumid)) ) {
							$private_status = ( $is_private ? 'wpforo-public' : 'wpforo-private');
							$private_icon = ( $private_status == 'wpforo-public') ? 'eye' : 'eye-slash';
							$button_html[] = '<span wpf-tooltip="'.esc_attr(wpforo_phrase( str_replace('wpforo-', '', $private_status), false)).'" id="wpfprivate'. intval($topicid) .'" class="wpf-action '. $private_status .'"><i id="privateicon'. intval($topicid) .'"  class="fas fa-'. esc_attr($private_icon) .' wpfsx"></i><span id="privatetext'. intval($topicid) .'">' . wpforo_phrase( str_replace('wpforo-', '', $private_status), false).'</span></span>';
						}
					}
				 	break; 
				case 'solved':
					$solved_status = ( $is_solved ? 'wpforo-unsolved' : 'wpforo-solved');
					if( WPF()->perm->forum_can('sv', $forumid) || (WPF()->current_userid == $post['userid'] && WPF()->perm->forum_can('osv', $forumid)) ) {
						$button_html[] = '<span wpf-tooltip="'.esc_attr(wpforo_phrase( str_replace('wpforo-', '', $solved_status), false)).'" id="wpfsolved'. wpforo_bigintval($postid) .'" class="wpf-action '. $solved_status .'"><i class="fas fa-check-circle wpfsx"></i><span id="solvedtext'. wpforo_bigintval($postid) .'">' . wpforo_phrase( str_replace('wpforo-', '', $solved_status), false).'</span></span>';
                    }
				 	break;
                case 'approved':
                    if( WPF()->perm->forum_can('au', $forumid) && $login ) {
                        $approve_status = ( !$is_approve ? 'wpforo-unapprove' : 'wpforo-approve');
                        $approve_icon = ( $approve_status == 'wpforo-unapprove') ? 'fa-exclamation-circle' : 'fa-check';
                        $button_html[] = '<span wpf-tooltip="'.esc_attr(wpforo_phrase( str_replace('wpforo-', '', $approve_status), false)).'" id="wpfapprove'. wpforo_bigintval($postid) .'" class="wpf-action '. $approve_status .'"><i id="approveicon'. wpforo_bigintval($postid) .'"   class="fas '. esc_attr($approve_icon) .' wpfsx"></i><span id="approvetext'. wpforo_bigintval($postid) .'">' . wpforo_phrase( str_replace('wpforo-', '', $approve_status), false).'</span></span>';
                    }
                    break;
                case 'close':
					if( WPF()->perm->forum_can('cot', $forumid) && $login ) {
						$open_status = ( $is_closed ? 'wpforo-open' : 'wpforo-close' );
						$open_icon = ($open_status == 'wpforo-open') ? 'unlock' : 'lock';
						$button_html[] = '<span  wpf-tooltip="'.esc_attr(wpforo_phrase( str_replace('wpforo-', '', $open_status), false)).'" id="wpfclose'. intval($topicid) .'" class="wpf-action '. $open_status .'"><i id="closeicon'. intval($topicid) .'" class="fas fa-'. esc_attr($open_icon) .' wpfsx"></i><span id="closetext'. intval($topicid) .'">' . wpforo_phrase( str_replace('wpforo-', '', $open_status), false).'</span></span>';
					}
				 	break; 
				case 'tools':
					if( WPF()->perm->forum_can('mt', $forumid) && $login ) {
						$button_html[] = '<span wpf-tooltip="'.esc_attr(wpforo_phrase('Tools: Move, Split, Merge', false)).'" wpf-tooltip-size="medium" class="wpf-action wpforo-tools"><i class="fas fa-cog"></i><span class="wpf-button-text">' . wpforo_phrase('Tools', false).' <sep>&nbsp;|</sep> </span></span>';
					}
				 	break; 
				case 'edit':
						if($is_closed) break;
						$diff = current_time( 'timestamp', 1 ) - strtotime($post['created']);
						if( !$login && isset($post['email'])
                                && wpforo_is_owner($post['userid'], $post['email'])
                                    && WPF()->perm->forum_can( ($is_topic ? 'eot' : 'eor' ), $forumid )
                                        && $diff < WPF()->post->options[($is_topic ? 'eot' : 'eor' ).'_durr']
							  ) {
								$a = ( $is_topic ) ? 'wpfedittopicpid' : ''; 
								$b = ( $is_topic ) ? $postid : $postid;
								$button_html[] = '<span wpf-tooltip="'.esc_attr(wpforo_phrase('Edit', false)).'" id="'. esc_attr( $a . $b ) .'" class="wpforo-edit wpf-action"><i class="fas fa-edit wpfsx"></i><span class="wpf-button-text">' . wpforo_phrase('Edit', false).'</span></span>';
							
						}
						elseif( $login ) {
							if( WPF()->perm->forum_can( ($is_topic ? 'et' : 'er'), $forumid ) || 
							   		(	WPF()->current_userid == $post['userid'] 
									 	&& WPF()->perm->forum_can( ($is_topic ? 'eot' : 'eor' ), $forumid ) 
									 	&& ( WPF()->post->options[($is_topic ? 'eot' : 'eor' ).'_durr'] == 0 ||
                                            $diff < WPF()->post->options[($is_topic ? 'eot' : 'eor' ).'_durr'])
									) 
							  ) {
								$a = ( $is_topic ) ? 'wpfedittopicpid' : ''; 
								$b = ( $is_topic ) ? $postid : $postid;
								$button_html[] = '<span wpf-tooltip="'.esc_attr(wpforo_phrase('Edit', false)).'" id="'. esc_attr( $a . $b ) .'" class="wpforo-edit wpf-action"><i class="fas fa-edit wpfsx"></i><span class="wpf-button-text">' . wpforo_phrase('Edit', false).'</span></span>';
							}
						} 
				 	break; 
				case 'delete':
					if( $login ){
						//if( WPF()->member->current_user_is_new() && $post['status'] ){
							//New registered user's unapproved topic/post | No Delete button. 
						//}
						//else{
							$diff = current_time( 'timestamp', 1 ) - strtotime($post['created']);
							if( WPF()->perm->forum_can( ($is_topic ? 'dt' : 'dr' ), $forumid ) ||
                                (WPF()->current_userid == $post['userid'] &&
                                    WPF()->perm->forum_can( ($is_topic ? 'dot' : 'dor' ), $forumid ) &&
                                    ( WPF()->post->options[($is_topic ? 'dot' : 'dor' ).'_durr'] == 0 ||
                                        $diff < WPF()->post->options[($is_topic ? 'dot' : 'dor' ).'_durr'])
                                )
                            ){
								$a = ( $is_topic ) ? 'wpftopicdelete' : 'wpfreplydelete'; 
								$b = ( $is_topic ) ? $topicid : $postid;
								$button_html[] = '<span wpf-tooltip="'.esc_attr(wpforo_phrase('Delete', false)).'" id="'. esc_attr( $a . $b ) .'" class="wpf-action wpforo-delete"><i class="fas fa-trash-alt wpfsx"></i><span class="wpf-button-text">' . wpforo_phrase('Delete', false).'</span></span>';
							}
						//}
					}
				 	break; 
				case 'link':
					$url = ( $is_topic ) ? WPF()->topic->get_topic_url( $topic ) : wpforo_post( $postid, 'url' );
					$button_html[] = '<a wpf-tooltip="'.esc_attr(wpforo_phrase('Post link', false)).'" href="'. esc_url($url) .'"><i class="fas fa-link wpfsx"></i></a>';
				 	break; 
				case 'positivevote':
					if( WPF()->perm->forum_can('v', $forumid) && $login ) {
						$button_html[] = '<i class="wpforo-voteup fas fa-play fa-rotate-270 wpfcl-0" data-type="' . ( $is_topic ? 'topic' : 'reply' ) . '" data-postid="'. wpforo_bigintval($postid) .'"></i>';
					}else{
						$button_html[] = '<i class="not_reg_user fas fa-play fa-rotate-270 wpfcl-0"></i>';
					}
				 	break; 
				case 'negativevote':
					if( WPF()->perm->forum_can('v', $forumid) && $login ) {
						$button_html[] = '<i class="wpforo-votedown fas fa-play fa-rotate-90 wpfcl-0" data-type="' . ( $is_topic ? 'topic' : 'reply' ) . '" data-postid="'. wpforo_bigintval($postid) .'"></i>';
					}else{
						$button_html[] = '<i class="not_reg_user fas fa-play fa-rotate-90 wpfcl-0"></i>';
					}
				 	break; 
				case 'isanswer':
					if ( ! $is_topic ) {
						$has_is_answer_post = WPF()->topic->has_is_answer_post( $post['topicid'] );
						$is_answer          = (bool) WPF()->post->is_answered( $postid );
						$class              = ( $is_answer ) ? '' : '-not';
						if ( ! $has_is_answer_post || ( $has_is_answer_post && $is_answer ) ) {
							if ( $login ) {
								$button_html[] = '<div class="wpf-toggle' . esc_attr( $class ) . '-answer" data-postid="' . wpforo_bigintval( $postid ) . '"><i class="fas fa-check"></i></div>';
							} else {
								$button_html[] = '<div class="wpf-toggle' . esc_attr( $class ) . '-answer not_reg_user"><i class="fas fa-check"></i></div>';
							}
						}
					}
				 	break;
			} //switch
		} //foreach

        $before = '<span class="wpforo-action-buttons-wrap">';
        $after = '</span>';
        $html = $before . implode('', $button_html) . $after;
        if( !$echo ) return $html;
        echo $html; return '';
	}

    /**
     * display QA Layout Votes count for loop current post
     * @param array $post loop current post array
     * @return void
     */
	public function vote_count($post){
	    $votes = ( !empty($post['votes']) ? $post['votes'] : 0 );
	    printf('<span class="wpfvote-num wpfcl-0">%d</span>', $votes);
    }

	function breadcrumb($url_data){

		extract($url_data, EXTR_OVERWRITE); $lenght = apply_filters( 'wpforo_breadcrumb_text_length', 19 ); ?>

        <style>.wpf-item-element{display: inline;}</style>
        <div class="wpf-breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">
		<?php switch($template) :
                case 'search': ?>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element wpf-root" ><a itemprop="item" href="<?php echo wpforo_home_url() ?>" title="<?php esc_attr( wpforo_phrase('Forums') ) ?>"><i class="fas fa-home"></i><span itemprop="name" style="display:none;"><?php wpforo_phrase('Forums') ?></span></a><meta itemprop="position" content="1"></div>
                    <div class="wpf-item-element active"><span><?php wpforo_phrase('Search') ?></span></div>
                    <a href="#" class="wpf-end">&nbsp;</a>
                <?php break; case 'signup': ?>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element wpf-root" ><a itemprop="item" href="<?php echo wpforo_home_url() ?>" title="<?php esc_attr( wpforo_phrase('Forums') ) ?>"><i class="fas fa-home"></i><span itemprop="name" style="display:none;"><?php wpforo_phrase('Forums') ?></span></a><meta itemprop="position" content="1"></div>
                    <div class="wpf-item-element active"><span><?php wpforo_phrase('Register') ?></span></div>
                    <a href="#" class="wpf-end">&nbsp;</a>
                <?php break; case 'signin': ?>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element wpf-root" ><a itemprop="item" href="<?php echo wpforo_home_url() ?>" title="<?php esc_attr( wpforo_phrase('Forums') ) ?>"><i class="fas fa-home"></i><span itemprop="name" style="display:none;"><?php wpforo_phrase('Forums') ?></span></a><meta itemprop="position" content="1"></div>
                    <div class="wpf-item-element active"><span><?php wpforo_phrase('Login') ?></span></div>
                    <a href="#" class="wpf-end">&nbsp;</a>
                <?php break; case 'members': ?>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element wpf-root" ><a itemprop="item" href="<?php echo wpforo_home_url() ?>" title="<?php esc_attr( wpforo_phrase('Forums') ) ?>"><i class="fas fa-home"></i><span itemprop="name" style="display:none;"><?php wpforo_phrase('Forums') ?></span></a><meta itemprop="position" content="1"></div>
                    <?php if(isset($_GET['wpfms'])) : ?>
                        <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element"><a itemprop="item" href="<?php echo wpforo_home_url($this->slugs['members']) ?>"><?php wpforo_phrase('Members') ?></a><meta itemprop="position" content="2"></div>
                    <div class="wpf-item-element active"><span><?php wpforo_phrase('Search') ?></span></div>
                    <?php else : ?>
                    <div class="wpf-item-element active"><span><?php wpforo_phrase('Members') ?></span></div>
                    <?php endif ?>
                    <a href="#" class="wpf-end">&nbsp;</a>
                <?php break; case 'recent': ?>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element wpf-root"><a itemprop="item" href="<?php echo wpforo_home_url() ?>" title="<?php esc_attr( wpforo_phrase('Forums') ) ?>"><i class="fas fa-home"></i><span itemprop="name" style="display:none;"><?php wpforo_phrase('Forums') ?></span></a><meta itemprop="position" content="1"></div>
                        <?php if( wpfval($_GET, 'view') && $_GET['view'] == 'unread' ): ?>
                    <div class="wpf-item-element active"><span><?php wpforo_phrase('Unread Posts') ?></span></div>
                        <?php else: ?>
                    <div class="wpf-item-element active"><span><?php wpforo_phrase('Recently Added') ?></span></div>
                        <?php endif; ?>
                    <a href="#" class="wpf-end">&nbsp;</a>
                <?php break; case 'tags': ?>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element wpf-root"><a itemprop="item" href="<?php echo wpforo_home_url() ?>" title="<?php esc_attr( wpforo_phrase('Forums') ) ?>"><i class="fas fa-home"></i><span itemprop="name" style="display:none;"><?php wpforo_phrase('Forums') ?></span></a><meta itemprop="position" content="1"></div>
                    <div class="wpf-item-element active"><span><?php wpforo_phrase('Tags') ?></span></div>
                    <a href="#" class="wpf-end">&nbsp;</a>
                <?php break; case 'profile': ?>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element wpf-root"><a itemprop="item" href="<?php echo wpforo_home_url() ?>" title="<?php esc_attr( wpforo_phrase('Forums') ) ?>"><i class="fas fa-home"></i><span itemprop="name" style="display:none;"><?php wpforo_phrase('Forums') ?></span></a><meta itemprop="position" content="1"></div>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element"><a itemprop="item" href="<?php echo wpforo_home_url($this->slugs['members']) ?>"><span itemprop="name"><?php wpforo_phrase('Members') ?></span></a><meta itemprop="position" content="2"></div>
                    <div class="wpf-item-element active"><span><?php @wpforo_text( wpforo_make_dname($user['display_name'], $user['user_nicename']), $lenght ) ?></span></div>
                    <a href="#" class="wpf-end">&nbsp;</a>
                <?php break; case 'account': ?>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element wpf-root"><a itemprop="item" href="<?php echo wpforo_home_url() ?>" title="<?php esc_attr( wpforo_phrase('Forums') ) ?>"><i class="fas fa-home"></i><span itemprop="name" style="display:none;"><?php wpforo_phrase('Forums') ?></span></a><meta itemprop="position" content="1"></div>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element"><a itemprop="item" href="<?php echo wpforo_home_url($this->slugs['members']) ?>"><span itemprop="name"><?php wpforo_phrase('Members') ?></span></a><meta itemprop="position" content="2"></div>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element"><a itemprop="item" href="<?php echo esc_url($user['profile_url']) ?>"><span itemprop="name"><?php wpforo_text( wpforo_make_dname($user['display_name'], $user['user_nicename']), $lenght ) ?></span></a><meta itemprop="position" content="3"></div>
                    <div class="wpf-item-element active"><span><?php wpforo_phrase('Account') ?></span></div>
                    <a href="#" class="wpf-end">&nbsp;</a>
                <?php break; case 'activity': ?>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element wpf-root"><a itemprop="item" href="<?php echo wpforo_home_url() ?>" title="<?php esc_attr( wpforo_phrase('Forums') ) ?>"><i class="fas fa-home"></i><span itemprop="name" style="display:none;"><?php wpforo_phrase('Forums') ?></span></a><meta itemprop="position" content="1"></div>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element" ><a itemprop="item" href="<?php echo wpforo_home_url($this->slugs['members']) ?>"><span itemprop="name"><?php wpforo_phrase('Members') ?></span></a><meta itemprop="position" content="2"></div>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element" ><a itemprop="item" href="<?php echo esc_url($user['profile_url']) ?>"><span itemprop="name"><?php wpforo_text( wpforo_make_dname($user['display_name'], $user['user_nicename']), $lenght ) ?></span></a><meta itemprop="position" content="3"></div>
                    <div class="wpf-item-element active"><span><?php wpforo_phrase('Activity') ?></span></div>
                    <a href="#" class="wpf-end">&nbsp;</a>
                <?php break; case 'subscriptions': ?>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element wpf-root"><a itemprop="item" href="<?php echo wpforo_home_url() ?>" title="<?php esc_attr( wpforo_phrase('Forums') ) ?>"><i class="fas fa-home"></i><span itemprop="name" style="display:none;"><?php wpforo_phrase('Forums') ?></span></a><meta itemprop="position" content="1"></div>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element"><a itemprop="item" href="<?php echo wpforo_home_url($this->slugs['members']) ?>"><span itemprop="name"><?php wpforo_phrase('Members') ?></span></a><meta itemprop="position" content="2"></div>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element"><a itemprop="item" href="<?php echo esc_url($user['profile_url']) ?>"><span itemprop="name"><?php wpforo_text( wpforo_make_dname($user['display_name'], $user['user_nicename']), $lenght ) ?></span></a><meta itemprop="position" content="3"></div>
                    <div class="wpf-item-element active"><span><?php wpforo_phrase('Subscriptions') ?></span></div>
                    <a href="#" class="wpf-end">&nbsp;</a>
                <?php break; case 'messages': ?>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-root"><a itemprop="item" href="<?php echo wpforo_home_url() ?>" title="<?php esc_attr( wpforo_phrase('Forums') ) ?>"><i class="fas fa-home"></i><span itemprop="name" style="display:none;"><?php wpforo_phrase('Forums') ?></span></a><meta itemprop="position" content="1"></div>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element"><a itemprop="item" href="<?php echo wpforo_home_url($this->slugs['members']) ?>"><span itemprop="name"><?php wpforo_phrase('Members') ?></span></a><meta itemprop="position" content="2"></div>
                    <?php if(!empty($user)) : ?>
                        <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element"><a itemprop="item" href="<?php echo esc_url($user['profile_url']) ?>"><span itemprop="name"><?php wpforo_text( wpforo_make_dname($user['display_name'], $user['user_nicename']), $lenght ) ?></span></a><meta itemprop="position" content="3"></div>
                    <?php endif ?>
                    <div class="wpf-item-element active"><span><?php wpforo_phrase('Messages') ?></span></div>
                    <a class="wpf-end">&nbsp;</a>
                <?php break; case 'topic': ?>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element wpf-root<?php echo ( !isset($forumid) ? ' active' : '' ) ?>"><a itemprop="item" href="<?php echo ( !isset($forumid) ? '#' : wpforo_home_url() ) ?>" title="<?php esc_attr( wpforo_phrase('Forums') ) ?>"><i class="fas fa-home"></i><span itemprop="name" style="display:none;"><?php wpforo_phrase('Forums') ?></span></a><meta itemprop="position" content="1"></div>
                    <?php if(isset($forumid)) : ?>
                        <?php $relative_ids = array();
                        WPF()->forum->get_parents($forumid, $relative_ids);
                        foreach( $relative_ids as $key => $rel_forumid ) : ?>
                            <?php $forum = wpforo_forum($rel_forumid) ?>
                            <?php if(!empty($forum)): ?>
                                <?php if( $key != ( count($relative_ids) - 1 ) ) : ?>
                                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element"><a itemprop="item" href="<?php echo esc_url( $forum['url'] ) ?>" title="<?php echo esc_attr($forum['title']) ?>"><span itemprop="name"><?php wpforo_text($forum['title'], $lenght) ?></span></a><meta itemprop="position" content="<?php echo $key+2; ?>"></div>
                                <?php else : ?>
                                    <div class="wpf-item-element active"><span><?php wpforo_text($forum['title'], $lenght) ?></span></div>
                                <?php endif ?>
                            <?php endif ?>
                        <?php endforeach ?>
                    <?php endif ?>
                    <a href="#" class="wpf-end">&nbsp;</a>
                <?php break; case 'post': $key = 0; ?>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element wpf-root<?php echo ( !isset($forumid) ? ' active' : '' ) ?>"><a itemprop="item" href="<?php echo ( !isset($forumid) ? '#' : wpforo_home_url() ) ?>" title="<?php esc_attr( wpforo_phrase('Forums') ) ?>"><i class="fas fa-home"></i><span itemprop="name" style="display:none;"><?php wpforo_phrase('Forums') ?></span></a><meta itemprop="position" content="1"></div>
                    <?php if(isset($forumid)) : ?>
                        <?php $relative_ids = array();
                        WPF()->forum->get_parents($forumid, $relative_ids);
                        foreach( $relative_ids as $key => $rel_forumid ) : ?>
                            <?php $forum = wpforo_forum($rel_forumid) ?>
                            <?php if(!empty($forum)): ?>
                                <div class="wpf-item-element" itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem"><a itemprop="item" href="<?php echo esc_url( $forum['url'] ) ?>" title="<?php echo esc_attr($forum['title']) ?>"><span itemprop="name"><?php wpforo_text($forum['title'], $lenght) ?></span></a><meta itemprop="position" content="<?php echo $key+2; ?>"></div>
                            <?php endif ?>
                        <?php endforeach ?>
                    <?php endif ?>
                    <?php if(!empty($topic)) : ?>
                        <div class="wpf-item-element active"><span><?php wpforo_text($topic['title'], $lenght) ?></span></div>
                    <?php endif ?>
                    <a href="#" class="wpf-end">&nbsp;</a>
                <?php break; default: ?>
                    <?php if( wpfval(WPF()->current_object, 'forumid') ): ?>
                        <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element wpf-root<?php echo ( !isset($forumid) ? ' active' : '' ) ?>"><a itemprop="item" href="<?php echo ( !isset($forumid) ? '#' : wpforo_home_url() ) ?>" title="<?php esc_attr( wpforo_phrase('Forums') ) ?>"><i class="fas fa-home"></i><span itemprop="name" style="display:none;"><?php wpforo_phrase('Forums') ?></span></a><meta itemprop="position" content="1"></div>
                        <a href="#" class="wpf-end">&nbsp;</a>
                    <?php else: ?>
                    <div itemprop="itemListElement" itemscope="" itemtype="https://schema.org/ListItem" class="wpf-item-element wpf-root active"><a itemprop="item" href="<?php echo wpforo_home_url() ?>" title="<?php esc_attr( wpforo_phrase('Forums') ) ?>"><span itemprop="name"><?php wpforo_phrase('Forums') ?></span></a><meta itemprop="position" content="1"></div>
                        <a href="#" class="wpf-end">&nbsp;</a>
                    <?php endif ?>
			<?php endswitch; ?>
		</div>
		<?php
	}
	
	function icon($type, $item = array(), $echo = true, $data = 'icon' ){
		
		$icon = array();
		$status = false;
		
		if( isset($item['status']) && $item['status'] ){
			$icon['class'] = 'fas fa-exclamation-circle';
			$icon['color'] = 'wpfcl-5';
			$icon['title'] = wpforo_phrase('Unapproved', false);
			if($echo) { 
				$status = true; echo ($data == 'icon') ? implode(' ', $icon) : $icon['title']; 
			} 
			else{ 
				return ($data == 'icon') ? implode(' ', $icon) : $icon['title']; 
			}
		}
		
		if(isset($item['type'])){
			
			if( $type == 'topic' ){
				if(WPF()->topic->is_private($item['topicid'])){
					$icon['class'] = 'fas fa-eye-slash';
					$icon['color'] = 'wpfcl-1';
					$icon['title'] = wpforo_phrase('Private', false);
					if($echo) {
						$status = true; echo ($data == 'icon') ? implode(' ', $icon) : $icon['title'];
					}
					else{
						return ($data == 'icon') ? implode(' ', $icon) : $icon['title'];
					}
				}
				if( wpforo_topic($item['topicid'], 'solved') ){
					$icon['class'] = 'fas fa-check-circle';
					$icon['color'] = 'wpfcl-8';
					$icon['title'] = wpforo_phrase('Solved', false);
					if($echo) {
						$status = true; echo ($data == 'icon') ? implode(' ', $icon) : $icon['title'];
					}
					else{
						return ($data == 'icon') ? implode(' ', $icon) : $icon['title'];
					}
				}
			}

			if( $item['closed'] && $item['type'] == 1 ){
				$icon['class'] = 'fas fa-lock';
				$icon['color'] = 'wpfcl-1';
				$icon['title'] = wpforo_phrase('Closed', false);
				if($echo) { $status = true; echo ($data == 'icon') ? implode(' ', $icon) : $icon['title']; } else{ return ($data == 'icon') ? implode(' ', $icon) : $icon['title']; }
			}
			elseif( $item['closed'] && $item['type'] != 1  ){
				$icon['class'] = 'fas fa-lock';
				$icon['color'] = 'wpfcl-1';
				$icon['title'] = wpforo_phrase('Closed', false);
				if($echo) { $status = true; echo ($data == 'icon') ? implode(' ', $icon) : $icon['title']; } else{ return ($data == 'icon') ? implode(' ', $icon) : $icon['title']; }
			}
			elseif( !$item['closed'] && $item['type'] == 1  ){
				$icon['class'] = 'fas fa-thumbtack';
				$icon['color'] = 'wpfcl-5';
				$icon['title'] = wpforo_phrase('Sticky', false);
				if($echo) { $status = true; echo ($data == 'icon') ? implode(' ', $icon) : $icon['title']; } else{ return ($data == 'icon') ? implode(' ', $icon) : $icon['title']; }
			}
			
			if( $status ){
				//do nothing
			}
			else{
				if( $type == 'forum' ){
					$icon['class'] = 'fas fa-comments';
					$icon['color'] = 'wpfcl-2';
				}
				elseif( $type == 'topic' ){
                    $icon = $this->icon_status( $item['posts'] );
				}
				if($echo) {
				    echo ($data == 'icon') ? implode(' ', $icon) : ( wpfval($icon,'title') ? $icon['title']: '' );
				} else{
				    return ($data == 'icon') ? implode(' ', $icon) : ( wpfval($icon,'title') ? $icon['title']: '' );
				}
			}
			
		}
		else{
			return false;
		}
		
	}

	function icon_status( $item ){
        $icon = array();
        if( wpfval($item, 'type')){
            $icon['sticky']['class'] = 'fas fa-thumbtack';
            $icon['sticky']['color'] = 'wpfcl-5';
            $icon['sticky']['title'] = wpforo_phrase('Sticky', false);
        }
        if( wpfval($item,'topicid') && wpforo_topic($item['topicid'], 'solved')){
            $icon['is_answer']['class'] = 'fas fa-check-circle';
            $icon['is_answer']['color'] = 'wpfcl-8';
            $icon['is_answer']['title'] = wpforo_phrase('Solved', false);
        }
        if( wpfval($item, 'closed')){
            $icon['closed']['class'] = 'fas fa-lock';
            $icon['closed']['color'] = 'wpfcl-1';
            $icon['closed']['title'] = wpforo_phrase('Closed', false);
        }
        if( wpfval($item, 'status')){
            $icon['status']['class'] = 'fas fa-exclamation-circle';
            $icon['status']['color'] = 'wpfcl-5';
            $icon['status']['title'] = wpforo_phrase('Unapproved', false);
        }
        if( wpfval($item,'private')){
            $icon['private']['class'] = 'fas fa-eye-slash';
            $icon['private']['color'] = 'wpfcl-1';
            $icon['private']['title'] = wpforo_phrase('Private', false);
        }
        return $icon;
    }

    function icon_base( $post_count ){
        $icon = array();
        if( $post_count < 2 ){
            $icon['class'] = 'far fa-file';
            $icon['color'] = 'wpfcl-2';
            $icon['title'] = wpforo_phrase('Not Replied', false);
        }
        elseif( $post_count > 1 && $post_count <= 5 ){
            $icon['class'] = 'far fa-file-alt';
            $icon['color'] = 'wpfcl-2';
            $icon['title'] = wpforo_phrase('Replied', false);
        }
        elseif( $post_count > 5 && $post_count <= 20 ){
            $icon['class'] = 'fas fa-file-alt';
            $icon['color'] = 'wpfcl-2';
            $icon['title'] = wpforo_phrase('Active', false);
        }
        elseif( $post_count > 20 ){
            $icon['class'] = 'fas fa-file-alt';
            $icon['color'] = 'wpfcl-5';
            $icon['title'] = wpforo_phrase('Hot', false);
        }
        else{
            $icon['class'] = 'far fa-file';
            $icon['color'] = 'wpfcl-2';
            $icon['title'] = '';
        }
        return $icon;
    }

	public function member_buttons( $member ){
		if(empty($member)) return;
		$profile_access = ( WPF()->perm->usergroup_can('vprf') ?  true : false );
		
		if( $profile_access ){
			?>
			<a class="wpf-member-profile-button" title="<?php wpforo_phrase('Profile') ?>" href="<?php echo esc_url(WPF()->member->profile_url($member)) ?>">
				<i class="fas fa-user"></i>
			</a>
			<a class="wpf-member-profile-button" title="<?php wpforo_phrase('Activity') ?>" href="<?php echo esc_url(WPF()->member->profile_url($member, 'activity')) ?>">
				<i class="far fa-comments"></i>
			</a>
			<a class="wpf-member-profile-button" title="<?php wpforo_phrase('Subscriptions') ?>" href="<?php echo esc_url(WPF()->member->profile_url($member, 'subscriptions')) ?>">
				<i class="fas fa-rss"></i>
			</a>
			<?php do_action( 'wpforo_member_info_buttons', $member ); ?>
			<?php
		}
	}
	
	public function member_social_buttons( $member ){
		
		$socnets = array();
		if(empty($member)) return;
		$social_access = ( WPF()->perm->usergroup_can('vmsn') ?  true : false );
		
		if( $social_access ){
			
			if( isset($member['facebook']) && $member['facebook'] ){
				$socnets['facebook']['set'] = $member['facebook'];
				$member['facebook'] = ( strpos($member['facebook'], 'facebook.com') === FALSE ) ? 'https://www.facebook.com/' . trim($member['facebook'], '/') : $member['facebook'] ;
				$socnets['facebook']['value'] = $member['facebook'];
				$socnets['facebook']['protocol'] = 'https://';
				$socnets['facebook']['title'] = wpforo_phrase('Facebook', false);
			}
			
			if( isset($member['twitter']) && $member['twitter'] ){
				$socnets['twitter']['set'] = $member['twitter'];
				$member['twitter'] = ( strpos($member['twitter'], 'twitter.com') === FALSE ) ? 'http://twitter.com/' . trim($member['twitter'], '/') : $member['twitter'] ;
				$socnets['twitter']['value'] = $member['twitter'];
				$socnets['twitter']['protocol'] = 'https://';
				$socnets['twitter']['title'] = wpforo_phrase('Twitter', false);
			}
			
			if( isset($member['gtalk']) && $member['gtalk'] ){
				$socnets['gtalk']['set'] = $member['gtalk'];
				$socnets['gtalk']['value'] = $member['gtalk'];
				$socnets['gtalk']['protocol'] = 'https://';
				$socnets['gtalk']['title'] = wpforo_phrase('Google+', false);
			}
			
			if( isset($member['yahoo']) && $member['yahoo'] ){
				$socnets['yahoo']['set'] = $member['yahoo'];
				$socnets['yahoo']['value'] = $member['yahoo'];
				$socnets['yahoo']['protocol'] = 'mailto:';
				$socnets['yahoo']['title'] = wpforo_phrase('Yahoo', false);
			}
			
			if( isset($member['aim']) && $member['aim'] ){
				$socnets['aim']['set'] = $member['aim'];
				$socnets['aim']['value'] = $member['aim'];
				$socnets['aim']['protocol'] = 'mailto:';
				$socnets['aim']['title'] = wpforo_phrase('AOL IM', false);
			}
			
			if( isset($member['icq']) && $member['icq'] ){
				$socnets['icq']['set'] = $member['icq'];
				$socnets['icq']['value'] = 'www.icq.com/whitepages/cmd.php?uin=' . $member['icq'] . '&action=message';
				$socnets['icq']['protocol'] = 'https://';
				$socnets['icq']['title'] = wpforo_phrase('ICQ', false);
			}
			
			if( isset($member['msn']) && $member['msn'] ){
				$socnets['msn']['set'] = $member['msn'];
				$socnets['msn']['value'] = $member['msn'];
				$socnets['msn']['protocol'] = 'mailto:';
				$socnets['msn']['title'] = wpforo_phrase('MSN', false);
			}
			
			if( isset($member['skype']) && $member['skype'] ){
				$socnets['skype']['set'] = $member['skype'];
				$socnets['skype']['value'] = $member['skype'];
				$socnets['skype']['protocol'] = 'skype:';
				$socnets['skype']['title'] = wpforo_phrase('Skype', false);
			}
			
			?>
            <div class="wpf-member-socnet-wrap">
				<?php if(!empty($socnets)): ?>
					<?php foreach( $socnets as $key => $socnet ): ?>
                        <?php if( !$socnet['set'] ) continue; ?>
                        <?php $title = $member['display_name'] . ' - ' . $socnet['title']; ?>
                        <?php $url = ($key == 'skype') ? 'skype:' . esc_attr($socnet['value']) : esc_url($socnet['protocol'] . str_replace( array('https://', 'http://', 'skype:', 'mailto:'), '', $socnet['value'])); ?>
                        <a href="<?php echo $url ?>" class="wpf-member-socnet-button" title="<?php echo esc_attr($title) ?>">
                            <img src="<?php echo esc_url(WPFORO_URL) ?>/wpf-assets/images/sn/<?php echo $key ?>.png" alt="<?php echo esc_attr($title) ?>" title="<?php echo esc_attr($title) ?>" />
                        </a> 
                    <?php endforeach; ?>
                <?php endif; ?>
            	<?php do_action( 'wpforo_member_socnet_buttons', $member ); ?>
            </div>
			<?php
		}
	}
	
	public function init_member_templates(){
		WPF()->member_tpls = array(
			'account' => wpftpl('profile-account.php'),
			'activity' => wpftpl('profile-activity.php'),
			'subscriptions' => wpftpl('profile-subscriptions.php')
		);
		WPF()->member_tpls = apply_filters('wpforo_member_templates_filter', WPF()->member_tpls);
		WPF()->member_tpls['profile'] = wpftpl('profile-home.php');
	}
	
	function has_menu(){
		return has_nav_menu( 'wpforo-menu' );
	}
	
	function nav_menu(){
		if ( has_nav_menu( 'wpforo-menu' ) ){
			$defaults = array(
				'theme_location'  => 'wpforo-menu',
				'menu'            => '',
				'container'       => '',
				'container_class' => '',
				'container_id'    => '',
				'menu_class'      => 'wpf-menu',
				'menu_id'         => 'wpf-menu',
				'echo'            => true,
				'fallback_cb'     => 'wp_page_menu',
				'before'          => '',
				'after'           => '',
				'link_before'     => '',
				'link_after'      => '',
				'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
				'depth'           => 0,
				'walker'          => ''
			);
			wp_nav_menu( $defaults );
		}
	}
	
	function init_nav_menu(){
		
		if(isset(WPF()->current_object) && !empty(WPF()->current_object)){
			
			extract(WPF()->current_object, EXTR_OVERWRITE);
			
			WPF()->menu['wpforo-home'] = array(
				'href' => wpforo_home_url(),
				'label' => wpforo_phrase('forums', FALSE),
				'attr' => ( $template == 'forum' || $template == 'topic' || $template == 'post' ? ' class="wpforo-active"' : '' ),
				'submenues' => array()
			);
			
			if(WPF()->perm->usergroup_can('vmem')){
				WPF()->menu['wpforo-members'] = array(
					'href' => wpforo_home_url($this->slugs['members']),
					'label' => wpforo_phrase('members', FALSE),
					'attr' => ( $template == 'members' ? ' class="wpforo-active"' : '' ),
					'submenues' => array()
				);
			}
			
			WPF()->menu['wpforo-recent'] = array(
				'href' => wpforo_home_url($this->slugs['recent']),
				'label' => wpforo_phrase('Recent Posts', FALSE),
				'attr' => ( ( !wpfval($_GET, 'view') && $template == 'recent' ) ? ' class="wpforo-active"' : '' ),
				'submenues' => array()
			);

            WPF()->menu['wpforo-unread'] = array(
                'href' => wpforo_home_url($this->slugs['recent'] . '?view=unread'),
                'label' => wpforo_phrase('Unread Posts', FALSE),
                'attr' => ( ( wpfval($_GET, 'view') && $_GET['view'] == 'unread') ? ' class="wpforo-active"' : '' ),
                'submenues' => array()
            );

            WPF()->menu['wpforo-tags'] = array(
                'href' => wpforo_home_url($this->slugs['tags']),
                'label' => wpforo_phrase('Tags', FALSE),
                'attr' => ( $template == 'tags' ? ' class="wpforo-active"' : '' ),
                'submenues' => array()
            );
			
			if( is_user_logged_in() ){

			    $member_id = WPF()->current_userid;
			    $url_profile = WPF()->member->get_profile_url($member_id, 'profile');
			    $url_account = WPF()->member->get_profile_url($member_id, 'account');
                $url_activity = WPF()->member->get_profile_url($member_id, 'activity');
                $url_subscriptions = WPF()->member->get_profile_url($member_id, 'subscriptions');

				WPF()->menu['wpforo-profile-home'] = array(
					'href' => $url_profile,
					'label' => wpforo_phrase('my profile', FALSE),
					'attr' => ( isset(WPF()->member_tpls[$template]) && WPF()->member_tpls[$template] && WPF()->current_object['user_is_same_current_user'] ? ' class="wpforo-active"' : '' ),
					'submenues' => array()
				);
				WPF()->menu['wpforo-profile-account'] = array(
					'href' => $url_account,
					'label' => wpforo_phrase('account', FALSE),
					'attr' => ( $template == 'account' && WPF()->current_object['user_is_same_current_user'] ? ' class="wpforo-active"' : '' ),
					'submenues' => array()
				);
				WPF()->menu['wpforo-profile-activity'] = array(
					'href' => $url_activity,
					'label' => wpforo_phrase('activity', FALSE),
					'attr' => ( $template == 'activity' && WPF()->current_object['user_is_same_current_user'] ? ' class="wpforo-active"' : '' ),
					'submenues' => array()
				);
				WPF()->menu['wpforo-profile-subscriptions'] = array(
					'href' => $url_subscriptions,
					'label' => wpforo_phrase('subscriptions', FALSE),
					'attr' => ( $template == 'subscriptions' && WPF()->current_object['user_is_same_current_user'] ? ' class="wpforo-active"' : '' ),
					'submenues' => array()
				);
				WPF()->menu['wpforo-logout'] = array(
					'href' => wpforo_home_url('?wpforo=logout'),
					'label' => wpforo_phrase('logout', FALSE),
					'attr' => '',
					'submenues' => array()
				);
				
			}else{
				
				if( wpforo_feature('user-register') ){
					WPF()->menu['wpforo-register'] = array(
						'href' => wpforo_register_url(),
						'label' => wpforo_phrase('register', FALSE),
						'attr' => ( isset($_GET['wpforo']) && $_GET['wpforo'] == 'signup' ? ' class="wpforo-active"' : '' ),
						'submenues' => array()
					);
				}
				WPF()->menu['wpforo-login'] = array(
					'href' => wpforo_login_url(),
					'label' => wpforo_phrase('login', FALSE),
					'attr' => ( isset($_GET['wpforo']) && $_GET['wpforo'] == 'signin' ? ' class="wpforo-active"' : '' ),
					'submenues' => array()
				);
			}
			
			WPF()->menu = apply_filters('wpforo_menu_array_filter', WPF()->menu);
		}
	}
	
	/**
	*
	* Checks in current active theme options if certain layout exists.
	*
	* @since 1.0.0
	*
	* @param  mixed 	$identifier			Layout id (folder name) OR @layout variable in header ( 1 or Extended )
	* @param  string	$identifier_type	The type of first parameter 'id' OR 'name' (@layout)
	*
	* @return boolean						true/false
	* 
	**/
	function layout_exists( $identifier, $identifier_type = 'id' ){
		
		$layouts = $this->options['layouts'];
		
		if( $identifier_type == 'id' ){
			if( isset($layouts[$identifier]) && !empty($layouts[$identifier])){
				return true;
			}
			else{
				return false;
			}
		}
		elseif( $identifier_type = 'name' ){
			foreach( $layouts as $id => $layout ){
				if( !isset($layout['name']) && $layout['name'] == $identifier ){
					return true;
				}
			}
			return false;
		}

		return false;
	}
	
	/**
	*
	* Finds and returns all layouts information in array from theme's /layouts/ folder
	*
	* @since 1.0.0
	*
	* @param  string 	$theme		Theme id ( folder name ) e.g. 'classic'
	*
	* @return array
	* 
	**/
	function find_layouts( $theme ){
		$layout_data = array();
		$layouts = $this->find_themes('/'.$theme.'/layouts', 'php', 'layout');
		if(!empty($layouts)){
			foreach( $layouts as $layout ){
				$lid = trim(basename(dirname( $layout['file']['value'] )), '/');
				$layout_data[$lid]['id'] = intval($lid);
				$layout_data[$lid]['name'] = esc_html($layout['name']['value']);
				$layout_data[$lid]['version'] = $layout['version']['value'];
				$layout_data[$lid]['description'] = $layout['description']['value'];
				$layout_data[$lid]['author'] = $layout['author']['value'];
				$layout_data[$lid]['url'] = $layout['layout_url']['value'];
				$layout_data[$lid]['file'] = $layout['file']['value'];
			}
		}
		return $layout_data;
	}
	
	function show_layout_selectbox($layoutid = 0){
		$layouts = $this->find_layouts( WPFORO_THEME );
		if( !empty($layouts) ){
			foreach( $layouts as $layout ) : ?>  
				<option value="<?php echo esc_attr(trim($layout['id'])) ?>" <?php echo ( $layoutid == $layout['id'] ? 'selected' : '' ); ?> ><?php echo esc_html($layout['name']) ?></option>
				<?php
			endforeach;
		}
	}

	/**
	 * @param string $theme theme dir name if empty use current theme dir
	 *
	 * @return string dynamicly generated css
	 */
	public function generate_dynamic_css( $theme = '' ) {
		$css     = '';
		$COLORS  = array();
		$search  = array();
		$replace = array();

		$style  = wpfval( $this->options, 'style' );
		$styles = wpfval( $this->options, 'styles' );
		if ( ! empty( $style ) && ! empty( $styles ) ) {
			foreach ( $styles[ $style ] as $color_key => $color_value ) {
				if ( $color_value ) {
					$search[]                           = '__WPFCOLOR_' . $color_key . '__';
					$replace[]                          = $color_value;
					$COLORS[ 'WPFCOLOR_' . $color_key ] = $color_value;
				}
			}
		}

		$css_matrix = ( $theme ? WPFORO_THEME_DIR . '/' . $theme : WPFORO_TEMPLATE_DIR ) . '/styles/matrix.css';
		if ( file_exists( $css_matrix ) ) {
			$css = wpforo_get_file_content( $css_matrix );
		}

		$css = apply_filters( 'wpforo_dynamic_css_filter', $css, $COLORS );

		$css = str_replace( $search, $replace, $css );

		return trim( $css );
	}
	
	/**
	*
	* Finds and returns styles array from theme's /styles/colors.php file
	*
	* @since 1.0.0
	*
	* @param  string 	$theme		Theme id ( folder name ) e.g. 'classic'
	*
	* @return array
	* 
	**/
	function find_styles( $theme ){
		$colors = array();
		$color_file = WPFORO_THEME_DIR . '/' . $theme . '/styles/colors.php';
		if( file_exists($color_file) ){
			include( $color_file );
		}
		return $colors;
	}
	
	/**
	*
	* Scans certain theme directory and returns all information in array ( theme header, layouts, styles ).
	*
	* @since 1.0.0
	*
	* @param  string 	$theme_file			Theme folder name or main css file base path ( 'classic' OR classic/style.css' )
	*
	* @return array
	* 
	**/
	function find_theme( $theme_file ){
		$theme = array();
		$theme_file = trim(trim($theme_file, '/'));
		
		if( preg_match('|\.[\w\d]{2,4}$|is', $theme_file) ){
			$theme_folder = trim(basename(dirname($theme_file)), '/');
		}
		else{
			$theme_folder = $theme_file;
			$theme_file = $theme_file . '/style.css';
		}
		
		if( !is_readable( WPFORO_THEME_DIR . '/' . $theme_file ) ){
			$theme['error'] = __('Theme file not readable', 'wpforo') .' ('.$theme_file.')';
		}
		else{
			$theme_data = $this->find_theme_headers( WPFORO_THEME_DIR . '/' . $theme_file );
			$theme['id'] = $theme_folder;
			$theme['name'] = $theme_data['name']['value'];
			$theme['version'] = $theme_data['version']['value'];
			$theme['description'] = $theme_data['description']['value'];
			$theme['author'] = $theme_data['author']['value'];
			$theme['url'] = $theme_data['theme_url']['value'];
			$theme['file'] = $theme_file;
			$theme['folder'] = $theme_folder;
			$theme['layouts'] = $this->find_layouts( $theme_folder );
			$styles = $this->find_styles( $theme_folder );
			if(!empty($styles)){
				reset($styles);
				$theme['style'] = key($styles);
				$theme['styles'] = $styles;
			}
        }

        return $theme;
    }
	
	/**
	*
	* Scans wpForo themes (wpf-themes) folder, reads main files' headers and returns information about all themes in array.
	* This function can also be used to scan and get information about layouts in each theme /layouts/ folder.
	*
	* @since 1.0.0
	*
	* @param  string 	$base_dir		Absolute path to scan directory (e.g. /home/public_html/wp-content/plugins/wpforo/wpf-themes/) 
	* @param  string 	$ext			File extension which may contain header information
	* @param  string 	$mode			'theme' or 'layout'
	*
	* @return array
	* 
	**/
	function find_themes( $base_dir = '', $ext = 'css', $mode = 'theme' ){
		$themes = array ();
		$themes_dir = @opendir( WPFORO_THEME_DIR . $base_dir );
		$theme_files = array();
		if( $themes_dir ){
			while( ($file = readdir( $themes_dir )) !== false ){
				if( substr($file, 0, 1) == '.' ) continue;
				if( is_dir( WPFORO_THEME_DIR . $base_dir .'/'.$file ) ){
					$themes_subdir = @opendir( WPFORO_THEME_DIR . $base_dir .'/'.$file );
					if( $themes_subdir ){
						while(($subfile = readdir( $themes_subdir ) ) !== false ){
							if( substr($subfile, 0, 1) == '.' ) continue;
							if( substr($subfile, -4) == '.' . $ext ) $theme_files[] = "$file/$subfile";
						}
						closedir( $themes_subdir );
					}
				} 
				else{
					if( substr($file, -4) == '.' . $ext ) $theme_files[] = $file;
				}
			}
			closedir( $themes_dir );
		}
		if( empty($theme_files) ) return $themes;
		foreach( $theme_files as $theme_file ){
			if( !is_readable( WPFORO_THEME_DIR . $base_dir . '/' . $theme_file ) ) continue;
			if( $mode == 'theme' ){
				$theme_data = $this->find_theme_headers( WPFORO_THEME_DIR . $base_dir . '/' . $theme_file );
			}
			elseif( $mode == 'layout' ){
				$theme_data = $this->find_layout_headers( WPFORO_THEME_DIR . $base_dir . '/' . $theme_file );
			}
			if( empty($theme_data['name']['value']) ) continue;
			$themes[wpforo_clear_basename($theme_file)] = $theme_data;
		}
		return $themes;
	}
	
	/**
	*
	* Reads theme main file's header variables and returns information in array.
	*
	* @since 1.0.0
	*
	* @param  string 	$file	Absolute path to file (e.g. /home/public_html/wp-content/plugins/wpforo/wpf-themes/style.css) 
	*
	* @return array
	* 
	**/
	function find_theme_headers( $file ){
		$theme_headers = array();
		$headers = array(
			'name' => 'Theme Name',
			'version' => 'Version',
			'description' => 'Description',
			'author' => 'Author',
			'theme_url' => 'Theme URI',
		);
		$fp = fopen( $file, 'r' );
		$data = fread( $fp, 8192 );
		fclose( $fp );
		$data = str_replace( "\r", "\n", $data );
		foreach ( $headers as $header_key => $header_name ){
			if ( preg_match( '|^[\s\t\/*#@]*' . preg_quote( $header_name, '|' ) . ':(.*)$|mi', $data, $match ) && $match[1] ){
				$theme_headers[$header_key]['name'] = $header_name;
				$theme_headers[$header_key]['value'] = trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $match[1]));
			}
			else{
				$theme_headers[$header_key]['name'] = $header_name;
				$theme_headers[$header_key]['value'] = '';
			}
		}
		$theme_headers['file']['name'] = 'file';
		$theme_headers['file']['value'] = $file;
		return $theme_headers;
	}
	
	/**
	*
	* Reads layout main file's header variables and returns information in array.
	*
	* @since 1.0.0
	*
	* @param  string 	$file	Absolute path to file (e.g. /home/public_html/wp-content/plugins/wpforo/wpf-themes/layouts/1/forum.php) 
	*
	* @return array
	* 
	**/
	function find_layout_headers( $file ){
		$theme_headers = array();
		$headers = array(
			'name' => 'layout',
			'version' => 'version',
			'description' => 'description',
			'author' => 'author',
			'layout_url' => 'url',
		);
		$fp = fopen( $file, 'r' );
		$data = fread( $fp, 8192 );
		fclose( $fp );
		$data = str_replace( "\r", "\n", $data );
		foreach ( $headers as $header_key => $header_name ){
			if ( preg_match( '|^[\s\t\/*#@]*' . preg_quote( $header_name, '|' ) . ':(.*)$|mi', $data, $match ) && $match[1] ){
				$theme_headers[$header_key]['name'] = $header_name;
				$theme_headers[$header_key]['value'] = trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $match[1]));
			}
			else{
				$theme_headers[$header_key]['name'] = $header_name;
				$theme_headers[$header_key]['value'] = '';
			}
		}
		$theme_headers['file']['name'] = 'file';
		$theme_headers['file']['value'] = trim(str_replace( WPFORO_THEME_DIR, '', $file), '/');
		return $theme_headers;
	}

	public function copyright(){
		if( wpforo_feature('copyright') ): ?>
			<div id="wpforo-poweredby">
		        <p class="wpf-by">
					<span onclick='javascript:document.getElementById("bywpforo").style.display = "inline";document.getElementById("awpforo").style.display = "none";' id="awpforo"> <img align="absmiddle" title="<?php esc_attr( wpforo_phrase('Powered by') ) ?> wpForo version <?php echo esc_html(WPFORO_VERSION) ?>" alt="Powered by wpForo" class="wpdimg" src="<?php echo WPFORO_URL ?>/wpf-assets/images/wpforo-info.png" alt="wpForo"> </span><a id="bywpforo" target="_blank" href="http://wpforo.com/">&nbsp;<?php wpforo_phrase('Powered by') ?> wpForo version <?php echo esc_html(WPFORO_VERSION) ?></a>
				</p>
		    </div>
			<?php 
		endif; 
	}

	public function member_menu( $userid, $menu = array() ){
		if( empty($menu) ) $menu = array('profile' => 'fas fa-user', 'account' => 'fas fa-cog', 'activity' => 'fas fa-comments', 'subscriptions' => 'fas fa-rss');
		$menu = apply_filters('wpforo_member_menu_filter', $menu, $userid);
		if( !($userid == WPF()->current_userid || WPF()->perm->usergroup_can('em')) ) unset($menu['account']);
		if( !($userid == WPF()->current_userid || WPF()->perm->usergroup_can('vpra')) ) unset($menu['activity']);
		if( !($userid == WPF()->current_userid || WPF()->perm->usergroup_can('vprs')) ) unset($menu['subscriptions']);
		foreach( $menu as $key => $value ) :
            ?>
	        <a class="wpf-profile-menu <?php echo ( WPF()->current_object['template'] == $key ? ' wpforo-active' : '' ) ?>" href="<?php echo esc_url( WPF()->member->get_profile_url($userid, $key) ) ?>">
	        	<i class="<?php echo $value ?>"></i> <?php wpforo_phrase($key) ?>
	        </a>
			<?php
		endforeach;
	}

	public function member_template(){
		$permission  = true;
		extract(WPF()->current_object, EXTR_OVERWRITE);
		extract($user, EXTR_OVERWRITE);
		if( $template == 'account' && !($userid == WPF()->current_userid || WPF()->perm->usergroup_can('em')) ) $permission = false;
		if( $template == 'activity' && !($userid == WPF()->current_userid || WPF()->perm->usergroup_can('vpra')) ) $permission = false;
		if( $template == 'subscriptions' && !($userid == WPF()->current_userid || WPF()->perm->usergroup_can('vprs')) ) $permission = false;
		if( $permission ){
			include( (isset(WPF()->member_tpls[$template]) && WPF()->member_tpls[$template] ? WPF()->member_tpls[$template] : WPF()->member_tpls['profile']) );
		}
		else{
			?>
            <div class="wpfbg-7 wpf-page-message-wrap">
				<div class="wpf-page-message-text">
					<?php wpforo_phrase('You do not have permission to view this page') ?>
				</div>
			</div>
            <?php
		}
	}
	
	public function member_error(){
		echo apply_filters('wpforo_member_error_filter', wpforo_phrase('Members not found', FALSE));
	}

    /**
     * @deprecated since 1.5.0
     * @deprecated No longer used by core and not recommended.
     */
    public function field( $args, $wrap = true ){}

    /**
     * @deprecated since 1.5.0
     * @deprecated No longer used by core and not recommended.
     */
	public function field_wrap( $args, $field_html ){}

    /**
     * @deprecated since 1.5.0
     * @deprecated No longer used by core and not recommended.
     */
	public function form_fields( $fields ){}

	public function forum_subscribe_link(){
	    if ( WPF()->current_userid || WPF()->current_user_email ): ?>
            <?php if( wpfval( WPF()->current_object, 'forumid') && WPF()->perm->forum_can('sb', WPF()->current_object['forumid']) ): ?>
                <?php
                $args = array( "userid" => WPF()->current_userid, "itemid" => WPF()->current_object['forumid'], "type" => "forum", 'user_email' => WPF()->current_user_email );
                $subscribe = WPF()->sbscrb->get_subscribe( $args );
                if( isset( $subscribe['subid'] ) ): ?>
                    <span class="wpf-unsubscribe-forum wpf-action" id="wpfsubscribe-<?php echo WPF()->current_object['forumid'] ?>"><?php wpforo_phrase('Unsubscribe') ?></span>
                <?php else: ?>
                    <span class="wpf-subscribe-forum wpf-action wpfcl-5" id="wpfsubscribe-<?php echo WPF()->current_object['forumid'] ?>"><i class="far fa-envelope wpfcl-5"></i> <?php wpforo_phrase('Subscribe for new topics') ?></span>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif;
    }

    public function topic_subscribe_link(){
        if ( WPF()->current_userid || WPF()->current_user_email ){
            if( wpfval( WPF()->current_object, 'forumid') && WPF()->perm->forum_can('sb', WPF()->current_object['forumid']) ){
                $args = array( "userid" => WPF()->current_userid , "itemid" => WPF()->current_object['topicid'], "type" => "topic", 'user_email' => WPF()->current_user_email );
                $subscribe = WPF()->sbscrb->get_subscribe( $args );
                if( isset( $subscribe['subid'] ) ): ?>
                    <span class="wpf-unsubscribe-topic wpf-action" id="wpfsubscribe-<?php echo WPF()->current_object['topicid'] ?>" ><?php wpforo_phrase('Unsubscribe') ?></span>
                <?php else: ?>
                    <span class="wpf-subscribe-topic wpf-action wpfcl-5" id="wpfsubscribe-<?php echo WPF()->current_object['topicid'] ?>"  ><i class="far fa-envelope"></i> <?php wpforo_phrase('Subscribe for new replies') ?></span>
                <?php endif;
            }
        }
    }

    public function ajx_active_tab_content(){
        if( !empty($_POST['active_tab_id']) ){
            $active_tab_id = sanitize_textarea_field($_POST['active_tab_id']);
            switch ($active_tab_id){
                case 'topic_merge_form':
                    $this->topic_merge_form();
                    exit();
                break;
				case 'reply_move_form':
                    $this->reply_move_form();
                    exit();
                break;
                case 'topic_split_form':
                    $this->topic_split_form();
                    exit();
                break;
                case 'topic_move_form':
                    $this->topic_move_form();
                    exit();
                break;
            }
        }
        echo 0;
        exit();
    }

    public function add_footer_html(){
        ?>
        <div id="wpforo-load" class="wpforo-load">
            <i class="fas fa-3x fa-spinner fa-spin"></i>&nbsp;&nbsp;<br/>
            <span class="loadtext"><?php wpforo_phrase('Working') ?></span>
        </div>

        <div id="wpf-msg-box">
            <?php if( !WPF()->current_userid ) : ?>
                <p><?php echo sprintf( wpforo_phrase('Please %s or %s', FALSE), '<a href="' . wpforo_login_url() . '">'.wpforo_phrase('Login', FALSE).'</a>', '<a href="' . wpforo_register_url() . '">'.wpforo_phrase('Register', FALSE).'</a>' ) ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    public function posts_ordering_dropdown($orderby = null, $topicid = null){
	    if( is_null($topicid) ) $topicid = wpforo_bigintval( wpfval(WPF()->current_object, 'topicid') );
	    if( $topicid && $topic_url = wpforo_topic( $topicid, 'url' ) ){
		    if( is_null($orderby) ) $orderby = WPF()->current_object['orderby'];
            ?>
            <select onchange="window.location.assign(this.value)">
                <option value="<?php echo $topic_url ?>?orderby=votes" <?php wpfo_check($orderby, 'votes', 'selected') ?>>
				    <?php wpforo_phrase('Most Voted') ?>
                </option>
                <option value="<?php echo $topic_url ?>?orderby=oldest" <?php wpfo_check($orderby, 'oldest', 'selected') ?>>
				    <?php wpforo_phrase('Oldest') ?>
                </option>
                <option value="<?php echo $topic_url ?>?orderby=newest" <?php wpfo_check($orderby, 'newest', 'selected') ?>>
				    <?php wpforo_phrase('Newest') ?>
                </option>
            </select>

            <?php
	    }
    }

	public function editor_buttons( $editor = 'post' ) {
		$settings = array(
			'topic' => array(
				'wpautop'        => true,
				'media_buttons'  => false,
				'textarea_name'  => 'topic[body]',
				'textarea_rows'  => get_option( 'default_post_edit_rows', 20 ),// rows = "..."
				'tabindex'       => '',
				'editor_height'  => 180,
				'editor_css'     => '',
				'editor_class'   => '',
				'teeny'          => false,
				'dfw'            => false,
				'tinymce'        => array(
					'toolbar1'           => 'fontsizeselect,bold,italic,underline,strikethrough,forecolor,bullist,numlist,hr,alignleft,aligncenter,alignright,alignjustify,link,unlink,blockquote,pre,wpf_spoil,undo,redo,pastetext,source_code,emoticons,fullscreen',
					'toolbar2'           => '',
					'toolbar3'           => '',
					'toolbar4'           => '',
					'content_style'      => 'blockquote{border: #cccccc 1px dotted; background: #F7F7F7; padding:10px;font-size:12px; font-style:italic; margin: 20px 10px;}',
					'object_resizing'    => false,
					'autoresize_on_init' => true,
					'wp_autoresize_on'   => true
				),
				'quicktags'      => false,
				'default_editor' => 'tinymce'
			),
			'post'  => array(
				'wpautop'        => true,
				'media_buttons'  => false,
				'textarea_name'  => 'post[body]',
				'textarea_rows'  => get_option( 'default_post_edit_rows', 5 ),
				'editor_class'   => 'wpeditor',
				'teeny'          => false,
				'dfw'            => false,
				'editor_height'  => 180,
				'tinymce'        => array(
					'toolbar1'           => 'fontsizeselect,bold,italic,underline,strikethrough,forecolor,bullist,numlist,hr,alignleft,aligncenter,alignright,alignjustify,link,unlink,blockquote,pre,wpf_spoil,undo,redo,pastetext,source_code,emoticons,fullscreen',
					'toolbar2'           => '',
					'toolbar3'           => '',
					'toolbar4'           => '',
					'content_style'      => 'blockquote{border: #cccccc 1px dotted; background: #F7F7F7; padding:10px;font-size:12px; font-style:italic; margin: 20px 10px;}',
					'object_resizing'    => false,
					'autoresize_on_init' => true,
					'wp_autoresize_on'   => true
				),
				'quicktags'      => false,
				'default_editor' => 'tinymce'
			)
		);

		return apply_filters( 'wpforo_editor_settings', $settings[ $editor ] );
	}

	public function editor_settings_required_params($settings){
	    if( !wpfkey($settings, 'tinymce') ) $settings['tinymce'] = array();
	    $settings['tinymce']['setup'] = 'wpforo_tinymce_setup';
	    return $settings;
    }

	public function add_topic_button( $forumid = null ) {
	    $phrase = ( WPF()->forum->get_layout( $forumid ) == 3 ? wpforo_phrase( 'Ask a question', false ) : wpforo_phrase( 'Add Topic', false ) );
	    if( WPF()->current_object['template'] == 'forum' ) :
		    if( WPF()->forum->options['layout_threaded_add_topic_button'] ): ?>
			    <?php if( WPF()->perm->forum_can( 'ct', $forumid) ): ?>
                    <div class="wpf-head-bar-right">
                        <button class="wpf-button add_wpftopic" data-phrase="<?php echo $phrase ?>">
						    <?php echo $phrase ?>
                        </button>
                    </div>
			    <?php elseif( WPF()->current_user_groupid == 4 ) : ?>
                    <div class="wpf-head-bar-right">
                        <button class="wpf-button add_wpftopic not_reg_user" data-phrase="<?php echo $phrase ?>">
						    <?php echo $phrase ?>
                        </button>
                    </div>
			    <?php endif; ?>
		    <?php endif;
	    else :
            if ( WPF()->perm->forum_can( 'ct', $forumid ) ): ?>
                <button id="add_wpftopic" class="wpf-button" data-phrase="<?php echo $phrase ?>">
                    <?php echo $phrase ?>
                </button>
            <?php elseif ( WPF()->current_user_groupid == 4 ) : ?>
                <button id="add_wpftopic" class="wpf-button not_reg_user" data-phrase="<?php echo $phrase ?>">
                    <?php echo $phrase ?>
                </button>
            <?php endif;
        endif;
	}

	private function report_dialog(){
	    ?>
        <!-- Report Dialog  -->

        <div id="wpf_reportdialog" title="<?php esc_attr( wpforo_phrase('Report to Administration') ) ?>" style="display: none">
            <form id="wpf_reportform">
                <input type="hidden" id="wpf_reportpostid" value=""/>
                <textarea required style="width:100%; height:105px;" id="wpf_reportmessagecontent" placeholder="<?php esc_attr( wpforo_phrase('Write message') ) ?>"></textarea>
            </form>
            <input style="float: right;" id="wpf_sendreport" type="submit" value="<?php wpforo_phrase('Send Report') ?>"/>
        </div>

        <!-- Report Dialog end -->
        <?php
    }

    public function do_spoilers($text){
	    $text = preg_replace('#(?:<(p|a|span|blockquote|b|i|pre|font|del|strike|strong|em|div|u|center|marquee|table|tr|td|th|tt|sup|sub|s|ul|ol|li|small|code|h\d)(?:[\r\n\t\s\0]+[^<>]*?)?>[\r\n\t\s\0]*)\[[\r\n\t\s\0]*spoiler(?:[\r\n\t\s\0]*title[\r\n\t\s\0]*=[\r\n\t\s\0]*[\'\"]([^\'\"]+)[\'\"])?[\r\n\t\s\0]*\](?:[\r\n\t\s\0]*</\1>)#isu', '<div class="wpf-spoiler-wrap"><div class="wpf-spoiler-head"><i class="wpf-spoiler-chevron-name">'. wpforo_phrase('Spoiler', false) .'</i><i class="wpf-spoiler-chevron fas fa-chevron-down"></i><div class="wpf-spoiler-title">$2</div></div><div class="wpf-spoiler-body">', $text);
	    $text = preg_replace('#\[[\r\n\t\s\0]*spoiler(?:[\r\n\t\s\0]*title[\r\n\t\s\0]*=[\r\n\t\s\0]*[\'\"]([^\'\"]+)[\'\"])?[\r\n\t\s\0]*\]#isu', '<div class="wpf-spoiler-wrap"><div class="wpf-spoiler-head"><i class="wpf-spoiler-chevron-name">'. wpforo_phrase('Spoiler', false) .'</i><i class="wpf-spoiler-chevron fas fa-chevron-down"></i><div class="wpf-spoiler-title">$1</div></div><div class="wpf-spoiler-body">', $text);
	    $text = preg_replace('#(?:<(p|a|span|blockquote|b|i|pre|font|del|strike|strong|em|div|u|center|marquee|table|tr|td|th|tt|sup|sub|s|ul|ol|li|small|code|h\d)(?:[\r\n\t\s\0]+[^<>]*?)?>[\r\n\t\s\0]*)\[[\r\n\t\s\0]*/[\r\n\t\s\0]*spoiler[\r\n\t\s\0]*\](?:[\r\n\t\s\0]*</\1>)#isu', '</div></div>', $text);
	    $text = preg_replace('#\[[\r\n\t\s\0]*/[\r\n\t\s\0]*spoiler[\r\n\t\s\0]*\]#isu', '</div></div>', $text);
	    return $text;
    }
}