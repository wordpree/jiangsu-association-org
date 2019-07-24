<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
?>

	<div class="wpfl-2">
    	
		<div class="wpforo-topic-head">
			<div class="head-title"><?php wpforo_phrase('Topic Title') ?></div>
			<div class="head-stat-lastpost"><?php wpforo_phrase('Last Post') ?></div>
			<div class="head-stat-views"><?php wpforo_phrase('Views') ?></div>
			<div class="head-stat-posts"><?php wpforo_phrase('Posts') ?></div>
			<br class="wpf-clear">
		</div>
        
		<?php foreach($topics as $key => $topic) : ?>
			
			<?php 
				$member = wpforo_member($topic);
				if(isset($topic['last_post']) && $topic['last_post'] != 0){
					$last_post = wpforo_post($topic['last_post']); 
					$last_poster = wpforo_member($last_post);
				}
			 	$classes = $wpforo->tpl->icon('topic', $topic, false);
				$class = explode( ' ',  $classes); $class = ( isset($class[0]) ) ? 'wpf-' . str_replace('fa-', '', $class[0]) : '';
			?>
			  
          <div class="topic-wrap <?php echo $class ?>">
              <div class="wpforo-topic">
				  <?php if( wpforo_feature('avatars', $wpforo) ): ?>
                      <div class="wpforo-topic-avatar"><?php echo $wpforo->member->avatar($member, 'alt="'.esc_attr($member['display_name']).'"', 48, true) ?></div>
                  <?php endif; ?>
                  <div class="wpforo-topic-info">
                    <p class="wpforo-topic-title"><a href="<?php echo esc_url( wpforo_topic($topic['topicid'], 'url') ) ?>"><i class="fa fa-1x <?php echo $classes ?>" title="<?php $icon_title = $wpforo->tpl->icon('topic', $topic, false, 'title'); if( $icon_title ) echo esc_html($icon_title) ?>"></i> <?php echo esc_html($topic['title']) ?></a></p>
                    <p class="wpforo-topic-start-info wpfcl-2"><?php wpforo_member_link($member); ?>, <?php wpforo_date($topic['created']); ?></p>
                  	<div class="wpforo-topic-badges"><?php wpforo_hook('wpforo_topic_info_end', $topic); ?></div>
                  </div>
				  <?php if(isset($topic['last_post']) && $topic['last_post'] != 0) : ?>
                  		<div class="wpforo-topic-stat-lastpost"><span><?php wpforo_member_link($last_poster, 'by'); ?> <a href="<?php echo esc_url($last_post['url']) ?>" title="<?php wpforo_phrase('View the latest post') ?>"><i class="fa fa-chevron-right fa-sx wpfcl-a"></i></a></span><br> <?php wpforo_date($last_post['created']); ?></div>
				  <?php else: ?>
				  		<div class="wpforo-topic-stat-lastpost"></span><?php wpforo_phrase('Replies not found') ?></div>
				  <?php endif; ?>
                  <div class="wpforo-topic-stat-views"><?php echo intval($topic['views']) ?></div>
                  <div class="wpforo-topic-stat-posts"><?php echo intval($topic['posts']) ?></div>
                  <br class="wpf-clear">
              </div><!-- wpforo-topic -->
          </div><!-- topic-wrap -->
	    	
	        <?php do_action( 'wpforo_loop_hook', $key ) ?>  
	        
		<?php endforeach; ?>
    </div><!-- wpfl-2 -->
