<?php

if( empty( $list ) ) return;

$same_icon = ( $same_icon ) ? '<i class="icon ' . $same_icon . '"></i> ' : '';

echo '<ul class="list icons ' . $style . '">';
    
    foreach( $list as $item ) {
        $icon = ( isset( $item['icon'] ) && !empty( $item['icon'] ) ) ? '<i class="icon ' . $item['icon'] . '"></i> ' : $same_icon;
        echo '<li>' . $icon . $item['content'] . '</li>';
    }
    
echo '</ul>';