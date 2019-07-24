<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
	if( !current_user_can('administrator') ) exit;
?>


<form action="" method="POST" class="validate">
	<?php wp_nonce_field( 'wpforo-settings-styles' ); ?>
	<table class="wpforo_settings_table">
		<tbody>
			<tr>
				<th style="width:30%;"><label><?php _e('Font Sizes', 'wpforo'); ?>:</label></th>
				<td>
                <label style="display:inline-block; text-align:center; font-size:14px;">
                	<span><?php _e('Forums','wpforo'); ?>:</span> 
                	<select name="wpforo_style_options[font_size_forum]" style="min-width:80px;">
                    	<?php for( $a=11; $a < 28; $a++ ): ?><option value="<?php echo intval($a) ?>" <?php wpfo_check( $wpforo->style_options['font_size_forum'], $a, 'selected') ?>><?php echo intval($a); ?>px</option><?php endfor; ?>
                    </select>
                </label> &nbsp; 
                <label style="display:inline-block; text-align:center; font-size:14px;">
                	<span><?php _e('Topics','wpforo'); ?>:</span> 
                	<select name="wpforo_style_options[font_size_topic]" style="min-width:80px;">
                    	<?php for( $a=11; $a < 28; $a++ ): ?><option value="<?php echo intval($a) ?>" <?php wpfo_check( $wpforo->style_options['font_size_topic'], $a, 'selected') ?>><?php echo intval($a); ?>px</option><?php endfor; ?>
                    </select>
                </label> &nbsp; 
                <label style="display:inline-block; text-align:center; font-size:14px;">
                	<span><?php _e('Post Content','wpforo'); ?>:</span> 
                	<select name="wpforo_style_options[font_size_post_content]" style="min-width:80px;">
                    	<?php for( $a=11; $a < 28; $a++ ): ?><option value="<?php echo intval($a) ?>" <?php wpfo_check( $wpforo->style_options['font_size_post_content'], $a, 'selected') ?>><?php echo intval($a); ?>px</option><?php endfor; ?>
                    </select>
                </label>
				</td>
			</tr>
            <tr>
				<th style="width:30%;"><label><?php _e('Custom CSS Code', 'wpforo'); ?>:</label></th>
				<td>
               		<textarea name="wpforo_style_options[custom_css]" style="width:90%; height:130px; font-family:Consolas, 'Andale Mono', 'Lucida Console'; color:#666666; background:#fdfdfd;"><?php echo esc_textarea(stripslashes($wpforo->style_options['custom_css'])); ?></textarea>
				</td>
			</tr>
		</tbody>
	</table>
   <h3 style="margin:0px 20px 0px 20px; padding:10px 0px; border-bottom:3px solid #F5F5F5;"><?php _e('Forum Color Styles', 'wpforo'); ?></h3>
    <table style="width:95%; border:none; padding:5px; margin-left:10px; margin-top:15px;">
        <tbody>
            <tr class="form-field form-required">
                <td class="wpf-dw-td-value-p">
                	<table class="wpforo-style-color-wrapper" style="border-right:2px solid #eee; margin-right:10px; padding-left:5px; width:40px;">
                        <tr>
                            <td style="border-bottom:2px solid #EEEEEE; margin-bottom:5px;">
                                     #<div style="clear:both;"></div>
                            </td>
                        </tr>
                        <?php foreach( $wpforo->theme_options['styles']['default'] as $color_key => $color_value ): ?>
                            <tr>
                               <td>
                                    <div style="line-height:29px; font-size:14px; font-weight:bold;"><?php wpfo($color_key); ?></div>
                               </td>
                            </tr>
                        <?php endforeach; ?>
                        </table>
                	<?php foreach( $wpforo->theme_options['styles'] as $style => $colors ): ?>
                        <table class="wpforo-style-color-wrapper" style="border-right:2px solid #eee; margin-right:10px; padding-left:5px; <?php  echo ( $style == $wpforo->theme_options['style'] ) ? 'background: #E8FFE5; width: 200px; text-align: center;' : 'background: transparent'; ?>">
                                <tr>
                                    <td>
                                    <div style="float:left; text-align:center; width:27px;"><input style="margin:0px;" <?php if( $style == $wpforo->theme_options['style'] ) echo ' checked="checked"'; ?> type="radio" name="wpforo_theme_options[style]" value="<?php wpfo($style) ?>" id="wpforo_stle_<?php wpfo($style) ?>" /></div>
                                    <div style="text-transform:uppercase; text-align:left;float:left; font-weight:bold; font-size:14px;"><label for="wpforo_stle_<?php wpfo($style) ?>">&nbsp;<?php wpfo($style) ?></label></div>
                                    <div style="clear:both;"></div>
                                	</td>
                                </tr>
                                <?php foreach( $colors as $color_key => $color_value ): ?>
                                <tr>
                                    <td>
                                        <div class="wpforo-style-field">
                                        <?php if( $style == $wpforo->theme_options['style'] ): ?>
                                        	<input class="wpforo-color-field" name="wpforo_theme_options[styles][<?php wpfo($style) ?>][<?php wpfo($color_key); ?>]" type="text" value="<?php wpfo(strtoupper($color_value)); ?>" title="<?php wpfo(strtoupper($color_value)); ?>" />
										<?php else: ?>
                                        	<input style="width:90%;height: 22.5px; box-sizing:border-box; padding:0px;" name="wpforo_theme_options[styles][<?php wpfo($style) ?>][<?php wpfo($color_key); ?>]" type="color" value="<?php wpfo(strtoupper($color_value)); ?>" title="<?php wpfo(strtoupper($color_value)); ?>" />
                                        <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                        </table>    
                	<?php endforeach; ?>
                    <div style="clear:both;"></div>
                </td>
            </tr>
        </tbody>
    </table>
	<div style="clear:both;"></div>
    <div class="wpforo_settings_foot">
        <input type="submit" class="button button-primary" value="<?php _e('Update Options', 'wpforo'); ?>" />
    </div>
</form>
<script language="javascript">jQuery(document).ready(function($){$(function () { $('.wpforo-color-field').wpColorPicker(); });});</script>