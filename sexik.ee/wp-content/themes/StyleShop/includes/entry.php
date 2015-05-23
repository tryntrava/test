<article id="post-<?php the_ID(); ?>" <?php post_class('entry clearfix'); ?>>
	<h2 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
<?php
	$index_postinfo = et_get_option( 'styleshop_postinfo1' );

	$thumb = '';
	$width = (int) apply_filters( 'et_blog_image_width', 629 );
	$height = (int) apply_filters( 'et_blog_image_height', 240 );
	$classtext = '';
	$titletext = get_the_title();
	$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Indeximage' );
	$thumb = $thumbnail["thumb"];
?>

<?php if ( 'on' == et_get_option( 'styleshop_thumbnails_index', 'on' ) && '' != $thumb ) { ?>
	<a href="<?php the_permalink(); ?>">
		<?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext ); ?>
	</a>
<?php } ?>

	<div class="entry-content">
<?php
	if ( $index_postinfo ) {
		echo '<p class="meta-info">';
		et_postinfo_meta( $index_postinfo, et_get_option( 'styleshop_date_format', 'M j, Y' ), esc_html__( '0 comments', 'StyleShop' ), esc_html__( '1 comment', 'StyleShop' ), '% ' . esc_html__( 'comments', 'StyleShop' ) );
		echo '</p>';
	}
?>
	<?php if ( 'false' == et_get_option( 'styleshop_blog_style', 'false' ) ) { ?>
		<p><?php truncate_post( 480 ); ?></p>
	<?php } else { ?>
		<?php the_content(''); ?>
	<?php } ?>
		<a href="<?php the_permalink(); ?>" class="read-more"><?php esc_html_e( 'Read More', 'StyleShop' ); ?></a>
	</div> <!-- .entry-content -->
</article> <!-- end .entry -->