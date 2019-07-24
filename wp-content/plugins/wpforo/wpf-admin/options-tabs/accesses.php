<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
	if( !current_user_can('administrator') ) exit;
?>

<?php if( !isset($_GET['action']) ): ?>
	<?php $accesses = $wpforo->perm->get_accesses() ?>
    <h2 style="margin-top:0px; margin-bottom:20px;"><a href="?page=wpforo-settings&tab=accesses&action=add" class="add-new-h2"><?php _e('Add New Forum Access', 'wpforo'); ?></a></h2>
    <table id="usergroup_table" class="wp-list-table widefat fixed posts" cellspacing="0">
		<thead>
			<tr>
                <th scope="col" id="title" class="manage-column column-title sorted desc" style="padding:10px; font-size:14px; padding-left:15px; font-weight:bold;"><span><?php _e('Access names', 'wpforo'); ?></span></th>
            </tr>
		</thead>
		<tbody id="the-list">
			<?php foreach($accesses as $key => $access) : ?>
            	<?php $bgcolor = ( $key % 2 ) ? '#FFFFFF' : '#FCFCFC' ; ?>
				<tr id="post-2" class="post-1 type-post status-publish format-standard hentry category-uncategorized alternate iedit author-self" valign="top">
					<td class="post-title page-title column-title" style="border-bottom:1px dotted #CCCCCC; padding-left:20px; background:<?php echo esc_attr($bgcolor) ?>;">
						<strong class="row-title">
							<a href="?page=wpforo-settings&tab=accesses&action=edit&access=<?php echo esc_attr($access['access']) ?>" title="<?php echo esc_attr($access['title']) ?>">
								<?php _e( $access['title'], 'wpforo') ?>
							</a>
                            <p class="wpf-info">
								<?php if($access['title'] == 'Read only access') { _e('This access is usually used for ', 'wpforo'); echo '<span style="color:#F45B00"><b>'; _e('Guests', 'wpforo'); echo '</b></span> ';  _e('usergroup', 'wpforo'); } ?>
                                <?php if($access['title'] == 'Standard access') { _e('This access is usually used for ', 'wpforo'); echo '<span style="color:#F45B00"><b>'; _e('Registered', 'wpforo'); echo '</b></span> '; _e('usergroup', 'wpforo'); } ?>
                                <?php if($access['title'] == 'Full access') { _e('This access is usually used for ', 'wpforo'); echo '<span style="color:#F45B00"><b>'; _e('Admin', 'wpforo'); echo '</b></span> '; _e('usergroup', 'wpforo'); } ?>
                            </p>
						</strong>
						<div class="row-actions">
							<span class="edit"><a href="?page=wpforo-settings&tab=accesses&action=edit&access=<?php echo esc_attr($access['access']) ?>"><?php _e('edit', 'wpforo'); ?></a> |</span>
							<?php if( $access['accessid'] > 5 ): ?>
                            	<span class="trash"><a class="submitdelete" href="<?php echo wp_nonce_url( '?page=wpforo-settings&tab=accesses&action=del&accessid=' . esc_attr($access['accessid']) , 'wpforo_access_delete' ) ?>"  onclick = "if (! confirm('<?php _e('Are you sure you want to remove this access set? Usergroups which attached to this access will lost all forum permissions.'); ?>')) { return false; }" ><?php _e('delete', 'wpforo'); ?></a></span>
							<?php endif; ?>
                        </div>
					</td>
				</tr>
			<?php endforeach ?>			
		</tbody>
	</table>
<?php elseif( isset($_GET['action']) && ( $_GET['action'] == 'edit' || $_GET['action'] == 'add' ) ) : ?>
	<div class="form-wrap">
    	<div class="form-wrap">
            <form id="add_access" action="" method="post">
            	<?php wp_nonce_field( 'wpforo-access-addedit' ); ?>
                <input type="hidden" name="access[action]" value="<?php echo ( $_GET['action'] == 'add'  ? 'add' : 'edit' ) ?>" />
                <input type="hidden" name="access[key]" value="<?php echo ( isset($_GET['access']) ? esc_attr(sanitize_text_field($_GET['access'])) : '' ) ?>" />
                <label class="wpf-label-big"><?php _e('Access name', 'wpforo'); ?></label>
                <?php if( isset( $_GET['access'] ) ){ $access = $wpforo->perm->get_access( $_GET['access'] );} ?>
                <input name="access[name]" type="text" size="40" required="TRUE" value="<?php echo ( $_GET['action'] == 'edit' ? esc_attr($access['title']) : '') ?>" style="background:#FDFDFD; width:30%; min-width:320px;">
                <p>&nbsp;</p>
                
                <?php 
                $access_key = ( isset( $_GET['access'] ) ? $_GET['access'] : 0 ); 
                $cans = $wpforo->perm->forum_cans_form( $access_key ); ?>
                <?php $n = 0; foreach( $cans as $can => $data  ): ?>
                    <?php if( $n%4 == 0 ): ?>
                    </table>
                    <table class="wpf-table-box-left" style="margin-right:15px; margin-bottom:15px;  min-width:320px;">
                         <?php endif; ?>
                        <tr>
                            <th class="wpf-dw-td-nowrap"><label class="wpf-td-label" for="wpf-can-<?php echo esc_attr($can) ?>"><?php echo esc_html( __( $data['name'], 'wpforo' ) ) ?></label></th>
                            <td class="wpf-dw-td-value" style="text-align:center;"><input id="wpf-can-<?php echo esc_attr($can) ?>" type="checkbox" name="cans[<?php echo esc_attr($can) ?>]" value="1" <?php echo ( $data['value'] ) ? 'checked="checked"' : ''; ?>></td>
                        </tr>
                <?php $n++; endforeach ?>
                </table>
                <div class="clear"></div>
                <input type="submit" class="button button-primary forum_submit" value="<?php echo ( $_GET['action'] == 'add'  ? __('Save', 'wpforo') : __('Update', 'wpforo') ) ?>">
            </form>
        </div>
	</div>
<?php endif ?>