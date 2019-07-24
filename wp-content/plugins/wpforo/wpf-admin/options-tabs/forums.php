<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
	if( !current_user_can('administrator') ) exit;
?>


<form action="" method="POST" class="validate">
	<?php wp_nonce_field( 'wpforo-settings-forums' ); ?>
	<table class="wpforo_settings_table">
		<tbody>
            <?php do_action( 'wpforo_settings_forums', $wpforo ); ?>
		</tbody>
	</table>
    <div class="wpforo_settings_foot">
        <input type="submit" class="button button-primary" value="<?php _e('Update Options', 'wpforo'); ?>" />
    </div>
</form>
