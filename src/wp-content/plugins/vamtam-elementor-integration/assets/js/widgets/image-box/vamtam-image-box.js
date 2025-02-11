class VamtamImageBox extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.elementor-widget-container',
				eye: '.vamtam-eye .eye',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );
		return {
			$container: this.$element.find( selectors.container ),
			$container: this.$element.find( selectors.container ),
			$eye: this.$element.find( selectors.eye ),
		};
	}

	bindEvents() {
		this.elements.$container.on( 'mousemove', this.onContainerMouseMove.bind( this ) );
		this.elements.$container.on( 'mouseleave', this.onContainerMouseLeave.bind( this ) );
		this.randomMouseMove = this.randomMouseMove.bind( this );
		this.randomMouseMove();
	}

	randomMouseMove() {
		var _self = this;

		this.shouldMoveRandomly = this.$element.hasClass( 'eye-random' );

		if ( ! this.shouldMoveRandomly || this.hasSetInterval ) {
			return;
		}

		var interval = parseInt( this.elements.$eye.data( 'eye-interval' ) );

		setInterval( function() {
			if ( _self.mouseInsideElement || ! _self.shouldMoveRandomly ) {
				return;
			}
			var nextRotation = Math.floor( Math.random() * 360 ) + 1;

			_self.elements.$eye.css( {
				'transform': 'rotate(' + nextRotation + 'deg)',
				'transition': 'transform 3s ease',
			} );

		}, ( interval * 1000 ) );

		this.hasSetInterval = true;
	}

	onContainerMouseLeave() {
		this.mouseInsideElement = false;
	}

	onContainerMouseMove( event ) {
		this.mouseInsideElement = true;

		if ( ! this.elements.$eye.length ) {
			return;
		}

		var eye = this.elements.$eye,
			x   = ( eye.offset().left ) + ( eye.width() / 2 ),
			y   = ( eye.offset().top ) + ( eye.height() / 2 ),
			rad = Math.atan2( event.pageX - x, event.pageY -  y),
			rot = ( rad * ( 180 / Math.PI ) * -1 ) + 180;

		eye.css( {
			'transform': 'rotate(' + rot + 'deg)',
			'transition': '',
		} );
	}


}


jQuery( window ).on( 'elementor/frontend/init', () => {
	if ( ! elementorFrontend.elementsHandler || ! elementorFrontend.elementsHandler.attachHandler ) {
		const addHandler = ( $element ) => {
			elementorFrontend.elementsHandler.addHandler( VamtamImageBox, {
				$element,
			} );
		};

		elementorFrontend.hooks.addAction( 'frontend/element_ready/image-box.default', addHandler );
	} else {
		elementorFrontend.elementsHandler.attachHandler( 'image-box', VamtamImageBox );
	}
} );
