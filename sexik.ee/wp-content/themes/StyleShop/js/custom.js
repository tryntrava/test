(function($){

	$.fn.et_simple_slider = function( options ) {
		var settings = $.extend( {
			slide         			: '.et-slide',				 	// slide class
			arrows					: '.et-slider-arrows',			// arrows container class
			prev_arrow				: '.et-arrow-prev',				// left arrow class
			next_arrow				: '.et-arrow-next',				// right arrow class
			controls 				: '.et-controllers a',			// control selector
			control_active_class	: 'et-active-control',			// active control class name
			previous_text			: 'Previous',					// previous arrow text
			next_text				: 'Next',						// next arrow text
			fade_speed				: 500,							// fade effect speed
			use_arrows				: true,							// use arrows?
			use_controls			: true,							// use controls?
			manual_arrows			: '',							// html code for custom arrows
			append_controls_to		: '',							// controls are appended to the slider element by default, here you can specify the element it should append to
			controls_class			: 'et-controllers',				// controls container class name
			slideshow				: false,						// automattic animation?
			slideshow_speed			: 7000							// automattic animation speed
		}, options );

		return this.each( function() {
			var $et_slider 			= $(this),
				$et_slide			= $et_slider.find( settings.slide ),
				et_slides_number	= $et_slide.length,
				et_fade_speed		= settings.fade_speed,
				et_active_slide		= 0,
				$et_slider_arrows,
				$et_slider_prev,
				$et_slider_next,
				$et_slider_controls,
				et_slider_timer,
				controls_html = '',
				et_animation_running = false;

			if ( settings.use_arrows && et_slides_number > 1 ) {
				if ( settings.manual_arrows == '' )
					$et_slider.append( '<div class="et-slider-arrows"><a class="et-arrow-prev" href="#">' + settings.previous_text + '</a><a class="et-arrow-next" href="#">' + settings.next_text + '</a></div>' );
				else
					$et_slider.append( settings.manual_arrows );

				$et_slider_arrows 	= $( settings.arrows );
				$et_slider_prev 	= $et_slider.find( settings.prev_arrow );
				$et_slider_next 	= $et_slider.find( settings.next_arrow );

				$et_slider_next.click( function(){
					if ( et_animation_running )	return false;

					et_slider_move_to( 'next' );

					return false;
				} );

				$et_slider_prev.click( function(){
					if ( et_animation_running )	return false;

					et_slider_move_to( 'previous' );

					return false;
				} );
			}

			if ( settings.use_controls && et_slides_number > 1 ) {
				for ( var i = 1; i <= et_slides_number; i++ ) {
					controls_html += '<a href="#"' + ( i == 1 ? ' class="' + settings.control_active_class + '"' : '' ) + '>' + i + '</a>';
				}

				controls_html =
					'<div class="' + settings.controls_class + '">' +
						controls_html +
					'</div>';

				if ( settings.append_controls_to == '' )
					$et_slider.append( controls_html );
				else
					$( settings.append_controls_to ).append( controls_html );

				$et_slider_controls	= $et_slider.find( settings.controls ),

				$et_slider_controls.click( function(){
					if ( et_animation_running )	return false;

					et_slider_move_to( $(this).index() );

					return false;
				} );
			}

			et_slider_auto_rotate();

			function et_slider_auto_rotate(){
				if ( settings.slideshow && et_slides_number > 1 ) {
					et_slider_timer = setTimeout( function() {
						et_slider_move_to( 'next' );
					}, settings.slideshow_speed );
				}
			}

			function et_slider_move_to( direction ) {
				var $active_slide = $et_slide.eq( et_active_slide ),
					$next_slide;

				et_animation_running = true;

				if ( direction == 'next' || direction == 'previous' ){

					if ( direction == 'next' )
						et_active_slide = ( et_active_slide + 1 ) < et_slides_number ? et_active_slide + 1 : 0;
					else
						et_active_slide = ( et_active_slide - 1 ) >= 0 ? et_active_slide - 1 : et_slides_number - 1;

				} else {

					if ( et_active_slide == direction ) return;

					et_active_slide = direction;

				}

				$next_slide	= $et_slide.eq( et_active_slide );

				$et_slide.each( function(){
					$(this).css( 'zIndex', 1 );
				} );
				$active_slide.css( 'zIndex', 2 );
				$next_slide.css( { 'display' : 'block', opacity : 0 } )

				$et_slider_controls.removeClass( settings.control_active_class ).eq( et_active_slide ).addClass( settings.control_active_class );

				$next_slide.delay(400).animate( { opacity : 1 }, et_fade_speed );
				$active_slide.addClass( 'et_slide_transition' ).delay(400).animate( { opacity : 0 }, et_fade_speed, function(){
					$(this).css('display', 'none').removeClass( 'et_slide_transition' );
					et_animation_running = false;
				} );

				if ( typeof et_slider_timer != 'undefined' ) {
					clearInterval( et_slider_timer );
					et_slider_auto_rotate();
				}
			}
		} );
	}

	$.fn.et_carousel_slider = function( options ) {
		var settings = $.extend( {
			slide 					: 'li',				 			// slide class
			arrows					: '.et-slider-arrows',			// arrows container class
			prev_arrow				: '.et-arrow-prev',				// left arrow class
			next_arrow				: '.et-arrow-next',				// right arrow class
			scroll_speed			: 500,							// fade effect speed
			use_arrows				: true,							// use arrows?
			manual_arrows			: ''							// html code for custom arrows
		}, options );

		return this.each( function() {
			var $et_slider 				= $(this),
				$et_slider_wrapper		= $et_slider.find( 'ul' ),
				$et_slide				= $et_slider.find( settings.slide ),
				et_slides_number		= $et_slide.length,
				et_scroll_speed			= settings.scroll_speed,
				et_active_slide			= 1,
				et_slider_total_width	= $et_slide.width() * et_slides_number,
				modifier				= 3,
				container_width			= $('#container').width(),
				et_is_animated			= false;

			$et_slider_wrapper.width( et_slider_total_width );

			if ( settings.use_arrows && et_slides_number > 1 ) {
				if ( settings.manual_arrows == '' )
					$et_slider.append( '<div class="et-slider-arrows"><a class="et-arrow-prev" href="#">' + settings.previous_text + '</a><a class="et-arrow-next" href="#">' + settings.next_text + '</a></div>' );
				else
					$et_slider.append( settings.manual_arrows );

				// show slider arrows on mobile devices only, if we have less than 4 slides
				if ( et_slides_number < 4 ) $et_slider.addClass( 'et_only_mobile_arrows' );

				$et_slider_arrows 	= $( settings.arrows );
				$et_slider_prev 	= $et_slider.find( settings.prev_arrow );
				$et_slider_next 	= $et_slider.find( settings.next_arrow );

				$et_slider_next.click( function(){
					if ( et_is_animated ) return false;

					et_slider_move_to( 'next' );

					return false;
				} );

				$et_slider_prev.click( function(){
					if ( et_is_animated ) return false;

					et_slider_move_to( 'previous' );

					return false;
				} );
			}

			function et_slider_move_to( direction ) {
				var $cloned_element,
					$left_modifier;

				et_is_animated = true;

				if ( direction == 'next' ){
					// clone the first item
					$cloned_element = $et_slide.eq(0).clone();

					// extend the container width temporarily and add the first cloned slide as new last element
					$et_slider_wrapper.css( 'width', et_slider_total_width + $et_slide.width() ).append( $cloned_element );

					// slide one item at a time
					$et_slider_wrapper.animate( { 'left' : '-=' + $et_slide.width() }, 500, function(){
						// remove the original first item that was cloned previously
						$et_slide.eq(0).remove();

						// now that the first slide was removed - change the slider offset to 0px and restore the slider width
						$et_slider_wrapper.css( { 'left' : '0px', 'width' : et_slider_total_width } );

						// update cached variable to apply new slides order
						$et_slide = $et_slider.find( settings.slide );

						// animation is finished
						et_is_animated = false;
					} );
				}

				if ( direction == 'previous' ){
					$cloned_element = $et_slide.filter(':last').clone();
					$et_slider_wrapper.css( { 'width' : et_slider_total_width + $et_slide.width(), 'left' : '-' + $et_slide.width() + 'px' } ).prepend( $cloned_element );

					$et_slider_wrapper.animate( { 'left' : 0 }, 500, function(){
						$et_slide.filter(':last').remove();
						$et_slider_wrapper.css( { 'left' : '0px', 'width' : et_slider_total_width } );

						$et_slide = $et_slider.find( settings.slide );

						et_is_animated = false;
					} );
				}
			}

			$(window).resize( function(){
				et_slider_total_width 	= $et_slide.width() * et_slides_number;

				$et_slider_wrapper.width( et_slider_total_width );
			} );
		} );
	}


	var $featured_slider 	= $( '#featured' ),
		$main_container		= $('#container'),
		$main_body			= $('body'),
		$mobile_sidebar		= $( '#mobile-sidebar' ),
		$main_wrapper		= $( '#main-page-wrapper' );

	$(document).ready( function(){
		var $et_top_menu 		= $( 'ul.nav' ),
			$comment_form		= $( '#commentform' ),
			mobile_sidebar_width;

		$('#top-categories').prepend( '<li class="overlay"></li>' );

		$('#offers').et_carousel_slider( {
			manual_arrows : '<div class="et-slider-arrows"><a class="et-arrow-prev" href="#">' + '<span>' + 'Previous' + '</span>' + '</a><a class="et-arrow-next" href="#">' + '<span>' + 'Next' + '</span>' + '</a></div>'
		} );

		$( '#top-categories' ).find('>li>a').append( '<span class="menu-highlight"></span>' );

		$et_top_menu.superfish({
			delay		: 500, 										// one second delay on mouseout
			animation	: { opacity : 'show', height : 'show' },	// fade-in and slide-down animation
			speed		: 'fast', 									// faster animation speed
			autoArrows	: true, 									// disable generation of arrow mark-up
			dropShadows	: false										// disable drop shadows
		});

		if ( $('ul.et_disable_top_tier').length ) $("ul.et_disable_top_tier > li > ul").prev('a').attr('href','#');

		$mobile_sidebar.find('#toggle-sidebar').click( function(){
			if ( ! $main_body.hasClass( 'et-sidebar-open' ) ){
				mobile_sidebar_width = $main_container.width() > 320 ? 430 : 273;

				$main_body.addClass( 'et-sidebar-open' );
				//$main_container.css( 'marginRight', '0' );
				//$main_wrapper.css( 'marginLeft', '-' + ( $main_container.width() - ( $main_body.width() - mobile_sidebar_width ) + 50 ) + 'px' );
				$main_wrapper.css( 'left', '-' + ( mobile_sidebar_width + 45 ) + 'px' );
				$main_container.css( 'marginRight', 0 );
			} else {
				$main_body.removeClass( 'et-sidebar-open' );
				//$main_container.css( { 'marginLeft' : 'auto', 'marginRight' : 'auto' } );
				$main_wrapper.css( 'left', '0px' );
				$main_container.css( 'marginRight', 'auto' );
			}
		} );

		(function et_search_bar(){
			var $searchinput = $(".et-search-form .search_input, .search_input_text"),
				searchvalue = $searchinput.val();

			$searchinput.focus(function(){
				if (jQuery(this).val() === searchvalue) jQuery(this).val("");
			}).blur(function(){
				if (jQuery(this).val() === "") jQuery(this).val(searchvalue);
			});
		})();

		et_duplicate_menu( $('#top-navigation > nav > ul.nav'), $('.mobile-pages'), 'mobile_pages_menu', 'et_mobile_menu' );
		et_duplicate_menu( $('#top-categories'), $('.mobile-categories'), 'mobile_categories_menu', 'et_mobile_menu' );

		function et_duplicate_menu( menu, append_to, menu_id, menu_class ){
			var $cloned_nav;

			menu.clone().attr('id',menu_id).removeClass().attr('class',menu_class).appendTo( append_to );
			$cloned_nav = append_to.find('> ul');
			$cloned_nav.find('.menu_slide').remove();
			$cloned_nav.find('li:first').addClass('et_first_mobile_item');

			append_to.find('>ul').addClass( 'closed' );

			append_to.find('>a').click( function(){
				if ( $(this).siblings('ul').hasClass('closed') ){
					$(this).siblings('ul').removeClass( 'closed' ).addClass( 'opened' );
					$cloned_nav.slideDown( 500 );
				} else {
					$(this).siblings('ul').removeClass( 'opened' ).addClass( 'closed' );
					$cloned_nav.slideUp( 500 );
				}
				return false;
			} );

			append_to.find('a').click( function(event){
				event.stopPropagation();
			} );
		}

		$comment_form.find('input:text, textarea').each(function(index,domEle){
			var $et_current_input = jQuery(domEle),
				$et_comment_label = $et_current_input.siblings('label'),
				et_comment_label_value = $et_current_input.siblings('label').text();
			if ( $et_comment_label.length ) {
				$et_comment_label.hide();
				if ( $et_current_input.siblings('span.required') ) {
					et_comment_label_value += $et_current_input.siblings('span.required').text();
					$et_current_input.siblings('span.required').hide();
				}
				$et_current_input.val(et_comment_label_value);
			}
		}).bind('focus',function(){
			var et_label_text = jQuery(this).siblings('label').text();
			if ( jQuery(this).siblings('span.required').length ) et_label_text += jQuery(this).siblings('span.required').text();
			if (jQuery(this).val() === et_label_text) jQuery(this).val("");
		}).bind('blur',function(){
			var et_label_text = jQuery(this).siblings('label').text();
			if ( jQuery(this).siblings('span.required').length ) et_label_text += jQuery(this).siblings('span.required').text();
			if (jQuery(this).val() === "") jQuery(this).val( et_label_text );
		});

		// remove placeholder text before form submission
		$comment_form.submit(function(){
			$comment_form.find('input:text, textarea').each(function(index,domEle){
				var $et_current_input = jQuery(domEle),
					$et_comment_label = $et_current_input.siblings('label'),
					et_comment_label_value = $et_current_input.siblings('label').text();

				if ( $et_comment_label.length && $et_comment_label.is(':hidden') ) {
					if ( $et_comment_label.text() == $et_current_input.val() )
						$et_current_input.val( '' );
				}
			});
		});
	});

	$(window).load( function(){
		if ( $featured_slider.length ){
			et_slider_settings = {
				manual_arrows	: '<div class="et-slider-arrows"><a class="et-arrow-prev" href="#">' + '<span>' + 'Previous' + '</span>' + '</a><a class="et-arrow-next" href="#">' + '<span>' + 'Next' + '</span>' + '</a></div>',
				fade_speed		: 700
			}

			if ( $featured_slider.hasClass('et_slider_auto') ) {
				var et_slider_autospeed_class_value = /et_slider_speed_(\d+)/g;

				et_slider_settings.slideshow = true;

				et_slider_autospeed = et_slider_autospeed_class_value.exec( $featured_slider.attr('class') );

				et_slider_settings.slideshow_speed = et_slider_autospeed[1];
			}

			$featured_slider.et_simple_slider( et_slider_settings );
		}

		$('#mobile-sidebar').height( $( 'body' ).height() );
	} );

	$(window).resize( function(){
		var mobile_sidebar_width;

		if ( $main_container.width() > 440 ){
			$main_wrapper.css( { 'left' : 0 } );
			$main_container.css( { 'marginLeft' : 'auto', 'marginRight' : 'auto' } );
		} else {
			mobile_sidebar_width = $main_container.width() > 320 ? 430 : 273;

			if ( $main_body.hasClass( 'et-sidebar-open' ) ){
				$main_wrapper.css( 'left', '-' + ( mobile_sidebar_width + 45 ) + 'px' );
				$main_container.css( 'marginRight', 0 );
			}
		}

		$('#mobile-sidebar').height( $( 'body' ).height() );
	} );
})(jQuery)