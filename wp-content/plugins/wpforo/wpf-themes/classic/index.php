<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
?>
<?php
/**
 * Template Name:  WpForo Index (Forums List)
 */
?>
<?php if( $wpforo->use_home_url ) get_header() ?>
	<?php extract($wpforo->current_object, EXTR_OVERWRITE); ?>
	<?php include("header.php"); ?>
    <div class="wpforo-main">
    	<div class="wpforo-content"<?php echo is_active_sidebar('forum-sidebar') ? '' : 'style="width:100%"' ?>>
		<?php if( $wpforo->current_user_status == 'banned' || $wpforo->current_user_status == 'trashed' ) : ?>
			<p class="wpf-p-error"><?php wpforo_phrase('You have been banned. Please contact to forum administrators for more information.') ?></p>
		<?php else : ?>
			<?php
				if($template == 'search'){
					include( wpftpl('search.php') );
				}elseif($template == 'register'){
					include( wpftpl('register.php') );
				}elseif($template == 'login'){
					include( wpftpl('login.php') );
				}elseif($template == 'members'){
					$wpfms = (isset($_GET['wpfms'])) ? sanitize_text_field($_GET['wpfms']) : '';
					if($wpfms){
						$users_include = $wpforo->member->search($wpfms, array('user_nicename', 'user_email', 'display_name', 'title'));
					}
					$args = array(
						'offset' => ($paged - 1) * $wpforo->post_options['posts_per_page'],
						'row_count' => $wpforo->post_options['posts_per_page'],
						'orderby' => 'posts DESC, display_name'
					);
					if(!empty($users_include)) $args['include'] = $users_include;
					$items_count = 0;
					$members = $wpforo->member->get_members($args, $items_count);
					if(isset($users_include) && empty($users_include)){ $members = array(); $items_count = 0; }
					
					include( wpftpl('members.php') );
				}elseif( isset($wpforo->member_tpls[$template]) && $wpforo->member_tpls[$template] ){
					include( wpftpl('profile.php') );
				}else{
					if( $template == 'forum' || $template == 'topic' ) : ?>
						<?php if(!isset($forum_slug)) : ?><h2 id="wpforo-title"><?php echo esc_html($wpforo->general_options['title']) ?></h2><?php endif; ?>
						<?php $cats = $wpforo->forum->get_forums( (isset($forum_slug) &&  $forum_slug != '' ? array( "parent_slug" => $forum_slug ) : array( "type" => 'category' ) ) ); ?>
						
						<?php if(is_array($cats) && !empty($cats)) : ?>
							
							<?php foreach($cats as $key => $cat) : ?>
								<?php if( $wpforo->perm->forum_can( 'vf', $cat['forumid'] ) ): ?>
									<?php $forums = $wpforo->forum->get_forums( array( "parentid" => $cat['forumid'], "type" => 'forum' ) ); ?>
									<?php if(is_array($forums) && !empty($forums)) : ?>
                                    	<?php do_action( 'wpforo_category_loop_start', $cat, $key ) ?>
										<?php include( wpftpl('layouts/'.($cat['cat_layout'] ? $cat['cat_layout'] : 1).'/forum.php') ); ?>
                                        <?php do_action( 'wpforo_category_loop_end', $cat, $key ) ?>
									<?php endif; ?>
								<?php endif; //checking forum permissions (can view forum) ?>
							<?php endforeach; //$cats as $cat ?>
							
						<?php else : ?>
							<p class="wpf-p-error"><?php wpforo_phrase('No forums were found here.') ?></p>
			           	<?php endif; //is_array($cats) && !empty($cats) ?>
			           	
			      	<?php endif; //Forum template ?>
					
					<?php if( $template == 'topic' ) : ?>
						<?php if( is_array($cats) && !empty($cats) && $cat['is_cat'] == 0 ) : ?>
							
							<?php if( isset($forum_slug) && $forum_slug ) : ?>
							
                            	<?php $forum = $wpforo->forum->get_forum( array( 'slug' => $forum_slug ) );  ?>
                            
								<?php if(is_array($forum) && !empty($forum)) : ?>
                                    
                                    <?php if( $wpforo->perm->forum_can( 'vf', $forum['forumid'] ) ): ?>
                                        
                                        <div class="wpf-head-bar">
                                            <div class="wpf-head-bar-left">
                                                <h2 id="wpforo-title"><?php echo esc_html($forum['title']) ?></h2>
                                                <div class="wpf-action-link">
                                                <?php if ( is_user_logged_in() ): ?>
                                                    <?php 
                                                    $args = array( "userid" => $wpforo->current_userid , "itemid" => $forum['forumid'], "type" => "forum" );
                                                    $subscribe = $wpforo->sbscrb->get_subscribe( $args );
                                                    if( isset( $subscribe['subid'] ) ): ?>
                                                        <span class="wpf-unsubscribe-forum wpf-action" id="wpfsubscribe-<?php echo intval($forum['forumid']) ?>"><?php wpforo_phrase('Unsubscribe') ?></span> 
                                                    <?php else: ?>
                                                        <span class="wpf-subscribe-forum wpf-action" id="wpfsubscribe-<?php echo intval($forum['forumid']) ?>"><?php wpforo_phrase('Subscribe for new topics') ?></span> 
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                    <span class="wpf-feed">| <a href="<?php $wpforo->feed->rss2_url(); ?>" title="<?php wpforo_phrase('Forum RSS Feed') ?>" target="_blank"><span><?php wpforo_phrase('RSS') ?></span> <i class="fa fa-rss fa-0x"></i></a></span>
                                                </div>	
                                            </div>
                                            <?php if( $wpforo->perm->forum_can( 'ct', $cat['forumid']) ): ?>
                                                <div class="wpf-head-bar-right"><button class="wpf-button" id="add_wpftopic"><?php wpforo_phrase('Add topic') ?></button></div>
                                            <?php elseif( $wpforo->current_user_groupid == 4 ) : ?>
                                                <div class="wpf-head-bar-right"><button class="wpf-button not_reg_user" id="add_wpftopic"><?php wpforo_phrase('Add topic') ?></button></div>
                                            <?php endif; ?>
                                            <div class="wpf-clear"></div>
                                        </div>
                                        
                                        <?php if( is_user_logged_in() && $wpforo->perm->forum_can( 'ct', $cat['forumid'] ) ) $wpforo->tpl->topic_form($forum['forumid']); ?>
                                        
                                        <?php
                                            $args = array(
                                                'offset' => ($paged - 1) * $wpforo->post_options['topics_per_page'], 
                                                'row_count' => $wpforo->post_options['topics_per_page'],
                                                'forumid' => $cat['forumid'],
                                                'orderby' => 'type, modified',
                                                'order' => 'DESC'
                                            );
                                            $items_count = 0;
                                            $topics = $wpforo->topic->get_topics( $args, $items_count );
                                        ?>
                                        
                                        <?php if( is_array($topics) && !empty($topics) ) : ?>
                                            
                                            <?php $wpforo->tpl->pagenavi($paged, $items_count); ?>
                                            
                                            <?php include( wpftpl('layouts/'.($cat['cat_layout'] ? $cat['cat_layout'] : 1).'/topic.php') ); ?>
                                            
                                            <?php $wpforo->tpl->pagenavi($paged, $items_count); ?>
                                            
                                        <?php else : ?>
                                            <p class="wpf-p-error"><?php wpforo_phrase('No topics were found here') ?>  </p>
                                        <?php endif; ?>
                                        
                                    <?php endif; //chekcing permissions (can view forum) ?>
                                    
                                <?php else : ?>
                                    <?php include( wpftpl('404.php') ) ?>
                                <?php endif; ?> 
							
							<?php else : ?>	
								<?php include( wpftpl('404.php') ) ?>
                            <?php endif; ?>
                                    
						<?php endif; ?>
					<?php endif; ?>
					
					<?php if( $template == 'post' ) : ?>
						<?php 
							if( is_array($forum) && !empty($forum) ) :
								
									if( $wpforo->perm->forum_can( 'vt', $forum['forumid'] ) ):
										
										if( is_array($topic) && !empty($topic) ) : ?>
										
											<?php if( isset($topic['private']) && $topic['private'] && !wpforo_is_owner($topic['userid']) && !$wpforo->perm->forum_can( 'vp', $forum['forumid'] ) ): ?>
                                            	<p class="wpf-p-error"><?php wpforo_phrase('Permission denied') ?></p>
											<?php else: ?>
                                            	
												<?php
                                                $cat_layout = $wpforo->forum->get_layout( array( 'topicid' =>  $topic['topicid'] ) );
												$args = array(
                                                    'offset' => ($paged - 1) * $wpforo->post_options['posts_per_page'],
                                                    'row_count' => $wpforo->post_options['posts_per_page'],
                                                    'topicid' => $topic['topicid'],
													'forumid' => $forum['forumid']
                                                );
                                                $items_count = 0;
                                                $posts = $wpforo->post->get_posts( $args, $items_count);
                                                ?>
											
												<?php if( is_array($posts) && !empty($posts) ) : ?>
                                                    <div class="wpf-head-bar">
                                                        <h2 id="wpforo-title"><?php $icon_title = $wpforo->tpl->icon('topic', $topic, false, 'title'); if( $icon_title ) echo '<span class="wpf-status-title">[' . esc_html($icon_title) . ']</span> ' ?><?php echo esc_html($topic['title']) ?>&nbsp;&nbsp;</h2>
                                                        <?php if ( is_user_logged_in() ): ?>
                                                            <div class="wpf-action-link">
                                                                <?php 
                                                                $args = array( "userid" => $wpforo->current_userid , "itemid" => $topic['topicid'], "type" => "topic" );
                                                                $subscribe = $wpforo->sbscrb->get_subscribe( $args );
                                                                if( isset( $subscribe['subid'] ) ): ?>
                                                                    <span class="wpf-unsubscribe-topic wpf-action" id="wpfsubscribe-<?php echo intval($topic['topicid']) ?>" ><?php wpforo_phrase('Unsubscribe') ?></span>
                                                                <?php else: ?>
                                                                    <span class="wpf-subscribe-topic wpf-action" id="wpfsubscribe-<?php echo intval($topic['topicid']) ?>"  ><?php wpforo_phrase('Subscribe for new replies') ?></span>
                                                                <?php endif; ?>
                                                            </div>	
                                                        <?php endif; ?>
                                                    </div>
                                                        
                                                    <?php $wpforo->tpl->pagenavi( $paged, $items_count ); ?>
                                                    
                                                    <?php include( wpftpl('layouts/'.($cat_layout ? $cat_layout : 1).'/post.php') ); ?>
                                                    
                                                    <?php $wpforo->tpl->pagenavi($paged, $items_count); ?>
                                                    
                                                    <?php 
                                                        if(is_user_logged_in()){
                                                            $default = array(
                                                                "topic_closed" => $topic['closed'], 	
                                                                "topicid" => $topic['topicid'],  		
                                                                "forumid" => $forum['forumid'],
                                                                "layout" => ($cat_layout ? $cat_layout : 1),	
                                                                "topic_title" => $topic['title']		
                                                            );
                                                            $wpforo->tpl->reply_form( $default );
                                                        }
                                                    ?>
                                                <?php else : ?>	
                                                    <?php include( wpftpl('404.php') ) ?>
                                                <?php endif; ?>    
                                                
                                            <?php endif; ?>	
                                            
										<?php else : ?>	
											<?php include( wpftpl('404.php') ) ?>
										<?php endif; ?>
                                        
									<?php endif; //checking permission can view topic ?>
								
							<?php else : ?>
								<?php include( wpftpl('404.php') ) ?>
							<?php endif ?>
							
					<?php endif; ?>
				<?php } ?>
	           </div>
	           <?php if (is_active_sidebar('forum-sidebar')) : ?>
		           <div class="wpforo-right-sidebar">
		           		<?php dynamic_sidebar('forum-sidebar') ?>
		           </div>
	           <?php endif; ?>
	        <?php endif; ?>
           <div class="wpf-clear"></div>
      </div>
<?php include("footer.php") ?>

<?php if( $wpforo->use_home_url ) get_footer() ?>