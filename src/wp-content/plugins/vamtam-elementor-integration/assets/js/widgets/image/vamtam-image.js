class VamtamImage extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				container: '.elementor-widget-container',
				image: '.elementor-image img',
				imageWrap: '.vamtam-image-wrapper',
				widget: '.elementor-widget-container',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );
		return {
			$container: this.$element.find( selectors.container ),
			$image: this.$element.find( selectors.image ),
			$imageWrap: this.$element.find( selectors.imageWrap ),
			$widget: this.$element.find( selectors.widget ),
		};
	}

	onInit( ...args ) {
		super.onInit( ...args );
		this.handleImageGrowAnims();
	}

	handleImageGrowAnims() {
		var elementSettings    = this.getElementSettings(),
			growAnims          = [ 'growFromLeft', 'growFromRight' ],
			growWithScaleAnims = [ 'imageGrowWithScaleLeft', 'imageGrowWithScaleRight', 'imageGrowWithScaleTop', 'imageGrowWithScaleBottom' ];

			if ( growAnims.includes( elementSettings._animation ) || growAnims.includes( elementSettings.animation ) ) {
				// Apply the anim to the img element.
				this.elements.$image.addClass( 'elementor-invisible' );
				window.VAMTAM_FRONT.WidgetsObserver.observe( this.$element[0], this.animateScale.bind(this) );
			} else if ( growWithScaleAnims.includes( elementSettings._animation ) || growWithScaleAnims.includes( elementSettings.animation ) ) {
				// The grow part of the animation is being animated by the global handler
				// since it is on the main $element. The image part (scale) we animate manually.
				this.elements.$imageWrap.addClass( 'elementor-invisible' );
				this.elements.$image.addClass( 'elementor-invisible' );
				this.animDuration = this.calcAnimDuration();
				window.VAMTAM_FRONT.WidgetsObserver.observe( this.$element[0], this.animate.bind(this) );
			}
	}

	animate() {
		this.animateGrow();
		this.animateScale();
	}

	calcAnimDuration() {
		const speed  = this.$element.hasClass( 'animated-slow' ) ? 100 : this.$element.hasClass( 'animated-fast' ) ? 400 : 200, // px per second
			duration = this.elements.$imageWrap[0].offsetWidth / speed;
		return duration;
	}

	animateGrow() {
		const $imageWrap  = this.elements.$imageWrap,
			animation = this.getAnimation();

		if ( 'none' === animation ) {
			$imageWrap.removeClass( 'elementor-invisible' );
			return;
		}

		const elementSettings = this.getElementSettings(),
			animationDelay    = elementSettings._animation_delay || elementSettings.animation_delay || 0,
			animationDuration = this.animDuration || elementSettings.animation_duration || '';

		$imageWrap.removeClass( animation );

		if ( this.currentAnimation ) {
			$imageWrap.removeClass( this.currentAnimation );
		}

		this.currentAnimation = animation;

		if ( animationDuration ) {
			// Calculated animation duration for constant speed, regardless of distance traveled.
			$imageWrap[ 0 ].style[ '-webkit-animation-duration' ] = this.animDuration + 's';
			$imageWrap[ 0 ].style[ '-moz-animation-duration' ] = this.animDuration + 's';
			$imageWrap[ 0 ].style[ '-o-animation-duration' ] = this.animDuration + 's';
			$imageWrap[ 0 ].style[ 'animation-duration' ] = this.animDuration + 's';
		} else {
			// Fallback to Elementor's default values. Variable speed, based on distance traveled.
			if ( this.$element.hasClass( 'animated-slow' ) ) {
				$imageWrap.addClass( 'animated-slow' );
			}

			if ( this.$element.hasClass( 'animated-fast' ) ) {
				$imageWrap.addClass( 'animated-fast' );
			}
		}

		setTimeout( () => {
			window.requestAnimationFrame( () => {
				$imageWrap.removeClass( 'elementor-invisible' ).addClass( 'animated ' + animation );
			} );
		}, animationDelay );
	}

	animateScale() {
		const $image  = this.elements.$image,
			animation = this.getAnimation();

		if ( 'none' === animation ) {
			$image.removeClass( 'elementor-invisible' );
			return;
		}

		const elementSettings = this.getElementSettings(),
			animationDelay    = elementSettings._animation_delay || elementSettings.animation_delay || 0,
			animationDuration = this.animDuration || elementSettings.animation_duration || '';

		$image.removeClass( animation );

		if ( this.currentAnimation ) {
			$image.removeClass( this.currentAnimation );
		}

		this.currentAnimation = animation;

		if ( animationDuration ) {
			// Calculated animation duration for constant speed, regardless of distance traveled.
			$image[ 0 ].style[ '-webkit-animation-duration' ] = animationDuration + 's';
			$image[ 0 ].style[ '-moz-animation-duration' ] = animationDuration + 's';
			$image[ 0 ].style[ '-o-animation-duration' ] = animationDuration + 's';
			$image[ 0 ].style[ 'animation-duration' ] = animationDuration + 's';
		} else {
			// Fallback to Elementor's default values. Variable speed, based on distance traveled.
			if ( this.$element.hasClass( 'animated-slow' ) ) {
				$image.addClass( 'animated-slow' );
			}

			if ( this.$element.hasClass( 'animated-fast' ) ) {
				$image.addClass( 'animated-fast' );
			}
		}

		setTimeout( () => {
			window.requestAnimationFrame( () => {
				$image.removeClass( 'elementor-invisible' ).addClass( 'animated ' + animation );
			} );
		}, animationDelay );
	}

	getAnimation() {
		return this.getCurrentDeviceSetting( 'animation' ) || this.getCurrentDeviceSetting( '_animation' );
	}

	onElementChange( propertyName ) {
		if ( /^_?animation/.test( propertyName ) ) {
			this.animate();
		}
	}
}


jQuery( window ).on( 'elementor/frontend/init', () => {
	if ( ! elementorFrontend.elementsHandler || ! elementorFrontend.elementsHandler.attachHandler ) {
		const addHandler = ( $element ) => {
			elementorFrontend.elementsHandler.addHandler( VamtamImage, {
				$element,
			} );
		};

		elementorFrontend.hooks.addAction( 'frontend/element_ready/image.default', addHandler, 100 );
	} else {
		elementorFrontend.elementsHandler.attachHandler( 'image', VamtamImage );
	}

} );
