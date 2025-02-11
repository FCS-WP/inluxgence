class VamtamSearchForm extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				toggle: '.elementor-search-form__toggle',
				closeBtn: '.dialog-close-button',
				formContainer: '.elementor-search-form__container',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );
		return {
			$toggle: this.$element.find( selectors.toggle ),
			$closeBtn: this.$element.find( selectors.closeBtn ),
			$formContainer: this.$element.find( selectors.formContainer ),
		};
	}

	bindEvents() {
		var disableScroll = this.$element.hasClass( 'vamtam-has-disable-scroll' );

		if ( disableScroll ) {
			this.elements.$toggle.on( 'click', this.onOpen.bind( this ) );
			this.elements.$closeBtn.on( 'click', this.onClose.bind( this ) );
			this.elements.$formContainer.on( 'click keyup', this.onClose.bind( this ) );
		}
	}

	onClose( e ) {
		if ( e.type === 'keyup' && e.key !== 'Escape' ) {
			return;
		}

		if ( e.type === 'click' &&
			e.target !== this.elements.$closeBtn[ 0 ] &&
			! this.elements.$closeBtn[ 0 ].contains( e.target ) &&
			e.target !== this.elements.$formContainer[ 0 ] ) {
			return;
		}

		// Enable page scroll.
		jQuery( 'html, body' ).removeClass( 'vamtam-disable-scroll' );
		// Show stt.
		jQuery( '#scroll-to-top' ).removeClass( 'hidden' );
	}

	onOpen() {
		// Disable page scroll.
		jQuery( 'html, body' ).addClass( 'vamtam-disable-scroll' );
		// Hide stt
		jQuery( '#scroll-to-top' ).addClass( 'hidden' );
	}
}


jQuery( window ).on( 'elementor/frontend/init', () => {
	if ( ! elementorFrontend.elementsHandler || ! elementorFrontend.elementsHandler.attachHandler ) {
		const addHandler = ( $element ) => {
			elementorFrontend.elementsHandler.addHandler( VamtamSearchForm, {
				$element,
			} );
		};

		elementorFrontend.hooks.addAction( 'frontend/element_ready/search-form.default', addHandler );
	} else {
		elementorFrontend.elementsHandler.attachHandler( 'search-form', VamtamSearchForm );
	}
} );
