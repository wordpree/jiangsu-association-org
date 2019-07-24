<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
	if( !current_user_can('administrator') ) exit;
?>

	<?php if( !isset( $_GET['action'] ) ): ?>
    
    	<?php if (!class_exists('Akismet')): ?>
    		<div style="width:94%; clear:both; margin:0px 0 15px 0; text-align:center; line-height:22px; font-size:14px; color:#D35206; border:1px dotted #ccc; padding:10px 20px 10px 20px;; background:#F7F5F5;">
				<a href="https://wordpress.org/plugins/akismet/" target="_blank">Akismet</a> <?php _e('is not installed! For an advanced Spam Control please install Akismet antispam plugin, it works well with wpForo Spam Control system. Akismet is already integrated with wpForo. It\'ll help to filter posts and protect forum against spam attacks.', 'wpforo'); ?>
            </div>
    	<?php else: ?>
        	
		<?php endif; ?>
        
    	<form action="" method="POST" class="validate">
            <?php wp_nonce_field( 'wpforo-tools-antispam' ); ?>
            <div class="wpf-tool-box wpf-spam-attach right-box">
            	<h3>
				<?php _e('Spam Control', 'wpforo'); ?>
                <p class="wpf-info"><?php _e('Some useful options to limit just registered users and minimize spam. This control don\'t affect users whose Usergroup has "Can edit member" and "Can pass moderation" permissions.', 'wpforo'); ?></p>
                </h3>
                <div style="margin-top:10px; clear:both;">
                	<table style="width:100%;">
                      <tbody>
                        <tr>
                            <th><label><?php _e('Enable wpForo Spam Control','wpforo'); ?>:</label></th>
                            <td>
                                <div class="wpf-switch-field">
                                    <input id="spam_filter_yes" type="radio" name="wpforo_tools_antispam[spam_filter]" value="1" <?php wpfo_check($wpforo->tools_antispam['spam_filter'], 1); ?>/><label for="spam_filter_yes"><?php _e('Yes','wpforo'); ?></label> &nbsp;  
                                    <input id="spam_filter_no" type="radio" name="wpforo_tools_antispam[spam_filter]" value="0" <?php wpfo_check($wpforo->tools_antispam['spam_filter'], 0); ?>/><label for="spam_filter_no"><?php _e('No','wpforo'); ?></label> 
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><label><?php _e('Ban user when spam is suspicted','wpforo'); ?>:</label></th>
                            <td>
                                <div class="wpf-switch-field">
                                    <input id="spam_user_ban_yes" type="radio" name="wpforo_tools_antispam[spam_user_ban]" value="1" <?php wpfo_check($wpforo->tools_antispam['spam_user_ban'], 1); ?>/><label for="spam_user_ban_yes"><?php _e('Yes','wpforo'); ?></label> &nbsp;  
                                    <input id="spam_user_ban_no" type="radio" name="wpforo_tools_antispam[spam_user_ban]" value="0" <?php wpfo_check($wpforo->tools_antispam['spam_user_ban'], 0); ?>/><label for="spam_user_ban_no"><?php _e('No','wpforo'); ?></label> 
                                </div>
                            </td>
                        </tr>  	
                        <tr style="visibility:hidden;">
                            <th><label><?php _e('Notify via email when new user is banned','wpforo'); ?>:</label></th>
                            <td>
                                <div class="wpf-switch-field">
                                    <input id="spam_user_ban_notification_yes" type="radio" name="wpforo_tools_antispam[spam_user_ban_notification]" value="1" <?php wpfo_check($wpforo->tools_antispam['spam_user_ban_notification'], 1); ?>/><label for="spam_user_ban_notification_yes"><?php _e('Yes','wpforo'); ?></label> &nbsp;  
                                    <input id="spam_user_ban_notification_no" type="radio" name="wpforo_tools_antispam[spam_user_ban_notification]" value="0" <?php wpfo_check($wpforo->tools_antispam['spam_user_ban_notification'], 0); ?>/><label for="spam_user_ban_notification_no"><?php _e('No','wpforo'); ?></label> 
                                </div>
                            </td>
                        </tr> 
                        <tr>
                            <th><label ><?php _e('Spam Suspicion Level for Topics', 'wpforo'); ?></label></th>
                            <td><input type="number" min="0" max="100" name="wpforo_tools_antispam[spam_filter_level_topic]" value="<?php wpfo($wpforo->tools_antispam['spam_filter_level_topic']) ?>" class="wpf-field" /></td>
                        </tr> 	
                        <tr>
                            <th><label ><?php _e('Spam Suspicion Level for Posts', 'wpforo'); ?></label></th>
                            <td><input type="number" min="0" max="100" name="wpforo_tools_antispam[spam_filter_level_post]" value="<?php wpfo($wpforo->tools_antispam['spam_filter_level_post']) ?>" class="wpf-field" /></td>
                        </tr> 
                        <?php if (class_exists('Akismet')): ?>
                        <tr>
                            <td colspan="2" style="color:#fff; background:#7C9B2E; font-size:20px; padding:10px 10px; text-align:center; font-family:'Lucida Grande', 'Lucida Sans Unicode'"><strong>A&middot;kis&middot;met</strong> <?php _e(' is enabled','wpforo'); ?></td>
                        </tr> 
                        <?php endif; ?>	
                      </tbody>
                    </table>
                </div>
            </div>
            <div class="wpf-tool-box wpf-spam-attach left-box">
            	<h3>
				<?php _e('New Registered User', 'wpforo'); ?>
                <p class="wpf-info"><?php _e('Some useful options to limit just registered users and minimize spam. These options don\'t affect users whose Usergroup has "Can edit member" and "Can pass moderation" permissions.', 'wpforo'); ?></p>
                </h3>
                <div style="margin-top:10px; clear:both;">
                	<table style="width:100%;">
                      <tbody>
                        <tr>
                            <th style="width:65%;">
                            	<label ><?php _e('User is New (under hard spam control) during', 'wpforo'); ?></label>
                            </th>
                            <td><?php _e('first', 'wpforo'); ?> <input type="number" min="0" name="wpforo_tools_antispam[new_user_max_posts]" value="<?php wpfo($wpforo->tools_antispam['new_user_max_posts']) ?>" class="wpf-field" style="width:50px;" /> <?php _e('posts', 'wpforo'); ?></td>
                        </tr>
                        <tr>
                            <th style="width:65%;"><label ><?php _e('Min number of posts to be able attach files', 'wpforo'); ?></label></th>
                            <td><input type="number" min="0" name="wpforo_tools_antispam[min_number_post_to_attach]" value="<?php wpfo($wpforo->tools_antispam['min_number_post_to_attach']) ?>" class="wpf-field" style="max-width:80px;" /></td>
                        </tr>
                        <tr>
                            <th><label><?php _e('Min number of posts to be able post links', 'wpforo'); ?></label></th>
                            <td><input type="number" min="0" name="wpforo_tools_antispam[min_number_post_to_link]" value="<?php wpfo($wpforo->tools_antispam['min_number_post_to_link']) ?>" class="wpf-field" style="max-width:80px;" /></td>
                        </tr>
                        <tr>
                            <th colspan="2">
                            <label><?php _e('Do not allow to attach files with following extensions:', 'wpforo'); ?></label>
                            <textarea name="wpforo_tools_antispam[limited_file_ext]" style="width:100%; height:60px; margin-top:10px;  color:#666666; background:#fdfdfd;"><?php echo esc_textarea(stripslashes($wpforo->tools_antispam['limited_file_ext'])); ?></textarea></td>
                        </tr>	  	
                      </tbody>
                    </table>
                </div>
            </div>
            <div class="wpf-tool-box wpf-spam-attach right-box" id="spam-files">
				<?php 
				$site = get_bloginfo('url');
                $upload_dir = wp_upload_dir();
                $default_attachments_dir =  $upload_dir['basedir'] . '/wpforo/default_attachments/';
                ?>
            	<h3>
				<?php _e('Possible Spam Attachments', 'wpforo'); ?>
                <p class="wpf-info"><?php _e('This tool is designed to find attachment which have been uploaded by spammers. The tool checks most common spammer filenames and suggest to delete but you should check one by one and make sure those are spam files before deleting.', 'wpforo'); ?></p>
                </h3>
                <div class="wpf-spam-attach-dir"><?php _e('Directory', 'wpforo'); ?>: <?php echo str_replace($site, '', $upload_dir['baseurl']); ?>/wpforo/default_attachments/&nbsp;</div>
                <div style="margin-top:10px; clear:both;">
                	<table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tbody>
                        <?php 
						if(is_dir($default_attachments_dir)):
							if ($handle = opendir($default_attachments_dir)):
								while (false !== ($filename = readdir($handle))):
                                    if( $filename == '.' ||  $filename == '..') continue;

                                    $level = 0;  $color ='';
                                    $file = $default_attachments_dir . '/' . $filename;
                                    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
									if( !$level = $wpforo->moderation->spam_file($filename) ) continue;
									if( $level == 2 ) $color = 'style="color:#EE9900;"';
									if( $level == 3 ) $color = 'style="color:#FF0000;"';
									if( $level == 4 ) $color = 'style="color:#BB0000;"';
									?>
										<tr>
                                          <td class="wpf-spam-item" <?php echo $color; ?> title="<?php echo $upload_dir['baseurl'] .'/wpforo/default_attachments/'. $filename ?>">
										  	<?php if( $wpforo->moderation->spam_file($filename, 'file-open') ): ?>
                                            	<a href="<?php echo $upload_dir['baseurl'] .'/wpforo/default_attachments/'. $filename ?>" target="_blank" <?php echo $color ?>><?php echo wpforo_text($filename, 50, false); ?></a>
                                            <?php else: ?>
                                            	<?php echo $filename; ?>
                                            <?php endif; ?>
												<?php echo ' (' . strtoupper($extension) . ' | ' . wpforo_human_filesize(filesize($file), 1) . ')'; ?>
                                          </td>
										  <td class="wpf-actions"><a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=wpforo-tools&tab=antispam&action=delete-spam-file&sfname=' . urlencode($filename) ), 'wpforo_tools_antispam_files' ); ?>" title="<?php _e('Delete this file', 'wpforo'); ?>"  onclick="return confirm('<?php _e('Are you sure you want to permanently delete this file?', 'wpforo'); ?>');"><?php _e('Delete', 'wpforo'); ?></a></td>
										</tr>
									<?php 
								endwhile;
								closedir($handle);
							endif;
						endif;
						?>
                        <tr style="background:#fff;">
                          <td colspan="2" class="wpf-actions" style="padding-top:20px; text-align:right;">
                          	<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=wpforo-tools&tab=antispam&action=delete-all&level=1' ), 'wpforo_tools_antispam_files' ); ?>" 
                            	title="<?php _e('Click to delete Blue marked files', 'wpforo'); ?>" 
                                   onclick="return confirm('<?php _e('Are you sure you want to delete all BLUE marked files listed here. Please download Wordpress /wp-content/uploads/wpforo/ folder to your local computer before deleting files, this is not undoable.', 'wpforo'); ?>');">
								<?php _e('Delete All', 'wpforo'); ?>
                            </a> | 
                            <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=wpforo-tools&tab=antispam&action=delete-all&level=2' ), 'wpforo_tools_antispam_files' ); ?>" 
                            	title="<?php _e('Click to delete Orange marked files', 'wpforo'); ?>" 
                                	style="color:#EE9900;" 
                                    	onclick="return confirm('<?php _e('Are you sure you want to delete all ORANGE marked files listed here. Please download Wordpress /wp-content/uploads/wpforo/ folder to your local computer before deleting files, this is not undoable.', 'wpforo'); ?>');">
								<?php _e('Delete All', 'wpforo'); ?>
                            </a> | 
                            <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=wpforo-tools&tab=antispam&action=delete-all&level=3' ), 'wpforo_tools_antispam_files' ); ?>" 
                            	title="<?php _e('Click to delete Red marked files', 'wpforo'); ?>" 
                                	style="color:#FF0000;" 
                                    	onclick="return confirm('<?php _e('Are you sure you want to delete all RED marked files listed here. Please download Wordpress /wp-content/uploads/wpforo/ folder to your local computer before deleting files, this is not undoable.', 'wpforo'); ?>');">
								<?php _e('Delete All', 'wpforo'); ?>
                            </a> | 
                            <a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=wpforo-tools&tab=antispam&action=delete-all&level=4' ), 'wpforo_tools_antispam_files' ); ?>" 
                            	title="<?php _e('Click to delete Dark Red marked files', 'wpforo'); ?>" 
                                	style="color:#BB0000;" 
                                    	onclick="return confirm('<?php _e('Are you sure you want to delete all DARK RED marked files listed here. Please download Wordpress /wp-content/uploads/wpforo/ folder to your local computer before deleting files, this is not undoable.', 'wpforo'); ?>');">
								<?php _e('Delete All', 'wpforo'); ?>
                            </a>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                </div>
            </div>
            <div style="clear:both;"></div>
            <div class="wpforo_settings_foot" style="clear:both; margin-top:20px;">
                <input type="submit" class="button button-primary" value="<?php _e('Update Options', 'wpforo'); ?>" />
            </div>
		</form>
	<?php endif ?>