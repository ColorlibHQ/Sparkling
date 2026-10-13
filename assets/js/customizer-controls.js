/**
 * Customizer controls for Sparkling.
 *
 * Registers the "sparkling-link" section type: a section that is a link
 * rather than a panel of controls (inc/class-sparkling-customize-link-section.php).
 * Core hides any section with no active controls, so without this they are
 * printed and never shown.
 *
 * @package sparkling
 */
( function ( api ) {
	'use strict';

	api.sectionConstructor[ 'sparkling-link' ] = api.Section.extend( {
		// A link, not a panel: nothing to expand.
		attachEvents: function () {},

		isContextuallyActive: function () {
			return true;
		},
	} );
}( wp.customize ) );
