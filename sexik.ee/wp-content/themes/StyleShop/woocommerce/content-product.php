<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibility
if ( ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;

$thumb = '';
$width = 140;
$height = 135;
$classtext = '';
$titletext = get_the_title();
$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'ProductImage' );
$thumb = $thumbnail["thumb"];

$et_price_before = 'variable' == $product->product_type ? $product->min_variation_regular_price : $product->regular_price;
?>
<li>

	<?php // do_action( 'woocommerce_before_shop_loop_item' ); ?>

	<a href="<?php the_permalink(); ?>">
		<?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext ); ?>
	</a>
	<?php woocommerce_show_product_sale_flash( $post, $product ); ?>
	<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

<?php
	$product_ids_on_sale = et_woocommerce_get_product_on_sale_ids();
?>

<?php if ( ! in_array( get_the_ID(), array_map( 'intval', $product_ids_on_sale ) ) ) { ?>
	<?php if ( '' != $product->get_price_html() ) : ?>
	<span class="et-main-price"><?php echo $product->get_price_html(); ?></span>
	<?php endif; ?>
<?php } else { ?>
	<span class="et-price-button">
		<span class="et-price-before"><del><?php echo woocommerce_price( $et_price_before ); ?></del></span>
		<span class="et-price-sale"><?php echo woocommerce_price( $product->get_price() ); ?></span>
	</span>
<?php } ?>

	<?php // do_action( 'woocommerce_after_shop_loop_item' ); ?>

</li>