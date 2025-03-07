var VamtamAnimatedHeadline = elementorModules.frontend.handlers.Base.extend({
	svgPaths: {
        circle: ['M325,18C228.7-8.3,118.5,8.3,78,21C22.4,38.4,4.6,54.6,5.6,77.6c1.4,32.4,52.2,54,142.6,63.7 c66.2,7.1,212.2,7.5,273.5-8.3c64.4-16.6,104.3-57.6,33.8-98.2C386.7-4.9,179.4-1.4,126.3,20.7'],
        underline_zigzag: ['M9.3,127.3c49.3-3,150.7-7.6,199.7-7.4c121.9,0.4,189.9,0.4,282.3,7.2C380.1,129.6,181.2,130.6,70,139 c82.6-2.9,254.2-1,335.9,1.3c-56,1.4-137.2-0.3-197.1,9'],
        x: ['M497.4,23.9C301.6,40,155.9,80.6,4,144.4', 'M14.1,27.6c204.5,20.3,393.8,74,467.3,111.7'],
        strikethrough: ['M3,75h493.5'],
        curly: ['M3,146.1c17.1-8.8,33.5-17.8,51.4-17.8c15.6,0,17.1,18.1,30.2,18.1c22.9,0,36-18.6,53.9-18.6 c17.1,0,21.3,18.5,37.5,18.5c21.3,0,31.8-18.6,49-18.6c22.1,0,18.8,18.8,36.8,18.8c18.8,0,37.5-18.6,49-18.6c20.4,0,17.1,19,36.8,19 c22.9,0,36.8-20.6,54.7-18.6c17.7,1.4,7.1,19.5,33.5,18.8c17.1,0,47.2-6.5,61.1-15.6'],
        diagonal: ['M13.5,15.5c131,13.7,289.3,55.5,475,125.5'],
        double: ['M8.4,143.1c14.2-8,97.6-8.8,200.6-9.2c122.3-0.4,287.5,7.2,287.5,7.2', 'M8,19.4c72.3-5.3,162-7.8,216-7.8c54,0,136.2,0,267,7.8'],
        double_underline: ['M5,125.4c30.5-3.8,137.9-7.6,177.3-7.6c117.2,0,252.2,4.7,312.7,7.6', 'M26.9,143.8c55.1-6.1,126-6.3,162.2-6.1c46.5,0.2,203.9,3.2,268.9,6.4'],
        underline: ['M7.7,145.6C109,125,299.9,116.2,401,121.3c42.1,2.2,87.6,11.8,87.3,25.7']
    },
    getDefaultSettings: function getDefaultSettings() {
    var iterationDelay = this.getElementSettings('rotate_iteration_delay'),
        settings = {
        animationDelay: iterationDelay || 2500,
        //letters effect
        lettersDelay: iterationDelay * 0.02 || 50,
        //typing effect
        typeLettersDelay: iterationDelay * 0.06 || 150,
        selectionDuration: iterationDelay * 0.2 || 500,
        //clip effect
        revealDuration: iterationDelay * 0.24 || 600,
        revealAnimationDelay: iterationDelay * 0.6 || 1500,
        // Highlighted headline
        highlightAnimationDuration: this.getElementSettings('highlight_animation_duration') || 1200,
        highlightAnimationDelay: this.getElementSettings('highlight_iteration_delay') || 8000
    };
    settings.typeAnimationDelay = settings.selectionDuration + 800;
    settings.selectors = {
        headline: '.elementor-headline',
        dynamicWrapper: '.elementor-headline-dynamic-wrapper',
        dynamicText: '.elementor-headline-dynamic-text',
        blurredLetters: '.vamtam-letter.blurred',
		notBlurredLetters: '.vamtam-letter:not(.blurred)',
    };
    settings.classes = {
        dynamicText: 'elementor-headline-dynamic-text',
        dynamicLetter: 'elementor-headline-dynamic-letter',
        textActive: 'elementor-headline-text-active',
        textInactive: 'elementor-headline-text-inactive',
        letters: 'elementor-headline-letters',
        animationIn: 'elementor-headline-animation-in',
        typeSelected: 'elementor-headline-typing-selected',
        activateHighlight: 'e-animated',
        hideHighlight: 'e-hide-highlight'
    };
    return settings;
    },
    getDefaultElements: function getDefaultElements() {
    var selectors = this.getSettings('selectors');
    return {
        $headline: this.$element.find(selectors.headline),
        $dynamicWrapper: this.$element.find(selectors.dynamicWrapper),
        $dynamicText: this.$element.find(selectors.dynamicText),
        $blurredLetters: this.$element.find( selectors.blurredLetters ),
		$notBlurredLetters: this.$element.find( selectors.notBlurredLetters ),
    };
    },
    getNextWord: function getNextWord($word) {
    return $word.is(':last-child') ? $word.parent().children().eq(0) : $word.next();
    },
    switchWord: function switchWord($oldWord, $newWord) {
    $oldWord.removeClass('elementor-headline-text-active').addClass('elementor-headline-text-inactive');
    $newWord.removeClass('elementor-headline-text-inactive').addClass('elementor-headline-text-active');
    this.setDynamicWrapperWidth($newWord);
    },
    singleLetters: function singleLetters() {
    var classes = this.getSettings('classes');
    this.elements.$dynamicText.each(function () {
        var $word = jQuery(this),
            letters = $word.text().split(''),
            isActive = $word.hasClass(classes.textActive);
        $word.empty();
        letters.forEach(function (letter) {
        var $letter = jQuery('<span>', {
            class: classes.dynamicLetter
        }).text(letter);

        if (isActive) {
            $letter.addClass(classes.animationIn);
        }

        $word.append($letter);
        });
        $word.css('opacity', 1);
    });
    },
    showLetter: function showLetter($letter, $word, bool, duration) {
    var self = this,
        classes = this.getSettings('classes');
    $letter.addClass(classes.animationIn);

    if (!$letter.is(':last-child')) {
        setTimeout(function () {
        self.showLetter($letter.next(), $word, bool, duration);
        }, duration);
    } else if (!bool) {
        setTimeout(function () {
        self.hideWord($word);
        }, self.getSettings('animationDelay'));
    }
    },
    hideLetter: function hideLetter($letter, $word, bool, duration) {
    var self = this,
        settings = this.getSettings();
    $letter.removeClass(settings.classes.animationIn);

    if (!$letter.is(':last-child')) {
        setTimeout(function () {
        self.hideLetter($letter.next(), $word, bool, duration);
        }, duration);
    } else if (bool) {
        setTimeout(function () {
        self.hideWord(self.getNextWord($word));
        }, self.getSettings('animationDelay'));
    }
    },
    showWord: function showWord($word, $duration) {
    var self = this,
        settings = self.getSettings(),
        animationType = self.getElementSettings('animation_type');

    if ('typing' === animationType) {
        self.showLetter($word.find('.' + settings.classes.dynamicLetter).eq(0), $word, false, $duration);
        $word.addClass(settings.classes.textActive).removeClass(settings.classes.textInactive);
    } else if ('clip' === animationType) {
        self.elements.$dynamicWrapper.animate({
        width: $word.width() + 10
        }, settings.revealDuration, function () {
        setTimeout(function () {
            self.hideWord($word);
        }, settings.revealAnimationDelay);
        });
    }
    },
    hideWord: function hideWord($word) {
    var self = this,
        settings = self.getSettings(),
        classes = settings.classes,
        letterSelector = '.' + classes.dynamicLetter,
        animationType = self.getElementSettings('animation_type'),
        nextWord = self.getNextWord($word);

    if (!this.isLoopMode && $word.is(':last-child')) {
        return;
    }

    if ('typing' === animationType) {
        self.elements.$dynamicWrapper.addClass(classes.typeSelected);
        setTimeout(function () {
        self.elements.$dynamicWrapper.removeClass(classes.typeSelected);
        $word.addClass(settings.classes.textInactive).removeClass(classes.textActive).children(letterSelector).removeClass(classes.animationIn);
        }, settings.selectionDuration);
        setTimeout(function () {
        self.showWord(nextWord, settings.typeLettersDelay);
        }, settings.typeAnimationDelay);
    } else if (self.elements.$headline.hasClass(classes.letters)) {
        var bool = $word.children(letterSelector).length >= nextWord.children(letterSelector).length;
        self.hideLetter($word.find(letterSelector).eq(0), $word, bool, settings.lettersDelay);
        self.showLetter(nextWord.find(letterSelector).eq(0), nextWord, bool, settings.lettersDelay);
        self.setDynamicWrapperWidth(nextWord);
    } else if ('clip' === animationType) {
        self.elements.$dynamicWrapper.animate({
        width: '2px'
        }, settings.revealDuration, function () {
        self.switchWord($word, nextWord);
        self.showWord(nextWord);
        });
    } else {
        self.switchWord($word, nextWord);
        setTimeout(function () {
        self.hideWord(nextWord);
        }, settings.animationDelay);
    }
    },
    setDynamicWrapperWidth: function setDynamicWrapperWidth($word) {
    var animationType = this.getElementSettings('animation_type');

    if ('clip' !== animationType && 'typing' !== animationType) {
        this.elements.$dynamicWrapper.css('width', $word.width());
    }
    },
    animateHeadline: function animateHeadline() {
    var self = this,
        animationType = self.getElementSettings('animation_type'),
        $dynamicWrapper = self.elements.$dynamicWrapper;

    if ('clip' === animationType) {
        $dynamicWrapper.width($dynamicWrapper.width() + 10);
    } else if ('typing' !== animationType) {
        self.setDynamicWrapperWidth(self.elements.$dynamicText);
    } //trigger animation


    setTimeout(function () {
        self.hideWord(self.elements.$dynamicText.eq(0));
    }, self.getSettings('animationDelay'));
    },
    getSvgPaths: function getSvgPaths(pathName) {
    var pathsInfo = this.svgPaths[pathName],
        $paths = jQuery();
    pathsInfo.forEach(function (pathInfo) {
        $paths = $paths.add(jQuery('<path>', {
        d: pathInfo
        }));
    });
    return $paths;
    },
    addHighlight: function addHighlight() {
    var elementSettings = this.getElementSettings(),
        $svg = jQuery('<svg>', {
        xmlns: 'http://www.w3.org/2000/svg',
        viewBox: '0 0 500 150',
        preserveAspectRatio: 'none'
    }).html(this.getSvgPaths(elementSettings.marker));
    this.elements.$dynamicWrapper.append($svg[0].outerHTML);
    },
    rotateHeadline: function rotateHeadline() {
    var settings = this.getSettings(); //insert <span> for each letter of a changing word

    if (this.elements.$headline.hasClass(settings.classes.letters)) {
        this.singleLetters();
    } //initialise headline animation


    this.animateHeadline();
    },
    initHeadline: function initHeadline() {
      var headlineStyle = this.getElementSettings('headline_style');

      if ('rotate' === headlineStyle) {
          this.rotateHeadline();
      } else if ('highlight' === headlineStyle) {
          this.addHighlight();
          this.activateHighlightAnimation();
      }

      this.deactivateScrollListener();
    },
    activateHighlightAnimation: function activateHighlightAnimation() {
    var _this = this;

    var settings = this.getSettings(),
        classes = settings.classes,
        $headline = this.elements.$headline;
    $headline.removeClass(classes.hideHighlight).addClass(classes.activateHighlight);

    if (!this.isLoopMode) {
        return;
    }

    setTimeout(function () {
        $headline.removeClass(classes.activateHighligh).addClass(classes.hideHighlight);
    }, settings.highlightAnimationDuration + settings.highlightAnimationDelay * .8);
    setTimeout(function () {
        _this.activateHighlightAnimation(false);
    }, settings.highlightAnimationDuration + settings.highlightAnimationDelay);
    },
    activateScrollListener: function activateScrollListener() {
    var _this2 = this;

    var scrollBuffer = -100;
    this.intersectionObservers.startAnimation.observer =  elementorModules.utils.Scroll.scrollObserver({
        offset: "0px 0px ".concat(scrollBuffer, "px"),
        callback: function callback(event) {
        if (event.isInViewport) {
            _this2.initHeadline();
        }
        }
    });
    this.intersectionObservers.startAnimation.element = this.elements.$headline[0];
    this.intersectionObservers.startAnimation.observer.observe(this.intersectionObservers.startAnimation.element);
    },
    deactivateScrollListener: function deactivateScrollListener() {
    this.intersectionObservers.startAnimation.observer.unobserve(this.intersectionObservers.startAnimation.element);
    },
    initBlurredLetters: function initBlurredLetters() {
        var $blurredLetters = this.elements.$blurredLetters,
            $notBlurredLetters = this.elements.$notBlurredLetters;

		window.VAMTAM_FRONT.WidgetsObserver.observe( this.$element[0], function () {
            jQuery.each( $blurredLetters, function ( i, letter )  {
                var delay = jQuery( letter ).data( 'delay' ) || '0';
                jQuery( letter ).css( {
                    'animation': 'vamtam-blurred-letters 1.3s ' + delay + 's ease-in forwards',
                } );
            });
            jQuery.each( $notBlurredLetters, function ( i, letter )  {
                jQuery( letter ).css( {
                    'animation': 'vamtam-blurred-letters 1.3s 0s ease-in forwards',
                } );
            });
        }.bind(this) );
    },
    onInit: function onInit() {
        elementorModules.frontend.handlers.Base.prototype.onInit.apply(this, arguments);
        var elementSettings = this.getElementSettings();
        if ( 'blurred_letters' === elementSettings.headline_style ) {
            this.initBlurredLetters();
        } else {
            this.intersectionObservers = {
                startAnimation: {
                    observer: null,
                    element: null
                }
                };
            this.isLoopMode = 'yes' === this.getElementSettings('loop');
            this.activateScrollListener();
        }
    },
});

jQuery( window ).on( 'elementor/frontend/init', () => {
	if ( ! elementorFrontend.elementsHandler || ! elementorFrontend.elementsHandler.attachHandler ) {
		const addHandler = ( $element ) => {
			elementorFrontend.elementsHandler.addHandler( VamtamAnimatedHeadline, {
				$element,
			} );
		};

		elementorFrontend.hooks.addAction( 'frontend/element_ready/vamtam-animated-headline.default', addHandler );
	} else {
		elementorFrontend.elementsHandler.attachHandler( 'vamtam-animated-headline', VamtamAnimatedHeadline );
	}
} );
