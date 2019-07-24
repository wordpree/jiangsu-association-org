<?php
	// Exit if accessed directly
	if( !defined( 'ABSPATH' ) ) exit;

/**
 * Template Name:  Forum Index (Forums List)
 */

if( WPF()->use_home_url ) get_header(); ?>
<?php extract(WPF()->current_object, EXTR_OVERWRITE); ?>
<?php include( wpftpl('header.php') ); ?>

<div class="wpforo-main <?php echo 'wpft-' . WPF()->current_object['template'] ?>">
    <div class="wpforo-content <?php if(WPF()->api->options['sb_location_toggle'] === 'right') echo 'wpfrt' ?>" <?php echo is_active_sidebar('forum-sidebar') ? '' : 'style="width:100%"' ?>>
        <?php do_action( 'wpforo_content_start' ); ?>
        <?php if( !in_array( WPF()->current_user_status, array('banned', 'trashed')) ) :

            if( !WPF()->current_object['is_404'] ){
                if(WPF()->current_object['template'] === 'search'){
                    include( wpftpl('search.php') );
                }elseif(WPF()->current_object['template'] === 'recent'){
                    include( wpftpl('recent.php') );
                }elseif(WPF()->current_object['template'] === 'tags'){
                    include( wpftpl('tags.php') );
                }elseif(WPF()->current_object['template'] === 'register'){
                    include( wpftpl('register.php') );
                }elseif(WPF()->current_object['template'] === 'login'){
                    include( wpftpl('login.php') );
                }elseif(WPF()->current_object['template'] === 'lostpassword'){
                    do_shortcode('[wpforo-lostpassword]');
                }elseif(WPF()->current_object['template'] === 'resetpassword'){
                    do_shortcode('[wpforo-resetpassword]');
                }elseif(WPF()->current_object['template'] === 'page'){
                    wpforo_page();
                }elseif(WPF()->current_object['template'] === 'members'){
                    include( wpftpl('members.php') );
                }elseif( wpfval(WPF()->member_tpls, WPF()->current_object['template']) ) {
                    include( wpftpl( 'profile.php' ) );
                }elseif( in_array(WPF()->current_object['template'], array('forum', 'topic')) ){
                    include( wpftpl('forum.php') );
                    if( WPF()->current_object['template'] === 'topic' ){
	                    include( wpftpl('topic.php') );
                    }
                }elseif( WPF()->current_object['template'] === 'post' ){
                    include( wpftpl('post.php') );
                }
            }else{
                include( wpftpl('404.php') );
            }

        else : ?>
            <p class="wpf-p-error">
                <?php wpforo_phrase('You have been banned. Please contact to forum administrators for more information.') ?>
            </p>
        <?php endif; ?>
    </div>
	<?php if (is_active_sidebar('forum-sidebar')) : ?>
        <div class="wpforo-right-sidebar">
			<?php dynamic_sidebar('forum-sidebar') ?>
        </div>
	<?php endif; ?>
   <div class="wpf-clear"></div>
</div>

<?php include( wpftpl('footer.php') );
if( WPF()->use_home_url ) get_footer();  ?>