<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;

function wpforo_thread_topic_template( $topicid ){
    $thread = wpforo_thread( $topicid );
    if(empty($thread)) return;
    ?>
    <div class="wpf-thread <?php wpforo_unread($topicid, 'topic'); ?>">
        <div class="wpf-thread-body">
            <div class="wpf-thread-box wpf-thread-status">
                <div class="wpf-thread-statuses" <?php echo $thread['wrap']; ?>><?php echo $thread['icons_html']; ?></div>
            </div>
            <div class="wpf-thread-box wpf-thread-title">
                <span class="wpf-thread-status-mobile"><?php wpforo_topic_icon($thread); ?> </span>
                <a href="<?php echo esc_url($thread['url']) ?>" title="<?php echo esc_attr($thread['title'])?>"><?php wpforo_text($thread['title'], WPF()->forum->options['layout_threaded_intro_topics_length']); ?></a> <?php wpforo_viewing( $thread ); ?>
                <?php wpforo_tags($thread, true, 'text') ?>
            </div>
            <div class="wpf-thread-box wpf-thread-posts">
                <?php echo (intval($thread['posts']) - 1) ?>
            </div>
            <div class="wpf-thread-box wpf-thread-views">
                <?php echo $thread['views'] ?>
            </div>
            <div class="wpf-thread-box wpf-thread-users">
                <?php echo $thread['users_html']; ?>
                <div class="wpf-thread-date-mobile"><?php echo $thread['last_post_date'] ?></div>
            </div>
            <div class="wpf-thread-box wpf-thread-date">
                <?php echo $thread['last_post_date'] ?>
            </div>
        </div>
    </div>
    <?php
}
?>