/**
 * Degriz EU Guarantee - the button opens the notice in a dialog on screens wide enough to read it; on a phone and
 * without JavaScript it follows its link to the PDF, which can be zoomed.
 */
( function () {
	'use strict';

	var wide = window.matchMedia ? window.matchMedia( '(min-width: 601px)' ) : { matches: true };

	document.addEventListener( 'click', function ( e ) {
		var trigger = e.target.closest ? e.target.closest( '[data-deg-open]' ) : null;
		if ( ! trigger ) {
			if ( e.target.matches && e.target.matches( 'dialog.deg__dialog' ) ) {
				e.target.close();
			}
			return;
		}
		var dialog = document.getElementById( trigger.getAttribute( 'data-deg-open' ) );
		if ( ! wide.matches || ! dialog || ! dialog.showModal ) {
			return;
		}
		e.preventDefault();
		dialog.showModal();
	} );
}() );
