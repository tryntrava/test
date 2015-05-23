/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	wp.customize( 'et_styleshop[link_color]', function( value ) {
		value.bind( function( to ) {
			$( 'a, #container a' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_styleshop[font_color]', function( value ) {
		value.bind( function( to ) {
			$( 'body, #main-footer' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_styleshop[headings_color]', function( value ) {
		value.bind( function( to ) {
			$( 'h1, h2, h3, h4, h5, h6, #special-offers h1, .home-area h1, .widget h4.widgettitle, .entry h2.title a, h1.title, #comments, #reply-title' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_styleshop[top_menu_bar]', function( value ) {
		value.bind( function( to ) {
			$( '#top-categories, .nav ul' ).css( 'background', to );
		} );
	} );

	wp.customize( 'et_styleshop[menu_link_color]', function( value ) {
		value.bind( function( to ) {
			$( '#top-categories a' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_styleshop[main_footer]', function( value ) {
		value.bind( function( to ) {
			$( '#main-footer' ).css( 'background', to );
		} );
	} );

	wp.customize( 'et_styleshop[highlight_color]', function( value ) {
		value.bind( function( to ) {
			$( '#top-categories a .menu-highlight, #mobile_menu .menu-highlight' ).css( 'background', to );
		} );
	} );

	wp.customize( 'et_styleshop[color_schemes]', function( value ) {
		value.bind( function( to ) {
			var $body = $( 'body' ),
				body_classes = $body.attr( 'class' ),
				et_customizer_color_scheme_prefix = 'et_color_scheme_',
				body_class;

			body_class = body_classes.replace( /et_color_scheme_[^\s]+/, '' );
			$body.attr( 'class', $.trim( body_class ) );

			if ( 'none' !== to  )
				$body.addClass( et_customizer_color_scheme_prefix + to );
		} );
	} );
} )( jQuery );