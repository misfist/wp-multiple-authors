/**
 * WP Multiple Authors
 * https://github.com/misfist/wp-multiple-authors
 *
 * Licensed under the GPLv2+ license.
 */

window.WPMultipleAuthors = window.WPMultipleAuthors || {};

( function( window, document, $, plugin ) {
	let $c = {};

	plugin.init = function() {
		plugin.cache();
		plugin.bindEvents();
	};

	plugin.cache = function() {
		$c.window = $( window );
		$c.body = $( document.body );
	};

	plugin.bindEvents = function() {
	};

	$( plugin.init );
}( window, document, require( 'jquery' ), window.WPMultipleAuthors ) );
