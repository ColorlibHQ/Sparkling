( function() {// jscs:ignore validateLineBreaks
	var isWebkit = navigator.userAgent.toLowerCase().indexOf( 'webkit' ) > -1,
	    isOpera  = navigator.userAgent.toLowerCase().indexOf( 'opera' )  > -1,
	    isIE     = navigator.userAgent.toLowerCase().indexOf( 'msie' )   > -1,
        eventMethod;

	if ( ( isWebkit || isOpera || isIE ) && 'undefined' !== typeof( document.getElementById ) ) {
		eventMethod = ( window.addEventListener ) ? 'addEventListener' : 'attachEvent';
		window[ eventMethod ]( 'hashchange', function() {
			var element = document.getElementById( location.hash.substring( 1 ) );

			if ( element ) {
				if ( ! /^(?:a|select|input|button|textarea)$/i.test( element.tagName ) ) {
                    element.tabIndex = -1;
                }
				element.focus();
			}
		}, false );
	}
})();
