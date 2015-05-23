<?php

/**
 * Option/Settings Helper Functions
 *
 * @category Utilities
 * @package Spyropress
 *
 */

/** Option Getter and Formatter **********************/

function get_float_class( $float ) {
    
    // check for null
    if ( ! $float ) return;

    $allowed_floats = array( 'left' => 'pull-left', 'right' => 'pull-right' );

    if ( in_array( $float, array_keys( $allowed_floats ) ) )
        $float = $allowed_floats[$float];

    return $float;
}

/**
 * Row Class
 */
function get_row_class( $return = false ) {
    global $spyropress;

    if ( $return )
        return $spyropress->row_class;
    echo $spyropress->row_class;
}

/**
 * Column Class
 */
function get_column_class( $column ) {
    if( 'skt' == get_html_framework() ) return get_skeleton_col_class( $column );
    
    if( 'bs' == get_html_framework() ) return get_bootstrap_class( $column );
    
    if( 'bs3' == get_html_framework() ) return get_bootstrap3_class( $column );
    
    if( 'fd3' == get_html_framework() ) return get_foundation3_col_class( $column );
}

/**
 * Bootstrap Class
 */
function get_bootstrap_class( $column ) {

    $class = 'span12';

    switch ( $column ) {
        case 2:
            $class = 'span6';
            break;
        case 3:
            $class = 'span4';
            break;
        case 4:
            $class = 'span3';
            break;
        case 6:
            $class = 'span2';
            break;
    }

    return $class;
}

/**
 * Bootstrap Class
 */
function get_bootstrap3_class( $column ) {

    $class = 'col-md-12';

    switch ( $column ) {
        case 2:
            $class = 'col-md-6';
            break;
        case 3:
            $class = 'col-md-4';
            break;
        case 4:
            $class = 'col-md-3';
            break;
        case 6:
            $class = 'col-md-2';
            break;
    }

    return $class;
}

/**
 * Skeleton Classes
 */
function get_skeleton_col_class( $column ) {

    $class = get_skeleton_class( 16 );

    switch ( $column ) {
        case 2:
            $class = get_skeleton_class( 8 );
            break;
        case 3:
            $class = get_skeleton_class( '1/3' );
            break;
        case 4:
            $class = get_skeleton_class( 4 );
            break;
        case 8:
            $class = get_skeleton_class( 2 );
            break;
    }

    return $class;
}

function get_skeleton_class( $column ) {
    
    $classes = array(
        1 => 'one columns',
        2 => 'two columns',
        3 => 'three columns',
        4 => 'four columns',
        5 => 'five columns',
        6 => 'six columns',
        7 => 'seven columns',
        8 => 'eight columns',
        9 => 'nine columns',
        10 => 'ten columns',
        11 => 'eleven columns',
        12 => 'twelve columns',
        13 => 'thirteen columns',
        14 => 'fourteen columns',
        15 => 'fifteen columns',
        16 => 'sixteen columns',
        '1/3' => 'one-third column',
        '2/3' => 'two-thirds column',
    );
    
    return $classes[$column];
}

function get_admin_column_class( $column ) {
    
    $class = '';
    if( 12 == get_grid_columns() ) {

        $class = 'span12';
        
        switch ( $column ) {
            case 2:
                $class = 'span6';
                break;
            case 3:
                $class = 'span4';
                break;
            case 4:
                $class = 'span3';
                break;
            case 6:
                $class = 'span2';
                break;
        }
    }
    elseif( 16 == get_grid_columns() ) {

        $class = 'span16';
        
        switch ( $column ) {
            case 2:
                $class = 'span8';
                break;
            case 3:
                $class = 'span1by3';
                break;
            case 4:
                $class = 'span4';
                break;
            case 8:
                $class = 'span2';
                break;
        }
    }
    
    return $class;
}

/**
 * Foundation3 Classes
 */

function get_foundation3_col_class( $column ) {

    $class = get_foundation3_class( 12 );

    switch ( $column ) {
        case 2:
            $class = get_foundation3_class( 6 );
            break;
        case 3:
            $class = get_foundation3_class( 4 );
            break;
        case 4:
            $class = get_foundation3_class( 3 );
            break;
        case 6:
            $class = get_foundation3_class( 2 );
            break;
    }

    return $class;
}

function get_foundation3_class( $column ) {

    $classes = array(
        1 => 'one columns',
        2 => 'two columns',
        3 => 'three columns',
        4 => 'four columns',
        5 => 'five columns',
        6 => 'six columns',
        7 => 'seven columns',
        8 => 'eight columns',
        9 => 'nine columns',
        10 => 'ten columns',
        11 => 'eleven columns',
        12 => 'twelve columns'
    );
    
    return $classes[$column];
}

/**
 * Get HTML Framework
 */
function get_html_framework() {
    global $spyropress;
    return $spyropress->framework;
}

/**
 * Get Grid Column
 */
function get_grid_columns() {
    global $spyropress;
    return $spyropress->grid_columns;
}

/**
 * First Column Class accoring to framework
 */
function get_first_column_class() {
    
    // Skeleton
    if( 'skt' == get_html_framework() ) return 'alpha';
    
    // Others
    return 'column_first';
}

/**
 * Last Column Class accoring to framework
 */
function get_last_column_class() {
    
    // Skeleton
    if( 'skt' == get_html_framework() ) return 'omega';
    
    // Foundation
    if( 'fd3' == get_html_framework() ) return 'end';
    
    // Others
    return 'column_last';
}

/** Data Sources for Post Type and Taxonomies **********************/

/**
 * Buckets
 */
function spyropress_get_buckets() {
    
    $buckets = array();
    
    if ( ! post_type_exists( 'bucket' ) ) return $buckets;
    
    // get posts
    $args = array(
        'post_type' => 'bucket',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'asc'
    );
    $posts = get_posts( $args );
    if ( !empty( $posts ) ) {
        foreach ( $posts as $post ) {
            $buckets[$post->ID] = $post->post_title;
        }
    }

    return $buckets;
}

