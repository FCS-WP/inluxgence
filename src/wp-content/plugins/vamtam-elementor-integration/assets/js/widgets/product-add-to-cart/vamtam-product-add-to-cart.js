var VamtamProductAddToCart = elementorModules.frontend.handlers.Base.extend({
	onInit: function onInit() {
		elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
		if ( window.VAMTAM.bijouxCustomNumericInputs ) {
			window.VAMTAM.bijouxCustomNumericInputs();
		}

		this.checkHandleBijouxButtonType();
	},

	checkHandleBijouxButtonType() {
		if ( ! this.$element.hasClass( 'vamtam-has-bijoux-alt' ) ) {
			return;
		}

		const adcBtns = this.$element.find( '.single_add_to_cart_button, .added_to_cart' );

		jQuery.each( adcBtns, ( i, adcBtn ) => {
			jQuery( adcBtn ).prepend( '<span class="vamtam-prefix"></span>' );
		} );
	}
});

jQuery( window ).on( 'elementor/frontend/init', () => {
	if ( ! elementorFrontend.elementsHandler || ! elementorFrontend.elementsHandler.attachHandler ) {
		const addHandler = ( $element ) => {
			elementorFrontend.elementsHandler.addHandler( VamtamProductAddToCart, {
				$element,
			} );
		};
		if ( VAMTAM_FRONT.theme_supports( 'product-add-to-cart--bijoux-button-type' ) ) {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/woocommerce-product-add-to-cart.default', addHandler );
		}
	} else {
		if ( VAMTAM_FRONT.theme_supports( 'product-add-to-cart--bijoux-button-type' ) ) {
			elementorFrontend.elementsHandler.attachHandler( 'woocommerce-product-add-to-cart', VamtamProductAddToCart );
		}
	}
} );
