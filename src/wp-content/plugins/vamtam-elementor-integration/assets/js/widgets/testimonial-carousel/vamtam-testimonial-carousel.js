const VamtamTestimonialCarouselHandler = elementorModules.frontend.handlers.Base.extend( {
	getDefaultSettings: function getDefaultSettings() {
		return {
		  selectors: {
			slider: '.swiper-container, .swiper',
			slide: '.swiper-slide',
			activeSlide: '.swiper-slide-active',
			activeDuplicate: '.swiper-slide-duplicate-active'
		  },
		  classes: {
			animated: 'animated',
		  },
		  attributes: {
			dataSliderOptions: 'slider_options',
			dataAnimation: 'animation'
		  }
		};
	},
	getDefaultElements: function getDefaultElements() {
		var selectors = this.getSettings('selectors');
		var elements = {
			$slider: this.$element.find(selectors.slider)
		};
		elements.$mainSwiperSlides = elements.$slider.find(selectors.slide);
		return elements;
	},
	onInit: function onInit() {
		const _self = this;
		elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
		setTimeout( () => {
			_self.updateSwiperInstance();
		}, 50 );
	},
	updateSwiperInstance: async function updateSwiperInstance() {
		this.swiper = this.elements.$slider.data( 'swiper' );

		if ( ! this.swiper ) {
			return;
		}

		if ( ! this.swiper.params.watchSlidesProgress ) {
			if ( this.getElementSettings().disable_slide_to_click === 'yes' ) {
				// Disable slide on click.
				this.swiper.params.slideToClickedSlide = false;
			}
			// Enable watchSlidesProgress.
			this.swiper.params.watchSlidesProgress = true;
			// Enable watchSlidesVisibility.
			this.swiper.params.watchSlidesVisibility = true;
			// Update.
			await this.swiper.update();
			// Run Inner Anims for visible slides (inital).
			this.triggerInnerAnimsForVisibleSlides();

			const _self = this;
			this.swiper.on( 'slideChange', () => {
				_self.triggerInnerAnimsForVisibleSlides();
			} );
		}
	},
	triggerInnerAnimsForVisibleSlides: function triggerInnerAnims () {
		// Determine visible slides.
		this.$visibleSlides = this.elements.$mainSwiperSlides.filter( '.swiper-slide-visible' );
		// Run Inner Anims for visible slides.
		this.$visibleSlides.each( ( i, visibleSlide ) => {
			this.triggerInnerAnims( jQuery( visibleSlide ) );
		});
	},
	triggerInnerAnims: function triggerInnerAnims( $visibleSlide ) {
		const visbileSlideIndex = $visibleSlide.data( 'swiper-slide-index' ),
			$animsInSlide       = $visibleSlide.find( '[data-settings*="animation"]' );

		if ( this.slidesAnimated ) {
			if ( this.slidesAnimated.includes( visbileSlideIndex ) ) {
				// Already animated the current slide once.
				return;
			}
		} else {
			this.slidesAnimated = [];
		}

		function getAnimation( settings ) {
			return elementorFrontend.getCurrentDeviceSetting( settings, 'animation' ) || elementorFrontend.getCurrentDeviceSetting( settings, '_animation' );
		}

		function getAnimationDelay( settings ) {
			return elementorFrontend.getCurrentDeviceSetting( settings, 'animation_delay' ) || elementorFrontend.getCurrentDeviceSetting( settings, '_animation_delay' ) || 0;
		}

		const _self = this;
		$animsInSlide.each( function ( i, el ) {
			const $el      = jQuery( el ),
				settings   = $el.data( 'settings' ),
				anim       = settings && getAnimation( settings ),
				animDelay  = settings && getAnimationDelay( settings );

			if ( anim ) {
				_self.slidesAnimated.push( visbileSlideIndex );
				$el.addClass( 'elementor-invisible' ).removeClass( 'animated' ).removeClass( anim );
				setTimeout( function() {
					$el.removeClass( 'elementor-invisible' ).addClass( 'animated ' + anim );
				}, animDelay );
			}
		} );

	},
} );

jQuery( window ).on( 'elementor/frontend/init', () => {
	if ( ! elementorFrontend.elementsHandler || ! elementorFrontend.elementsHandler.attachHandler ) {
		const addHandler = ( $element ) => {
			elementorFrontend.elementsHandler.addHandler( VamtamTestimonialCarouselHandler, {
				$element,
			} );
		};

		elementorFrontend.hooks.addAction( 'frontend/element_ready/testimonial-carousel.default', addHandler, 9999 );
	} else {
		elementorFrontend.elementsHandler.attachHandler( 'testimonial-carousel', VamtamTestimonialCarouselHandler );
	}
} );
