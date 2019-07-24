<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
 
/**
 * wpForo Classic Theme Functions
 * @hook: action - 'init'
 * @description: only for wpForo theme functions. For WordPress theme functions use functions-wp.php file.
 * @theme: Classic
 */

function wpforo_classic_wpforo_frontend_enqueue(){
	global $wpforo;
	if(function_exists('is_wpforo_page')){
	   if(is_wpforo_page()){
			wp_register_style( 'wpforo-uidialog-style', WPFORO_URL . '/wpf-assets/css/jquery-ui.css', false, WPFORO_VERSION );
			wp_enqueue_style('wpforo-uidialog-style');
			
			if( file_exists(WPFORO_TEMPLATE_DIR . '/colors.css') ){
				wp_register_style( 'wpforo-dynamic-style', WPFORO_TEMPLATE_URL . '/colors.css', false, WPFORO_VERSION );
				wp_enqueue_style('wpforo-dynamic-style');
			}
	   }
	}
	elseif ( !is_front_page() && !is_home() ) {
		wp_register_style( 'wpforo-uidialog-style', WPFORO_URL . '/wpf-assets/css/jquery-ui.css', false, WPFORO_VERSION );
		wp_enqueue_style('wpforo-uidialog-style');
	}
}
add_action('wp_enqueue_scripts', 'wpforo_classic_wpforo_frontend_enqueue', 11);

function wpforo_classic_forum_options($wpforo){
	?>
    <?php if($wpforo->tpl->layout_exists(1)): ?>
		<?php 
        if(!isset($wpforo->forum_options['layout_extended_intro_topics_toggle'])) $wpforo->forum_options['layout_extended_intro_topics_toggle'] = 1;
        if(!isset($wpforo->forum_options['layout_extended_intro_topics_count'])) $wpforo->forum_options['layout_extended_intro_topics_count'] = 3;
        ?>
        <tr>
            <th><label><?php _e('Extended Layout - Intro topics','wpforo'); ?>:</label></th>
            <td>
                <div class="wpf-switch-field">
                    <input id="show-tte" type="radio" name="wpforo_forum_options[layout_extended_intro_topics_toggle]" value="1" <?php wpfo_check($wpforo->forum_options['layout_extended_intro_topics_toggle'], 1); ?>/><label for="show-tte"><?php _e('Expanded','wpforo'); ?></label> &nbsp;  
                    <input id="hide-tte" type="radio" name="wpforo_forum_options[layout_extended_intro_topics_toggle]" value="0" <?php wpfo_check($wpforo->forum_options['layout_extended_intro_topics_toggle'], 0); ?>/><label for="hide-tte"><?php _e('Collapsed','wpforo'); ?></label> 
                </div>
            </td>
        </tr>
        <tr>
            <th><label for="tdcs"><?php _e('Extended Layout - Number of intro topics','wpforo'); ?>:</label></th>
            <td>
                <input id="tdcs" name="wpforo_forum_options[layout_extended_intro_topics_count]" type="number" min="0" value="<?php wpfo( $wpforo->forum_options['layout_extended_intro_topics_count'] ) ?>" class="wpf-field-small" />
            </td>
        </tr>
    <?php endif; ?>
    <?php if($wpforo->tpl->layout_exists(3)): ?>
		<?php 
        if(!isset($wpforo->forum_options['layout_qa_intro_topics_toggle'])) $wpforo->forum_options['layout_qa_intro_topics_toggle'] = 1;
        if(!isset($wpforo->forum_options['layout_qa_intro_topics_count'])) $wpforo->forum_options['layout_qa_intro_topics_count'] = 3;
        ?>
        <tr>
            <th><label><?php _e('Q&A layout - Intro topics','wpforo'); ?>:</label></th>
            <td>
                <div class="wpf-switch-field">
                    <input id="show-ttq" type="radio" name="wpforo_forum_options[layout_qa_intro_topics_toggle]" value="1" <?php wpfo_check($wpforo->forum_options['layout_qa_intro_topics_toggle'], 1); ?>/><label for="show-ttq"><?php _e('Expanded','wpforo'); ?></label> &nbsp; 
                    <input id="hide-ttq" type="radio" name="wpforo_forum_options[layout_qa_intro_topics_toggle]" value="0" <?php wpfo_check($wpforo->forum_options['layout_qa_intro_topics_toggle'], 0); ?>/><label for="hide-ttq"><?php _e('Collapsed','wpforo'); ?></label>  
                </div>
            </td>
        </tr>
        <tr>
            <th><label for="tdcq"><?php _e('Q&A Layout - Number of intro topics','wpforo'); ?>:</label></th>
            <td>
                <input id="tdcq" name="wpforo_forum_options[layout_qa_intro_topics_count]" type="number" min="0" value="<?php wpfo( $wpforo->forum_options['layout_qa_intro_topics_count'] ) ?>" class="wpf-field-small" />
            </td>
        </tr>
    <?php endif; ?>
    <?php
}
add_action('wpforo_settings_forums', 'wpforo_classic_forum_options');



