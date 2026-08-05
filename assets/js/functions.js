/**
 * Sparkling theme scripts.
 *
 * Plain DOM APIs -- no jQuery. The two globals (SparklingIsMobile and
 * generateMobileMenu) are kept on window because child themes may call them.
 */
( function () {
	'use strict';

	/**
	 * Run a callback once the DOM is ready.
	 *
	 * @param {Function} fn Callback.
	 */
	function ready( fn ) {
		if ( document.readyState !== 'loading' ) {
			fn();
		} else {
			document.addEventListener( 'DOMContentLoaded', fn );
		}
	}

	/**
	 * Add class names to every element matching a selector.
	 *
	 * @param {string} selector CSS selector.
	 * @param {Array}  classes  Class names to add.
	 */
	function addClasses( selector, classes ) {
		document.querySelectorAll( selector ).forEach( function ( el ) {
			el.classList.add.apply( el.classList, classes );
		} );
	}

	/**
	 * jQuery's "swing" easing, so the scroll-to-top motion is unchanged.
	 *
	 * @param {number} p Progress from 0 to 1.
	 * @return {number} Eased progress.
	 */
	function swing( p ) {
		return 0.5 - Math.cos( p * Math.PI ) / 2;
	}

	function prefersReducedMotion() {
		return window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	}

	/**
	 * Fade an element in or out, matching jQuery's default 400ms fade.
	 *
	 * @param {Element} el   Target element.
	 * @param {boolean} into True to fade in, false to fade out.
	 */
	function fade( el, into ) {
		if ( ! el || el.dataset.sparklingFading === String( into ) ) {
			return;
		}

		el.dataset.sparklingFading = String( into );

		if ( prefersReducedMotion() ) {
			el.style.opacity = into ? '1' : '';
			el.style.display = into ? 'block' : 'none';
			return;
		}

		var duration = 400;
		var start    = null;
		var from     = parseFloat( window.getComputedStyle( el ).opacity );

		if ( isNaN( from ) ) {
			from = into ? 0 : 1;
		}

		if ( into ) {
			el.style.display = 'block';
		}

		function step( ts ) {
			if ( start === null ) {
				start = ts;
			}

			var p = Math.min( ( ts - start ) / duration, 1 );
			var v = from + ( ( into ? 1 : 0 ) - from ) * swing( p );

			el.style.opacity = String( v );

			if ( p < 1 ) {
				window.requestAnimationFrame( step );
			} else if ( ! into ) {
				el.style.display = 'none';
				el.style.opacity = '';
			}
		}

		window.requestAnimationFrame( step );
	}

	/**
	 * Detect a mobile user agent.
	 *
	 * Kept as-is (including the UA sniff) because the mobile menu styling below
	 * depends on it and child themes may override this function.
	 *
	 * @return {boolean} True on a mobile user agent.
	 */
	window.SparklingIsMobile = function () {
		return (
			navigator.userAgent.match( /Android/i ) ||
			navigator.userAgent.match( /webOS/i ) ||
			navigator.userAgent.match( /iPhone/i ) ||
			navigator.userAgent.match( /iPod/i ) ||
			navigator.userAgent.match( /iPad/i ) ||
			navigator.userAgent.match( /BlackBerry/ )
		);
	};

	/**
	 * Toggle the tap-friendly menu class on touch devices at tablet width and up.
	 */
	window.generateMobileMenu = function () {
		var menu = document.querySelector( '#masthead .site-navigation-inner .navbar-collapse > ul.nav' );

		if ( ! menu ) {
			return;
		}

		if ( window.SparklingIsMobile() && window.innerWidth > 767 ) {
			menu.classList.add( 'sparkling-mobile-menu' );
		} else {
			menu.classList.remove( 'sparkling-mobile-menu' );
		}
	};

	/**
	 * Apply Bootstrap classes to markup WordPress generates.
	 */
	function styleCoreMarkup() {
		addClasses( '.comment-reply-link', [ 'btn', 'btn-sm', 'btn-default' ] );
		addClasses(
			'#submit, button[type=submit], html input[type=button], input[type=reset], input[type=submit]',
			[ 'btn', 'btn-default' ]
		);
		addClasses( '.widget_rss ul', [ 'media-list' ] );
		addClasses( '.postform', [ 'form-control' ] );
		addClasses( 'table#wp-calendar', [ 'table', 'table-striped' ] );
	}

	/**
	 * Scroll-to-top button: reveal past 100px, and smooth-scroll on click.
	 */
	function initScrollToTop() {
		var button = document.querySelector( '.scroll-to-top' );

		if ( ! button ) {
			return;
		}

		function onScroll() {
			fade( button, ( window.pageYOffset || document.documentElement.scrollTop ) > 100 );
		}

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		onScroll();

		button.addEventListener( 'click', function ( event ) {
			event.preventDefault();

			var from = window.pageYOffset || document.documentElement.scrollTop;

			if ( ! from ) {
				return;
			}

			if ( prefersReducedMotion() ) {
				window.scrollTo( 0, 0 );
				return;
			}

			var duration = 800;
			var start    = null;

			function step( ts ) {
				if ( start === null ) {
					start = ts;
				}

				var p = Math.min( ( ts - start ) / duration, 1 );

				window.scrollTo( 0, from * ( 1 - swing( p ) ) );

				if ( p < 1 ) {
					window.requestAnimationFrame( step );
				}
			}

			window.requestAnimationFrame( step );
		} );
	}

	/**
	 * Dropdown toggles in the navbar.
	 */
	function initDropdowns() {
		document.querySelectorAll( '.sparkling-dropdown' ).forEach( function ( el ) {
			el.addEventListener( 'click', function () {
				if ( el.parentNode ) {
					el.parentNode.classList.toggle( 'open' );
				}
			} );
		} );
	}

	/**
	 * Reserve space under a fixed navbar so content is not hidden behind it.
	 */
	function initStickyHeader() {
		var navbar = document.querySelector( '.navbar-fixed-top' );

		if ( ! navbar ) {
			return;
		}

		var header = navbar.closest( 'header' );

		if ( ! header ) {
			return;
		}

		// getComputedStyle().height resolves to the content box, matching the
		// jQuery .height() this replaced.
		var adminbar    = document.getElementById( 'wpadminbar' );
		var navHeight   = parseFloat( window.getComputedStyle( navbar ).height ) || 0;
		var adminHeight = adminbar ? parseFloat( window.getComputedStyle( adminbar ).height ) || 0 : 0;

		header.style.marginBottom = ( adminbar ? Math.abs( navHeight - adminHeight ) : navHeight ) + 'px';
	}

	ready( function () {
		styleCoreMarkup();
		initScrollToTop();
		initDropdowns();
		initStickyHeader();
		window.generateMobileMenu();

		window.addEventListener( 'resize', window.generateMobileMenu );
	} );
}() );
