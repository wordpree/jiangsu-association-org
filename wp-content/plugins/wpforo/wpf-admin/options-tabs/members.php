<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
	if( !current_user_can('administrator') ) exit;
?>

<form action="" method="POST" class="validate">
	<?php wp_nonce_field( 'wpforo-settings-members' ); ?>
	<table class="wpforo_settings_table">
		<tbody>
        	<?php do_action( 'wpforo_settings_members_top', $wpforo ); ?>
			<tr>
				<th><label><?php _e('Online status timeout', 'wpforo'); ?>:</label></th>
				<td>
					<?php $online_timeout = wpfo($wpforo->member_options['online_status_timeout'], false) ?>
					<input name="wpforo_member_options[online_status_timeout]" type="number" min="1" value="<?php echo intval($online_timeout / 60 ) ?>" class="wpf-field-small" />&nbsp; <?php _e('minutes', 'wpforo'); ?>
				</td>
			</tr>
			<tr>
				<th><label><?php _e('Members URL structure', 'wpforo'); ?>:</label></th>
				<td>
					<?php $opt_id_attr = ''; $opt_nicename_attr = ''; ?>
					<?php if( wpfo($wpforo->member_options['url_structure'], false) == 'id' ){ $opt_id_attr = 'checked="checked"'; }else{ $opt_nicename_attr = 'checked="checked"'; } ?>
					<input id="id" type="radio" name="wpforo_member_options[url_structure]" value="id" <?php echo $opt_id_attr ?>/><label style="color: gray" for="id"><?php echo wpforo_home_url() ?>profile/<b style="color: #4093bf">USER_ID</b>/</label><br/>
					<input id="nicename" type="radio" name="wpforo_member_options[url_structure]" value="nicename" <?php echo $opt_nicename_attr ?>/><label style="color: gray" for="nicename"><?php echo wpforo_home_url() ?>profile/<b style="color: #4093bf">USER_NICENAME</b>/</label>
				</td>
			</tr>
            <tr>
				<th>
                	<label><?php _e('Custom Authorization URLs', 'wpforo'); ?>:</label>
                	<p class="wpf-info">
                    	<?php _e('Use this option only if you have set other pages for authorization. 
                        wpForo doesn\'t change its own URLs, these options are only for other plugin compatibility. 
                        For example, if you use BuddyPress or Ultimate Member plugin you can set these values:', 'wpforo'); ?><br />
                        <?php _e('Login URL', 'wpforo'); ?>: <strong>/login/</strong><br />
                        <?php _e('Register URL', 'wpforo'); ?>: <strong>/register/</strong><br />
                    </p>
                </th>
				<td>
					<ul>
                        <li><?php echo trim(get_bloginfo('url'),'/'); ?><input style="width:30%;padding: 3px 10px 3px 3px; vertical-align:middle; font-size:13px" type="text" name="wpforo_member_options[login_url]" value="<?php wpfo($wpforo->member_options['login_url']) ?>" /> &nbsp;<label style="font-size:13px"><?php _e('Login URL', 'wpforo') ?></label></li>
                        <li><?php echo trim(get_bloginfo('url'),'/'); ?><input style="width:30%;padding: 3px 10px 3px 3px; vertical-align:middle; font-size:13px" type="text" name="wpforo_member_options[register_url]" value="<?php wpfo($wpforo->member_options['register_url']) ?>" /> &nbsp;<label style="font-size:13px"><?php _e('Register URL', 'wpforo') ?></label></li>
                        <li><?php echo trim(get_bloginfo('url'),'/'); ?><input style="width:30%;padding: 3px 10px 3px 3px; vertical-align:middle; font-size:13px" type="text" name="wpforo_member_options[lost_password_url]" value="<?php wpfo($wpforo->member_options['lost_password_url']) ?>" /> &nbsp;<label style="font-size:13px"><?php _e('Lost Password URL', 'wpforo') ?></label></li>
                    </ul>
                </td>
			</tr>
            <tr>
				<th colspan="2">
                	<h3 style="font-weight:400; padding:5px 0px 10px 0px; margin:0px;"><?php _e('Member Reputation and Titles', 'wpforo'); ?></h3>
                </th>
			</tr>
            <tr>
				<td colspan="2" style="padding:5px 0px 0px 0px;">
                	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="wpf-rating-table">
                      <tbody>
                      <tr>
                      	<th style="width:10%;"><?php _e('Rating Level', 'wpforo'); ?></th>
                        <th style="width:15%;"><?php _e('Min Number of Posts', 'wpforo'); ?></th>
                        <th style="width:25%;"><?php _e('Member Title', 'wpforo'); ?></th>
                        <th style="text-align:center;width:10%;"><?php _e('Short Badge', 'wpforo'); ?></th>
                        <th style="text-align:center;width:10%;"><?php _e('Full Badge', 'wpforo'); ?></th>
                        <th style="text-align:center;width:10%;"><?php _e('Rating Color', 'wpforo'); ?></th>
                        <th style="width:20%;"><?php _e('Rating Icon', 'wpforo'); ?> | <a href="http://fontawesome.io/icons/" target="_blank" style="text-decoration:none;"><?php _e('More', 'wpforo'); ?>&raquo;</a></th>
                      </tr>
                      <?php $levels = $wpforo->member->levels(); ?>
                      <?php foreach( $levels as $level ): ?>
                      	<tr>
                          <td><h4><?php _e('Level', 'wpforo'); ?> <?php echo esc_html($level) ?></h4></td>
                          <td><input type="number" value="<?php echo $wpforo->member->rating($level, 'points') ?>" name="wpforo_member_options[rating][<?php echo esc_attr($level) ?>][points]" placeholder="<?php _e('Number of Posts', 'wpforo'); ?>"></td>
                          <td style="text-align:center;"><input type="text" value="<?php echo $wpforo->member->rating($level, 'title') ?>" name="wpforo_member_options[rating][<?php echo esc_attr($level) ?>][title]" placeholder="<?php _e('Custom Title', 'wpforo'); ?>"></td>
                          <td style="text-align:center;"><div class="wpf-badge-short wpf-badge-level-<?php echo esc_attr($level) ?>" style="background-color:<?php echo $wpforo->member->rating($level, 'color') ?>;"><?php echo $wpforo->member->rating_badge($level, 'short'); ?></div></td>
                          <td style="text-align:center;"><div class="wpf-badge-full wpf-badge-level-<?php echo esc_attr($level) ?>" style="color:<?php echo $wpforo->member->rating($level, 'color') ?>;"><?php echo $wpforo->member->rating_badge($level, 'full'); ?></div></td>
                          <td style="text-align:center;"><input type="color" value="<?php echo $wpforo->member->rating($level, 'color') ?>" name="wpforo_member_options[rating][<?php echo esc_attr($level) ?>][color]" placeholder="<?php _e('Color', 'wpforo'); ?>"></td>
                          <td><input type="text" value="<?php echo $wpforo->member->rating($level, 'icon') ?>" name="wpforo_member_options[rating][<?php echo esc_attr($level) ?>][icon]" placeholder="<?php _e('Badge Icon', 'wpforo'); ?>"></td>
                       </tr>
                      <?php endforeach; ?>
                      <?php $ugroups = $wpforo->usergroup->usergroup_list_data(); ?>
                      <tr>
                          <td colspan="3">
                          	<label style="text-transform:none;font-size: 14px; font-weight: 500; line-height:20px; padding:0px;"><?php _e('Enable Reputation Titles for selected usergroups', 'wpforo'); ?></label>
                          	<p class="wpf-info"><?php _e('This option depends on "Enable Member Rating Titles" parent option, witch located in wpForo Settings > Features Tab', 'wpforo'); ?></p>
                          </td>
                          <td colspan="4">
                          <?php 
							foreach($ugroups as $ugroup){
								$value = ( isset($wpforo->member_options['rating_title_ug'][$ugroup['groupid']]) ) ? $wpforo->member_options['rating_title_ug'][$ugroup['groupid']] : 0;
								echo '<label style="display:inline-block; text-align:center;"><input name="wpforo_member_options[rating_title_ug]['.intval($ugroup['groupid']).']" value="1" type="checkbox" ' . wpfo_check(1, $value, 'checked', false) . ' /> '.esc_html($ugroup['name']).'</label>';
							}
						  ?>
                          </td>
                      </tr>
                      <tr>
                          <td colspan="3">
                          	<label style="text-transform:none;font-size: 14px; font-weight: 500; line-height:20px;"><?php _e('Enable Reputation Badges for selected usergroups', 'wpforo'); ?></label>
                          	<p class="wpf-info"><?php _e('This option depends on "Enable Member Rating" parent option, witch located in wpForo Settings > Features Tab', 'wpforo'); ?></p>
                          </td>
                          <td colspan="4">
                          <?php 
							foreach($ugroups as $ugroup){
								$value = ( isset($wpforo->member_options['rating_badge_ug'][$ugroup['groupid']]) ) ? $wpforo->member_options['rating_badge_ug'][$ugroup['groupid']] : 0;
								echo '<label style="display:inline-block; text-align:center;"><input name="wpforo_member_options[rating_badge_ug]['.intval($ugroup['groupid']).']" value="1" type="checkbox" ' . wpfo_check(1, $value, 'checked', false) . ' /> '.esc_html($ugroup['name']).'</label>';
							}
						  ?>
                          </td>
                      </tr>
                      <tr>
                          <td colspan="3">
                          	<label style="text-transform:none;font-size: 14px; font-weight: 500; line-height:20px;"><?php _e('Display Usergroup under Post Author Avatar', 'wpforo'); ?></label>
                          </td>
                          <td colspan="4">
                          <?php 
							foreach($ugroups as $ugroup){
								$value = ( isset($wpforo->member_options['title_usergroup'][$ugroup['groupid']]) ) ? $wpforo->member_options['title_usergroup'][$ugroup['groupid']] : 0;
								echo '<label style="display:inline-block; text-align:center;"><input name="wpforo_member_options[title_usergroup]['.intval($ugroup['groupid']).']" value="1" type="checkbox" ' . wpfo_check(1, $value, 'checked', false) . ' /> '.esc_html($ugroup['name']).'</label>';
							}
						  ?>
                          </td>
                      </tr>
                      </tbody>
                    </table>

                </td>
			</tr>
            <?php do_action( 'wpforo_settings_members_bottom', $wpforo ); ?>
		</tbody>
	</table>
    <div class="wpforo_settings_foot">
        <input type="submit" class="button button-primary" value="<?php _e('Update Options', 'wpforo'); ?>" />
    </div>
</form>