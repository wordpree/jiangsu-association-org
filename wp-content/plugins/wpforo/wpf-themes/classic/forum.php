<?php
if( !$forum = WPF()->current_object['forum'] ) : ?>
	<h1 id="wpforo-title">
		<?php echo esc_html(WPF()->general_options['title']) ?>
		<div class="wpforo-feed">
            <span class="wpf-unread-posts">
                <a href="<?php echo esc_url(wpforo_home_url(WPF()->tpl->slugs['recent'] . '?view=unread' )) ?>">
                    <i class="fas fa-layer-group" style="padding-right: 1px; font-size: 13px;"></i> <span><?php wpforo_phrase('Unread Posts') ?></span>
                </a>
            </span>
			<?php if( wpforo_feature('rss-feed') ): ?>
				<sep> &nbsp;|&nbsp; </sep>
				<span class="wpf-feed-forums">
	                <a href="<?php wpforo_feed_rss2_url(true, 'forum') ?>"  title="<?php wpforo_phrase('Forums RSS Feed') ?>" target="_blank">
	                    <span><?php wpforo_phrase('Forums') ?></span> <i class="fas fa-rss wpfsx"></i>
	                </a>
                    </span><sep> &nbsp;|&nbsp; </sep>
				<span class="wpf-feed-topics">
                    <a href="<?php wpforo_feed_rss2_url(true, 'topic') ?>"  title="<?php wpforo_phrase('Topics RSS Feed') ?>" target="_blank">
                        <span><?php wpforo_phrase('Topics') ?></span> <i class="fas fa-rss wpfsx"></i>
                    </a>
                </span>
			<?php endif; ?>
		</div>
	</h1>
<?php elseif( $forum['is_cat'] ) : ?>
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
        <div class="wpf-clear"></div>
    </div>
<?php endif;
if( $cats = WPF()->current_object['categories'] ) :
	$forum_template = 'forum.php';
	if ( WPF()->current_object['template'] == 'topic' && WPF()->current_object['layout'] == 4 ) {
		$forum_template = 'forum-sub.php';
	}
	foreach($cats as $key => $cat){
		if( WPF()->perm->forum_can( 'vf', $cat['forumid'] ) ){
			$args = array( "parentid" => $cat['forumid'], "type" => 'forum' );
			if( $forums = WPF()->forum->get_forums( $args ) ){
				$layout = ($cat['cat_layout'] ? intval($cat['cat_layout']) : 1);
				do_action( 'wpforo_category_loop_start', $cat, $key );
				include( wpftpl('layouts/' . $layout . '/' . $forum_template) );
				do_action( 'wpforo_category_loop_end', $cat, $key );
			}
		}
	}
else : ?>
	<p class="wpf-p-error">
		<?php wpforo_phrase('No forums were found here.') ?>
	</p>
<?php endif;