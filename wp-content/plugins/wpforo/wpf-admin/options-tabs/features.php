<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
	if( !current_user_can('administrator') ) exit;
?>

<?php
$options = array(
	'user-admin-bar' => array( 'label' => __('Show Admin Bar for Members', 'wpforo'),  'type' => '',  'required' => '', 'value' => 0, 'description' => __('This option doesn\'t affect website admins.', 'wpforo') ),
	'page-title' => array( 'label' => __('Show Forum Page Title', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1 ),
	'top-bar' => array( 'label' => __('Show Top/Menu Bar', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'top-bar-search' => array( 'label' => __('Show Top Search', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'breadcrumb' => array( 'label' => __('Show Breadcrumb', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'footer-stat' => array( 'label' => __('Show Forum Statistic', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'author-link' => array( 'label' => __('Replace Author Link to Forum Profile', 'wpforo'),  'type' => '',  'required' => '', 'value' => 0 ),
	'comment-author-link' => array( 'label' => __('Replace Comment Author Link to Forum Profile', 'wpforo'),  'type' => '',  'required' => '', 'value' => 0 ),
	'user-register' => array( 'label' => __('Enable User Registration', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1, 'description' => __('This option is not synced with WordPress "Anyone can register" option in Dashboard > Settings > General admin page. If this option is enabled new users will always be able to register.', 'wpforo') ),
	'user-register-email-confirm' => array( 'label' => __('Enable User Registration email confirmation', 'wpforo'),  'type' => '',  'required' => '', 'value' => 0, 'description' => __('If you have enabled this option, after registering, user can not login without confirming the email.', 'wpforo') ),
	'register-url' => array( 'label' => __('Replace Registration Page URL to Forum URL', 'wpforo'),  'type' => '',  'required' => '', 'value' => 0 ),
	'login-url' => array( 'label' => __('Replace Login Page URL to Forum URL', 'wpforo'),  'type' => '',  'required' => '', 'value' => 0 ),
	'replace-avatar' => array( 'label' => __('Replace Author Avatar with Forum Profile Avatar', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'avatars' => array( 'label' => __('Enable Avatars', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'custom-avatars' => array( 'label' => __('Enable Custom Avatars', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'signature' => array( 'label' => __('Allow Member Signature', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'rating' => array( 'label' => __('Enable Member Rating', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'rating_title' => array( 'label' => __('Enable Member Rating Titles', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'member_cashe' => array( 'label' => __('Enable Member Cache', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'object_cashe' => array( 'label' => __('Enable Object Cache', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'html_cashe' => array( 'label' => __('Enable HTML Cache', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'memory_cashe' => array( 'label' => __('Enable Memory Cache', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'seo-title' => array( 'label' => __('Enable wpForo SEO for Meta Titles', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'seo-meta' => array( 'label' => __('Enable wpForo SEO for Meta Tags', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'seo-profile' => array( 'label' => __('Enable User Profile Page indexing', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'font-awesome' => array( 'label' => __('Enable wpForo Font-Awesome Lib', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'user-synch' => array( 'label' => __('Turn Off User Syncing Note', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1),
	'output-buffer' => array( 'label' => __('Enable Output Buffer', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1, 'description' => __('This feature is useful if you\'re adding content before or after [wpforo] shortcode in page content. Also it useful if forum is loaded before website header, on top of the front-end.', 'wpforo')),
	'wp-date-format' => array( 'label' => __('Enable WordPress Date/Time Format', 'wpforo'),  'type' => '',  'required' => '', 'value' => 0, 'description' => __('You can manage WordPress date and time format in WordPress Settings > General admin page.', 'wpforo')), 
	'subscribe_conf' => array( 'label' => __('Enable Subscription Confirmation', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1, 'description' => __('Forum and Topic subscription with double opt-in/confirmation system.', 'wpforo') ),
	'subscribe_checkbox_on_post_editor' => array( 'label' => __('Topic subscription option on post editor', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1, 'description' => __('This option adds topic subscription checkbox next to new topic and post submit button.', 'wpforo') ),
	'subscribe_checkbox_default_status' => array( 'label' => __('Topic subscription option on post editor - checked/enabled', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1, 'description' => __('Enable this option if you want the topic subscription checkbox to be checked by default.', 'wpforo') ),
	'attach-media-lib' => array( 'label' => __('Insert Forum Attachments to Media Library', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1, 'description' => __('Enable this option to be able manage forum attachments in Dashboard > Media > Library admin page.', 'wpforo') ),
	'debug-mode' => array( 'label' => __('Enable Debug Mode', 'wpforo'),  'type' => '',  'required' => '', 'value' => 0, 'description' => __('If you got some issue with wpForo, please enable this option before asking for support, this outputs hidden important information to help us debug your issue.', 'wpforo')), 
	'copyright' => array( 'label' => __('Help wpForo to grow, show plugin info', 'wpforo'),  'type' => '',  'required' => '', 'value' => 1, 'description' => __('Please enable this option to help wpForo get more popularity as your thank to the hard work we do for you totally free. This option adds a very small icon in forum footer, which will allow your site visitors recognize the name of forum solution you use.', 'wpforo')),
);

?>
<form action="" method="POST" class="validate">
	<?php wp_nonce_field( 'wpforo-features' ); ?>
    <table class="wpforo_settings_table">
        <tbody>
            <?php foreach($options as $key => $option): ?>
            	<?php  if( !isset($wpforo->features[$key]) ){ $wpforo->features[$key] = ''; } ?>
                <tr>
                    <th>
                    	<label><?php echo esc_html($option['label']); ?></label>
                    	<p class="wpf-info"><?php if(isset($option['description'])) echo esc_html($option['description']); ?></p>
                    </th>
                    <td>
                        <div class="wpf-switch-field">
                            <input type="radio" value="1" name="wpforo_features[<?php echo esc_attr($key); ?>]" id="wpf_<?php echo esc_attr($key); ?>_1" <?php wpfo_check($wpforo->features[$key], 1); ?>><label for="wpf_<?php echo esc_attr($key); ?>_1"><?php _e('Yes', 'wpforo'); ?></label> &nbsp;  
                            <input type="radio" value="0" name="wpforo_features[<?php echo esc_attr($key); ?>]" id="wpf_<?php echo esc_attr($key); ?>_0" <?php wpfo_check($wpforo->features[$key], 0); ?>><label for="wpf_<?php echo esc_attr($key); ?>_0"><?php _e('No', 'wpforo'); ?></label> 
                        	<?php if($key == 'copyright') echo '<span style="color:#009900; font-weight:400; font-size:14px;">&nbsp;Thank you!</span>'; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php do_action( 'wpforo_settings_theme', $wpforo ); ?>
        </tbody>
    </table>
    <div class="wpforo_settings_foot">
        <input type="submit" class="button button-primary" value="<?php _e('Update Options', 'wpforo'); ?>" />
    </div>
</form>