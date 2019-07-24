<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
?>

<div class="wpforo-profile-wrap">
	<?php if( !empty($user) && ( $wpforo->current_userid == $user['userid'] || $wpforo->perm->usergroup_can('vprf')) ) : 
		$filtered_user = apply_filters('wpforo_profile_header_obj', $user);
		extract($filtered_user); ?>
	    
	    <div class="wpforo-profile-head">
	  		
	  	  <?php do_action( 'wpforo_profile_plugin_menu_action', $userid ); ?>
	      
	      <div class="h-header">
	      	<?php if( wpforo_feature('avatars', $wpforo) ): $rsz =''; ?>
	        	<div class="h-left"><?php echo $wpforo->member->avatar($filtered_user, 'alt="'.esc_html($display_name).'"', 150); ?></div>
	        <?php else: $rsz = ' style="margin-left:10px;"'; endif; ?>
	        <div class="h-right" <?php echo $rsz; //This is a HTML content; ?>>
	             <div class="h-top">
	                <div class="profile-display-name">
	                	<?php $wpforo->member->show_online_indicator($userid) ?>
	                    <?php echo $display_name ? esc_html($display_name) : esc_html(urldecode($user_nicename)) ?>
	                </div>
	                <div class="profile-stat-data">
	                    <div class="profile-stat-data-item"><?php wpforo_phrase('Group') ?>: <?php wpforo_phrase($groupname) ?></div>
	                    <div class="profile-stat-data-item"><?php wpforo_phrase('Joined') ?>: <?php wpforo_date($user_registered, 'Y/m/d') ?></div>
                        <div class="profile-stat-data-item"><?php wpforo_phrase('Title') ?>: <?php wpforo_member_title($filtered_user); ?></div>
	                    <?php do_action( 'wpforo_profile_data_item', $wpforo->current_object ) ?>
						<?php $enabled_for_usergroup = ( isset($wpforo->member_options['rating_badge_ug'][$groupid]) && $wpforo->member_options['rating_badge_ug'][$groupid] ) ? true : false ; ?>
						<?php if( wpforo_feature('rating', $wpforo) && $enabled_for_usergroup ): ?>
	                        <div class="profile-rating-bar">
	                            <div class="profile-rating-bar-wrap" title="<?php wpforo_phrase('Member Rating') ?>">
	                                <?php $levels = $wpforo->member->levels(); ?>
									<?php $rating_level = $wpforo->member->rating_level( $posts, false );?>
	                                <?php for( $a=1; $a <= $rating_level; $a++ ): ?>
	                                    <div class="rating-bar-cell" style="background-color:<?php echo esc_attr($filtered_user['stat']['color']); ?>;">
	                                        <i class="fa <?php echo sanitize_html_class($wpforo->member->rating($a, 'icon')) ?>"></i>
	                                    </div>
	                                <?php endfor; ?>
	                                <?php for( $i = ($rating_level+1); $i <= (count($levels)-1); $i++ ): ?>
	                                    <div class="wpfbg-7 rating-bar-cell" >
	                                        <i class="fa <?php echo sanitize_html_class($wpforo->member->rating($i, 'icon')) ?>"></i>
	                                    </div>
	                                <?php endfor; ?>
	                            </div>
	                        </div>
	                        <div class="wpf-profile-badge" title="<?php wpforo_phrase('Rating Badge') ?>" style="background-color:<?php echo esc_attr($filtered_user['stat']['color']); ?>;">
	                            <?php echo $wpforo->member->rating_badge($rating_level, 'short'); ?>
	                        </div>
	                	<?php endif; ?>
	                	<?php do_action('wpforo_after_member_badge', $filtered_user); ?>
	                </div>
	            </div>
	        </div>
	      <div class="wpf-clear"></div>
	      </div>
	      <div class="h-footer wpfbg-2">
	      
	        <div class="h-bottom">
	            <?php $wpforo->tpl->member_menu($userid) ?>
	            <div class="wpf-clear"></div>
	        </div>
	      
	      </div>
	    </div>
	    <div class="wpforo-profile-content">
	    	<?php $wpforo->tpl->member_template() ?>
	    </div>
	<?php elseif( !empty($user) && !( $wpforo->current_userid == $user['userid'] || $wpforo->perm->usergroup_can('vprf')) ) : ?>
		<div class="wpforo-profile-content wpfbg-7">
			<div class="wpfbg-7" style="border: #E6E6E6 1px solid; margin-top:3px;">
				<div style="border: 1px dotted #91cf89; display: block; font-size: 14px; text-align: center; padding: 5px 10px; margin: 10px auto; width: auto; color: #000; background-color: #F5F5F5;">
					<?php wpforo_phrase('You have not permission to this page') ?>
				</div>
			</div>
		</div>
	<?php else : ?>
		<div class="wpforo-profile-content wpfbg-7">
			<div class="wpfbg-7" style="border: #E6E6E6 1px solid; margin-top:3px;">
				<div style="border: 1px dotted #91cf89; display: block; font-size: 14px; text-align: center; padding: 5px 10px; margin: 10px auto; width: auto; color: #000; background-color: #F5F5F5;">
					<?php $wpforo->tpl->member_error() ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>