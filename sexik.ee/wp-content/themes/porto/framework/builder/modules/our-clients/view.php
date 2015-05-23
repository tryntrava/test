<?php

if( empty( $clients ) ) return;

echo '<div class="row center"><div class="owl-carousel" data-plugin-options=\'{"items": 6, "singleItem": false, "autoPlay": true}\'>';
    
    foreach( $clients as $client ) {
        echo '<div><img class="img-responsive" alt="" src="' . $client['logo'] . '"></div>';
    }

echo '</div></div>';