<?php
if( WPF()->perm->forum_can('vt') ):

	if( ($forum = WPF()->current_object['forum']) && ($topic = WPF()->current_object['topic']) && ($posts = WPF()->current_object['posts']) ) :
		if( !($topic['private'] && !wpforo_is_owner($topic['userid'], $topic['email']) && !WPF()->perm->forum_can('vp')) ): ?>

			<div class="wpf-head-bar">
				<h1 id="wpforo-title">
					<?php
					$icon_title = WPF()->tpl->icon('topic', $topic, false, 'title');
					if( $icon_title ) echo '<span class="wpf-status-title">[' . esc_html($icon_title) . ']</span> ';
					echo esc_html( wpforo_text($topic['title'], 0, false) );
					?>&nbsp;&nbsp;
				</h1>
				<div class="wpf-action-link"><?php WPF()->tpl->topic_subscribe_link() ?></div>
			</div>

			<?php
			wpforo_template_pagenavi('wpf-navi-post-top');
			if( $forum['cat_layout'] == 3 ) include_once(wpftpl('layouts/3/comment.php'));
			include( wpftpl('layouts/' . $forum['cat_layout'] . '/post.php') );
			wpforo_template_pagenavi('wpf-navi-post-bottom');

			if( WPF()->perm->forum_can('cr') ) {
				$args = array(
					"topic_closed" => $topic['closed'],
					"topicid" => $topic['topicid'],
					"forumid" => $forum['forumid'],
					"layout" => $forum['cat_layout'],
					"topic_title" => wpforo_text($topic['title'], 0, false)
				);
				WPF()->tpl->reply_form( $args );
			}
			do_action( 'wpforo_post_list_footer' );

		else: ?>
			<p class="wpf-p-error">
				<?php wpforo_phrase('Topic are private, please register or login for further information') ?>
			</p>
		<?php endif;
	endif;

else : ?>
	<p class="wpf-p-error">
		<?php wpforo_phrase("You don't have permissions to see this page, please register or login for further information") ?>
	</p>
<?php endif;