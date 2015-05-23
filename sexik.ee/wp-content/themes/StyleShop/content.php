<?php
/**
 * The template for displaying posts on single pages
 *
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix entry' ); ?>>
<?php
	$thumb = '';
	$width = (int) apply_filters( 'et_blog_image_width', 629 );
	$height = (int) apply_filters( 'et_blog_image_height', 240 );
	$classtext = '';
	$titletext = get_the_title();
	$thumbnail = get_thumbnail( $width, $height, '', $titletext, $titletext, false, 'Indeximage' );
	$thumb = $thumbnail["thumb"];

	$postinfo = et_get_option( 'styleshop_postinfo2' );
	$show_thumb = is_page() ? et_get_option( 'styleshop_page_thumbnails', 'false' ) : et_get_option( 'styleshop_thumbnails', 'on' );
?>
	<h1 class="title"><?php the_title(); ?></h1>

<?php if ( '' != $thumb && 'false' != $show_thumb ) { ?>
	<a href="<?php the_permalink(); ?>">
		<?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext ); ?>
	</a>
<?php } ?>

	<div class="entry-content">
<?php
	if ( $postinfo && ! is_page() ) {
		echo '<p class="meta-info">';
		et_postinfo_meta( $postinfo, et_get_option( 'styleshop_date_format', 'M j, Y' ), esc_html__( '0 comments', 'StyleShop' ), esc_html__( '1 comment', 'StyleShop' ), '% ' . esc_html__( 'comments', 'StyleShop' ) );
		echo '</p>';
	}
?>
	<?php
		the_content();
		wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'StyleShop' ), 'after' => '</div>' ) );
	?>
	</div> <!-- .entry-content -->
</article> <!-- end .post-->