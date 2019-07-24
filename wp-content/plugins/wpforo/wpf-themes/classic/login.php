<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
?>

<p id="wpforo-title"><?php wpforo_phrase('Forum - Login') ?></p>
 
<form name="wpflogin" action="" method="POST">
  <div class="wpforo-login-wrap">
    <div class="wpforo-login-content">
     <table class="wpforo-login-table wpfcl-1" width="100%" border="0" cellspacing="0" cellpadding="0" style="width:100%; display:table;">
     <tbody style="width:100%;">
          <tr class="wpfbg-9">
            <td class="wpf-login-label">
            	<p class="wpf-label wpfcl-1"><?php wpforo_phrase('Username') ?>:</p>
            </td>
            <td class="wpf-login-field"><input autofocus required="TRUE" type="text" name="log" class="wpf-login-text wpfw-60" /></td>
          </tr>
          <tr class="wpfbg-9">
            <td class="wpf-login-label">
            	<p class="wpf-label wpfcl-1"><?php wpforo_phrase('Password') ?>:</p>
            </td>
            <td class="wpf-login-field"><input required="TRUE" type="password" name="pwd" class="wpf-login-text wpfw-60" /></td>
          </tr>
          <tr class="wpfbg-9">
          	<td class="wpf-login-label"></td>
          	<td><?php do_action('login_form') ?></td>
          </tr>
          <tr class="wpfbg-9">
            <td class="wpf-login-label">&nbsp;</td>
            <td class="wpf-login-field">
            <p class="wpf-extra wpfcl-1">
            <input type="checkbox" value="1" name="rememberme" id="wpf-login-remember"> 
            <label for="wpf-login-remember"><?php wpforo_phrase('Remember Me') ?> |</label>
            <a href="<?php echo wpforo_lostpass_url(); ?>" class="wpf-forgot-pass"><?php wpforo_phrase('Lost your password?') ?></a> 
            </p>
            <input type="submit" name="wpforologin" value="<?php wpforo_phrase('Sign In') ?>" />
            </td>
          </tr>
          </tbody>
       </table>
  	</div>
  </div>
</form>

<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>