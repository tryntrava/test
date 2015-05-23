<?php

// chcek
if ( empty( $accordions ) ) return;

global $accordion_ids;
$count = 0;
$content = '';
++$accordion_ids;

foreach( $accordions as $tab ) {
    ++$count;
    $active = ( $count == 1 ) ? ' in' : '';

    // content
    if( isset( $tab['bucket'] ) && !empty( $tab['bucket'] ) ) {
        $args = array(
            'post_type' => 'bucket',
            'p' => $tab['bucket']
        );
        $query = new WP_Query( $args );
        while( $query->have_posts() ) {
            $query->the_post();
            $xcontent = spyropress_get_the_content();
        }
        wp_reset_query();
    }
    else {
        $xcontent = do_shortcode( $tab['content'] );
    }

    $icon = isset( $tab['icon'] ) ? '<i class="icon ' . $tab['icon'] . '"></i> ' : '';
    $content .= '
    <div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion' . $accordion_ids . '" href="#collapse' . $count . '">
					' . $icon . $tab['title'] . '
				</a>
			</h4>
		</div>
		<div id="collapse' . $count . '" class="accordion-body collapse' . $active . '">
			<div class="panel-body">
				' . $xcontent . '
			</div>
		</div>
	</div>';
}

if( $title )
    echo '<h2' . spyropress_build_atts( array( 'animation' => $animation ), 'data-appear-' ) . '>' . $title . '</h2>';

?>
<div class="panel-group" id="accordion<?php echo $accordion_ids; ?>">
    <?php echo $content; ?>
</div> <!-- end tabbable -->