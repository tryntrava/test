<?php

// chcek
if ( empty( $tabs ) ) return;

$count = 0;
$tab_nav = $tabs_content = '';
foreach( $tabs as $tab ) {
    ++$count;
    $li_class = ( $count == 1 ) ? ' class="active"' : '';
    $active = ( $count == 1 ) ? ' active' : '';
    
    $icon = isset( $tab['icon'] ) ? '<i class="icon ' . $tab['icon'] . '"></i> ' : '';
    $tab_nav .= '<li' . $li_class . '><a href="#tab' . $count . '" data-toggle="tab">' . $icon . $tab['title'] . '</a></li>';
    $tabs_content .= '<div class="tab-pane' . $active . '" id="tab' . $count . '">';
    
    // content
    if( isset( $tab['bucket'] ) ) {
        $args = array(
            'post_type' => 'bucket',
            'p' => $tab['bucket']
        );
        $query = new WP_Query( $args );
        while( $query->have_posts() ) {
            $query->the_post();
            $tabs_content .= spyropress_get_the_content();
        }
    }
    else {
        $tabs_content .= do_shortcode( $tab['content'] );
    }
    $tabs_content .= '</div> <!-- end tab-pane -->';
}
wp_reset_query();
?>
<div class="tabs">
    <ul class="nav nav-tabs">
        <?php echo $tab_nav; ?>
    </ul>
    <div class="tab-content">
        <?php echo $tabs_content; ?>
    </div>
</div> <!-- end tabbable -->