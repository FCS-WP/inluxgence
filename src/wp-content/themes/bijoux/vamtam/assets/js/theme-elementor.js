// Theme-Elementor related code for both frontend and editor.
( function( $, undefined ) {
	"use strict";

	window.VAMTAM = window.VAMTAM || {}; // Namespace

	$( function() {
		// Bijoux-only.
		var bijouxCustomNumericInputs = function () {
			var numInputs = document.querySelectorAll( 'input[type="number"]:not([max="1"])' );
			numInputs.forEach( function( input ) {
				if ( input.closest( '.vamtam-count-wrap' ) ) {
					return;
				}

				var $parent = $( input ).parent();

				$parent.append( '<div class="vamtam-count-wrap"></div>' );
				$parent.append( '<span class="vamtam-increment"  onclick="this.parentNode.querySelector(\'input[type=number]\').stepUp();this.parentNode.querySelector(\'input[type=number]\').dispatchEvent(new Event(\'change\', {bubbles: true}))">+</span>' );
				$parent.append( '<span class="vamtam-decrement"  onclick="this.parentNode.querySelector(\'input[type=number]\').stepDown();this.parentNode.querySelector(\'input[type=number]\').dispatchEvent(new Event(\'change\', {bubbles: true}))">-</span>' );

				var $wrap = $parent.find( '.vamtam-count-wrap' ),
					$inc  = $parent.find( '.vamtam-increment' ),
					$dec  = $parent.find( '.vamtam-decrement' );

				$( $dec ).appendTo( $wrap );
				$( input ).appendTo( $wrap );
				$( $inc ).appendTo( $wrap );
			} );
		};

		window.VAMTAM.bijouxCustomNumericInputs = bijouxCustomNumericInputs;

		var isFrontend = ! window.elementorFrontend.isEditMode();

		if ( isFrontend ) {
			$( window ).ready( function () {
				bijouxCustomNumericInputs();
			} );
		} else {
			$(window).on('elementor/frontend/init', function(){
				window.elementor.on('document:loaded', function(){
					// The timeout is for ensuring that when the function is run, the frontend DOM has
					// been created (when on editor). Can't seem to find a proper event for this from Elementor.
					setTimeout( function() {
						bijouxCustomNumericInputs();
					}, 1000 );
				} );
			} );
		}
	});
})( jQuery );
