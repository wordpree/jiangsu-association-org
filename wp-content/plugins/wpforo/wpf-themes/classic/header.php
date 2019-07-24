<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;
?>

<div id="wpforo-wrap" class="<?php do_action( 'wpforo_wrap_class', $wpforo ); ?>">

	<?php do_action( 'wpforo_top_hook' ); ?>
	
    <?php if( wpforo_feature('top-bar', $wpforo) ): ?>
        <div id="wpforo-menu" style="display:table; width:100%;">
            <?php do_action( 'wpforo_menu_bar_start', $wpforo ); ?>
            <div class="wpf-left" style="display:table-cell">
                <?php if($wpforo->tpl->has_menu()): ?>
                    <span class="wpf-res-menu"><i class="fa fa-bars"></i></span>
                    <?php $wpforo->tpl->nav_menu() ?>
                <?php endif; ?>
            </div>
            <div class="wpf-right wpf-search" style="display:table-cell; text-align:right; position:relative;">
                <?php if( wpforo_feature('top-bar-search', $wpforo) ): ?>
                    <form action="<?php echo wpforo_home_url() ?>" method="get">
                        <?php wpforo_make_hidden_fields_from_url( wpforo_home_url() ) ?>
                        <i class="fa fa-search"></i><input class="wpf-search-field" name="wpfs" type="text" value="" style="margin-right:10px;" />
                    </form>
                <?php endif; ?>
            </div>
            <?php do_action( 'wpforo_menu_bar_end', $wpforo ); ?>
        </div>
     <?php endif; ?>
    
    <?php if( wpforo_feature('breadcrumb', $wpforo) ): ?>
    	<?php $wpforo->tpl->breadcrumb($wpforo->current_object) ?>
    <?php endif; ?>
    
	<?php do_action( 'wpforo_header_hook' ); ?>