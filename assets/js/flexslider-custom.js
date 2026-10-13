/**
 * Front page slider.
 *
 * Starts FlexSlider once every slide image has loaded, so smoothHeight measures
 * real images. This used $( window ).load( fn ), an event alias jQuery 3.0
 * removed: on jQuery 3 it only worked through jQuery Migrate, and on jQuery 4
 * it threw and the slider never started.
 *
 * @package sparkling
 */
( function ( $ ) {
	'use strict';

	function init() {
		if ( 'function' !== typeof $.fn.flexslider ) {
			return;
		}

		$( '.flexslider' ).flexslider( {
			prevText: '',
			nextText: '',
			animation: 'fade',
			easing: 'swing',
			direction: 'horizontal',
			reverse: false,
			animationLoop: true,
			smoothHeight: true,
			startAt: 0,
			slideshow: true,
			slideshowSpeed: 7000,
			animationSpeed: 600,
			initDelay: 0,
			randomize: false,
			fadeFirstSlide: true,
			pauseOnAction: true,
			pauseOnHover: false,
			pauseInvisible: true,
			useCSS: true,
			touch: true,
			// The arrows are icon-only links; give them names.
			start: function ( slider ) {
				var labels = window.sparklingSlider || {};

				slider.find( '.flex-prev' ).attr( 'aria-label', labels.prev || 'Previous slide' );
				slider.find( '.flex-next' ).attr( 'aria-label', labels.next || 'Next slide' );
			},
		} );
	}

	// The load event may already have fired when this footer script runs.
	if ( 'complete' === document.readyState ) {
		init();
	} else {
		$( window ).on( 'load', init );
	}
}( jQuery ) );
