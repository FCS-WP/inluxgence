class VamtamNavMenu extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				toggle: '.elementor-menu-toggle',
				dropdownMenu: '.elementor-nav-menu__container.elementor-nav-menu--dropdown',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings( 'selectors' );
		return {
			$toggle: this.$element.find( selectors.toggle ),
			$dropdownMenu: this.$element.find( selectors.dropdownMenu ),
		};
	}

	onInit( ...args ) {
		super.onInit( ...args );

		if ( VAMTAM_FRONT.theme_supports( 'nav-menu--toggle-diff-icon-dimensions' ) ) {
        	this.toggleDimensionsDiscrepancyFix();
		}

		if ( VAMTAM_FRONT.theme_supports( 'nav-menu--disable-scroll-on-mobile' ) ) {
			this.handleMobileDisableScroll();
		}

		this.submenuIconFix();

		this.customToggleIconActiveClass();

		this.megaMenuToggleFix();
	}

	// That's for fixing a flickering issue when closing a mega-menu by clicking on it's toggle.
	megaMenuToggleFix() {
		document.addEventListener( 'click', function( event ) {
			if ( event.target.classList.contains( 'mega-menu' ) || event.target.closest( '.mega-menu' ) ) {
				if ( jQuery( '.vamtam-header-mega-menu:visible' ).length ) {
					event.stopImmediatePropagation();
				}
			}
		}, true );
	}

	submenuIconFix() {
		const elementSettings = this.getElementSettings(),
			iconValue = elementSettings.submenu_icon.value;

		if (iconValue && iconValue !== '<i class=""></i>') {
			this.$element.addClass('vamtam-has-submenu-icon');
		}
	}

	customToggleIconActiveClass() {
		const elementSettings = this.getElementSettings(),
			hasCustomActiveToggleIcon = elementSettings.toggle_icon_active && elementSettings.toggle_icon_active.value,
			activeClass = 'vamtam-has-custom-active-toggle-icon';

		if ( hasCustomActiveToggleIcon ) {
			this.$element.addClass( activeClass );
		} else {
			// that's mostly for the editor.
			this.$element.removeClass( activeClass );
		}
	}

	// Fixes a positioning bug for dropwdown menu, when there is a difference between
    // open & close icons for the toggle element.
    toggleDimensionsDiscrepancyFix() {
		this.elements.$toggle.on( 'click', this.stretchMenu.bind( this ) );
        this.initStretchElement();
    }

	initStretchElement() {
		this.stretchElement = new elementorModules.frontend.tools.StretchElement( {
			element: this.elements.$dropdownMenu
		} );
	}

	stretchMenu() {
        const _this = this,
			ms = jQuery( 'html' ).hasClass( 'ios-safari' ) ? 100 : 50;

        setTimeout( () => {
            if ( ! _this.elements.$toggle.hasClass( 'elementor-active' ) ) {
                return;
            }

            if ( _this.getElementSettings( 'full_width' ) ) {
                _this.stretchElement.stretch();
            } else {
                _this.stretchElement.reset();
            }
        }, ms );
    }

	handleMobileDisableScroll() {
		const $el = this.$element,
			_this = this;

		let lockedScroll   = false,
			prevIsBelowMax = window.VAMTAM.isBelowMaxDeviceWidth();


		const disableScroll = function ( implicit = false ) {
			// Disable page scroll.
			jQuery( 'html, body' ).addClass( 'vamtam-disable-scroll' );
			if ( ! implicit ) {
				lockedScroll = true;
			}
		};
		const enableScroll = function ( implicit = false ) {
			// Enable page scroll.
			jQuery( 'html, body' ).removeClass( 'vamtam-disable-scroll' );
			if ( ! implicit ) {
				lockedScroll = false;
			}
		};

		const toggleHandler = function ( e ) {
            // Timeout is there so the active class has been toggled accordingly, prior to checking.
            setTimeout( () => {
                if ( e.target.closest( '.vamtam-has-mobile-disable-scroll' ) ) {
                    if ( _this.elements.$toggle.hasClass( 'elementor-active' ) ) {
                        disableScroll();
                    } else {
                        enableScroll();
                    }
                }
            }, 50 );
		}

		var resizeHandler = function () {
			var isBelowMax = window.VAMTAM.isBelowMaxDeviceWidth();
			if ( ( prevIsBelowMax !== isBelowMax ) && lockedScroll ) {
				if ( isBelowMax ) {
					// We are at below-max breakpoint.
					// Disable scroll.
					disableScroll( true );
				} else {
					// We are at max breakpoint.
					// Enable scroll.
					enableScroll( true );
				}
				prevIsBelowMax = isBelowMax;
			}
		};

		const bfcacheHandler = function (e) {
			setTimeout(() => {
				// If loaded from bfcache, re-enable scroll.
				if ( e.persisted ) {
					enableScroll();
					jQuery( 'body' ).click(); // Trigger a click to close any open overlays (for older themes).
				}
			}, 0);
		}

		if ( $el.hasClass( 'vamtam-has-mobile-disable-scroll' ) ) {
			this.elements.$toggle.on( 'click', toggleHandler );
			window.addEventListener( 'pageshow', bfcacheHandler );
			window.addEventListener( 'resize', window.VAMTAM.debounce( resizeHandler, 200 ), false );
		}
	}
}

jQuery( window ).on( 'elementor/frontend/init', () => {
	if ( ! elementorFrontend.elementsHandler || ! elementorFrontend.elementsHandler.attachHandler ) {
		const addHandler = ( $element ) => {
			elementorFrontend.elementsHandler.addHandler( VamtamNavMenu, {
				$element,
			} );
		};
		elementorFrontend.hooks.addAction( 'frontend/element_ready/nav-menu.default', addHandler, 100 );
	} else {
		elementorFrontend.elementsHandler.attachHandler( 'nav-menu', VamtamNavMenu );
	}
} );
