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

		$( '.flexslider' ).each( function () {
			var element = $( this );
			var motion = window.matchMedia( '(prefers-reduced-motion: reduce)' );
			var labels = window.sparklingSlider || {};
			var tabIndexes = new WeakMap();
			var playing = ! motion.matches;
			var hovering = false;
			var playButton;

			if ( element.find( '.slides > li' ).length < 2 ) {
				element.flexslider( { slideshow: false, animationSpeed: 0 } );
				return;
			}

			function showSlide( slider, active ) {
				slider.slides.each( function ( index ) {
					var hidden = index !== active;
					this.setAttribute( 'aria-hidden', hidden ? 'true' : 'false' );
					this.toggleAttribute( 'inert', hidden );
					// Keep the original tab order, including custom tabindex values.
					$( this ).find( 'a[href], button, input, select, textarea, [tabindex], [contenteditable], summary, audio[controls], video[controls]' ).each( function () {
						if ( ! tabIndexes.has( this ) ) {
							tabIndexes.set( this, this.getAttribute( 'tabindex' ) );
						}
						var original = tabIndexes.get( this );
						if ( hidden ) {
							this.setAttribute( 'tabindex', '-1' );
						} else if ( original === null ) {
							this.removeAttribute( 'tabindex' );
						} else {
							this.setAttribute( 'tabindex', original );
						}
					} );
				} );
				slider.find( '.flex-control-nav a' ).each( function ( index ) {
					if ( index === active ) {
						this.setAttribute( 'aria-current', 'true' );
					} else {
						this.removeAttribute( 'aria-current' );
					}
				} );
			}

			function playback( slider ) {
				if ( playing && ! hovering && ! document.hidden ) {
					slider.play();
				} else {
					slider.pause();
				}
				playButton.text( playing ? labels.pause || 'Pause slideshow' : labels.play || 'Play slideshow' );
			}

			element.flexslider( {
				prevText: '',
				nextText: '',
				animation: 'fade',
				easing: 'swing',
				direction: 'horizontal',
				reverse: false,
				animationLoop: true,
				smoothHeight: true,
				startAt: 0,
				slideshow: playing,
				slideshowSpeed: 7000,
				animationSpeed: motion.matches ? 0 : 600,
				initDelay: 0,
				randomize: false,
				fadeFirstSlide: ! motion.matches,
				pauseOnAction: true,
				pauseOnHover: false,
				// Our visibility handler preserves an explicit pause; FlexSlider's
				// handler restarts the slideshow whenever the tab becomes visible.
				pauseInvisible: false,
				useCSS: true,
				touch: true,
				start: function ( slider ) {
					slider.find( '.flex-prev' ).attr( 'aria-label', labels.prev || 'Previous slide' );
					slider.find( '.flex-next' ).attr( 'aria-label', labels.next || 'Next slide' );
					slider.find( '.flex-control-nav a' ).each( function ( index ) {
						$( this ).attr( 'aria-label', ( labels.slide || 'Go to slide %d' ).replace( '%d', index + 1 ) );
					} );
					slider.find( '.flex-direction-nav a, .flex-control-nav a' ).attr( 'role', 'button' );
					slider.on( 'keydown', '.flex-direction-nav a, .flex-control-nav a', function ( event ) {
						if ( event.key === ' ' ) {
							event.preventDefault();
							$( this ).trigger( 'click' );
						}
					} );

					playButton = $( '<button type="button" class="sparkling-slider-toggle"></button>' );
					// Keep the controls outside FlexSlider's measured slide height.
					slider.wrap( '<div class="sparkling-slider-group"></div>' );
					var group = slider.parent();
					$( '<div class="sparkling-slider-controls"></div>' ).append( playButton ).appendTo( group );
					playButton.on( 'click', function () {
						playing = ! playing;
						playback( slider );
					} );

					// Focus stops automatic rotation until the visitor explicitly plays it.
					group.on( 'focusin', function ( event ) {
						// Pointer activation must toggle the state shown before clicking.
						if ( event.target === playButton[0] && ! event.target.matches( ':focus-visible' ) ) {
							return;
						}
						playing = false;
						playback( slider );
					} );
					group.on( 'mouseenter', function () {
						hovering = true;
						playback( slider );
					} );
					group.on( 'mouseleave', function () {
						hovering = false;
						playback( slider );
					} );
					slider.on( 'click', '.flex-direction-nav a, .flex-control-nav a', function () {
						playing = false;
						playback( slider );
					} );
					motion.addEventListener( 'change', function () {
						slider.vars.animationSpeed = motion.matches ? 0 : 600;
						playing = false;
						playback( slider );
					} );
					document.addEventListener( 'visibilitychange', function () {
						playback( slider );
					} );
					showSlide( slider, slider.currentSlide );
					playback( slider );
				},
				before: function ( slider ) {
					showSlide( slider, slider.animatingTo );
				},
				after: function ( slider ) {
					showSlide( slider, slider.currentSlide );
				},
			} );
		} );
	}

	// The load event may already have fired when this footer script runs.
	if ( 'complete' === document.readyState ) {
		init();
	} else {
		$( window ).on( 'load', init );
	}
}( jQuery ) );
