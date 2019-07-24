<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
?>


<?php do_action( 'wpforo_footer_hook' ) ?>

<!-- forum statistic -->
	<div class="wpf-clear"></div>
   
	<div id="wpforo-footer">
    	<?php do_action( 'wpforo_stat_bar_start', $wpforo ); ?>
     	<?php if( wpforo_feature('footer-stat', $wpforo) ): ?>
            <div id="wpforo-stat-header">
                <i class="fa fa-bar-chart"></i>&nbsp; <span><?php wpforo_phrase('Forum Statistics') ?></span>
            </div>
            <div id="wpforo-stat-body">
                <?php $stat = $wpforo->statistic();  ?>
                <div class="wpforo-stat-table">
                    <div class="wpf-row wpf-stat-data">
                        <div class="wpf-stat-item">
                            <i class="fa fa-comments"></i>
                            <span class="wpf-stat-value"><?php echo wpforo_print_number($stat['forums']) ?></span>
                            <span class="wpf-stat-label"><?php wpforo_phrase('Forums') ?></span>
                        </div>
                        <div class="wpf-stat-item">
                            <i class="fa fa-file-text"></i>
                            <span class="wpf-stat-value"><?php echo wpforo_print_number($stat['topics']) ?></span>
                            <span class="wpf-stat-label"><?php wpforo_phrase('Topics') ?></span>
                        </div>
                        <div class="wpf-stat-item"> 
                            <i class="fa fa-reply fa-rotate-180"></i>
                            <span class="wpf-stat-value"><?php echo wpforo_print_number($stat['posts']) ?></span>
                            <span class="wpf-stat-label"><?php wpforo_phrase('Posts') ?></span>
                        </div>
                        <div class="wpf-stat-item">
                            <i class="fa fa-lightbulb-o"></i>
                            <span class="wpf-stat-value"><?php echo wpforo_print_number($stat['online_members_count']) ?></span>
                            <span class="wpf-stat-label"><?php wpforo_phrase('Online') ?></span>
                        </div>
                        <div class="wpf-stat-item">
                            <i class="fa fa-user"></i>
                            <span class="wpf-stat-value"><?php echo wpforo_print_number($stat['members']) ?></span>
                            <span class="wpf-stat-label"><?php wpforo_phrase('Members') ?></span>
                        </div>
                    </div>
                    <div class="wpf-row wpf-last-info">
                    	<?php if(isset($stat['last_post_title']) && $stat['last_post_title']): ?>
                        <p class="wpf-stat-other">
                            <span ><i class="fa fa-pencil"></i> <?php wpforo_phrase('Latest Post') ?>: <a href="<?php echo esc_url($stat['last_post_url']) ?>"><?php echo esc_html($stat['last_post_title']) ?></a></span>
                            <span><i class="fa fa-user-plus"></i> <?php wpforo_phrase('Our newest member') ?>: <a href="<?php echo esc_url($stat['newest_member_profile_url']) ?>"><?php echo esc_html($stat['newest_member_dname']) ?></a></span>
                        </p>
                        <?php endif; ?>
                        <p class="wpf-topic-icons">
                        	<span class="wpf-stat-label"><?php wpforo_phrase('Topic Icons') ?>:</span>
                            <span><i class="fa fa-file-o wpfcl-2"></i> <?php wpforo_phrase('New') ?></span>
                            <span><i class="fa fa-file-text-o wpfcl-2"></i> <?php wpforo_phrase('Replied') ?></span>
                            <span><i class="fa fa-file-text wpfcl-2"></i> <?php wpforo_phrase('Active') ?></span>
                            <span><i class="fa fa-file-text wpfcl-5"></i> <?php wpforo_phrase('Hot') ?></span>
                            <span><i class="fa fa-thumb-tack wpfcl-5"></i> <?php wpforo_phrase('Sticky') ?></span>
                            <span><i class="fa fa-check-circle wpfcl-8"></i> <?php wpforo_phrase('Solved') ?></span>
                            <span><i class="fa fa-eye-slash wpfcl-1"></i> <?php wpforo_phrase('Private') ?></span>
                            <span><i class="fa fa-lock wpfcl-1"></i> <?php wpforo_phrase('Closed') ?></span>
                        </p>
                    </div>
                </div>
            </div>
		<?php endif; ?>
        <?php $wpforo->tpl->copyright() ?>
        <?php do_action( 'wpforo_stat_bar_end', $wpforo ); ?>
  	</div>	<!-- wpforo-footer -->
  	
  	<?php do_action( 'wpforo_bottom_hook' ) ?>
    <?php wpforo_debug($wpforo); ?>
    
</div><!-- wpforo-wrap -->

<div id="wpforo-load" class="wpforo-load">
	<i class="fa fa-3x fa-spinner fa-spin"></i>&nbsp;&nbsp;<br/>
	<span class="loadtext"><?php wpforo_phrase('Working') ?></span>
</div>

<div id="wpf-msg-box">
	<p class="wpf-msg-box-triangle-right top" style="font-size:0px"><span><?php echo sprintf( wpforo_phrase('Please %s or %s', FALSE), '<a href="' . wpforo_login_url() . '" rel="nofollow">'.wpforo_phrase('Login', FALSE).'</a>', '<a href="' . wpforo_register_url() . '" rel="nofollow">'.wpforo_phrase('Register', FALSE).'</a>' ) ?></span></p>	
</div>