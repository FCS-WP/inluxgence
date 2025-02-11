class VamtamProductsBase extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.elementor-widget-container',
				productImages: 'img.attachment-woocommerce_thumbnail',
				widget: '.elementor-widget-container',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );
		return {
			$container: this.$element.find( selectors.container ),
			$productImages: this.$element.find( selectors.productImages ),
			$widget: this.$element.find( selectors.widget ),
		};
	}

	onInit( ...args ) {
		super.onInit( ...args );
		this.handleProductImageAnims();
	}

	handleProductImageAnims() {
		var elementSettings     = this.getElementSettings(),
			hasProductImageAnim = false;

		if ( elementSettings.product_image_animation ) {
			this.handleProductImageAnim();
			hasProductImageAnim = elementSettings.product_image_animation !== 'none';
		}
		if ( elementSettings.product_image_hover_animation && ! hasProductImageAnim ) {
			this.handleProductImageHoverAnim();
		}
	}

	handleProductImageAnim() {
		var _this = this;
		this.elements.$productImages.each( function( i, img ) {
			jQuery( img ).addClass( 'elementor-invisible' );
			window.VAMTAM_FRONT.WidgetsObserver.observe( img, _this.animateProductImage( i ).bind( this ) );
		} );
	}

	handleProductImageHoverAnim( imgEl = null ) {
		var elementSettings = this.getElementSettings(),
			animation       = elementSettings.product_image_hover_animation;

		if ( ! animation ) {
			return;
		}

		if ( imgEl ) {
			jQuery( imgEl ).addClass( 'elementor-animation-' + animation );
		} else {
			this.elements.$productImages.each( function( i, img ) {
				jQuery( img ).addClass( 'elementor-animation-' + animation );
			} );
		}

	}

	animateProductImage( i ) {
		const $image  = jQuery( this.elements.$productImages[ i ] ),
			animation = this.getProductImageAnimation(),
			_this     = this;

		if ( 'none' === animation ) {
			$image.removeClass( 'elementor-invisible' );
			return;
		}

		$image.removeClass( animation );

		if ( this.currentAnimation ) {
			$image.removeClass( this.currentAnimation );
		}

		this.currentAnimation = animation;

		$image.removeClass( 'elementor-invisible' ).addClass( 'animated ' + animation );
		$image.on( 'webkitAnimationEnd mozAnimationEnd animationend', function() {
			jQuery( this ).removeClass( 'animated' );
			// When we have an entrance animation and a hover one, we enable the hover one only after the
			// entrance has finished.
			_this.handleProductImageHoverAnim( this );
		} )
	}

	getProductImageAnimation() {
		return this.getCurrentDeviceSetting( 'product_image_animation' ) || this.getCurrentDeviceSetting( '_product_image_animation' );
	}

	onElementChange( propertyName ) {
		if ( /^_?product_image_animation/.test( propertyName ) ) {
			this.animateProductImage();
		}
	}
}


jQuery( window ).on( 'elementor/frontend/init', () => {
	const widgets = [
		'woocommerce-product-related',
		'woocommerce-product-upsell',
		'wc-archive-products',
		'woocommerce-products',
	];

	if ( ! elementorFrontend.elementsHandler || ! elementorFrontend.elementsHandler.attachHandler ) {
		const addHandler = ( $element ) => {
			elementorFrontend.elementsHandler.addHandler( VamtamProductsBase, {
				$element,
			} );
		};
		widgets.forEach( widget  => {
			elementorFrontend.hooks.addAction( `frontend/element_ready/${widget}.default`, addHandler, 100 );
		} );
	} else {
		widgets.forEach( widget  => {
			elementorFrontend.elementsHandler.attachHandler( widget, VamtamProductsBase );
		} );
	}
} );