/**
 * Custom Taxonomies
 */
function spyropress_get_taxonomies( $tax = '' ) {
    
    if ( ! $tax ) return array();

    $terms = get_terms( $tax );
    $taxs = array();
    if ( !empty( $terms ) )
        foreach ( $terms as $term )
            $taxs[$term->slug] = $term->name;

    return $taxs;
}

/** Custom Data Sources ********************************************/

/**
 * jQuery Easing Options
 */
function spyropress_get_options_easing() {
    return array(
        'linear' => __( 'linear', 'spyropress' ),
        'jswing' => __( 'jswing', 'spyropress' ),
        'def' => __( 'def', 'spyropress' ),
        'easeInQuad' => __( 'easeInQuad', 'spyropress' ),
        'easeOutQuad' => __( 'easeOutQuad', 'spyropress' ),
        'easeInOutQuad' => __( 'easeInOutQuad', 'spyropress' ),
        'easeInCubic' => __( 'easeInCubic', 'spyropress' ),
        'easeOutCubic' => __( 'easeOutCubic', 'spyropress' ),
        'easeInOutCubic' => __( 'easeInOutCubic', 'spyropress' ),
        'easeInQuart' => __( 'easeInQuart', 'spyropress' ),
        'easeOutQuart' => __( 'easeOutQuart', 'spyropress' ),
        'easeInOutQuart' => __( 'easeInOutQuart', 'spyropress' ),
        'easeInQuint' => __( 'easeInQuint', 'spyropress' ),
        'easeOutQuint' => __( 'easeOutQuint', 'spyropress' ),
        'easeInOutQuint' => __( 'easeInOutQuint', 'spyropress' ),
        'easeInSine' => __( 'easeInSine', 'spyropress' ),
        'easeOutSine' => __( 'easeOutSine', 'spyropress' ),
        'easeInOutSine' => __( 'easeInOutSine', 'spyropress' ),
        'easeInExpo' => __( 'easeInExpo', 'spyropress' ),
        'easeOutExpo' => __( 'easeOutExpo', 'spyropress' ),
        'easeInOutExpo' => __( 'easeInOutExpo', 'spyropress' ),
        'easeInCirc' => __( 'easeInCirc', 'spyropress' ),
        'easeOutCirc' => __( 'easeOutCirc', 'spyropress' ),
        'easeInOutCirc' => __( 'easeInOutCirc', 'spyropress' ),
        'easeInElastic' => __( 'easeInElastic', 'spyropress' ),
        'easeOutElastic' => __( 'easeOutElastic', 'spyropress' ),
        'easeInOutElastic' => __( 'easeInOutElastic', 'spyropress' ),
        'easeInBack' => __( 'easeInBack', 'spyropress' ),
        'easeOutBack' => __( 'easeOutBack', 'spyropress' ),
        'easeInOutBack' => __( 'easeInOutBack', 'spyropress' ),
        'easeInBounce' => __( 'easeInBounce', 'spyropress' ),
        'easeOutBounce' => __( 'easeOutBounce', 'spyropress' ),
        'easeInOutBounce' => __( 'easeInOutBounce', 'spyropress' ),
    );
}

function spyropress_get_options_float() {
    return array(
        'none' => __( 'None', 'spyropress' ),
        'left' => __( 'Left', 'spyropress' ),
        'right' => __( 'Right', 'spyropress' ),
    );
}

function spyropress_get_options_link( $fields ) {
    // check for emptiness
    if ( empty( $fields ) ) $fields = array();

    return array_merge( $fields, array(
        array(
            'label' => __( 'URL/Link Setting', 'spyropress' ),
            'type' => 'toggle'
        ),

        array(
            'label' => __( 'Link Text', 'spyropress' ),
            'id' => 'url_text',
            'type' => 'text'
        ),

        array(
            'label' => __( 'URL/Hash', 'spyropress' ),
            'id' => 'url',
            'type' => 'text'
        ),

        array(
            'label' => __( 'Link to Post/Page', 'spyropress' ),
            'id' => 'link_url',
            'type' => 'custom_post',
            'post_type' => array( 'post', 'page' )
        ),

        array( 'type' => 'toggle_end' )
    ) );
}

function spyropress_get_options_social() {
    return array(
        'behance' => __( 'Behance', 'spyropress' ),
        'digg' => __( 'Digg', 'spyropress' ),
        'dribble' => __( 'Dribble', 'spyropress' ),
        'facebook' => __( 'Facebook', 'spyropress' ),
        'flickr' => __( 'Flickr', 'spyropress' ),
        'forrst' => __( 'Forrst', 'spyropress' ),
        'foursquare' => __( 'Foursquare', 'spyropress' ),
        'github' => __( 'Github', 'spyropress' ),
        'googleplus' => __( 'Google+', 'spyropress' ),
        'html5' => __( 'HTML5', 'spyropress' ),
        'icloud' => __( 'iCloud', 'spyropress' ),
        'instagram' => __( 'Instagram', 'spyropress' ),
        'lastfm' => __( 'Lastfm', 'spyropress' ),
        'linkedin' => __( 'Linkedin', 'spyropress' ),
        'envelope' => __( 'Mail', 'spyropress' ),
        'myspace' => __( 'MySpace', 'spyropress' ),
        'paypal' => __( 'PayPal', 'spyropress' ),
        'picasa' => __( 'Picasa', 'spyropress' ),
        'pinterest' => __( 'Pinterest', 'spyropress' ),
        'reddit' => __( 'Reddit', 'spyropress' ),
        'rss' => __( 'RSS', 'spyropress' ),
        'skype' => __( 'Skype', 'spyropress' ),
        'stumbleupon' => __( 'Stumbleupon', 'spyropress' ),
        'tumblr' => __( 'Tumblr', 'spyropress' ),
        'twitter' => __( 'Twitter', 'spyropress' ),
        'vimeo' => __( 'Vimeo', 'spyropress' ),
        'vk' => __( 'VK', 'spyropress' ),
        'wordpress' => __( 'Wordpress', 'spyropress' ),
        'yahoo' => __( 'Yahoo', 'spyropress' ),
        'yelp' => __( 'Yelp', 'spyropress' ),
        'youtube' => __( 'Youtube', 'spyropress' ),
        'zerply' => __( 'Zerply', 'spyropress' ),
    );
}

