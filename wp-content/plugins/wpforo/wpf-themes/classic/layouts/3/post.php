<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
?>

<div class="wpfl-3">
    <div class="wpforo-post-head"> 
        <div class="wpf-left">&nbsp;<a href="<?php echo esc_url( wpforo_post($topic['last_post'], 'url') ); ?>" class="wpfcl-2"><i class="fa fa-caret-square-o-down fa-0x wpfcl-3"></i> &nbsp; <span class="wpfcl-3"><?php wpforo_phrase('Last Post'); ?></span></a></div>
        <div class="wpf-right">&nbsp;<a href="<?php $wpforo->feed->rss2_url(); ?>" class="wpfcl-2" title="<?php wpforo_phrase('Topic RSS Feed') ?>"><span class="wpfcl-3"><?php wpforo_phrase('RSS') ?></span> <i class="fa fa-rss fa-0x wpfcl-3"></i></a></div>
        <br class="wpf-clear" />
    </div>
	<?php foreach($posts as $key => $post ) : $is_topic = ( $key ? FALSE : TRUE ); ?>
		
	 	<?php if($post['parentid'] == 0): ?>
			<?php $member = wpforo_member($post); ?>
		        <div id="post-<?php echo intval($post['postid']) ?>" class="post-wrap <?php if( !$post['is_first_post'] ) echo 'wpf-answer-wrap' ?>">
		              <div class="wpforo-post wpfcl-1">
		                <div class="wpf-left">
		                	<div class="wpforo-post-voting">
		                    	<div class="wpf-positive">
		                    		<?php 
			                       	 	$buttons = array('positivevote');
										$wpforo->tpl->buttons( $buttons, $forum, $topic, $post, $is_topic );  
		                       	 	?>
		                    	</div>
			                        <div class="wpf-vote-number">
				                        <span id="wpfvote-num-<?php echo intval($post['postid']) ?>" class="wpfcl-0">
				                       	 	<?php echo $wpforo->post->get_post_votes_sum($post['postid']); ?>
				                        </span>
			                        </div>
		                        <div class="wpf-negative">
		                        	<?php 
			                       	 	$buttons = array('negativevote');
										$wpforo->tpl->buttons( $buttons, $forum, $topic, $post, $is_topic );  
		                       	 	?>
		                        </div>
		                        
		                    	<?php
		                    		if( !$post['is_first_post'] ){
		    	                		$buttons = array(  'isanswer' );
										$wpforo->tpl->buttons( $buttons, $forum, $topic, $post, $is_topic );  
									}
		                    	?>
		                    </div>
		                </div><!-- left -->
		                <div class="wpf-right">
                        	<div class="wpforo-post-content-top">
                            	<?php if($post['status']): ?><span class="wpf-mod-message"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?php wpforo_phrase('Awaiting moderation') ?></span><?php endif; ?> 
                                <div class="wpforo-post-link"><?php $buttons = array( 'link' ); $wpforo->tpl->buttons( $buttons, $forum, $topic, $post, $is_topic ); ?></div>
                                <div class="wpforo-post-date"><?php wpforo_date($post['created']); ?></div>
                                <div class="wpf-clear-right"></div>
                            </div>
		                    <div class="wpforo-post-content">
							 	<?php echo wpforo_content_filter( wpforo_kses($post['body'], 'post') ) ?>
                                <?php wpforo_post_edited($post); ?>
                                <?php do_action( 'wpforo_tpl_post_loop_after_content', $post, $member ) ?>
                                <?php if( wpforo_feature('signature', $wpforo) ): ?>
                                	<?php if( trim($member['signature'])): ?><div class="wpforo-post-signature"><?php echo wpautop(wpforo_kses(stripslashes($member['signature']), 'user_description')) ?></div><?php endif ?>
			          			<?php endif; ?>
                            </div>
		                    <div class="wpforo-post-author">
		                            <div class="wpforo-post-lb-box">
		                            	<?php 
											if( $post['is_first_post'] ){ 
												$buttons = array( 'answer' ); 
												echo '<div class="wpf-answer-button">'; 
												$wpforo->tpl->buttons( $buttons, $forum, $topic, $post, $is_topic ); 
												echo '</div>'; 
											}
											$buttons = array( 'comment' ); 
											echo '<div class="wpf-add-comment-button">'; 
											$wpforo->tpl->buttons( $buttons, $forum, $topic, $post, $is_topic ); 
											echo '</div>'; 
										?>
		                            </div>
                                    <div class="wpforo-post-author-data">
                                        <div class="wpforo-box-l3a-wrap wpforo-post-author-data-content">
                                            <div class="wpforo-box-l3a-top"></div>
                                            <div class="wpforo-box-l3a-lr">
                                                <?php if( wpforo_feature('avatars', $wpforo) ): $rsz =''; ?>
                                                	<div class="wpforo-box-l3a-left"><?php echo $wpforo->member->avatar($member, 'alt="'.esc_attr($member['display_name']).'"', 96, true) ?></div>
                                                <?php else: $rsz = 'style="margin-left:10px;"'; endif; ?>
                                                <div class="wpforo-box-l3a-right" <?php echo $rsz; //This is a HTML content// ?>>
                                                    <span class="author-name"><?php wpforo_member_link($member); ?></span>&nbsp;<span><?php $wpforo->member->show_online_indicator($member['userid']) ?></span><br />
                                                    <span class="author-title">
														<?php wpforo_member_title($member) ?>
                                                    </span><br />
                                                    <?php wpforo_member_badge($member, ' | ') ?>
                                                    <span class="author-posts">
														<?php echo intval($member['posts']) ?> <?php wpforo_phrase('Posts') ?></span><br />
                                                        <span class="author-stat-item"><i class="fa fa-question-circle wpfcl-6" title="<?php wpforo_phrase('Questions') ?>"></i><?php echo intval($member['questions']) ?></span>
                                                        <span class="author-stat-item"><i class="fa fa-check-square wpfcl-5" title="<?php wpforo_phrase('Answers') ?>"></i><?php echo intval($member['answers']) ?></span>
                                                        <span class="author-stat-item"><i class="fa fa-comment wpfcl-0" title="<?php wpforo_phrase('Comments') ?>"></i><?php echo intval($member['comments']) ?></span>
                                                    </span>
                                                </div>
                                                <div class="wpf-clear"></div>
                                            </div>
                                        </div>
		                            </div>
		                    </div><!-- wpforo-post-author -->
                            <div class="wpforo-post-tool-bar">
								<?php if( $post['is_first_post'] ){
                                    $buttons = array( 'report', 'sticky', 'private', 'close', 'move', 'edit', 'delete' );
                                    $wpforo->tpl->buttons( $buttons, $forum, $topic, $post, $is_topic );  
                                }else{
                                    $buttons = array( 'report', 'edit', 'delete' );
                                    $wpforo->tpl->buttons( $buttons, $forum, $topic, $post );  
                                } ?>
                            </div>
		                </div><!-- right -->
		                <div class="wpf-clear"></div>
		              </div><!-- wpforo-post -->
		          </div><!-- post-wrap -->
		        	
		 			<?php 
						$comments = $wpforo->post->get_posts( array( 'parentid' => $post['postid']  ) );
						if(is_array($comments) && !empty($comments)):
							foreach($comments as $comment) : ?>
							  <?php $comment_member = wpforo_member($comment); ?>
						        <div id="post-<?php echo intval($comment['postid']) ?>" class="comment-wrap">
						              <div class="wpforo-comment wpfcl-1">
						                <div class="wpf-left">
						                	<div class="wpf-comment-icon wpfcl-0"><i class="fa fa-reply fa-rotate-180"></i></div>
						                </div>
						                <div class="wpf-right">
						                    <div class="wpforo-comment-content">
						                        <?php echo wpforo_content_filter( wpforo_kses($comment['body'], 'post') ) ?> 
                                                <div class="wpforo-comment-footer">
	                                                <span class="wpfcl-0" style="white-space:nowrap"><?php wpforo_member_link($comment_member, 'by'); ?> 
							                        <?php wpforo_date($comment['created']); ?></span>
							                        <?php do_action( 'wpforo_tpl_post_loop_after_content', $comment, $comment_member ) ?>
                                                    <?php wpforo_post_edited($comment); ?>
                                                </div>
						                        <div class="wpforo-comment-action-links">&nbsp;
						                        	<?php 
						                        		$buttons = array( 'report', 'edit', 'delete', 'link' );
														$wpforo->tpl->buttons( $buttons, $forum, $comment, $comment );
													?>
						                        </div>
						                    </div>
						                </div><!-- right -->
						                <div class="wpf-clear"></div>
						              </div><!-- wpforo-post -->
						         </div><!-- comment-wrap -->
						<?php endforeach; ?>
	          	<?php else: ?> 
                	<div class="wpfsep">&nbsp;</div>
                <?php endif; ?>
	    <?php endif; ?>
	    
	    <?php do_action( 'wpforo_loop_hook', $key ) ?>
	    
   <?php endforeach; ?>
</div><!-- wpfl-3 -->