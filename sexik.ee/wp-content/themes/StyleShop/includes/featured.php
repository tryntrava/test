<?php
	$featured_slider_class = '';
	if ( 'on' == et_get_option( 'styleshop_slider_auto', 'false' ) ) $featured_slider_class = ' et_slider_auto et_slider_speed_' . et_get_option( 'styleshop_slider_autospeed', '7000' );
?>
<div id="featured"<?php if ( '' != $featured_slider_class ) printf( ' class="%s"', esc_attr( $featured_slider_class ) ); ?>>
	<div id="et-slides">
	<?php
		$featured_cat = et_get_option( 'styleshop_feat_cat' );
		$featured_num = (int) et_get_option( 'styleshop_featured_num',3 );

		if ( 'false' == et_get_option( 'styleshop_use_pages','false' ) ) {
			if ( 'on' == et_get_option( 'styleshop_use_products_for_slider','on' ) && class_exists( 'woocommerce' ) ){
				$featured_query = new WP_Query( apply_filters( 'et_featured_product_args', array(
					'posts_per_page' 	=> intval( $featured_num ),
					'post_type'			=> 'product',
					'meta_query'		=> array(
							array( 'key' => '_visibility', 'value' => array( 'catalog', 'visible' ),'compare' => 'IN' )
						),
					'tax_query'			=> array(
						array(
							'taxonomy' 	=> 'product_cat',
							'field' 	=> 'id',
							'operator'	=> 'IN',
							'terms'		=> array(  get_catId( et_get_option('styleshop_feat_products_cat') ) )
						)
					)
				) ) );
			} else {
				$featured_query = new WP_Query( apply_filters( 'et_featured_post_args', array(
					'posts_per_page' 	=> intval( $featured_num ),
					'cat' 				=> (int) get_catId( et_get_option('styleshop_feat_posts_cat') )
				) ) );
			}
		} else {
			global $pages_number;

			if ( '' != et_get_option( 'styleshop_feat_pages' ) ) $featured_num = count( et_get_option( 'styleshop_feat_pages' ) );
			else $featured_num = $pages_number;

			$featured_query = new WP_Query(
				apply_filters( 'et_featured_page_args',
					array(	'post_type'			=> 'page',
							'orderby'			=> 'menu_order',
							'order' 			=> 'ASC',
							'post__in' 			=> (array) array_map( 'intval', et_get_option( 'styleshop_feat_pages', '', 'page' ) ),
							'posts_per_page' 	=> (int) $featured_num
						)
				)
			);
		}

		while ( $featured_query->have_posts() ) : $featured_query->the_post();
			$width = (int) apply_filters( 'slider_image_width', 960 );
			$height = (int) apply_filters( 'slider_image_height', 600 );
			$title = get_the_title();
			$thumbnail = get_thumbnail( $width, $height, '', $title, $title, false, 'Featured' );
			$thumb = $thumbnail["thumb"];

			$post_id = get_the_ID();
			$more_link = ( $slide_more_link = get_post_meta( $post_id, '_et_slide_more_link', true ) ) && '' != $slide_more_link ? $slide_more_link : get_permalink();
			$slide_title = ( $slide_title_text = get_post_meta( $post_id, '_et_slide_title', true ) ) && '' != $slide_title_text ? $slide_title_text : get_the_title();
			$slide_description = ( $slide_description_text = get_post_meta( $post_id, '_et_slide_description', true ) ) && '' != $slide_description_text ? $slide_description_text : '';
			$button_content = ( $button_text = get_post_meta( $post_id, '_et_button_text', true ) ) && '' != $button_text ? $button_text : '';
	?>
		<div class="et-slide">
			<?php print_thumbnail( $thumb, $thumbnail["use_timthumb"], $title, $width, $height, '' ); ?>
			<div class="description">
				<header class="heading-title">
					<h1><a href="<?php echo esc_url( $more_link ); ?>"><?php echo esc_html( $slide_title ); ?></a></h1>
				<?php if ( '' != $slide_description ) { ?>
					<h2><?php echo esc_html( $slide_description ); ?></h2>
				<?php } ?>
				</header>
			</div>
		<?php if ( '' != $button_content ) { ?>
			<div class="et-slide-button">
				<a href="<?php echo esc_url( $more_link ); ?>" class="more-button"><?php echo esc_html( $button_content ); ?></a>
			</div> <!-- .et-slide-button -->
		<?php } ?>
		</div> <!-- .et-slide -->
	<?php
		endwhile; wp_reset_postdata();
	?>

	</div> <!-- #et-slides -->
</div> <!-- #featured -->