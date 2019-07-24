<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
	if( !current_user_can('administrator') ) exit;
?>

<?php if(!isset($wpforo->post_options['max_upload_size'])){ $upload_max_filesize = @ini_get('upload_max_filesize'); $upload_max_filesize = wpforo_human_size_to_bytes($upload_max_filesize); if( !$upload_max_filesize || $upload_max_filesize > 10485760 ) $upload_max_filesize = 10485760; $wpforo->post_options['max_upload_size'] = $upload_max_filesize; } ?>
<form action="" method="POST" class="validate">
	<?php wp_nonce_field( 'wpforo-settings-posts' ); ?>
    <table class="wpforo_settings_table">
		<tbody>
        	<?php do_action( 'wpforo_settings_post_top', $wpforo ); ?>
			<tr>
				<th><label for="topics_per_page"><?php _e('Number of Topics per Page', 'wpforo'); ?></label></th>
				<td><input id="topics_per_page" type="number" min="1" name="wpforo_post_options[topics_per_page]" value="<?php wpfo($wpforo->post_options['topics_per_page']) ?>" class="wpf-field-small" /></td>
			</tr>
			<tr>
				<th><label for="eot_durr"><?php _e('Allow Edit Own Topic for', 'wpforo'); ?></label></th>
				<td><input id="eot_durr" type="number" min="1" name="wpforo_post_options[eot_durr]" value="<?php wpfo($wpforo->post_options['eot_durr']/60) ?>" class="wpf-field-small" />&nbsp; <?php _e('minutes', 'wpforo') ?></td>
			</tr>
			<tr>
				<th><label for="dot_durr"><?php _e('Allow Delete Own Topic for', 'wpforo'); ?></label></th>
				<td><input id="dot_durr" type="number" min="1" name="wpforo_post_options[dot_durr]" value="<?php wpfo($wpforo->post_options['dot_durr']/60) ?>" class="wpf-field-small" />&nbsp; <?php _e('minutes', 'wpforo') ?></td>
			</tr>
			<tr>
				<th><label for="posts_per_page"><?php _e('Number of Posts per Page', 'wpforo'); ?></label></th>
				<td><input id="posts_per_page" type="number" min="1" name="wpforo_post_options[posts_per_page]" value="<?php wpfo($wpforo->post_options['posts_per_page']) ?>" class="wpf-field-small" /></td>
			</tr>
			<tr>
				<th><label for="eor_durr"><?php _e('Allow Edit Own Post for', 'wpforo'); ?></label></th>
				<td><input id="eor_durr" type="number" min="1" name="wpforo_post_options[eor_durr]" value="<?php wpfo($wpforo->post_options['eor_durr']/60) ?>" class="wpf-field-small" />&nbsp; <?php _e('minutes', 'wpforo') ?></td>
			</tr>
			<tr>
				<th><label for="dor_durr"><?php _e('Allow Delete Own post for', 'wpforo'); ?></label></th>
				<td><input id="dor_durr" type="number" min="1" name="wpforo_post_options[dor_durr]" value="<?php wpfo($wpforo->post_options['dor_durr']/60) ?>" class="wpf-field-small" />&nbsp; <?php _e('minutes', 'wpforo') ?></td>
			</tr>
            
            <tr>
				<th>
                	<label><?php _e('Maximum upload file size', 'wpforo'); ?></label>
                	<p class="wpf-info"><?php _e('You can not set this value more than "upload_max_filesize" and "post_max_size". If you want to increase server parameters please contact to your hosting service support.', 'wpforo'); ?></p>
                </th>
				<td>
                	<input type="number" min="1" name="wpforo_post_options[max_upload_size]" value="<?php echo wpforo_print_size($wpforo->post_options['max_upload_size'], false) ?>" class="wpf-field-small" />&nbsp; <?php _e('MB', 'wpforo') ?>
                	<p class="wpf-info">
                     	<?php
							_e('Server "upload_max_filesize" is '); echo ini_get('upload_max_filesize') . '<br/>';
							_e('Server "post_max_size" is '); echo ini_get('post_max_size');
                        ?>
                    </p>
                </td>
			</tr> 
			
			<tr>
				<th>
                	<label><?php _e('Attachment click - message for non-permitted users', 'wpforo'); ?></label>
                	<p class="wpf-info"><?php _e('This message will be displayed when a non-permitted forum member clicks on attached file link in topic and posts.', 'wpforo'); ?></p>
                </th>
				<td>
					<textarea name="wpforo_post_options[attach_cant_view_msg]"><?php echo esc_textarea( ( !empty( $wpforo->post_options['attach_cant_view_msg'] ) ? $wpforo->post_options['attach_cant_view_msg'] : '' ) ) ?></textarea>
                </td>
			</tr>
            <?php do_action( 'wpforo_settings_post_bottom', $wpforo ); ?>
		</tbody>
	</table>
    <div class="wpforo_settings_foot">
        <input type="submit" class="button button-primary" value="<?php _e('Update Options', 'wpforo'); ?>" />
    </div>
</form>