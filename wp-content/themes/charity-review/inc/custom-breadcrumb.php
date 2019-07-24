<?php

if (! function_exists('charity_review_breadcrumb')) {
	function charity_review_breadcrumb(){
        // Boxed or Fullwidth
        $boxedornot = charity_review_boxedornot();
        global $post;

        echo '<div class = "breadcrumbs">';

        if ($boxedornot == 'fullwidth') {?>
            <div class="container full-width-container">
        <?php }

            if ( !is_home() ) {
                echo '<a href="';
                echo esc_url(home_url());
                echo '">';
                echo '<span class="home"><i class="fa fa-home"></i></span>';
                echo '</a><span class="delimiter"><i class="fa fa-angle-right"></i></span>';

                if ( is_category() ) {
                    echo "<span class='delimiter'>";
                    echo single_cat_title(); echo "</span>";
                } elseif ( is_single() ) {
                    echo '<span class="delimiter">';
                    the_title();
                    echo '</span>';
                } elseif ( is_search() ) {
                    echo '<span class="delimiter">';
                    echo __('Search Results for ','charity-review');
                    echo '<strong>';
                    echo get_search_query();
                    echo'</strong></span>';
                }  elseif ( is_404() ) {
                    echo '<span class="delimiter">';
                    echo __('404 - Page not found ','charity-review');
                    echo '</span>';
                } elseif ( is_day() ) {
                    echo '<span class="delimiter">';
                    echo __('Archive for ','charity-review');
                    echo the_time('F jS, Y');
                    echo'</span>';
                } elseif ( is_tag() ) {
                    echo '<span class="delimiter">';
                        single_tag_title();
                    echo'</span>';
                } elseif ( is_author() ) {
                echo '<span class="delimiter"> ';
                    echo __('Author Archive ','charity-review');
                    the_author();
                echo'</span>';
            } elseif ( has_post_format() ) {
                    echo '<span class="delimiter">';
                    echo get_post_format();
                    echo'</span>';
                } elseif ( is_page() ) {
                    if( $post->post_parent ){
                        $anc = get_post_ancestors( $post->ID );
                        $title = esc_attr( get_the_title() );
                        foreach ( $anc as $ancestor ) {
                            $output = '<a href="'. esc_url( get_permalink( $ancestor ) ) .'" title="'. esc_attr( get_the_title( $ancestor ) ) .'">'. esc_attr( get_the_title( $ancestor ) ) .'</a> <span class="delimiter"><i class="fa fa-angle-right"></i></span> ';
                        }
                        echo $output;
                        echo '<span title="'. $title .'"> '.$title .'</span>';
                    } else {
                        echo '<span class="delimiter"> '.esc_attr( get_the_title() ) .'</span><span class="delimiter">';
                    }
                }
            }
            elseif ( is_month() ) { echo '<span class="delimiter">Archive for '; get_the_date('F, Y'); echo'</span>'; }
            elseif ( is_year() ) { echo '<span class="delimiter">Archive for '; get_the_date('Y'); echo'</span>'; }
            elseif ( isset($_GET['paged'] ) && !empty( $_GET['paged'] ) ) { echo '<span class="delimiter">Blog Archives '; echo'</span>'; }
            if ($boxedornot == 'fullwidth') {?>
                </div>
            <?php }
        echo '</div>';
}
}