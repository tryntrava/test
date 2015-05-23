<?php

// Setup Instance for view
$instance = spyropress_clean_array( $instance );

if ( $instance['title'] ) echo '<h2>' . $instance['title'] . '</h2>';

echo '<div class="row"><div class="owl-carousel push-bottom" data-plugin-options=\'{"items": 1, "autoHeight": true}\'>';

    // output content
    echo $this->query( $instance, '{content}' );

echo '</div></div>';