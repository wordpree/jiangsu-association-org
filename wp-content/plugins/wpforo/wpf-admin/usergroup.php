<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
	if( !current_user_can('administrator') ) exit;
?>

<div id="wpf-admin-wrap" class="wrap"><div id="icon-users" class="icon32"><br /></div>
	<h2 style="padding:30px 0px 10px 0px;line-height: 20px;"><?php _e( 'Usergroups', 'wpforo') ?> <a href="<?php echo admin_url( 'admin.php?page=wpforo-usergroups&action=add' ) ?>" class="add-new-h2"><?php _e( 'Add New', 'wpforo') ?></a></h2>
	<?php $wpforo->notice->show(FALSE) ?>
	
	<!-- ###############################################################   Usergroup Main Form -->
	
	<?php if( !isset($_GET['action']) || ( $_GET['action'] != 'add' && $_GET['action'] != 'del' && $_GET['action'] != 'edit') ) : ?>
		<br/>
		<table id="usergroup_table" class="wp-list-table widefat fixed posts" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" id="title" class="manage-column column-title sorted desc" style="padding:10px; font-size:14px; padding-left:15px; font-weight:bold;"><span><?php _e( 'Usergroup Name', 'wpforo') ?></span></th>
				<th scope="col" id="title" class="manage-column column-title sorted desc" style="padding:10px; font-size:14px; padding-left:15px; font-weight:bold;"><span><?php _e( 'Users Count', 'wpforo') ?></span></th>
			</tr>
		</thead>
        <tbody id="the-list">
			<?php $ugroups = $wpforo->usergroup->usergroup_list_data(); ?>
			<?php foreach( $ugroups as $key => $ugroup ) : ?>
            	<?php $bgcolor = ( $key % 2 ) ? '#FFFFFF' : '#FCFCFC' ; ?>
                <tr id="usergroup-<?php echo intval($ugroup['groupid']) ?>" class="format-standard hentry alternate iedit" valign="top">
                    <td class="post-title page-title column-title" style="border-bottom:1px dotted #CCCCCC; padding-left:20px; background:<?php echo esc_attr($bgcolor) ?>;">
                        <?php $edit_url = ( $ugroup['groupid'] != 1 ? admin_url( 'admin.php?page=wpforo-usergroups&gid=' . $ugroup['groupid'] . '&action=edit' ) : '#') ?>
                        <strong>
                            <a class="row-title" href="<?php echo esc_url($edit_url) ?>" title="<?php _e( 'Usergroup Name', 'wpforo') ?>">
                                <?php echo esc_html($ugroup['name']) ?>
                            </a>
                        </strong>
                        <div class="row-actions">
                            <?php if( $ugroup['groupid'] != 1 ): ?>
                                <span class="edit"><a title="<?php _e( 'Edit this usergroup', 'wpforo') ?>"  href="<?php echo admin_url( 'admin.php?page=wpforo-usergroups&gid=' . intval($ugroup['groupid']) . '&action=edit' ) ?>"><?php _e( 'Edit', 'wpforo') ?></a> |</span>
                                <?php if( $ugroup['groupid'] != 4 ): ?><span class="trash"><a class="submitdelete" title="<?php _e( 'Delete this usergroup', 'wpforo') ?>" href="<?php echo admin_url( 'admin.php?page=wpforo-usergroups&gid=' . intval($ugroup['groupid']) . '&action=del' ) ?>"><?php _e( 'Delete', 'wpforo') ?></a> |</span><?php endif; ?>
                            <?php endif; ?>
                            <span class="view"><a title="<?php _e( 'View users list in this usergroup', 'wpforo') ?>"  href="<?php echo admin_url( 'admin.php?ids=&page=wpforo-members&s=&action=-1&groupid=' . intval($ugroup['groupid']) . '&paged=1&action2=-1' ) ?>" rel="permalink"><?php _e( 'View', 'wpforo') ?></a></span>
                        </div>
                    </td>
                    <td class="column-title" style="border-bottom:1px dotted #CCCCCC; vertical-align:middle; padding-left:20px; background:<?php echo esc_html($bgcolor) ?>;">
                        <strong><a class="row-title" href="<?php echo admin_url( 'admin.php?ids=&page=wpforo-members&s=&action=-1&groupid=' . intval($ugroup['groupid']) . '&paged=1&action2=-1' ) ?>" title="<?php _e( 'Count of users in this usergroup', 'wpforo') ?>"><?php echo intval($ugroup['count']) ?></a></strong>
                    </td>
                </tr>
            <?php endforeach; ?>
			</tbody>
        </table>
	<?php endif; ?>	
	
	<!-- ###############################################################  Usergroup Main Form END -->
	
	<!-- ###############################################################  Add / Edit Usergroup Form -->
	
	<?php if( isset($_GET['action']) && ( $_GET['action'] == 'add' || $_GET['action'] == 'edit' ) ): ?>
	
    <div class="wpf-info-bar">
        <div class="form-wrap">
			<form id="add_ug" action="" method="post">
            	<?php wp_nonce_field( 'wpforo-usergroup-addedit' ); ?>
				<input type="hidden" name="usergroup[action]" value="<?php echo ( $_GET['action'] == 'add' ? 'add' : 'edit' ) ?>"/>
				<label class="wpf-label-big"><?php _e( 'Usergroup Name', 'wpforo'); if( isset($_GET['gid']) && $_GET['gid'] == 4 ) echo '<span>: ' . __('Guest', 'wpforo') . '</span><br><br>'; ?> </label> 
				<?php 
					if(isset( $_GET['gid'] )){
						$group = $wpforo->usergroup->get_usergroup($_GET['gid']); 
						$group_name = $group['name'];	
					}else{
						$group_name  = '';
					}
				 ?>
                <input name="usergroup[name]" <?php echo ( isset($_GET['gid']) && $_GET['gid'] == 4 ) ? 'type="hidden"' : 'type="text"'; ?>  value="<?php echo esc_attr($group_name) ?>" required="TRUE" style="background:#FDFDFD; width:30%; min-width:320px; margin:20px 0px; display:block;"/>
				<?php  $cans = $wpforo->perm->usergroup_cans_form( ( isset($_GET['gid'] ) ? $_GET['gid'] : FALSE ) ); ?>
                <?php  $n = 0; foreach( $cans as $can => $data ) : ?>
                     <?php if( $n%4 == 0 ): ?>
                     	</table>
                        <table class="wpf-table-box-left" style="margin-right:15px; margin-bottom:15px;  min-width:320px;">
		                     <?php endif; ?>
		                     <tr>
		                         <th class="wpf-dw-td-nowrap"><label class="wpf-td-label" for="wpf-can-<?php echo esc_attr($can) ?>"><?php echo esc_html( __($data['name'], 'wpforo') ) ?></label></th>
		                         <td class="wpf-dw-td-value"><input id="wpf-can-<?php echo esc_attr($can) ?>" type="checkbox" name="cans[<?php echo esc_attr($can) ?>]" value="1" <?php echo ( $data['value'] ) ? 'checked="checked"' : ''; ?>></td>
		                     </tr>
		                <?php $n++; endforeach; ?>
						</table>
                <div class="clear"></div>
				<input type="submit" class="button button-primary forum_submit" value="<?php echo ( $_GET['action'] == 'add' ? __( 'add', 'wpforo') : __( 'save', 'wpforo') ); ?>">
			</form>
		</div>
    </div>
    
    	
	<?php endif; ?>	
	<!-- ###############################################################  END of Add  / Edit Usergroup -->
	
	<!-- ###############################################################  DELETE Usergroup -->
	<?php if( isset($_GET['action']) && $_GET['action'] == 'del') : ?>
			<form action="" method="post">
            <?php wp_nonce_field( 'wpforo-usergroup-delete' ); ?>
			<input type="hidden" name="wpforo_delete" value="1"/>
			<div class="form-wrap">
				<div class="form-field form-required">
					<div class="form-field">
						<table>
							<tr>
								<td>
									<label for="delete_ug" class="menu_delete" style="color: red;">
										<?php _e( 'Delete Chosen Usergroup And Users', 'wpforo') ?>
									</label>
								</td>
								<td width="20px">
									<input id="delete_ug" type="radio" name="usergroup[delete]" value="1"  onchange="mode_changer_ug('false');"/>
								</td>
							</tr>
							<tr>
								<td>
									<label for="marge">
										<?php _e( 'Delete Chosen Usergroup And Join Users To Other Usergroup', 'wpforo') ?>
									</label>
								</td>
								<td>
									<input id="marge" type="radio" name="usergroup[delete]" value="0" checked="" onchange="mode_changer_ug('true');"/>
								</td>
							</tr>
							<tr>
								<td>
									<select id="ug_select" name="usergroup[mergeid]" class="postform" >
										<?php $wpforo->usergroup->show_selectbox() ?>
									</select>
									<p><?php _e( 'Users will be join this usergroup', 'wpforo') ?></p>
								</td>
							</tr>
						</table>
					</div>
					<input id="ug_submit"  type="submit" name="usergroup[submit]" class="button button-primary forum_submit" value="<?php _e( 'Delete', 'wpforo') ?>" />
				</div>
			</div>
			</form>
	<?php endif; ?>	
	<!-- ###############################################################  DELETE Usergroup -->
	
	