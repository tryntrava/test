<?php
	if ( ! is_active_sidebar( 'sidebar-2' ) && ! is_active_sidebar( 'sidebar-3' ) && ! is_active_sidebar( 'sidebar-4'  ) )
		return;
?>

<footer id="main-footer">
	<div id="footer-widgets" class="clearfix">
	<?php
		$footer_sidebars = array( 'sidebar-2', 'sidebar-3', 'sidebar-4' );
		foreach ( $footer_sidebars as $key => $footer_sidebar ){
			if ( is_active_sidebar( $footer_sidebar ) ) {
				$additional_footer_class = 2 == $key ? ' last' : '';
				if ( 0 == $key ) $additional_footer_class = ' first';

				echo '<div class="' . esc_attr( 'footer-column' . $additional_footer_class ) . '">';
				dynamic_sidebar( $footer_sidebar );
				echo '</div> <!-- end .footer-column -->';
			}
		}
	?>
	</div> <!-- #footer-widgets -->
</footer> <!-- #main-footer -->