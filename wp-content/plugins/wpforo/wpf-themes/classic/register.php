<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
	$user_login = (isset($_POST['wpfreg']['user_login'])) ? sanitize_user($_POST['wpfreg']['user_login']) : '';;
	$user_email = (isset($_POST['wpfreg']['user_email'])) ? sanitize_email($_POST['wpfreg']['user_email']) : '';
?>

<p id="wpforo-title"><?php wpforo_phrase('Forum - Registration') ?></p>

<?php if( wpforo_feature('user-register', $wpforo) ): ?>
    <form name="wpfreg" action="" method="POST">
      <div class="wpforo-register-wrap">
        <div class="wpforo-register-content">
         <table class="wpforo-register-table wpfcl-1" width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr class="wpfbg-9">
                <td class="wpf-register-label">
                    <p class="wpf-label wpfcl-1"><?php wpforo_phrase('Username') ?>:</p>
                    <span class="wpf-desc wpfcl-2"><?php wpforo_phrase('Length must be between 3 characters and 15 characters.') ?></span>
                </td>
                <td class="wpf-register-field"><input autofocus required="TRUE" type="text" value="<?php echo esc_attr($user_login) ?>" name="wpfreg[user_login]" class="wpf-register-text wpfw-70" maxlength="30" /></td>
              </tr>
              <tr class="wpfbg-7">
                <td class="wpf-register-label">
                    <p class="wpf-label wpfcl-1"><?php wpforo_phrase('Email') ?>:</p>
                </td>
                <td class="wpf-register-field"><input required="TRUE" type="text" value="<?php echo esc_attr($user_email) ?>" name="wpfreg[user_email]" class="wpf-register-text wpfw-70" /></td>
              </tr>
              <?php if( wpforo_feature('user-register-email-confirm', $wpforo) ): ?>
	            <tr class="wpfbg-9">
	                <td class="wpf-register-label">
	                </td>
	                <td class="wpf-register-field">
	                    <span class="wpf-desc wpfcl-2"><?php wpforo_phrase('After registration you will receive email confimation and link for set a new password') ?>:</span>
	                </td>
	            </tr>
	          <?php else : ?>
	          	<tr class="wpfbg-9">
	                <td class="wpf-register-label">
	                    <p class="wpf-label wpfcl-1"><?php wpforo_phrase('Password') ?>:</p>
	                    <span class="wpf-desc wpfcl-2"><?php wpforo_phrase('Must be minimum 6 characters.') ?></span>
	                </td>
	                <td class="wpf-register-field">
	                    <input required="TRUE" type="password" name="wpfreg[user_pass1]" class="wpf-register-text wpfw-50" /> <br />
	                    <input required="TRUE" type="password" name="wpfreg[user_pass2]" class="wpf-register-text wpfw-50" /> &nbsp;<span class="wpf-desc wpfcl-2" style="white-space:nowrap"><?php wpforo_phrase('confirm password') ?></span>
	                </td>
	            </tr>
              <?php endif ?>
              <tr class="wpfbg-7">
                <td class="wpf-register-label"></td>
                <td id="wpforo_register_form_hook">
                    <?php do_action('register_form') ?>
                </td>
              </tr>
              <tr class="wpfbg-9">
                <td class="wpf-register-label">&nbsp;</td>
                <td class="wpf-register-field">&nbsp;<input type="submit" value="<?php wpforo_phrase('Register') ?>" /></td>
              </tr>
           </table>
        </div>
      </div>
    </form>
<?php else: ?>
<div class="wpforo-register-wrap">
    <div class="wpforo-register-content">
    	<p class="wpf-p-error"><?php wpforo_phrase('User registration is disabled') ?></p>
    </div>
</div>
<?php endif; ?>
<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>