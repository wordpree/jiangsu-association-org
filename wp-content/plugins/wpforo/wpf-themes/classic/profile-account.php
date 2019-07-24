<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
?>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$( "input[name='member\[avatar_url\]']" ).click(function(){
			$( "#wpfat_remote" ).prop('checked', true);
		});
		$( "input[name='avatar']" ).click(function(){
			$( "#wpfat_custom" ).prop('checked', true);
		});
	});
</script>
<div class="wpforo-profile-account wpfbg-7">
    <?php if( $ID == $wpforo->current_userid || 
				( $wpforo->perm->usergroup_can('em') && 
					$wpforo->perm->user_can_manage_user( $wpforo->current_userid, $ID ) ) ) : ?>   		
        <form action="" enctype="multipart/form-data" method="POST">
          <?php wp_nonce_field( 'wpforo_verify_form', 'wpforo_form' ); ?>
          <input type="hidden" name="wpforo_member_submit" value="1"/>
          <input type="hidden" name="member[userid]" value="<?php echo intval($ID) ?>"/>
          <input type="hidden" name="member[username]" value="<?php echo esc_attr($user_login) ?>"/>
          <input type="hidden" name="member[user_pass]" value="<?php echo esc_attr($user_pass) ?>"/>
          <div class="wpforo-table">
          	<div class="wpforo-tr">
                <div class="wpforo-profile-label wpforo-th wpfbg-7">&nbsp;</div>
                <div class="wpforo-profile-field wpforo-td wpfbg-7">&nbsp;</div>
              </div>
              <div class="wpforo-tr">
                <div class="wpforo-profile-label wpforo-th wpfbg-7"><?php wpforo_phrase('Username') ?>:</div>
                <div class="wpforo-profile-field wpforo-td wpfbg-9"><span class="wpf-username"><?php echo esc_html($user_login) ?></span></div>
              </div>
              <div class="wpforo-tr">
                <div class="wpforo-profile-label wpforo-th wpfbg-7"><label for="display_name"><?php wpforo_phrase('Display Name') ?>:</label></div>
                <div class="wpforo-profile-field wpforo-td wpfbg-9"><input autocomplete="off" id="display_name" required="TRUE" type="text" value="<?php echo esc_attr($display_name) ?>" name="member[display_name]" maxlength="50" /></div>
              </div>
              <div class="wpforo-tr">
                <div class="wpforo-profile-label wpforo-th wpfbg-7"><label for="nickname"><?php wpforo_phrase('Nickname') ?>:</label></div>
                <div class="wpforo-profile-field wpforo-td wpfbg-9"><input autocomplete="off" id="nickname" required="TRUE" type="text" value="<?php echo esc_attr(urldecode($user_nicename)) ?>" name="member[user_nickname]" maxlength="50" /></div>
              </div>
              <div class="wpforo-tr">
                <div class="wpforo-profile-label wpforo-th wpfbg-7"><label for="user_email"><?php wpforo_phrase('Email') ?>:</label></div>
                <div class="wpforo-profile-field wpforo-td wpfbg-9"><input autocomplete="off" id="user_email" required="TRUE" type="text" value="<?php echo esc_attr($user_email) ?>" name="member[user_email]" maxlength="50" /></div>
              </div>
            	<?php if( $wpforo->perm->usergroup_can('em') ) : ?>
	              <div class="wpforo-tr">
	                <div class="wpforo-profile-label wpforo-th wpfbg-7"><label for="title"><?php wpforo_phrase('Title') ?>:</label></div>
	                <div class="wpforo-profile-field wpforo-td wpfbg-9"><input autocomplete="off" id="title" type="text" value="<?php wpforo_phrase($title) ?>" name="member[title]" maxlength="50" /></div>
	              </div>
                <?php endif ?>
                <?php if( $wpforo->current_user_groupid == 1 && current_user_can('administrator') ) : ?>
	              <div class="wpforo-tr">
	                <div class="wpforo-profile-label wpforo-th wpfbg-7"><label for="groupid"><?php wpforo_phrase('User Group') ?>:</label></div>
	                <div class="wpforo-profile-field wpforo-td wpfbg-9">
	                	<select id="groupid" name="member[groupid]">
							<?php $wpforo->usergroup->show_selectbox($groupid) ?>
						</select>
	                </div>
	              </div>
	            <?php endif ?>
              <?php if( wpforo_feature('custom-avatars', $wpforo) && wpforo_feature('avatars', $wpforo) ): ?>
                  <div class="wpforo-tr">
                    <div class="wpforo-profile-label wpforo-th wpfbg-7"><?php wpforo_phrase('Avatar') ?>:</div>
                    <div class="wpforo-profile-field wpforo-td wpfbg-9">
                    <ul>
                        <li><input type="radio" name="member[avatar_type]" id="wpfat_gravatar" value="gravatar" <?php echo ( $avatar == '' || $avatar == NULL ? 'checked="checked"' : '' ); ?> />&nbsp; <label for="wpfat_gravatar"><?php wpforo_phrase('Wordpress avatar system') ?></label></li>
                        <li><input type="radio" name="member[avatar_type]" id="wpfat_remote" value="remote" <?php echo ( $avatar && strpos($avatar, 'wpforo/avatars') === FALSE ? 'checked="checked"' : '' ) ?> />&nbsp; <label for="wpfat_remote"><?php wpforo_phrase('Specify avatar by URL') ?>:</label> <input autocomplete="off" type="text" name="member[avatar_url]" value="<?php echo (strpos($avatar, 'wpforo/avatars') === FALSE ? esc_url($avatar) : '') ?>" maxlength="300" /></li>
                        <li>
                        <input type="radio" name="member[avatar_type]" id="wpfat_custom" value="custom" <?php echo ( strpos($avatar, 'wpforo/avatars') !== FALSE ? 'checked="checked"' : '' ) ?> />&nbsp; 
                        <label for="wpfat_custom"><?php wpforo_phrase('Upload an avatar') ?></label>
                        <?php echo ( strpos($avatar, 'wpforo/avatars') !== FALSE ? '<br /><img src="'.esc_url($avatar).'" class="wpf-custom-avatar-img"/>' : '' ) ?>&nbsp; <input class="wpf-custom-avatar" type="file" name="avatar" />&nbsp;
                        </li>
                    </ul>
                    </div>
                  </div>
              <?php endif; ?>
              <div class="wpforo-tr">
                <div class="wpforo-profile-label wpforo-th wpfbg-7"><label for="site"><?php wpforo_phrase('Website') ?>:</label></div>
                <div class="wpforo-profile-field wpforo-td wpfbg-9"><input autocomplete="off" id="site" type="text" value="<?php echo esc_url($site) ?>" name="member[site]" maxlength="255" /></div>
              </div>
              <div class="wpforo-tr">
                <div class="wpforo-profile-label wpforo-th wpfbg-7"><?php wpforo_phrase('Social Networks') ?>:</div>
                <div class="wpforo-profile-field wpforo-td wpfbg-9">
                    <div class="wpforo-profile-socnet wpforo-table">
                      <div class="wpforo-tr">
                        <div class="wpforo-td" style="width:20%"><label for="facebook"><?php wpforo_phrase('Facebook') ?>:</label></div><div class="wpforo-td" style="width:30%"><input autocomplete="off" id="facebook" type="text" value="<?php echo esc_attr($facebook) ?>" name="member[facebook]" maxlength="255" /></div>
                        <div class="wpforo-td" style="width:20%"><label for="twitter"><?php wpforo_phrase('Twitter') ?>:</label></div><div class="wpforo-td" style="width:30%"><input autocomplete="off" id="twitter" type="text" value="<?php echo esc_attr($twitter) ?>" name="member[twitter]" maxlength="255" /></div>
                      </div>
                      <div class="wpforo-tr">
                        <div class="wpforo-td"><label for="gtalk"><?php wpforo_phrase('Google+') ?>:</label></div><div class="wpforo-td"><input autocomplete="off" id="gtalk" type="text" value="<?php echo esc_attr($gtalk) ?>" name="member[gtalk]" maxlength="55" /></div>
                        <div class="wpforo-td"><label for="yahoo"><?php wpforo_phrase('Yahoo') ?>:</label></div><div class="wpforo-td"><input autocomplete="off" id="yahoo" type="text" value="<?php echo esc_attr($yahoo) ?>" name="member[yahoo]" maxlength="55" /></div>
                      </div>
                      <div class="wpforo-tr">
                        <div class="wpforo-td"><label for="aol"><?php wpforo_phrase('AOL IM') ?>:</label></div><div class="wpforo-td"><input autocomplete="off" id="aol" type="text" value="<?php echo esc_attr($aim) ?>" name="member[aim]" maxlength="55" /></div>
                        <div class="wpforo-td"><label for="icq"><?php wpforo_phrase('ICQ') ?>:</label></div><div class="wpforo-td"><input autocomplete="off" id="icq" type="text" value="<?php echo esc_attr($icq) ?>" name="member[icq]" maxlength="55" /></div>
                      </div>
                      <div class="wpforo-tr">
                        <div class="wpforo-td"><label for="msn"><?php wpforo_phrase('MSN') ?>:</label></div><div class="wpforo-td"><input autocomplete="off" id="msn" type="text" value="<?php echo esc_attr($msn) ?>" name="member[msn]" maxlength="55" /></div>
                        <div class="wpforo-td"><label for="skype"><?php wpforo_phrase('Skype') ?>:</label></div><div class="wpforo-td"><input autocomplete="off" id="skype" type="text" value="<?php echo esc_attr($skype) ?>" name="member[skype]" maxlength="55" /></div>
                      </div>
                    </div>
                </div>
              </div>
              <div class="wpforo-tr">
                <div class="wpforo-profile-label wpforo-th wpfbg-7"><label for="countries"><?php wpforo_phrase('Location') ?>:</label></div>
                <div class="wpforo-profile-field wpforo-td wpfbg-9">
                    <?php $countries =  $wpforo->countries ?>
                    <?php if(!empty($countries)) : ?>
                        <select id="countries" name="member[location]">
                        	<option value="">----</option>
                            <?php foreach($countries as $country) : ?>
                                <option value="<?php echo esc_attr($country) ?>" <?php echo ($country == $location ? 'selected="TRUE"' : '' ); ?>><?php echo esc_html($country) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif ?>
                </div>
              </div>
              <div class="wpforo-tr">
                <div class="wpforo-profile-label wpforo-th wpfbg-7"><label for="timezone"><?php wpforo_phrase('Timezone') ?>:</label></div>
                <div class="wpforo-profile-field wpforo-td wpfbg-9">
                    <select id="timezone" name="member[timezone]">
                    	<?php echo wp_timezone_choice($timezone) ?>
                    </select>
                </div>
              </div>
              <div class="wpforo-tr">
                <div class="wpforo-profile-label wpforo-th wpfbg-7"><label for="occupation"><?php wpforo_phrase('Occupation') ?>:</label></div>
                <div class="wpforo-profile-field wpforo-td wpfbg-9"><input autocomplete="off" id="occupation" type="text" value="<?php echo esc_attr($occupation) ?>" name="member[occupation]" maxlength="255" /></div>
              </div>
              <?php if( wpforo_feature('signature', $wpforo) ): ?>
                  <div class="wpforo-tr">
                    <div class="wpforo-profile-label wpforo-th wpfbg-7"><label for="signature"><?php wpforo_phrase('Signature') ?>:</label></div>
                    <div class="wpforo-profile-field wpforo-td wpfbg-9"><textarea id="signature" name="member[signature]" style="height:80px; width:100%;"><?php echo esc_textarea($signature) ?></textarea></div>
                  </div>
              <?php endif; ?>
              <div class="wpforo-tr">
                <div class="wpforo-profile-label wpforo-th wpfbg-7"><label for="about"><?php wpforo_phrase('About Me') ?>:</label></div>
                <div class="wpforo-profile-field wpforo-td wpfbg-9"><textarea id="about" name="member[about]"  style="height:120px; width:100%;"><?php echo esc_textarea($about) ?></textarea></div>
              </div>
              <div class="wpforo-tr">
                <div class="wpforo-profile-label wpforo-th wpfbg-7"><?php wpforo_phrase('Change password') ?>:</div>
                <div class="wpforo-profile-field wpforo-td wpfbg-9">
                    <input autocomplete="off" type="password" name="member[old_pass]" placeholder="<?php wpforo_phrase('old password') ?>"/><br/><br/>
                    <input autocomplete="off" type="password" name="member[new_pass]" placeholder="<?php wpforo_phrase('new password') ?>"/><br/><br/>
                    <input autocomplete="off" type="password" name="member[re_new_pass]" placeholder="<?php wpforo_phrase('new password again') ?>"/><br/><br/>
                </div>
              </div>
              <div class="wpforo-tr">
                <div class="wpforo-profile-label wpforo-th wpfbg-7">&nbsp;</div>
                <div class="wpforo-profile-field wpforo-td wpfbg-7"><input type="submit" value="<?php wpforo_phrase('Save Changes') ?>" /></div>
              </div>
           </div>
        </form>
    <?php else: ?>
    	<p class="wpf-p-error"><?php wpforo_phrase('Permission denied') ?></p>
	<?php endif; ?>
</div>