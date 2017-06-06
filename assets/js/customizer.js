/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-name a' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );
	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.site-name, .site-description' ).css( {
					'clip': 'rect(1px, 1px, 1px, 1px)',
					'position': 'absolute'
				} );
			} else {
				$( '.site-name, .site-description' ).css( {
					'clip': 'auto',
					'position': 'relative'
				} );

				$( '.site-name, .site-name a, .site-description, .site-description a' ).css( {
					color: to
				} );
			}
		} );
	} );


	$(document).ready(function () {
		if ( 'undefined' === typeof wp || !wp.customize || !wp.customize.selectiveRefresh ) {
			return;
		}

		wp.customize.selectiveRefresh.bind('widget-updated', function (placement) {
			$('.logo-carousel').flexslider({
				minItems      : 1,
				maxItems      : 4,
				move          : 1,
				itemWidth     : 200,
				itemMargin    : 0,
				animation     : "slide",
				slideshow     : true,
				slideshowSpeed: 3000,
				directionNav  : false,
				controlNav    : false
			});

			$('.parallax-window').parallax();

			if ( jQuery('.testimonial-section').length != 0 ) {
				testimonialHeight();
				setTimeout(function () {
					testimonialHeight();
				}, 3000);
			}

			$('.slider-arrow-controls').flexslider({
				controlNav: false
			});
			/*
			 * Resetting testimonial parallax height
			 */
			function testimonialHeight() {
				jQuery('.testimonial-section .parallax-window').css('height', jQuery('.testimonial-section .parallax-window .container').outerHeight() + 150);
				jQuery(window).trigger('resize').trigger('scroll');
			}
		});
	});

} )( jQuery );
