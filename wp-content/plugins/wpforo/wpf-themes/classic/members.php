<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
?>
<h2 id="wpforo-title"><?php wpforo_phrase('Forum Members') ?></h2>
<div class="wpforo-members-wrap">
	<?php if( $wpforo->perm->usergroup_can('vmem') ): ?>
	    <div class="wpforo-members-search">
	        <form action="<?php echo wpforo_home_url('members') ?>" method="get">
	        	<?php wpforo_make_hidden_fields_from_url( wpforo_home_url('members') ) ?>
	            <input placeholder="<?php wpforo_phrase('Insert member name or email') ?>" required="TRUE" type="text" name="wpfms" class="wpf-member-search-field wpfw-40" value="<?php echo esc_attr($wpfms) ?>" />
	            <input type="submit" class="wpf-member-search" value="<?php wpforo_phrase('Search') ?>" />
	        </form>
	     </div>
	    <div class="wpforo-members-content wpfbg-7">
	         <table width="100%" border="0" cellspacing="0" cellpadding="0" style="width:100%; display:table;">
	            <tr class="wpfbg-3">
	                <?php if( wpforo_feature('avatars', $wpforo) ): ?>
	                    <th class="wpf-members-avatar"><?php wpforo_phrase('Avatar') ?></th>
	                <?php endif; ?>
	                <th class="wpf-members-info"><?php wpforo_phrase('Member information') ?></th>
	                <th class="wpf-members-regdate"><?php wpforo_phrase('Registered date') ?></th>
	            </tr>
	          
	            <?php if(!empty($members)) : ?>
	                
	                <?php $bg = FALSE; foreach($members as $member) : ?>
	                    
	                  <tr<?php echo ( $bg ? ' style="background:#F7F7F7"' : '' ) ?>>
	                    <?php if( wpforo_feature('avatars', $wpforo) ): ?>
	                        <td class="wpf-members-avatar"><?php echo $wpforo->member->avatar($member, 'style="width:64px; height:64px;"'); ?></td>
	                    <?php endif; ?>
	                    <td class="wpf-members-info">
	                        <a href="<?php echo esc_url($wpforo->member->profile_url($member)) ?>" class="wpf-member-name" title="<?php $wpforo->member->show_online_indicator($member['ID'], FALSE) ?>">
	                            <?php $wpforo->member->show_online_indicator($member['ID']) ?>&nbsp;
	                            <?php echo  $member['display_name'] ? esc_html($member['display_name']) : esc_html(urldecode($member['user_nicename'])) ?>
	                        </a>
	                        
	                        <?php do_action('wpforo_after_member_badge', $member) ?>
	                        
	                        <br />
	                        <?php $enabled_for_usergroup = ( isset($wpforo->member_options['rating_badge_ug'][$member['groupid']]) && $wpforo->member_options['rating_badge_ug'][$member['groupid']] ) ? true : false ; ?>
	                        <span class="wpf-member-info wpfcl-1"> <i class="fa fa-users" title="<?php wpforo_phrase('Usergroup') ?>"></i>&nbsp; <?php wpforo_phrase($member['groupname']) ?> | <?php if( wpforo_feature('rating', $wpforo) && $enabled_for_usergroup ): ?><i class="fa fa-star" title="<?php wpforo_phrase('Rating') ?>"></i>&nbsp;<?php echo $wpforo->member->rating_level( $member['posts'], FALSE ) ?>/10  |<?php endif; ?> <?php wpforo_phrase('Posts') ?>: <?php echo intval($member['posts']) ?></span>
	                    	| <div class="wpf-member-profile-buttons" style="display:inline-block;">
                                <?php $wpforo->tpl->member_buttons($member) ?>
                            </div>
                        </td>
	                    <td class="wpf-members-regdate wpfcl-1"><?php wpforo_date($member['user_registered'], 'F j, Y') ?></td>
	                  </tr>
	                    
	                <?php $bg = ( $bg ? FALSE : TRUE ); endforeach; ?>
	              
	            <?php else : ?>
	                
	                <tr>
	                    <td colspan="3"><p class="wpf-p-error"> <?php wpforo_phrase('Members not found') ?> </p></td>
	                </tr>
	                
	            <?php endif ?>
	            
	         </table>
	    </div>
	    <div class="wpf-members-foot">
	        <?php $wpforo->tpl->pagenavi( $paged, $items_count, FALSE ); ?>
	    </div>
	<?php else : ?>
		<p class="wpf-p-error"> <?php wpforo_phrase('You have not permission to this page') ?> </p>
	<?php endif; ?>
</div>