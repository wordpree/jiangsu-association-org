<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
?>

<div class="wpforo-profile-home">
    
	<?php if( $wpforo->perm->usergroup_can('vmr') ): ?>
        <div class="wpf-profile-section wpf-ma-section">
            <div class="wpf-profile-section-head">
            	<i class="fa fa-bar-chart"></i>
				<?php wpforo_phrase('Member Activity'); ?>
            </div>
            <div class="wpf-profile-section-body">
                <div class="wpf-statbox wpfbg-9">
                    <div class="wpf-statbox-body">
                        <div class="wpf-statbox-icon wpfcl-5"><i class="fa fa-pencil"></i></div>
                        <div class="wpf-statbox-value"><?php wpforo_print_number($posts, true) ?></div>
                        <div class="wpf-statbox-title"><?php wpforo_phrase('Forum Posts') ?></div>
                    </div>
                </div>
                <div class="wpf-statbox wpfbg-9">
                    <div class="wpf-statbox-body">
                        <div class="wpf-statbox-icon wpfcl-5"><i class="fa fa-file-text"></i></div>
                        <div class="wpf-statbox-value"><?php echo (isset($stat['topics'])) ? (int)wpforo_print_number($stat['topics']) : 0 ; ?></div>
                        <div class="wpf-statbox-title"><?php wpforo_phrase('Topics') ?></div>
                    </div>
                </div>
                <div class="wpf-statbox wpfbg-9">
                    <div class="wpf-statbox-body">
                        <div class="wpf-statbox-icon wpfcl-5"><i class="fa fa-question"></i></div>
                        <div class="wpf-statbox-value"><?php wpforo_print_number($questions, true) ?></div>
                        <div class="wpf-statbox-title"><?php wpforo_phrase('Questions') ?></div>
                    </div>
                </div>
                <div class="wpf-statbox wpfbg-9">
                    <div class="wpf-statbox-body">
                        <div class="wpf-statbox-icon wpfcl-5"><i class="fa fa-check"></i></div>
                        <div class="wpf-statbox-value"><?php wpforo_print_number($answers, true) ?></div>
                        <div class="wpf-statbox-title"><?php wpforo_phrase('Answers') ?></div>
                    </div>
                </div>
                <div class="wpf-statbox wpfbg-9">
                    <div class="wpf-statbox-body">
                        <div class="wpf-statbox-icon wpfcl-5"><i class="fa fa-comment"></i></div>
                        <div class="wpf-statbox-value"><?php wpforo_print_number($comments, true) ?></div>
                        <div class="wpf-statbox-title"><?php wpforo_phrase('Question Comments') ?></div>
                    </div>
                </div>
                <div class="wpf-statbox wpfbg-9">
                    <div class="wpf-statbox-body">
                        <div class="wpf-statbox-icon wpfcl-5"><i class="fa fa-thumbs-up"></i> </div>
                        <div class="wpf-statbox-value"><?php wpforo_print_number($wpforo->member->get_votes_and_likes_count( $userid ), true);  ?></div>
                        <div class="wpf-statbox-title"><?php wpforo_phrase('Liked') ?></div>
                    </div>
                </div>
                <div class="wpf-statbox wpfbg-9">
                    <div class="wpf-statbox-body">
                        <div class="wpf-statbox-icon wpfcl-5"><i class="fa fa-thumbs-up fa-flip-horizontal"></i></div>
                        <div class="wpf-statbox-value"><?php wpforo_print_number($wpforo->member->get_user_votes_and_likes_count( $userid ), true);  ?></div>
                        <div class="wpf-statbox-title"><?php wpforo_phrase('Received Likes') ?></div>
                    </div>
                </div>
                <div class="wpf-statbox wpfbg-9">
                    <div class="wpf-statbox-body">
                        <div class="wpf-statbox-icon wpfcl-5"><i class="fa fa-star"></i></div>
                        <div class="wpf-statbox-value"><?php echo $wpforo->member->rating_level( $posts, FALSE ) ?>/10</div>
                        <div class="wpf-statbox-title"><?php wpforo_phrase('Rating') ?></div>
                    </div>
                </div>
                <div class="wpf-statbox wpfbg-9">
                    <div class="wpf-statbox-body">
                        <div class="wpf-statbox-icon wpfcl-5"><i class="fa fa-pencil-square"></i></div>
                        <div class="wpf-statbox-value"><?php echo $wpforo->member->blog_posts($userid, $user_email) ?></div>
                        <div class="wpf-statbox-title"><?php wpforo_phrase('Blog Posts') ?></div>
                    </div>
                </div>
                <div class="wpf-statbox wpfbg-9">
                    <div class="wpf-statbox-body">
                        <div class="wpf-statbox-icon wpfcl-5"><i class="fa fa-comments"></i></div>
                        <div class="wpf-statbox-value"><?php echo $wpforo->member->blog_comments($userid, $user_email) ?></div>
                        <div class="wpf-statbox-title"><?php wpforo_phrase('Blog Comments') ?></div>
                    </div>
                </div>
            	<div class="wpf-clear"></div>
             </div>
        </div>
    <?php endif; ?>
    
    <div class="wpf-profile-section wpf-mi-section">
     	<div class="wpf-profile-section-head">
        	<i class="fa fa-list"></i>
            <?php wpforo_phrase('Member Information'); ?>
        </div>
     	<div class="wpforo-table">
           <?php if( $wpforo->perm->usergroup_can('vmam') ): ?>
                <?php if($about) : ?>
                  <div class="wpforo-tr">
                    <div class="wpforo-profile-label wpforo-th wpfbg-7"><strong><?php wpforo_phrase('About Me') ?></strong></div>
                    <div class="wpforo-profile-field wpforo-td wpfbg-9"><p><?php echo wpforo_kses($about, 'user_description') ?></p></div>
                  </div>
                <?php endif ?>
            <?php endif ?>
            <?php if( $wpforo->perm->usergroup_can('vmw') ): ?>
                <?php if($site) : ?>
                  <div class="wpforo-tr">
                    <div class="wpforo-profile-label wpforo-th wpfbg-7"><?php wpforo_phrase('Website') ?></div>
                    <div class="wpforo-profile-field wpforo-td wpfbg-9"><?php echo esc_url($site) ?></div>
                  </div>
                <?php endif ?>
            <?php endif ?>
            <?php if( $facebook | $twitter | $gtalk | $yahoo | $aim | $icq | $msn | $skype ) : ?>
                <?php $social_access = ( $wpforo->perm->usergroup_can('vmsn') ?  TRUE : FALSE ) ?>
              <div class="wpforo-tr">
                <div class="wpforo-profile-label wpforo-th wpfbg-7"><?php wpforo_phrase('Social Networks') ?></div>
                <div class="wpforo-profile-field wpforo-td wpfbg-9">
                    <div class="wpforo-profile-socnet wpforo-table">
                      <div class="wpforo-tr">
                        <?php if($facebook) : ?>
                            <div class="wpforo-td" style="width:30%;"><?php wpforo_phrase('Facebook') ?>:</div><div class="wpforo-td"><?php echo ( $social_access  ? '<a href="'.esc_url($facebook).'" target="_blank">'.esc_html($facebook).'</a>' : ''  ) ?></div>
                        <?php endif ?>
                      </div>
                      <div class="wpforo-tr">
                        <?php if($twitter) : ?>
                            <div class="wpforo-td"><?php wpforo_phrase('Twitter') ?>:</div><div class="wpforo-td"> <?php echo ( $social_access  ? '<a href="'.esc_url($twitter).'" target="_blank">'.esc_html($twitter).'</a>' : ''  ) ?></div>
                        <?php endif ?>
                      </div>
                      <div class="wpforo-tr">
                        <?php if($gtalk) : ?>
                            <div class="wpforo-td"><?php wpforo_phrase('Google+') ?>:</div><div class="wpforo-td"><?php echo ( $social_access  ? '<a href="'.esc_url($gtalk).'" target="_blank">'.esc_html($gtalk).'</a>' : ''  ) ?></div>
                        <?php endif ?>
                      </div>
                      <div class="wpforo-tr">
                        <?php if($yahoo) : ?>
                            <div class="wpforo-td"><?php wpforo_phrase('Yahoo') ?>:</div><div class="wpforo-td"><?php echo ( $social_access  ? '<a href="'.esc_url($yahoo).'" target="_blank">'.esc_html($yahoo).'</a>' : ''  ) ?></div>
                        <?php endif ?>
                      </div>
                      <div class="wpforo-tr">
                        <?php if($aim) : ?>
                            <div class="wpforo-td"><?php wpforo_phrase('AOL IM') ?>:</div><div class="wpforo-td"><?php echo ( $social_access  ? '<a href="'.esc_url($aim).'" target="_blank">'.esc_html($aim).'</a>' : ''  ) ?></div>
                        <?php endif ?>
                      </div>
                      <div class="wpforo-tr">
                        <?php if($icq) : ?>
                            <div class="wpforo-td"><?php wpforo_phrase('ICQ') ?>:</div><div class="wpforo-td"><?php echo ( $social_access  ? '<a href="'.esc_url($icq).'" target="_blank">'.esc_html($icq).'</a>' : ''  ) ?></div>
                        <?php endif ?>
                      </div>
                      <div class="wpforo-tr">
                        <?php if($msn) : ?>
                            <div class="wpforo-td"><?php wpforo_phrase('MSN') ?>:</div><div class="wpforo-td"><?php echo ( $social_access  ? '<a href="'.esc_url($msn).'" target="_blank">'.esc_html($msn).'</a>' : ''  ) ?></div>
                        <?php endif ?>
                      </div>
                      <div class="wpforo-tr">
                        <?php if($skype) : ?>
                            <div class="wpforo-td"><?php wpforo_phrase('Skype') ?>:</div><div class="wpforo-td"><?php echo ( $social_access ? '<a href="skype:'.esc_html($skype).'?userinfo">'.esc_html($skype).'</a>' : ''  ) ?></div>
                        <?php endif ?>
                      </div>
                    </div>
                </div>
              </div>
            <?php endif ?>
            <?php if( $wpforo->perm->usergroup_can('vmlad') ): ?>
                <?php if($last_login) : ?>
                  <div class="wpforo-tr">
                    <div class="wpforo-profile-label wpforo-th wpfbg-7"><?php wpforo_phrase('Last Active') ?></div>
                    <div class="wpforo-profile-field wpforo-td wpfbg-9"><?php wpforo_date($last_login, 'F j, Y, g:i A') ?></div>
                  </div>
                <?php endif ?>
             <?php endif ?>
            <?php if( $wpforo->perm->usergroup_can('vml') ): ?>
                <?php if($location) : ?>
                  <div class="wpforo-tr">
                    <div class="wpforo-profile-label wpforo-th wpfbg-7"><?php wpforo_phrase('Location') ?></div>
                    <div class="wpforo-profile-field wpforo-td wpfbg-9"><?php echo esc_html($location) ?></div>
                  </div>
                <?php endif ?>
                <?php if($timezone) : ?>
                  <div class="wpforo-tr">
                    <div class="wpforo-profile-label wpforo-th wpfbg-7"><?php wpforo_phrase('Timezone') ?></div>
                    <div class="wpforo-profile-field wpforo-td wpfbg-9"><?php echo esc_html($timezone) ?></div>
                  </div>
                <?php endif ?>
            <?php endif ?>
            <?php if( $wpforo->perm->usergroup_can('vmo') ): ?>
              <?php if($occupation) : ?>
                  <div class="wpforo-tr">
                    <div class="wpforo-profile-label wpforo-th wpfbg-7"><?php wpforo_phrase('Occupation') ?></div>
                    <div class="wpforo-profile-field wpforo-td wpfbg-9"><?php echo esc_html($occupation) ?></div>
                  </div>
                <?php endif ?>
            <?php endif ?>
            <?php if( $wpforo->perm->usergroup_can('vms') ): ?>
                <?php if($signature) : ?>
                  <div class="wpforo-tr">
                    <div class="wpforo-profile-label wpforo-th wpfbg-7"><?php wpforo_phrase('Signature') ?></div>
                    <div class="wpforo-profile-field wpforo-td wpfbg-9"><p><?php echo wpautop(wpforo_kses(stripslashes($signature), 'user_description')) ?></p></div>
                  </div>
                <?php endif ?>
            <?php endif ?>
           </div>  
     </div>
</div>