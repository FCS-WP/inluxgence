class VamtamSlidesHandler extends elementorModules.frontend.handlers.SwiperBase {
	getDefaultSettings() {
		return {
			selectors: {
				slider: '.elementor-slides-wrapper',
				slide: '.swiper-slide',
				slideInnerContents: '.swiper-slide-contents',
				activeSlide: '.swiper-slide-active',
				activeDuplicate: '.swiper-slide-duplicate-active'
			},
			classes: {
				animated: 'animated',
				kenBurnsActive: 'elementor-ken-burns--active',
				slideBackground: 'swiper-slide-bg'
			},
			attributes: {
				dataSliderOptions: 'slider_options',
				dataAnimation: 'animation'
			}
		};
	}
	getDefaultElements() {
		const selectors = this.getSettings('selectors'),
			elements = {
				$swiperContainer: this.$element.find(selectors.slider)
			};
		elements.$slides = elements.$swiperContainer.find(selectors.slide);
		return elements;
	}
	getSwiperOptions() {
		const elementSettings = this.getElementSettings(),
			swiperOptions = {
				autoplay: this.getAutoplayConfig(),
				grabCursor: true,
				initialSlide: this.getInitialSlide(),
				slidesPerView: 1,
				slidesPerGroup: 1,
				loop: 'yes' === elementSettings.infinite,
				speed: elementSettings.transition_speed,
				effect: elementSettings.transition,
				observeParents: true,
				observer: true,
				handleElementorBreakpoints: true,
				on: {
					slideChange: () => {
						this.handleKenBurns();

						if ( elementSettings.infinite ) {
							this.ensureSlidesContentVisibility();
						}

						this.triggerInnerAnims();
					}
				}
			};
		const showArrows = 'arrows' === elementSettings.navigation || 'both' === elementSettings.navigation,
			pagination = 'dots' === elementSettings.navigation || 'both' === elementSettings.navigation;
		if (showArrows) {
			swiperOptions.navigation = {
				prevEl: '.elementor-swiper-button-prev',
				nextEl: '.elementor-swiper-button-next'
			};
		}
		if (pagination) {
			swiperOptions.pagination = {
				el: '.swiper-pagination',
				type: 'bullets',
				clickable: true
			};
		}
		if (true === swiperOptions.loop) {
			swiperOptions.loopedSlides = this.getSlidesCount();
		}
		if ('fade' === swiperOptions.effect) {
			swiperOptions.fadeEffect = {
				crossFade: true
			};
		}
		return swiperOptions;
	}
	getAutoplayConfig() {
		const elementSettings = this.getElementSettings();
		if ('yes' !== elementSettings.autoplay) {
			return false;
		}
		return {
			stopOnLastSlide: true,
			// Has no effect in infinite mode by default.
			delay: elementSettings.autoplay_speed,
			disableOnInteraction: 'yes' === elementSettings.pause_on_interaction
		};
	}
	initSingleSlideAnimations() {
		const settings = this.getSettings(),
			animation = this.elements.$swiperContainer.data(settings.attributes.dataAnimation);
		this.elements.$swiperContainer.find('.' + settings.classes.slideBackground).addClass(settings.classes.kenBurnsActive);

		// If there is an animation, get the container of the slide's inner contents and add the animation classes to it
		if (animation) {
			this.elements.$swiperContainer.find(settings.selectors.slideInnerContents).addClass(settings.classes.animated + ' ' + animation);
		}
	}
	async initSlider() {
		const $slider = this.elements.$swiperContainer;
		if (!$slider.length) {
			return;
		}
		if (1 >= this.getSlidesCount()) {
			return;
		}
		const Swiper = elementorFrontend.utils.swiper;
		this.swiper = await new Swiper($slider, this.getSwiperOptions());

		// Expose the swiper instance in the frontend
		$slider.data('swiper', this.swiper);

		// The Ken Burns effect will only apply on the specific slides that toggled the effect ON,
		// since it depends on an additional class besides 'elementor-ken-burns--active'
		this.handleKenBurns();
		const elementSettings = this.getElementSettings();
		if (elementSettings.pause_on_hover) {
			this.togglePauseOnHover(true);
		}
		const settings = this.getSettings();
		const animation = $slider.data(settings.attributes.dataAnimation);
		if (!animation) {
			return;
		}
		this.swiper.on('slideChangeTransitionStart', function () {
			const $sliderContent = $slider.find(settings.selectors.slideInnerContents);
			$sliderContent.removeClass(settings.classes.animated + ' ' + animation).hide();
		});
		this.swiper.on('slideChangeTransitionEnd', function () {
			const $currentSlide = $slider.find(settings.selectors.slideInnerContents);
			$currentSlide.show().addClass(settings.classes.animated + ' ' + animation);
		});
	}
	onInit() {
		elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
		// if (2 > this.getSlidesCount()) {
		// 	this.initSingleSlideAnimations();
		// 	return;
		// }
		// this.initSlider();
		this.applyOnChangeSlideHandlers();
	}
	getChangeableProperties() {
		return {
			pause_on_hover: 'pauseOnHover',
			pause_on_interaction: 'disableOnInteraction',
			autoplay_speed: 'delay',
			transition_speed: 'speed'
		};
	}
	updateSwiperOption(propertyName) {
		if (0 === propertyName.indexOf('width')) {
			this.swiper.update();
			return;
		}
		const elementSettings = this.getElementSettings(),
			newSettingValue = elementSettings[propertyName],
			changeableProperties = this.getChangeableProperties();
		let propertyToUpdate = changeableProperties[propertyName],
			valueToUpdate = newSettingValue;

		// Handle special cases where the value to update is not the value that the Swiper library accepts
		switch (propertyName) {
			case 'autoplay_speed':
				propertyToUpdate = 'autoplay';
				valueToUpdate = {
					delay: newSettingValue,
					disableOnInteraction: 'yes' === elementSettings.pause_on_interaction
				};
				break;
			case 'pause_on_hover':
				this.togglePauseOnHover('yes' === newSettingValue);
				break;
			case 'pause_on_interaction':
				valueToUpdate = 'yes' === newSettingValue;
				break;
		}

		// 'pause_on_hover' is implemented by the handler with event listeners, not the Swiper library
		if ('pause_on_hover' !== propertyName) {
			this.swiper.params[propertyToUpdate] = valueToUpdate;
		}
		this.swiper.update();
	}
	onElementChange(propertyName) {
		if (1 >= this.getSlidesCount()) {
			return;
		}
		const changeableProperties = this.getChangeableProperties();
		if (Object.prototype.hasOwnProperty.call(changeableProperties, propertyName)) {
			this.updateSwiperOption(propertyName);
			this.swiper.autoplay.start();
		}
	}
	onEditSettingsChange(propertyName) {
		if (1 >= this.getSlidesCount()) {
			return;
		}
		if ('activeItemIndex' === propertyName) {
			this.swiper.slideToLoop(this.getEditSettings('activeItemIndex') - 1);
			this.swiper.autoplay.stop();
		}
	}
	triggerInnerAnims() {
		const activeItemIndex = this.activeItemIndex || (this.swiper ? this.swiper.activeIndex : this.getInitialSlide()),
			realIndex = this.swiper ? this.swiper.realIndex : this.getInitialSlide(),
			$activeSlide = this.swiper ? jQuery(this.swiper.slides[activeItemIndex]) : jQuery(this.elements.$slides[activeItemIndex]),
			$animsInSlide = $activeSlide.find('[data-settings*="animation"]');

		if (activeItemIndex === 0 || realIndex === 0) {
			// First slide already properly animated.
			return;
		}

		if (this.slidesAnimated) {
			if (this.slidesAnimated.includes(realIndex)) {
				// Already animated the current slide once.
				return;
			}
		} else {
			this.slidesAnimated = [];
		}

		function getAnimation(settings) {
			return elementorFrontend.getCurrentDeviceSetting(settings, 'animation') || elementorFrontend.getCurrentDeviceSetting(settings, '_animation');
		}

		function getAnimationDelay(settings) {
			return elementorFrontend.getCurrentDeviceSetting(settings, 'animation_delay') || elementorFrontend.getCurrentDeviceSetting(settings, '_animation_delay') || 0;
		}

		const _self = this;
		$animsInSlide.each(function (i, el) {
			const $el = jQuery(el),
				settings = $el.data('settings'),
				anim = settings && getAnimation(settings),
				animDelay = settings && getAnimationDelay(settings);

			if (anim) {
				_self.slidesAnimated.push(realIndex);
				$el.addClass('elementor-invisible').removeClass('animated').removeClass(anim);
				setTimeout(function () {
					$el.removeClass('elementor-invisible').addClass('animated ' + anim);
				}, animDelay);
			}
		});

	}
	ensureSlidesContentVisibility($slide) {
		const slides = this.swiper ? this.swiper.slides : this.elements.$slides;
		jQuery(slides).each(function (i, slide) {
			jQuery(slide).find('.elementor-invisible').each(function (i, el) {
				jQuery(el).removeClass('elementor-invisible');
			});
		});
	}
	applyOnChangeSlideHandlers() {
		// Because the Swiper instance is asynchronously added to the data jQuery object,
		// we use setInterval to apply our handlers to the slideChange event.
		const intervalId = setInterval(() => {
			const elementSettings = this.getElementSettings();
			const $slider = this.elements.$swiperContainer;
			this.swiper = $slider.data('swiper');
			const _this = this;

			if ( this.swiper ) {
				clearInterval(intervalId);

				this.swiper.on('slideChange', function () {
					if ( elementSettings.infinite ) {
						_this.ensureSlidesContentVisibility();
					}

					_this.triggerInnerAnims();
				  });
			}
		}, 250 );
	}
}

jQuery( window ).on( 'elementor/frontend/init', () => {
	if ( ! elementorFrontend.elementsHandler || ! elementorFrontend.elementsHandler.attachHandler ) {
		const addHandler = ( $element ) => {
			elementorFrontend.elementsHandler.addHandler( VamtamSlidesHandler, {
				$element,
			} );
		};

		elementorFrontend.hooks.addAction( 'frontend/element_ready/slides.default', addHandler, 9999 );
	} else {
		elementorFrontend.elementsHandler.attachHandler( 'slides', VamtamSlidesHandler );
	}
} );
