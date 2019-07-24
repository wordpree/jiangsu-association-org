<?php
if( WPF()->perm->forum_can('vf') ):

	if( $forum = WPF()->current_object['forum'] ) : ?>
		<div class="wpf-subforum-sep" style="height:20px;"></div>
		<div class="wpf-head-bar">
			<div class="wpf-head-bar-left">
				<h1 id="wpforo-title"><?php echo esc_html($forum['title']) ?></h1>
				<?php if( $forum['description'] ): ?>
					<div id="wpforo-description"><?php echo $forum['description'] ?></div>
				<?php endif; ?>
				<div class="wpf-action-link">
					<?php WPF()->tpl->forum_subscribe_link() ?>
					<?php if( wpforo_feature('rss-feed') ): ?>
						<span class="wpf-feed">|
                            <a href="<?php wpforo_feed_rss2_url() ?>" title="<?php wpforo_phrase('Forum RSS Feed') ?>" target="_blank">
                                <span style="text-transform: uppercase;">
	                                <?php wpforo_phrase('RSS') ?>
                                </span>
                                <i class="fas fa-rss wpfsx"></i>
                            </a>
                        </span>
					<?php endif; ?>
				</div>
			</div>
			<div class="wpf-head-bar-right">
				<?php wpforo_template_add_topic_button() ?>
			</div>
			<div class="wpf-clear"></div>
		</div>

		<?php if( WPF()->perm->forum_can('ct') ) WPF()->tpl->topic_form($forum['forumid']);

		if( $topics = WPF()->current_object['topics'] ) {
			wpforo_template_pagenavi('wpf-navi-topic-top');
			include( wpftpl('layouts/' . $forum['cat_layout'] . '/topic.php') );
			wpforo_template_pagenavi('wpf-navi-topic-bottom');
			do_action( 'wpforo_topic_list_footer' );
		}else{ ?>
			<p class="wpf-p-error">
				<?php wpforo_phrase('No topics were found here') ?>
			</p>
			<?php
		}
	endif;

else : ?>
	<p class="wpf-p-error">
		<?php wpforo_phrase("You don't have permissions to see this page, please register or login for further information") ?>
	</p>
<?php endif;