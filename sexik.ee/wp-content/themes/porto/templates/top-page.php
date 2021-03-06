<?php

// if one page tempalte return;
if( is_page_template( 'one-page.php') ) return;

$page_options = get_post_meta( get_the_ID(), '_page_options', true );

// if no option return;
if( empty( $page_options ) ) return;

// if no header return;
if( 'none' == $page_options['top_header'] ) return;

// default header
if( 'default' == $page_options['top_header'] ) {
?>
<section class="page-top">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="breadcrumb">
                <?php                    
                    if(function_exists('bcn_display')) {
                        bcn_display_list();
                    }  
                ?>
                </ul>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<h2><?php the_title(); ?></h2>
			</div>
		</div>
	</div>
</section>
<?php
}
elseif( 'custom' == $page_options['top_header'] && isset( $page_options['bucket'] ) ) {
    
    $style = '';
    if( isset( $page_options['background'] ) && !empty( $page_options['background'] ) ) {
        $value = $page_options['background'];
        $img = '';
        $bg = array();

        if ( isset( $value['background-color'] ) )
            $bg[] = $value['background-color'];

        if ( isset( $value['background-pattern'] ) )
            $img = $value['background-pattern'];
        elseif ( isset( $value['background-image'] ) )
            $img = $value['background-image'];
        if ( $img )
            $bg[] = 'url(\'' . $img . '\')';

        if ( isset( $value['background-repeat'] ) )
            $bg[] = $value['background-repeat'];

        if ( isset( $value['background-attachment'] ) )
            $bg[] = $value['background-attachment'];

        if ( isset( $value['background-position'] ) )
            $bg[] = $value['background-position'];

        $style .= ( !empty( $bg ) ) ? ' background: ' . join( ' ', $bg ) . ';' : '';
    }
    
    if( isset( $page_options['border_top'] ) && !empty( $page_options['border_top'] ) )
        $style .= 'border-top-color:#' . str_replace( '#', '', $page_options['border_top'] ) . ';';
    
    if( isset( $page_options['border_bottom'] ) && !empty( $page_options['border_bottom'] ) )
        $style .= 'border-bottom-color:#' . str_replace( '#', '', $page_options['border_bottom'] ) . ';';
    
    if( !empty( $style ) )
        $style = 'style="' . $style . '"';
?>
<section class="page-top custom-product"<?php echo $style; ?>>
    <?php spyropress_the_bucket( $page_options['bucket'] ); ?>
</section>
<?php
}
?>