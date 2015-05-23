<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $woocommerce;

?>
<div class="images">
<?php
	$thumb = '';
	$width = (int) apply_filters( 'et_single_product_image_width', 280 );
	$height = (int) apply_filters( 'et_single_product_image_height', 231 );
	$classtext = '';
	$titletext = get_the_title();
	$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, true, 'SingleProductImage' );
	$thumb = $thumbnail["thumb"];

	if ( '' != $thumb ) {
		printf( '<a class="woocommerce-main-image zoom" itemprop="image" href="%1$s" title="%2$s" data-o_href="%1$s">',
			esc_url( $thumbnail['fullpath'] ),
			esc_attr( $titletext )
		);
		print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext );
		echo '</a>';
	}
?>

	<?php do_action('woocommerce_product_thumbnails'); ?>

</div>