function wpforo_classic_post_options($wpforo){
	?>
	<?php if($wpforo->tpl->layout_exists(1)): ?>
    	<?php 
        if(!isset($wpforo->post_options['layout_extended_intro_posts_toggle'])) $wpforo->post_options['layout_extended_intro_posts_toggle'] = 1;
        if(!isset($wpforo->post_options['layout_extended_intro_posts_count'])) $wpforo->post_options['layout_extended_intro_posts_count'] = 4;
        ?>
        <tr>
            <th><label><?php _e('Extended Layout - Intro posts','wpforo'); ?></label></th>
            <td>
                <div class="wpf-switch-field">
                    <input id="show-tte" type="radio" name="wpforo_post_options[layout_extended_intro_posts_toggle]" value="1" <?php wpfo_check($wpforo->post_options['layout_extended_intro_posts_toggle'], 1); ?>/><label for="show-tte"><?php _e('Expanded','wpforo'); ?></label> &nbsp;  
                    <input id="hide-tte" type="radio" name="wpforo_post_options[layout_extended_intro_posts_toggle]" value="0" <?php wpfo_check($wpforo->post_options['layout_extended_intro_posts_toggle'], 0); ?>/><label for="hide-tte"><?php _e('Collapsed','wpforo'); ?></label> 
                </div>
            </td>
        </tr>
        <tr>
            <th>
            	<label for="tdcs"><?php _e('Extended Layout - Number of intro posts','wpforo'); ?></label>
            	<p class="wpf-info"><?php _e('Set this option value 0 if you want to show all posts under topic intro area.','wpforo'); ?></p>
            </th>
            <td>
                <input id="tdcs" name="wpforo_post_options[layout_extended_intro_posts_count]" type="number" min="0" value="<?php wpfo( $wpforo->post_options['layout_extended_intro_posts_count'] ) ?>" class="wpf-field-small" />
            </td>
        </tr>
    <?php endif; ?>
    <?php
}
add_action('wpforo_settings_post_top', 'wpforo_classic_post_options');



function wpforo_classic_reply_form_head($string, $args){
	global $wpforo;
	if( $wpforo->tpl->layout_exists(3) ){
		if( $args['layout'] == 3 ){
			$string = preg_replace('|(<p[^><]*id="wpf-reply-form-title"[^><]*>)(.+?)(</p>)|is', '$1'.wpforo_phrase('Your Answer', false, 'default').'$3', $string);
		}
	}
	return $string;
}
add_filter('wpforo_reply_form_head', 'wpforo_classic_reply_form_head', 1, 2);



function wpforo_classic_reply_form_field_title($string, $args){
	global $wpforo;
	if( $wpforo->tpl->layout_exists(3) ){
		if( $args['layout'] == 3 ){
			$string = preg_replace('|[^\:]+\:|is', wpforo_phrase('Answer to', false, 'default') . ':', $string);
		}
	}
	return $string;
}
add_filter('wpforo_reply_form_field_title', 'wpforo_classic_reply_form_field_title', 1, 2);

function wpforo_classic_dark_class( $wpforo ){
	if(empty($wpforo)) return;
	if(isset($wpforo->theme_options['style'])) echo 'wpf-' . esc_attr($wpforo->theme_options['style']);
}

add_action('wpforo_wrap_class', 'wpforo_classic_dark_class');