function spyropress_get_options_animation() {
    return array(
        'flash' => __( 'flash', 'spyropress' ),
        'shake' => __( 'shake', 'spyropress' ),
        'bounce' => __( 'bounce', 'spyropress' ),
        'tada' => __( 'tada', 'spyropress' ),
        'swing' => __( 'swing', 'spyropress' ),
        'wobble' => __( 'wobble', 'spyropress' ),
        'wiggle' => __( 'wiggle', 'spyropress' ),
        'pulse' => __( 'pulse', 'spyropress' ),
        'fadeIn' => __( 'fadeIn', 'spyropress' ),
        'fadeInUp' => __( 'fadeInUp', 'spyropress' ),
        'fadeInDown' => __( 'fadeInDown', 'spyropress' ),
        'fadeInLeft' => __( 'fadeInLeft', 'spyropress' ),
        'fadeInRight' => __( 'fadeInRight', 'spyropress' ),
        'fadeInUpBig' => __( 'fadeInUpBig', 'spyropress' ),
        'fadeInDownBig' => __( 'fadeInDownBig', 'spyropress' ),
        'fadeInLeftBig' => __( 'fadeInLeftBig', 'spyropress' ),
        'fadeInRightBig' => __( 'fadeInRightBig', 'spyropress' ),
        'bounceIn' => __( 'bounceIn', 'spyropress' ),
        'bounceInUp' => __( 'bounceInUp', 'spyropress' ),
        'bounceInDown' => __( 'bounceInDown', 'spyropress' ),
        'bounceInLeft' => __( 'bounceInLeft', 'spyropress' ),
        'bounceInRight' => __( 'bounceInRight', 'spyropress' ),
        'rotateIn' => __( 'rotateIn', 'spyropress' ),
        'rotateInUpLeft' => __( 'rotateInUpLeft', 'spyropress' ),
        'rotateInDownLeft' => __( 'rotateInDownLeft', 'spyropress' ),
        'rotateInUpRight' => __( 'rotateInUpRight', 'spyropress' ),
        'rotateInDownRight' => __( 'rotateInDownRight', 'spyropress' )
    );
}

