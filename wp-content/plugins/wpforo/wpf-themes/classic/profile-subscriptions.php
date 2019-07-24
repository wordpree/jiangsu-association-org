<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
?>

<div class="wpforo-sbn-content wpfbg-7">
    <?php if(empty($subscribes)) : ?><p class="wpf-p-error"> <?php wpforo_phrase('No subscriptions found for this member.') ?> </p><?php endif; ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <?php $bg = FALSE; foreach( $subscribes as $subscribe ) : ?>
            <?php 
                if($subscribe['type'] == 'forum'){
                    $item = $wpforo->forum->get_forum($subscribe['itemid']);
                    $item_url = $wpforo->forum->get_forum_url($item['forumid']);
                }elseif($subscribe['type'] == 'topic'){
                    $item = $wpforo->topic->get_topic($subscribe['itemid']);
                    $item_url = $wpforo->topic->get_topic_url($item['topicid']);
                }
				if(empty($item)) continue;
            ?>
          <tr<?php echo ( $bg ? ' class="wpfbg-9"' : '' ) ?>>
            <td class="sbn-icon"><i class="fa fa-1x <?php echo ($subscribe['type'] == 'forum') ? 'fa-comments-o' : 'fa-file-text-o' ; ?>"></i></td>
            <td class="sbn-title"><a href="<?php echo esc_url($item_url) ?>"><?php echo esc_html($item['title']) ?></a></td>
            <?php if($userid == $wpforo->current_userid) : ?>
                <td class="sbn-action"><a href="<?php echo esc_url($wpforo->sbscrb->get_unsubscribe_link($subscribe['confirmkey'])) ?>"><?php wpforo_phrase('Unsubscribe'); ?></a></td>
            <?php else : ?>
                <td>&nbsp;</td>
            <?php endif ?>
          </tr>
        <?php $bg = ( $bg ? FALSE : TRUE ); endforeach ?>
   </table>
   <div class="sbn-foot">
        <?php $wpforo->tpl->pagenavi( $paged, $items_count ); ?>
    </div>
</div>
	
  