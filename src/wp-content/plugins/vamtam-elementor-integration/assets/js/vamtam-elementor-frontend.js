// Contains logic related to elementor.
( function( $, undefined ) {
	"use strict";

	window.VAMTAM_FRONT.elementor = window.VAMTAM_FRONT.elementor || {};

	var VAMTAM_ELEMENTOR = {
		domLoaded: function () {
			this.VamtamMainNavHandler.init();
			this.VamtamActionLinksHandler.init();
			this.VamtamPopupToggleHandler.init();
			this.VamtamPopupsHandler.init();

			if ( elementorFrontend.config.experimentalFeatures.e_optimized_css_loading ) {
				this.VamtamImprovedCSSLoadingHandler.init();
			}
		},
		pageLoaded: function () {
			// this.VamtamPopupToggleHandler.init();
			this.VamtamWidgetsHandler.stickyInsideParentFix();
		},
		// Hanldes issues related to the main na menu.
		VamtamMainNavHandler: {
			init: function() {
				this.fixMenuDrodownScrolling();
			},
			fixMenuDrodownScrolling: function () {
				var $mainMenuDropdown = $( '.elementor-location-header .elementor-nav-menu--dropdown-tablet .elementor-nav-menu--dropdown.elementor-nav-menu__container' ).first();
				var menuToggle        = $mainMenuDropdown.siblings( '.elementor-menu-toggle' )[ 0 ];

				if ( ! $mainMenuDropdown.length || ! menuToggle.length ) {
					return;
				}

				var onMenuToggleClick = function () {
					if ( $( menuToggle ).hasClass( 'elementor-active' ) ) {
						// For safari we substract an additional 100px to account for the bottom action-bar (different size per iOS version). Uggh..
						var height = $( 'html' ).hasClass( 'ios-safari' ) ? $mainMenuDropdown[ 0 ].getBoundingClientRect().top + 100 : $mainMenuDropdown[ 0 ].getBoundingClientRect().top;
						$mainMenuDropdown.css( 'max-height', 'calc(100vh - ' + height + 'px)' );
						menuToggle.removeEventListener( 'click', onMenuToggleClick );
					}
				}
				menuToggle.addEventListener( 'click', onMenuToggleClick );
			},
		},
		// Handles funcionality regarding action-linked popups.
		VamtamActionLinksHandler: {
			init: function() {
				this.popupActionLinks();
			},
			popupActionLinks: function() {
				var _self               = this,
					prevIsBelowMax      = window.VAMTAM.isBelowMaxDeviceWidth(),
					alignedPopups       = [];

				var handleAlignWithParent = function( popupId, popupParent, clearPrevPos ) {
					var popupWrapper   = $( '#elementor-popup-modal-' + popupId ),
						popup          = $( popupWrapper ).find( '.dialog-widget-content' ),
						adminBarHeight = window.VAMTAM.adminBarHeight;

					if ( ! popup.length || ! popupParent ) {
						return;
					}

					var parentPos = popupParent.getBoundingClientRect();

					if ( clearPrevPos ) {
						$( popup ).css( {
							top: '',
							left: '',
						} );
					} else {
						$( popup ).css( {
							top: parentPos.bottom - adminBarHeight,
							left: parentPos.left,
						} );
						// After the popup is hidden we unset top/left.
						( function () { // IIFE for closure so popup, popupWrapper are available.
							// Visibity check.
							var visibilityCheck = setInterval( function() {
								if ( $( popupWrapper ).css( 'display' ) === 'none' ) {
									$( popup ).css( {
										top: '',
										left: '',
									} );
									clearInterval( visibilityCheck );
								}
							}, 500 );
						})();
					}
				};

				var repositionAlignedPopups = function ( clear ) {
					alignedPopups.forEach( function( popup ) {
						if ( clear ) {
							handleAlignWithParent( popup.id, popup.parent, true );
						} else {
							handleAlignWithParent( popup.id, popup.parent );
						}
					} );
				};

				var popupResizeHandler = function () {
					var isBelowMax = window.VAMTAM.isBelowMaxDeviceWidth();
					if ( prevIsBelowMax !== isBelowMax) {
						// We changed breakpoint (max/below-max).
						if ( isBelowMax ) {
							// Clear popup vals set from desktop.
							repositionAlignedPopups( true );
						} else {
							repositionAlignedPopups();
						}
						prevIsBelowMax = isBelowMax;
					} else if ( ! isBelowMax ) {
						repositionAlignedPopups();
					}
				};

				var storePopup = function ( popupId, popupParent ) {
					// If exists, update parent, otherwise store.
					// A popup can have multiple parents. We only care for the last parent that triggers it each time.
					var done;

					alignedPopups.forEach( function( popup ) {
						if ( popup.id === popupId ) {
							popup.parent = popupParent;
							done = true;
						}
					} );

					if ( ! done ) {
						alignedPopups.push( {
							id: popupId,
							parent: popupParent,
						} );
					}
				}

				var checkAlignWithParent = function( e ) {
					var actionLink = $( e.currentTarget ).attr( 'href' );
					if ( ! actionLink ) {
						return;
					}

					var settings = _self.utils.getSettingsFromActionLink( actionLink );
					if ( settings && settings.align_with_parent ) {

						storePopup( settings.id, e.currentTarget );

						if ( window.VAMTAM.isMaxDeviceWidth() ) {
							// Desktop
							handleAlignWithParent( settings.id, e.currentTarget );
						}

						window.removeEventListener( 'resize', popupResizeHandler );
						window.addEventListener( 'resize', popupResizeHandler, false );
					}
				};

				elementorFrontend.elements.$document.on( 'click', 'a[href^="#elementor-action"]', checkAlignWithParent );
			},
			utils: {
				getSettingsFromActionLink: function( url ) {
					url = decodeURIComponent( url );

					if ( ! url ) {
						return;
					}

					var settingsMatch = url.match( /settings=(.+)/ );

					var settings = {};

					if ( settingsMatch ) {
						settings = JSON.parse( atob( settingsMatch[ 1 ] ) );
					}

					return settings;
				},
				getActionFromActionLink: function( url ) {
					url = decodeURIComponent( url );

					if ( ! url ) {
						return;
					}

					var actionMatch = url.match( /action=(.+?)&/ );

					if ( ! actionMatch ) {
						return;
					}

					var action = actionMatch[ 1 ];

					return action;
				}
			}
		},
		// Handles popup toggling.
		VamtamPopupToggleHandler: {
			init: function() {
				var popupToggles       = document.querySelectorAll( '.vamtam-popup-toggle' ),
					popupTogglesStates = [],
					prevIsBelowMax     = window.VAMTAM.isBelowMaxDeviceWidth();


				if ( ! popupToggles.length ) {
					return;
				}

				// Handle click.
				const onClickHandler = function( e, toggleFound, maybeClose = false ) {
					var curToggle, isResize = true;

					if ( e ) {
						if ( ! maybeClose ) {
							e.preventDefault();
						}
						isResize = false;
					}

					popupTogglesStates.forEach( function( toggle ) {
						if ( toggleFound.isSameNode( toggle.toggle ) || toggleFound.isSameNode( toggle.toggleClone ) ) {
							curToggle = toggle;
							return;
						}
					} );

					var curTogglePos      = curToggle.toggle.getBoundingClientRect(),
						curInnermostElPos = curToggle.innermostEl.getBoundingClientRect();

					if ( ! maybeClose ) {
						// Set clone pos.
						requestAnimationFrame( () => {
							Object.assign( curToggle.toggleClone.style, {
								top: curTogglePos.top + 'px',
								left: curTogglePos.left + 'px',
							} );
							Object.assign( curToggle.innermostElClone.style, {
								position: 'fixed',
								top: curInnermostElPos.top + 'px',
								left: curInnermostElPos.left + 'px',
							} );
						} );
					}

					if ( isResize ) {
						// We only need to update the clone pos.
						return;
					}

					const setToggleData = () => {
						curToggle.modalParent   = $( `#elementor-popup-modal-${curToggle.modalId}` );
						curToggle.modalContent  = curToggle.modalParent && $( curToggle.modalParent ).find( `.dialog-message.dialog-lightbox-message` );
						// Settings we only need to capture once.
						if ( ! curToggle.modalSettings ) {
							const popup =  $( curToggle.modalContent ).find( `[data-elementor-id="${curToggle.modalId}"]` );
							if ( popup.length ) {
								curToggle.modalSettings = JSON.parse( $( popup ).attr( 'data-elementor-settings' ) );
							}
						}
					};

					setToggleData();

					if ( ! curToggle.popupToggleActive ) {
						requestAnimationFrame( function() {
							curToggle.toggleClone.classList.remove( 'is-closed' );
							curToggle.toggleClone.classList.add( 'is-active' );
							curToggle.toggle.classList.add( 'clone-active' );
							curToggle.toggleClone.classList.add( 'is-clickable' );
							curToggle.popupToggleActive = true;
						} );
					} else {
						if ( ! curToggle.toggleClone.classList.contains( 'is-clickable') ) {
							return;
						}

						// maybeClose means that the popup toggle is active (overlay shown)
						// and we're checking if it should get hidden based on the linked modal's close condiitons.
						if ( maybeClose ) {
							if ( curToggle.modalId ) {
								if ( e.type === 'keyup' ) {
									if ( curToggle.modalSettings && curToggle.modalSettings.prevent_close_on_esc_key === 'yes' ) {
										// Disregard ESC.
										return;
									}
								} else if ( e.type === 'click' ) {
									if ( curToggle.modalSettings && curToggle.modalSettings.prevent_close_on_background_click === 'yes' ) {
										// Disregard click.
										return;
									} else {
										// Check if click was inside popup.
										if ( ( curToggle.modalContent.length && curToggle.modalContent[0] ) && ( e.target.isSameNode( curToggle.modalContent[0] ) || curToggle.modalContent[0].contains( e.target ) ) ) {
											// Click inside popup.
											return;
										}
									}
								}
							}
						}

						requestAnimationFrame( function() {
							curToggle.toggle.classList.remove( 'clone-active' );
							curToggle.toggleClone.classList.remove( 'is-active' );
							curToggle.toggleClone.classList.remove( 'is-clickable' );
							curToggle.toggleClone.classList.add( 'is-closed' );
							curToggle.popupToggleActive = false;
						} );
					}

					// Capture modal. Modals get destroyed after closing so we need to re-capture them.
					if ( ! maybeClose && curToggle.modalId ) {
						setTimeout(() => {
							setToggleData();
						}, 1000);
					}
				};

				// Handle resize.
				const onResizeHandler = function() {
					const activeClones = document.querySelectorAll( '.vamtam-popup-toggle-clone.is-active' ),
						isBelowMax     = window.VAMTAM.isBelowMaxDeviceWidth();

					// Disable active popups by clicking in the toggle clones.
					const forceDisablePopups = () => {
						activeClones.forEach( function( activeClone ) {
							$( activeClone ).click();
						} );
					};

					if ( prevIsBelowMax !== isBelowMax) {
						// We changed breakpoint (max/below-max).
						// Disable active popups.
						forceDisablePopups();
						// Re-copy required styles.
						popupTogglesStates.forEach( function( toggle ) {
							copyToggleStyles( toggle.toggle, toggle.toggleClone );
						} );
						prevIsBelowMax = isBelowMax;
					}

					activeClones.forEach( function( clone ) {
						requestAnimationFrame( () => {
							clone.classList.add( 'hidden' );
						} );

						onClickHandler( null, clone );

						setTimeout(() => {
							clone.classList.remove( 'hidden' );
						}, 700 );
					} );
				};

				// Initialize.
				popupToggles.forEach( function( toggle ) {
					var clone            = toggle.cloneNode( true ),
						innermostEl      = $( toggle ).find( '*:not(:has("*"))' ).first()[0],
						innermostElClone = $( clone ).find( '*:not(:has("*"))' ).first()[0],
						actionLink       = $( toggle ).find( 'a[href^="%23elementor-action"], a[href^="#elementor-action"]' ),
						actionSettings   = actionLink.length && window.VAMTAM_FRONT.elementor.urlActions.getSettingsFromActionLink( $( actionLink ).attr( 'href' ) ),
						modalId          = actionSettings && actionSettings.id;

					clone.classList.add( 'vamtam-popup-toggle-clone' );

					const newInnerEls = updateInnerElsForToggle( toggle, clone, innermostEl );

					if ( newInnerEls ) {
						innermostEl      = newInnerEls.innermostEl;
						innermostElClone = newInnerEls.innermostElClone;;
					}

					copyToggleStyles( toggle, clone );

					popupTogglesStates.push( {
						popupToggleActive:  false,
						toggle: toggle,
						toggleClone: clone,
						innermostEl: innermostEl,
						innermostElClone: innermostElClone,
						modalId: modalId,
						modalParent: undefined,
						modalContent: undefined,
						modalSettings: undefined,
					} );

					document.body.appendChild( clone );
				} );

				// For some types we need to update the innermostEl (+clone) so the styles copied styles are properly calculated.
				function updateInnerElsForToggle( toggle, clone, innermostEl ) {
					var newInnermostEl, newInnermostElClone;
					// Add more checks here for other types.
					if ( toggle.classList.contains( 'elementor-widget-icon' ) ) {
						newInnermostEl      = $( toggle ).find( '.elementor-icon svg, .elementor-icon' ).first()[0],
						newInnermostElClone = $( clone ).find( '.elementor-icon svg, .elementor-icon' ).first()[0];
					}

					if ( newInnermostEl && innermostEl !== newInnermostEl ) {
						return {
							innermostEl: newInnermostEl,
							innermostElClone: newInnermostElClone,
						}
					}
				}

				function copyToggleStyles( toggle, clone ) {
					// Add more checks here for other types.
					if ( toggle.classList.contains( 'elementor-widget-button' ) ) {
						copyRequiredStyles( toggle, clone, 'button' );
					} else if ( toggle.classList.contains( 'elementor-widget-icon' ) ) {
						copyRequiredStyles( toggle, clone, 'icon' );
					} else {
						copyRequiredStyles( toggle, clone );
					}
				}

				// Example types could be 'button' or 'menu-item' etc. We have to manually check which elements are important for each case.
				function copyRequiredStyles( toggle, clone, type ) {
					var innermostEl      = $( toggle ).find( '*:not(:has("*"))' ).first()[0],
						innermostElClone = $( clone ).find( '*:not(:has("*"))' ).first()[0];

					switch ( type ) {
						case 'button':
							// innermost.
							copyStylesFromTo( innermostEl, innermostElClone, type );
							// button.
							var btn      = $( toggle ).find( '.elementor-button' ).first()[0],
								btnClone = $( clone ).find( '.elementor-button' ).first()[0];
							copyStylesFromTo( btn, btnClone, type );
							// root.
							copyStylesFromTo( toggle, clone, type );
							break;
						case 'icon':
							// icon.
							var icon      = $( toggle ).find( '.elementor-icon svg, .elementor-icon i' ).first()[0],
								iconClone = $( clone ).find( '.elementor-icon svg, .elementor-icon i' ).first()[0];
								copyStylesFromTo( icon, iconClone, type );
							// root.
							copyStylesFromTo( toggle, clone, type );
							break;
						default:
							// innermost.
							copyStylesFromTo( innermostEl, innermostElClone );
							// root.
							copyStylesFromTo( toggle, clone );
							break;
					}
				}

				// Copies style properties based on type.
				// Example types could be 'button' or 'menu-item' etc. We have to manually check which styles are important for each case. Default is for buttons.
				function copyStylesFromTo( el, cloneEl, type ) {
					if ( ! el || ! cloneEl ) {
						return;
					}
					var styles = window.getComputedStyle( el, null);
					switch ( type ) {
						case 'icon':
							Object.assign( cloneEl.style, {
								font: styles.getPropertyValue( 'font' ) !== "" ? styles.getPropertyValue( 'font' ) : `${styles.getPropertyValue( 'font-style' )} ${styles.getPropertyValue( 'font-variant' )} ${styles.getPropertyValue( 'font-weight' ) } ${styles.getPropertyValue( 'font-size' )}/${styles.getPropertyValue( 'line-height' )} ${styles.getPropertyValue( 'font-family' )}`,
								width: styles.getPropertyValue( 'width' ),
								height: styles.getPropertyValue( 'height' ),
								color: styles.getPropertyValue( 'color' ),
								fill: styles.getPropertyValue( 'fill' ),
								stroke: styles.getPropertyValue( 'stroke' ),
							} );
							break;
						case 'button':
						default:
							Object.assign( cloneEl.style, {
								font: styles.getPropertyValue( 'font' ) !== "" ? styles.getPropertyValue( 'font' ) : `${styles.getPropertyValue( 'font-style' )} ${styles.getPropertyValue( 'font-variant' )} ${styles.getPropertyValue( 'font-weight' ) } ${styles.getPropertyValue( 'font-size' )}/${styles.getPropertyValue( 'line-height' )} ${styles.getPropertyValue( 'font-family' )}`,
								width: styles.getPropertyValue( 'width' ),
								height: styles.getPropertyValue( 'height' ),
								color: styles.getPropertyValue( 'color' ),
							} );
							break;
					}
				}

				// Add listener.
				document.body.addEventListener( 'click', function( e ) {
					var popupToggle  = ( e.target.classList.contains( 'vamtam-popup-toggle' ) ? e.target : false ) || e.target.closest( '.vamtam-popup-toggle' ),
						activeClones = document.querySelectorAll( '.vamtam-popup-toggle-clone.is-active' ),
						isClone      = popupToggle && popupToggle.classList.contains( 'vamtam-popup-toggle-clone' );

					const enableActivePopupClone = () => {
						let isActionLinkOrInisdeActionLink = false,
							href = $( e.target ).attr( 'href' );
						if ( href ) {
							isActionLinkOrInisdeActionLink = href.startsWith( '%23elementor-action' ) || href.startsWith( '#elementor-action' );
						} else {
							isActionLinkOrInisdeActionLink = e.target.closest( 'a[href^="%23elementor-action"], a[href^="#elementor-action"]' );
						}
						if ( isActionLinkOrInisdeActionLink ) {
							onClickHandler( e, popupToggle );
						}
					};

					const maybeDisablePopupClones = () => {
						activeClones.forEach( function( clone ) {
							onClickHandler( e, clone, true );
						} );
					};

					// The idea here is to prevent closing the popup, if the clone
					// is not yet clickable, although can't find a way to unhook
					// elementor's popup close action and thus our implementation
					// gets out of sync with theirs (we consider the popup active while it's been closed).
					// if ( popupToggle ) {
					// 	if ( isClone ) {
					// 		const isClickable = popupToggle.classList.contains( 'is-clickable' );
					// 		if ( ! isClickable ) {
					// 			e.preventDefault();
					// 			return;
					// 		}
					// 	}
					// }

					if ( popupToggle && ! isClone && ! activeClones.length ) {
						enableActivePopupClone();
					} else if ( activeClones.length ) {
						maybeDisablePopupClones();
						if ( popupToggle && ! isClone ) {
							enableActivePopupClone();
						}
					}
				} );
				// Add listener.
				document.body.addEventListener( 'keyup', function( e ) {
					if ( e.key === 'Escape' ) {
						var activeClones = document.querySelectorAll( '.vamtam-popup-toggle-clone.is-active' );
						activeClones.forEach( function( clone ) {
							onClickHandler( e, clone, true );
						} );
					}
				} );
				// Add listener.
				window.addEventListener( 'resize', window.VAMTAM.debounce( onResizeHandler, 100 ), false );
			},
		},
		// Handles Popup-related stuff.
		VamtamPopupsHandler: {
			init: function () {
				this.disablePopupAutoFocus();
				this.enablePageTransitionsForPopupLinks();
			},
			disablePopupAutoFocus: function () {
				// Update to elementor pro 3.11.2 results in accidentally focused links within popups.
				elementorFrontend.elements.$window.on( 'elementor/popup/show elementor/popup/hide', ( event ) => {
					setTimeout( () => { // For popups with entry animations.
						if ( document.activeElement.nodeName !== 'input' ) {
							document.activeElement.blur();
						}
					}, 100 );
				} );
			},
			enablePageTransitionsForPopupLinks() {
				if ( ! elementorFrontend.config.experimentalFeatures['page-transitions'] ) {
					return;
				}

				let $pageTransitionEl = jQuery('e-page-transition');

				if ( $pageTransitionEl.length ) {
					$pageTransitionEl = $pageTransitionEl[0];
				} else {
					return;
				}

				const triggers = $pageTransitionEl.getAttribute('triggers');

				if ( triggers ) {
					return; // User has selected custom triggers for the page transition.
				}

			const selector = '.elementor-popup-modal a:not( [data-elementor-open-lightbox="yes"] )',
					onLinkClickHandler = $pageTransitionEl.onLinkClick;

				if ( ! onLinkClickHandler ) {
					return;
				}

				// Re-route click event to PageTransitions onClick handler.
				jQuery( document ).on( 'click', selector, onLinkClickHandler.bind( $pageTransitionEl ) );
			}
		},
		VamtamImprovedCSSLoadingHandler: {
			init: () => {
				const widgetData = { ...VAMTAM_FRONT.widgets_assets_data }; // Added from server.


				if ( ! widgetData ) {
					return; // Nothing to do.
				} else {
					// So the paths are not displayed.
					$( '#vamtam-all-js-after' ).remove(); // Could be problematic if we need to add more inline stuff (wp_add_inline_script) to "vamtam-all" script.
					delete( VAMTAM_FRONT.widgets_assets_data );
				}

				let out = '';
				Object.keys(widgetData).forEach( widget => {
					const content = widgetData[ widget ][ 'content' ],
						widgetSelector = `.elementor-widget-${widget} > .elementor-widget-container`;

					if ( content ) {
						if ( $( `${widgetSelector} > style, ${widgetSelector} > link`).length ) {
							return; // Inline style has been added for widget.
						}

						// Add to fallback.
						out = out + '\r\n' + content;
					}
				});

				// Add fallback inline CSS to DOM.
				if ( out ) {
					$( 'body' ).append( `<div id="vamtam-css-opt-fallback">${out}</div>` );
				}
			}
		},
		VamtamWidgetsHandler: {
			// Fixes an issue where sticky elements that should stay inside their parent, outgrow their parent when it's dimensions change.
			stickyInsideParentFix: () => {
				const stickyInsideElements = document.querySelectorAll( `.elementor-sticky[data-settings*='"sticky_parent":"yes"']` );

				if ( ! stickyInsideElements.length ) {
					return;
				}

				stickyInsideElements.forEach( ( element ) => {
					window.VAMTAM_FRONT.WidgetsResizeObserver.observe( element, function() {
						window.VAMTAM.debounce( window.dispatchEvent( new Event( 'resize' ) ) , 500 );
					} );
				} );
			}
		},
		WidgetsObserver: {
			observer: null,

			targetsMap: new Map(),

			init() {
				if ( !this.observer ) {
					this.observer = new IntersectionObserver( this.handleIntersect.bind( this ), {
						root: null,
						threshold: 0,
						rootMargin: '0px',
					} );
				}
			},

			observe( target, onIntersect, options = { once: true } ) {
				this.init(); // Ensure the observer is initialized

				if ( !this.targetsMap.has( target ) ) {
					this.targetsMap.set( target, {
						onIntersect,
						options
					} );
					this.observer.observe( target );
				}
			},

			unobserve( target ) {
				if ( this.targetsMap.has( target ) ) {
					this.targetsMap.delete( target );
					this.observer.unobserve( target );
				}
			},

			handleIntersect( entries ) {
				entries.forEach( entry => {
					if ( this.targetsMap.has( entry.target ) ) {
						const { onIntersect, options } = this.targetsMap.get( entry.target );
						if ( entry.isIntersecting ) {
							onIntersect(); // Execute the stored callback

							// Unobserve the target if the 'once' option is set to true
							if ( options.once ) {
								this.unobserve( entry.target );
							}
						}
					}
				} );
			}
		},
		WidgetsResizeObserver: {
			observer: null,

			targetsMap: new Map(),

			init() {
				if ( !this.observer ) {
					this.observer = new ResizeObserver( this.handleResize.bind( this ) );
				}
			},

			observe( target, onElementResize ) {
				this.init(); // Ensure the observer is initialized

				if ( !this.targetsMap.has( target ) ) {
					this.targetsMap.set( target, {
						onElementResize,
					} );
					this.observer.observe( target );
				}
			},

			unobserve( target ) {
				if ( this.targetsMap.has( target ) ) {
					this.targetsMap.delete( target );
					this.observer.unobserve( target );
				}
			},

			handleResize( entries ) {
				entries.forEach( entry => {
					if ( this.targetsMap.has( entry.target ) ) {
						const { onElementResize } = this.targetsMap.get( entry.target );
						onElementResize(); // Execute the stored callback
					}
				} );
			}
		}
	}

	window.VAMTAM_FRONT.elementor.urlActions = VAMTAM_ELEMENTOR.VamtamActionLinksHandler.utils;
	window.VAMTAM_FRONT.WidgetsObserver = VAMTAM_ELEMENTOR.WidgetsObserver;
	window.VAMTAM_FRONT.WidgetsResizeObserver = VAMTAM_ELEMENTOR.WidgetsResizeObserver;

	$( document ).ready( function() {
		VAMTAM_ELEMENTOR.domLoaded();
	} );

	$( window ).on( 'load', function () {
		VAMTAM_ELEMENTOR.pageLoaded();
	} );
})( jQuery );
