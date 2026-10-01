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
	 * Open or close a menu item's submenu and report it on its toggle button.
	 *
	 * @param {Element} item The <li>.
	 * @param {boolean} open Whether to open it.
	 */
	function setSubmenuOpen( item, open ) {
		item.classList.toggle( 'open', open );

		var toggle = item.querySelector( ':scope > .sparkling-dropdown' );

		if ( toggle ) {
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		}
	}

	/**
	 * Submenu toggles in the navbar.
	 */
	function initDropdowns() {
		document.querySelectorAll( '.sparkling-dropdown' ).forEach( function ( toggle ) {
			toggle.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				setSubmenuOpen( toggle.parentNode, ! toggle.parentNode.classList.contains( 'open' ) );
			} );
		} );

		// A "#" parent item has nowhere to go, so it opens its submenu instead of
		// jumping to the top of the page.
		document.querySelectorAll( '.navbar-nav li.dropdown > a[href="#"]' ).forEach( function ( link ) {
			link.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				setSubmenuOpen( link.parentNode, ! link.parentNode.classList.contains( 'open' ) );
			} );
		} );

		// Escape closes the submenu that holds focus and returns focus to its toggle.
		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' !== event.key || ! event.target.closest ) {
				return;
			}

			var item = event.target.closest( '.navbar-nav li.open' );

			if ( ! item ) {
				return;
			}

			setSubmenuOpen( item, false );

			var toggle = item.querySelector( ':scope > .sparkling-dropdown' );

			if ( toggle ) {
				toggle.focus();
			}
		} );
	}

	/**
	 * Keep the menu button's aria-expanded in step with the collapsed menu.
	 *
	 * Bootstrap only updates triggers whose data-target is an #id; this one
	 * targets the .navbar-ex1-collapse class, which child themes rely on.
	 * Bootstrap announces the change through jQuery events, so listen there.
	 */
	function initMenuToggleState() {
		var toggle = document.querySelector( '.navbar-toggle[data-target=".navbar-ex1-collapse"]' );

		if ( ! toggle || ! window.jQuery ) {
			return;
		}

		window.jQuery( document ).on( 'shown.bs.collapse hidden.bs.collapse', '.navbar-ex1-collapse', function ( event ) {
			toggle.setAttribute( 'aria-expanded', 'shown' === event.type ? 'true' : 'false' );
		} );
	}

	/**
	 * Sticky header: keep the fixed navbar below the admin bar, reserve its
	 * height above the content, and make in-page links stop below it.
	 *
	 * The space was measured once, at load, so it went stale when the menu
	 * wrapped to a second row on a narrower window. With the admin bar the
	 * navbar sat at top: 0, under the bar, and the space was set to the
	 * difference of the two heights, so the bar hid the top of the navbar.
	 * And an anchor -- #comments, a "Skip to main content" target, a menu
	 * link to a section -- scrolled its target under the navbar.
	 */
	function initStickyHeader() {
		var navbar = document.querySelector( '.navbar-fixed-top' );
		var header = navbar ? navbar.closest( 'header' ) : null;

		if ( ! header ) {
			return;
		}

		var adminbar = document.getElementById( 'wpadminbar' );
		var queued   = false;

		function layout() {
			queued = false;

			var top = 0;
			var barFixed = false;

			if ( adminbar ) {
				// Below 600px the admin bar scrolls away with the page.
				barFixed = 'fixed' === window.getComputedStyle( adminbar ).position;
				top      = Math.max( 0, adminbar.getBoundingClientRect().bottom );
				navbar.style.top = top + 'px';
			}

			var navHeight = navbar.offsetHeight;

			header.style.marginBottom = navHeight + 'px';
			document.documentElement.style.scrollPaddingTop = ( ( barFixed ? top : 0 ) + navHeight ) + 'px';
		}

		function queue() {
			if ( ! queued ) {
				queued = true;
				window.requestAnimationFrame( layout );
			}
		}

		layout();
		window.addEventListener( 'resize', queue );

		if ( adminbar ) {
			window.addEventListener( 'scroll', queue, { passive: true } );
		}
	}

	ready( function () {
		styleCoreMarkup();
		initScrollToTop();
		initDropdowns();
		initMenuToggleState();
		initStickyHeader();
		window.generateMobileMenu();

		window.addEventListener( 'resize', window.generateMobileMenu );
	} );
}() );