function spyropress_get_options_fontawesome_icons(){
    
     return array(
        'icon-glass' => __( 'Glass', 'spyropress' ),
        'icon-music' => __( 'Music', 'spyropress' ),
        'icon-search' => __( 'Search', 'spyropress' ),
        'icon-envelope-o' => __( 'Envelope Outline', 'spyropress' ),
        'icon-heart' => __( 'Heart', 'spyropress' ),
        'icon-star' => __( 'Star', 'spyropress' ),
        'icon-star-o' => __( 'Star Outline', 'spyropress' ),
        'icon-user' => __( 'User', 'spyropress' ),
        'icon-film' => __( 'Film', 'spyropress' ),
        'icon-th-large' => __( 'Th Large', 'spyropress' ),
        'icon-th' => __( 'Th', 'spyropress' ),
        'icon-th-list' => __( 'Th List', 'spyropress' ),
        'icon-check' => __( 'Check', 'spyropress' ),
        'icon-times' => __( 'Times', 'spyropress' ),
        'icon-search-plus' => __( 'Search Plus', 'spyropress' ),
        'icon-search-minus' => __( 'Search Minus', 'spyropress' ),
        'icon-power-off' => __( 'Power Off', 'spyropress' ),
        'icon-signal' => __( 'Signal', 'spyropress' ),
        'icon-gear' => __( 'Gear', 'spyropress' ),
        'icon-cog' => __( 'Cog', 'spyropress' ),
        'icon-trash-o' => __( 'Trash Outline', 'spyropress' ),
        'icon-home' => __( 'Home', 'spyropress' ),
        'icon-file-o' => __( 'File Outline', 'spyropress' ),
        'icon-clock-o' => __( 'Clock Outline', 'spyropress' ),
        'icon-road' => __( 'Road', 'spyropress' ),
        'icon-download' => __( 'Download', 'spyropress' ),
        'icon-arrow-circle-o-down' => __( 'Arrow Circle Outline Down', 'spyropress' ),
        'icon-arrow-circle-o-up' => __( 'Arrow Circle Outline Up', 'spyropress' ),
        'icon-inbox' => __( 'Inbox', 'spyropress' ),
        'icon-play-circle-o' => __( 'Play Circle Outline', 'spyropress' ),
        'icon-rotate-right' => __( 'Rotate Right', 'spyropress' ),
        'icon-repeat' => __( 'Repeat', 'spyropress' ),
        'icon-refresh' => __( 'Refresh', 'spyropress' ),
        'icon-list-alt' => __( 'List Alt', 'spyropress' ),
        'icon-lock' => __( 'Lock', 'spyropress' ),
        'icon-flag' => __( 'Flag', 'spyropress' ),
        'icon-headphones' => __( 'Headphones', 'spyropress' ),
        'icon-volume-off' => __( 'Volume Off', 'spyropress' ),
        'icon-volume-down' => __( 'Volume Down', 'spyropress' ),
        'icon-volume-up' => __( 'Volume Up', 'spyropress' ),
        'icon-qrcode' => __( 'Qrcode', 'spyropress' ),
        'icon-barcode' => __( 'Barcode', 'spyropress' ),
        'icon-tag' => __( 'Tag', 'spyropress' ),
        'icon-tags' => __( 'Tags', 'spyropress' ),
        'icon-book' => __( 'Book', 'spyropress' ),
        'icon-bookmark' => __( 'Bookmark', 'spyropress' ),
        'icon-print' => __( 'Print', 'spyropress' ),
        'icon-camera' => __( 'Camera', 'spyropress' ),
        'icon-font' => __( 'Font', 'spyropress' ),
        'icon-bold' => __( 'Bold', 'spyropress' ),
        'icon-italic' => __( 'Italic', 'spyropress' ),
        'icon-text-height' => __( 'Text Height', 'spyropress' ),
        'icon-text-width' => __( 'Text Width', 'spyropress' ),
        'icon-align-left' => __( 'Align Left', 'spyropress' ),
        'icon-align-center' => __( 'Align Center', 'spyropress' ),
        'icon-align-right' => __( 'Align Right', 'spyropress' ),
        'icon-align-justify' => __( 'Align Justify', 'spyropress' ),
        'icon-list' => __( 'List', 'spyropress' ),
        'icon-dedent' => __( 'Dedent', 'spyropress' ),
        'icon-outdent' => __( 'Outdent', 'spyropress' ),
        'icon-indent' => __( 'Indent', 'spyropress' ),
        'icon-video-camera' => __( 'Video Camera', 'spyropress' ),
        'icon-picture-o' => __( 'Picture Outline', 'spyropress' ),
        'icon-pencil' => __( 'Pencil', 'spyropress' ),
        'icon-map-marker' => __( 'Map Marker', 'spyropress' ),
        'icon-adjust' => __( 'Adjust', 'spyropress' ),
        'icon-tint' => __( 'Tint', 'spyropress' ),
        'icon-edit' => __( 'Edit', 'spyropress' ),
        'icon-pencil-square-o' => __( 'Pencil Square Outline', 'spyropress' ),
        'icon-share-square-o' => __( 'Share Square Outline', 'spyropress' ),
        'icon-check-square-o' => __( 'Check Square Outline', 'spyropress' ),
        'icon-arrows' => __( 'Arrows', 'spyropress' ),
        'icon-step-backward' => __( 'Step Backward', 'spyropress' ),
        'icon-fast-backward' => __( 'Fast Backward', 'spyropress' ),
        'icon-backward' => __( 'Backward', 'spyropress' ),
        'icon-play' => __( 'Play', 'spyropress' ),
        'icon-pause' => __( 'Pause', 'spyropress' ),
        'icon-stop' => __( 'Stop', 'spyropress' ),
        'icon-forward' => __( 'Forward', 'spyropress' ),
        'icon-fast-forward' => __( 'Fast Forward', 'spyropress' ),
        'icon-step-forward' => __( 'Step Forward', 'spyropress' ),
        'icon-eject' => __( 'Eject', 'spyropress' ),
        'icon-chevron-left' => __( 'Chevron Left', 'spyropress' ),
        'icon-chevron-right' => __( 'Chevron Right', 'spyropress' ),
        'icon-plus-circle' => __( 'Plus Circle', 'spyropress' ),
        'icon-minus-circle' => __( 'Minus Circle', 'spyropress' ),
        'icon-times-circle' => __( 'Times Circle', 'spyropress' ),
        'icon-check-circle' => __( 'Check Circle', 'spyropress' ),
        'icon-question-circle' => __( 'Question Circle', 'spyropress' ),
        'icon-info-circle' => __( 'Info Circle', 'spyropress' ),
        'icon-crosshairs' => __( 'Crosshairs', 'spyropress' ),
        'icon-times-circle-o' => __( 'Times Circle Outline', 'spyropress' ),
        'icon-check-circle-o' => __( 'Check Circle Outline', 'spyropress' ),
        'icon-ban' => __( 'Ban', 'spyropress' ),
        'icon-arrow-left' => __( 'Arrow Left', 'spyropress' ),
        'icon-arrow-right' => __( 'Arrow Right', 'spyropress' ),
        'icon-arrow-up' => __( 'Arrow Up', 'spyropress' ),
        'icon-arrow-down' => __( 'Arrow Down', 'spyropress' ),
        'icon-mail-forward' => __( 'Mail Forward', 'spyropress' ),
        'icon-share' => __( 'Share', 'spyropress' ),
        'icon-expand' => __( 'Expand', 'spyropress' ),
        'icon-compress' => __( 'Compress', 'spyropress' ),
        'icon-plus' => __( 'Plus', 'spyropress' ),
        'icon-minus' => __( 'Minus', 'spyropress' ),
        'icon-asterisk' => __( 'Asterisk', 'spyropress' ),
        'icon-exclamation-circle' => __( 'Exclamation Circle', 'spyropress' ),
        'icon-gift' => __( 'Gift', 'spyropress' ),
        'icon-leaf' => __( 'Leaf', 'spyropress' ),
        'icon-fire' => __( 'Fire', 'spyropress' ),
        'icon-eye' => __( 'Eye', 'spyropress' ),
        'icon-eye-slash' => __( 'Eye Slash', 'spyropress' ),
        'icon-warning' => __( 'Warning', 'spyropress' ),
        'icon-exclamation-triangle' => __( 'Exclamation Triangle', 'spyropress' ),
        'icon-plane' => __( 'Plane', 'spyropress' ),
        'icon-calendar' => __( 'Calendar', 'spyropress' ),
        'icon-random' => __( 'Random', 'spyropress' ),
        'icon-comment' => __( 'Comment', 'spyropress' ),
        'icon-magnet' => __( 'Magnet', 'spyropress' ),
        'icon-chevron-up' => __( 'Chevron Up', 'spyropress' ),
        'icon-chevron-down' => __( 'Chevron Down', 'spyropress' ),
        'icon-retweet' => __( 'Retweet', 'spyropress' ),
        'icon-shopping-cart' => __( 'Shopping Cart', 'spyropress' ),
        'icon-folder' => __( 'Folder', 'spyropress' ),
        'icon-folder-open' => __( 'Folder Open', 'spyropress' ),
        'icon-arrows-v' => __( 'Arrows V', 'spyropress' ),
        'icon-arrows-h' => __( 'Arrows H', 'spyropress' ),
        'icon-bar-chart-o' => __( 'Bar Chart Outline', 'spyropress' ),
        'icon-twitter-square' => __( 'Twitter Square', 'spyropress' ),
        'icon-facebook-square' => __( 'Facebook Square', 'spyropress' ),
        'icon-camera-retro' => __( 'Camera Retro', 'spyropress' ),
        'icon-key' => __( 'Key', 'spyropress' ),
        'icon-gears' => __( 'Gears', 'spyropress' ),
        'icon-cogs' => __( 'Cogs', 'spyropress' ),
        'icon-comments' => __( 'Comments', 'spyropress' ),
        'icon-thumbs-o-up' => __( 'Thumbs Outline Up', 'spyropress' ),
        'icon-thumbs-o-down' => __( 'Thumbs Outline Down', 'spyropress' ),
        'icon-star-half' => __( 'Star Half', 'spyropress' ),
        'icon-heart-o' => __( 'Heart Outline', 'spyropress' ),
        'icon-sign-out' => __( 'Sign Out', 'spyropress' ),
        'icon-linkedin-square' => __( 'Linkedin Square', 'spyropress' ),
        'icon-thumb-tack' => __( 'Thumb Tack', 'spyropress' ),
        'icon-external-link' => __( 'External Link', 'spyropress' ),
        'icon-sign-in' => __( 'Sign In', 'spyropress' ),
        'icon-trophy' => __( 'Trophy', 'spyropress' ),
        'icon-github-square' => __( 'Github Square', 'spyropress' ),
        'icon-upload' => __( 'Upload', 'spyropress' ),
        'icon-lemon-o' => __( 'Lemon Outline', 'spyropress' ),
        'icon-phone' => __( 'Phone', 'spyropress' ),
        'icon-square-o' => __( 'Square Outline', 'spyropress' ),
        'icon-bookmark-o' => __( 'Bookmark Outline', 'spyropress' ),
        'icon-phone-square' => __( 'Phone Square', 'spyropress' ),
        'icon-twitter' => __( 'Twitter', 'spyropress' ),
        'icon-facebook' => __( 'Facebook', 'spyropress' ),
        'icon-github' => __( 'Github', 'spyropress' ),
        'icon-unlock' => __( 'Unlock', 'spyropress' ),
        'icon-credit-card' => __( 'Credit Card', 'spyropress' ),
        'icon-rss' => __( 'Rss', 'spyropress' ),
        'icon-hdd-o' => __( 'Hdd Outline', 'spyropress' ),
        'icon-bullhorn' => __( 'Bullhorn', 'spyropress' ),
        'icon-bell' => __( 'Bell', 'spyropress' ),
        'icon-certificate' => __( 'Certificate', 'spyropress' ),
        'icon-hand-o-right' => __( 'Hand Outline Right', 'spyropress' ),
        'icon-hand-o-left' => __( 'Hand Outline Left', 'spyropress' ),
        'icon-hand-o-up' => __( 'Hand Outline Up', 'spyropress' ),
        'icon-hand-o-down' => __( 'Hand Outline Down', 'spyropress' ),
        'icon-arrow-circle-left' => __( 'Arrow Circle Left', 'spyropress' ),
        'icon-arrow-circle-right' => __( 'Arrow Circle Right', 'spyropress' ),
        'icon-arrow-circle-up' => __( 'Arrow Circle Up', 'spyropress' ),
        'icon-arrow-circle-down' => __( 'Arrow Circle Down', 'spyropress' ),
        'icon-globe' => __( 'Globe', 'spyropress' ),
        'icon-wrench' => __( 'Wrench', 'spyropress' ),
        'icon-tasks' => __( 'Tasks', 'spyropress' ),
        'icon-filter' => __( 'Filter', 'spyropress' ),
        'icon-briefcase' => __( 'Briefcase', 'spyropress' ),
        'icon-arrows-alt' => __( 'Arrows Alt', 'spyropress' ),
        'icon-group' => __( 'Group', 'spyropress' ),
        'icon-users' => __( 'Users', 'spyropress' ),
        'icon-chain' => __( 'Chain', 'spyropress' ),
        'icon-link' => __( 'Link', 'spyropress' ),
        'icon-cloud' => __( 'Cloud', 'spyropress' ),
        'icon-flask' => __( 'Flask', 'spyropress' ),
        'icon-cut' => __( 'Cut', 'spyropress' ),
        'icon-scissors' => __( 'Scissors', 'spyropress' ),
        'icon-copy' => __( 'Copy', 'spyropress' ),
        'icon-files-o' => __( 'Files Outline', 'spyropress' ),
        'icon-paperclip' => __( 'Paperclip', 'spyropress' ),
        'icon-save' => __( 'Save', 'spyropress' ),
        'icon-floppy-o' => __( 'Floppy Outline', 'spyropress' ),
        'icon-square' => __( 'Square', 'spyropress' ),
        'icon-bars' => __( 'Bars', 'spyropress' ),
        'icon-list-ul' => __( 'List Ul', 'spyropress' ),
        'icon-list-ol' => __( 'List Ol', 'spyropress' ),
        'icon-strikethrough' => __( 'Strikethrough', 'spyropress' ),
        'icon-underline' => __( 'Underline', 'spyropress' ),
        'icon-table' => __( 'Table', 'spyropress' ),
        'icon-magic' => __( 'Magic', 'spyropress' ),
        'icon-truck' => __( 'Truck', 'spyropress' ),
        'icon-pinterest' => __( 'Pinterest', 'spyropress' ),
        'icon-pinterest-square' => __( 'Pinterest Square', 'spyropress' ),
        'icon-google-plus-square' => __( 'Google Plus Square', 'spyropress' ),
        'icon-google-plus' => __( 'Google Plus', 'spyropress' ),
        'icon-money' => __( 'Money', 'spyropress' ),
        'icon-caret-down' => __( 'Caret Down', 'spyropress' ),
        'icon-caret-up' => __( 'Caret Up', 'spyropress' ),
        'icon-caret-left' => __( 'Caret Left', 'spyropress' ),
        'icon-caret-right' => __( 'Caret Right', 'spyropress' ),
        'icon-columns' => __( 'Columns', 'spyropress' ),
        'icon-unsorted' => __( 'Unsorted', 'spyropress' ),
        'icon-sort' => __( 'Sort', 'spyropress' ),
        'icon-sort-down' => __( 'Sort Down', 'spyropress' ),
        'icon-sort-asc' => __( 'Sort Asc', 'spyropress' ),
        'icon-sort-up' => __( 'Sort Up', 'spyropress' ),
        'icon-sort-desc' => __( 'Sort Desc', 'spyropress' ),
        'icon-envelope' => __( 'Envelope', 'spyropress' ),
        'icon-linkedin' => __( 'Linkedin', 'spyropress' ),
        'icon-rotate-left' => __( 'Rotate Left', 'spyropress' ),
        'icon-undo' => __( 'Undo', 'spyropress' ),
        'icon-legal' => __( 'Legal', 'spyropress' ),
        'icon-gavel' => __( 'Gavel', 'spyropress' ),
        'icon-dashboard' => __( 'Dashboard', 'spyropress' ),
        'icon-tachometer' => __( 'Tachometer', 'spyropress' ),
        'icon-comment-o' => __( 'Comment Outline', 'spyropress' ),
        'icon-comments-o' => __( 'Comments Outline', 'spyropress' ),
        'icon-flash' => __( 'Flash', 'spyropress' ),
        'icon-bolt' => __( 'Bolt', 'spyropress' ),
        'icon-sitemap' => __( 'Sitemap', 'spyropress' ),
        'icon-umbrella' => __( 'Umbrella', 'spyropress' ),
        'icon-paste' => __( 'Paste', 'spyropress' ),
        'icon-clipboard' => __( 'Clipboard', 'spyropress' ),
        'icon-lightbulb-o' => __( 'Lightbulb Outline', 'spyropress' ),
        'icon-exchange' => __( 'Exchange', 'spyropress' ),
        'icon-cloud-download' => __( 'Cloud Download', 'spyropress' ),
        'icon-cloud-upload' => __( 'Cloud Upload', 'spyropress' ),
        'icon-user-md' => __( 'User Md', 'spyropress' ),
        'icon-stethoscope' => __( 'Stethoscope', 'spyropress' ),
        'icon-suitcase' => __( 'Suitcase', 'spyropress' ),
        'icon-bell-o' => __( 'Bell Outline', 'spyropress' ),
        'icon-coffee' => __( 'Coffee', 'spyropress' ),
        'icon-cutlery' => __( 'Cutlery', 'spyropress' ),
        'icon-file-text-o' => __( 'File Text Outline', 'spyropress' ),
        'icon-building-o' => __( 'Building Outline', 'spyropress' ),
        'icon-hospital-o' => __( 'Hospital Outline', 'spyropress' ),
        'icon-ambulance' => __( 'Ambulance', 'spyropress' ),
        'icon-medkit' => __( 'Medkit', 'spyropress' ),
        'icon-fighter-jet' => __( 'Fighter Jet', 'spyropress' ),
        'icon-beer' => __( 'Beer', 'spyropress' ),
        'icon-h-square' => __( 'H Square', 'spyropress' ),
        'icon-plus-square' => __( 'Plus Square', 'spyropress' ),
        'icon-angle-double-left' => __( 'Angle Double Left', 'spyropress' ),
        'icon-angle-double-right' => __( 'Angle Double Right', 'spyropress' ),
        'icon-angle-double-up' => __( 'Angle Double Up', 'spyropress' ),
        'icon-angle-double-down' => __( 'Angle Double Down', 'spyropress' ),
        'icon-angle-left' => __( 'Angle Left', 'spyropress' ),
        'icon-angle-right' => __( 'Angle Right', 'spyropress' ),
        'icon-angle-up' => __( 'Angle Up', 'spyropress' ),
        'icon-angle-down' => __( 'Angle Down', 'spyropress' ),
        'icon-desktop' => __( 'Desktop', 'spyropress' ),
        'icon-laptop' => __( 'Laptop', 'spyropress' ),
        'icon-tablet' => __( 'Tablet', 'spyropress' ),
        'icon-mobile-phone' => __( 'Mobile Phone', 'spyropress' ),
        'icon-mobile' => __( 'Mobile', 'spyropress' ),
        'icon-circle-o' => __( 'Circle Outline', 'spyropress' ),
        'icon-quote-left' => __( 'Quote Left', 'spyropress' ),
        'icon-quote-right' => __( 'Quote Right', 'spyropress' ),
        'icon-spinner' => __( 'Spinner', 'spyropress' ),
        'icon-circle' => __( 'Circle', 'spyropress' ),
        'icon-mail-reply' => __( 'Mail Reply', 'spyropress' ),
        'icon-reply' => __( 'Reply', 'spyropress' ),
        'icon-github-alt' => __( 'Github Alt', 'spyropress' ),
        'icon-folder-o' => __( 'Folder Outline', 'spyropress' ),
        'icon-folder-open-o' => __( 'Folder Open Outline', 'spyropress' ),
        'icon-smile-o' => __( 'Smile Outline', 'spyropress' ),
        'icon-frown-o' => __( 'Frown Outline', 'spyropress' ),
        'icon-meh-o' => __( 'Meh Outline', 'spyropress' ),
        'icon-gamepad' => __( 'Gamepad', 'spyropress' ),
        'icon-keyboard-o' => __( 'Keyboard Outline', 'spyropress' ),
        'icon-flag-o' => __( 'Flag Outline', 'spyropress' ),
        'icon-flag-checkered' => __( 'Flag Checkered', 'spyropress' ),
        'icon-terminal' => __( 'Terminal', 'spyropress' ),
        'icon-code' => __( 'Code', 'spyropress' ),
        'icon-reply-all' => __( 'Reply All', 'spyropress' ),
        'icon-mail-reply-all' => __( 'Mail Reply All', 'spyropress' ),
        'icon-star-half-empty' => __( 'Star Half Empty', 'spyropress' ),
        'icon-star-half-full' => __( 'Star Half Full', 'spyropress' ),
        'icon-star-half-o' => __( 'Star Half Outline', 'spyropress' ),
        'icon-location-arrow' => __( 'Location Arrow', 'spyropress' ),
        'icon-crop' => __( 'Crop', 'spyropress' ),
        'icon-code-fork' => __( 'Code Fork', 'spyropress' ),
        'icon-unlink' => __( 'Unlink', 'spyropress' ),
        'icon-chain-broken' => __( 'Chain Broken', 'spyropress' ),
        'icon-question' => __( 'Question', 'spyropress' ),
        'icon-info' => __( 'Info', 'spyropress' ),
        'icon-exclamation' => __( 'Exclamation', 'spyropress' ),
        'icon-superscript' => __( 'Superscript', 'spyropress' ),
        'icon-subscript' => __( 'Subscript', 'spyropress' ),
        'icon-eraser' => __( 'Eraser', 'spyropress' ),
        'icon-puzzle-piece' => __( 'Puzzle Piece', 'spyropress' ),
        'icon-microphone' => __( 'Microphone', 'spyropress' ),
        'icon-microphone-slash' => __( 'Microphone Slash', 'spyropress' ),
        'icon-shield' => __( 'Shield', 'spyropress' ),
        'icon-calendar-o' => __( 'Calendar Outline', 'spyropress' ),
        'icon-fire-extinguisher' => __( 'Fire Extinguisher', 'spyropress' ),
        'icon-rocket' => __( 'Rocket', 'spyropress' ),
        'icon-maxcdn' => __( 'Maxcdn', 'spyropress' ),
        'icon-chevron-circle-left' => __( 'Chevron Circle Left', 'spyropress' ),
        'icon-chevron-circle-right' => __( 'Chevron Circle Right', 'spyropress' ),
        'icon-chevron-circle-up' => __( 'Chevron Circle Up', 'spyropress' ),
        'icon-chevron-circle-down' => __( 'Chevron Circle Down', 'spyropress' ),
        'icon-html5' => __( 'Html5', 'spyropress' ),
        'icon-css3' => __( 'Css3', 'spyropress' ),
        'icon-anchor' => __( 'Anchor', 'spyropress' ),
        'icon-unlock-alt' => __( 'Unlock Alt', 'spyropress' ),
        'icon-bullseye' => __( 'Bullseye', 'spyropress' ),
        'icon-ellipsis-h' => __( 'Ellipsis H', 'spyropress' ),
        'icon-ellipsis-v' => __( 'Ellipsis V', 'spyropress' ),
        'icon-rss-square' => __( 'Rss Square', 'spyropress' ),
        'icon-play-circle' => __( 'Play Circle', 'spyropress' ),
        'icon-ticket' => __( 'Ticket', 'spyropress' ),
        'icon-minus-square' => __( 'Minus Square', 'spyropress' ),
        'icon-minus-square-o' => __( 'Minus Square Outline', 'spyropress' ),
        'icon-level-up' => __( 'Level Up', 'spyropress' ),
        'icon-level-down' => __( 'Level Down', 'spyropress' ),
        'icon-check-square' => __( 'Check Square', 'spyropress' ),
        'icon-pencil-square' => __( 'Pencil Square', 'spyropress' ),
        'icon-external-link-square' => __( 'External Link Square', 'spyropress' ),
        'icon-share-square' => __( 'Share Square', 'spyropress' ),
        'icon-compass' => __( 'Compass', 'spyropress' ),
        'icon-toggle-down' => __( 'Toggle Down', 'spyropress' ),
        'icon-caret-square-o-down' => __( 'Caret Square Outline Down', 'spyropress' ),
        'icon-toggle-up' => __( 'Toggle Up', 'spyropress' ),
        'icon-caret-square-o-up' => __( 'Caret Square Outline Up', 'spyropress' ),
        'icon-toggle-right' => __( 'Toggle Right', 'spyropress' ),
        'icon-caret-square-o-right' => __( 'Caret Square Outline Right', 'spyropress' ),
        'icon-euro' => __( 'Euro', 'spyropress' ),
        'icon-eur' => __( 'Eur', 'spyropress' ),
        'icon-gbp' => __( 'Gbp', 'spyropress' ),
        'icon-dollar' => __( 'Dollar', 'spyropress' ),
        'icon-usd' => __( 'Usd', 'spyropress' ),
        'icon-rupee' => __( 'Rupee', 'spyropress' ),
        'icon-inr' => __( 'Inr', 'spyropress' ),
        'icon-cny' => __( 'Cny', 'spyropress' ),
        'icon-rmb' => __( 'Rmb', 'spyropress' ),
        'icon-yen' => __( 'Yen', 'spyropress' ),
        'icon-jpy' => __( 'Jpy', 'spyropress' ),
        'icon-ruble' => __( 'Ruble', 'spyropress' ),
        'icon-rouble' => __( 'Rouble', 'spyropress' ),
        'icon-rub' => __( 'Rub', 'spyropress' ),
        'icon-won' => __( 'Won', 'spyropress' ),
        'icon-krw' => __( 'Krw', 'spyropress' ),
        'icon-bitcoin' => __( 'Bitcoin', 'spyropress' ),
        'icon-btc' => __( 'Btc', 'spyropress' ),
        'icon-file' => __( 'File', 'spyropress' ),
        'icon-file-text' => __( 'File Text', 'spyropress' ),
        'icon-sort-alpha-asc' => __( 'Sort Alpha Asc', 'spyropress' ),
        'icon-sort-alpha-desc' => __( 'Sort Alpha Desc', 'spyropress' ),
        'icon-sort-amount-asc' => __( 'Sort Amount Asc', 'spyropress' ),
        'icon-sort-amount-desc' => __( 'Sort Amount Desc', 'spyropress' ),
        'icon-sort-numeric-asc' => __( 'Sort Numeric Asc', 'spyropress' ),
        'icon-sort-numeric-desc' => __( 'Sort Numeric Desc', 'spyropress' ),
        'icon-thumbs-up' => __( 'Thumbs Up', 'spyropress' ),
        'icon-thumbs-down' => __( 'Thumbs Down', 'spyropress' ),
        'icon-youtube-square' => __( 'Youtube Square', 'spyropress' ),
        'icon-youtube' => __( 'Youtube', 'spyropress' ),
        'icon-xing' => __( 'Xing', 'spyropress' ),
        'icon-xing-square' => __( 'Xing Square', 'spyropress' ),
        'icon-youtube-play' => __( 'Youtube Play', 'spyropress' ),
        'icon-dropbox' => __( 'Dropbox', 'spyropress' ),
        'icon-stack-overflow' => __( 'Stack Overflow', 'spyropress' ),
        'icon-instagram' => __( 'Instagram', 'spyropress' ),
        'icon-flickr' => __( 'Flickr', 'spyropress' ),
        'icon-adn' => __( 'Adn', 'spyropress' ),
        'icon-bitbucket' => __( 'Bitbucket', 'spyropress' ),
        'icon-bitbucket-square' => __( 'Bitbucket Square', 'spyropress' ),
        'icon-tumblr' => __( 'Tumblr', 'spyropress' ),
        'icon-tumblr-square' => __( 'Tumblr Square', 'spyropress' ),
        'icon-long-arrow-down' => __( 'Long Arrow Down', 'spyropress' ),
        'icon-long-arrow-up' => __( 'Long Arrow Up', 'spyropress' ),
        'icon-long-arrow-left' => __( 'Long Arrow Left', 'spyropress' ),
        'icon-long-arrow-right' => __( 'Long Arrow Right', 'spyropress' ),
        'icon-apple' => __( 'Apple', 'spyropress' ),
        'icon-windows' => __( 'Windows', 'spyropress' ),
        'icon-android' => __( 'Android', 'spyropress' ),
        'icon-linux' => __( 'Linux', 'spyropress' ),
        'icon-dribbble' => __( 'Dribbble', 'spyropress' ),
        'icon-skype' => __( 'Skype', 'spyropress' ),
        'icon-foursquare' => __( 'Foursquare', 'spyropress' ),
        'icon-trello' => __( 'Trello', 'spyropress' ),
        'icon-female' => __( 'Female', 'spyropress' ),
        'icon-male' => __( 'Male', 'spyropress' ),
        'icon-gittip' => __( 'Gittip', 'spyropress' ),
        'icon-sun-o' => __( 'Sun Outline', 'spyropress' ),
        'icon-moon-o' => __( 'Moon Outline', 'spyropress' ),
        'icon-archive' => __( 'Archive', 'spyropress' ),
        'icon-bug' => __( 'Bug', 'spyropress' ),
        'icon-vk' => __( 'Vk', 'spyropress' ),
        'icon-weibo' => __( 'Weibo', 'spyropress' ),
        'icon-renren' => __( 'Renren', 'spyropress' ),
        'icon-pagelines' => __( 'Pagelines', 'spyropress' ),
        'icon-stack-exchange' => __( 'Stack Exchange', 'spyropress' ),
        'icon-arrow-circle-o-right' => __( 'Arrow Circle Outline Right', 'spyropress' ),
        'icon-arrow-circle-o-left' => __( 'Arrow Circle Outline Left', 'spyropress' ),
        'icon-toggle-left' => __( 'Toggle Left', 'spyropress' ),
        'icon-caret-square-o-left' => __( 'Caret Square Outline Left', 'spyropress' ),
        'icon-dot-circle-o' => __( 'Dot Circle Outline', 'spyropress' ),
        'icon-wheelchair' => __( 'Wheelchair', 'spyropress' ),
        'icon-vimeo-square' => __( 'Vimeo Square', 'spyropress' ),
        'icon-turkish-lira' => __( 'Turkish Lira', 'spyropress' ),
        'icon-try' => __( 'Try', 'spyropress' ),
        'icon-plus-square-o' => __( 'Plus Square Outline', 'spyropress' )
     );
}

function spyropress_get_options_bs_skins() {
    return array(
        'primary' => __( 'Primary', 'spyropress' ),
        'success' => __( 'Success', 'spyropress' ),
        'info' => __( 'Info', 'spyropress' ),
        'warning' => __( 'Warning', 'spyropress' ),
        'danger' => __( 'Danger', 'spyropress' ),
    );
}
?>