<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;

class wpForoFeed{
	
	private $wpforo;
	
	function __construct( $wpForo ){
		if(!isset($this->wpforo)) $this->wpforo = $wpForo;
	}
	
	function rss2_url($echo = true){
		$url = wpforo_get_request_uri();
		if(isset($this->wpforo->current_object['forumid'])){ $forumid = $this->wpforo->current_object['forumid']; }
		if(isset($this->wpforo->current_object['topicid'])){ $topicid = $this->wpforo->current_object['topicid']; }
		if(isset($forumid) && isset($topicid)){
			$rss2 = $url . '?type=rss2&forum=' . intval($forumid) . '&topic=' . intval($topicid);	
		}
		elseif(isset($forumid) && !isset($topicid)){
			$rss2 = $url . '?type=rss2&forum=' . intval($forumid);
		}
		
		$rss2 = esc_url($rss2);
		
		if($echo){
			echo $rss2;
		}
		else{
			return $rss2;
		}
	}
	
	function rss2_forum( $forum = array(), $topics = array() ){
		if(empty($forum)) return;
		header('Content-Type: ' . feed_content_type('rss2') . '; charset=' . get_option('blog_charset'), true);
		echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?' . '>';
		?><rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:content="http://purl.org/rss/1.0/modules/content/">
            <channel>
                <title><?php echo esc_html($forum['title']); ?> - <?php echo esc_html($this->wpforo->general_options['title']); ?></title>
                <link><?php echo esc_url($forum['forumurl']); ?></link>
                <description><?php echo esc_html($this->wpforo->general_options['description']); ?></description>
                <language><?php bloginfo_rss( 'language' ); ?></language>
                <lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', date('Y-m-d H:i:s'), false); ?></lastBuildDate>
                <generator>wpForo</generator>
                <ttl>60</ttl>
                <?php if(!empty($topics)): ?>
					<?php foreach($topics as $topic): ?>
                    <item>
                        <title><?php echo wpforo_removebb(esc_html($topic['title'])); ?></title>
                        <link><?php echo esc_url($topic['topicurl']); ?></link>
                        <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', $topic['created'], false); ?></pubDate>
                        <description><![CDATA[<?php echo wpforo_removebb(esc_html($topic['description'])) ?>]]></description>
                        <content:encoded><![CDATA[<?php echo wpforo_removebb(esc_html($topic['content'])) ?>]]></content:encoded>
                        <category domain="<?php echo esc_url($forum['forumurl']); ?>"><?php echo esc_html($forum['title']); ?></category>
                        <dc:creator><?php echo esc_html($topic['author']); ?></dc:creator>
                        <guid isPermaLink="true"><?php echo esc_url($topic['topicurl']); ?></guid>
                    </item>
                    <?php endforeach; ?>
                <?php endif; ?>
            </channel>
        </rss>
        <?php
		exit();
	}
	
	function rss2_topic( $forum = array(), $topic = array(), $posts = array() ){
		if(empty($forum)) return;
		header('Content-Type: ' . feed_content_type('rss2') . '; charset=' . get_option('blog_charset'), true);
		echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?' . '>';
		?><rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:content="http://purl.org/rss/1.0/modules/content/">
            <channel>
                <title><?php echo esc_html($topic['title']); ?> - <?php echo esc_html($forum['title']); ?></title>
                <link><?php echo esc_url($topic['topicurl']); ?></link>
                <description><?php echo esc_html($this->wpforo->general_options['description']); ?></description>
                <language><?php bloginfo_rss( 'language' ); ?></language>
                <lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', date('Y-m-d H:i:s'), false); ?></lastBuildDate>
                <generator>wpForo</generator>
                <ttl>60</ttl>
                <?php if(!empty($posts)): ?>
					<?php foreach($posts as $post): ?>
                    <item>
                        <title><?php echo wpforo_removebb(esc_html($post['title'])); ?></title>
                        <link><?php echo esc_url($post['posturl']); ?></link>
                        <pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', $post['created'], false); ?></pubDate>
                        <description><![CDATA[<?php echo wpforo_removebb(esc_html($post['description'])) ?>]]></description>
                        <content:encoded><![CDATA[<?php echo wpforo_removebb(esc_html($post['content'])) ?>]]></content:encoded>
                        <category domain="<?php echo esc_url($forum['forumurl']); ?>"><?php echo esc_html($forum['title']); ?></category>
                        <dc:creator><?php echo esc_html($post['author']); ?></dc:creator>
                        <guid isPermaLink="true"><?php echo esc_url($post['posturl']); ?></guid>
                    </item>
                    <?php endforeach; ?>
                <?php endif; ?>
            </channel>
        </rss>
        <?php
		exit();
	}

}


?>