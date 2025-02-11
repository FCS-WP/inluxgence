( function( $, undefined ) {
	"use strict";

	window.VAMTAM = window.VAMTAM || {}; // Namespace
	window.VAMTAM.CUSTOM_ANIMATIONS = {};

	$( function() {
		window.VAMTAM.CUSTOM_ANIMATIONS = {
			init: function () {
				this.VamtamCustomAnimations.init();
			},
			onDomReady: function () {
				this.VamtamCustomAnimations.scrollBasedAnims();
			},
			// Handles custom animations.
			VamtamCustomAnimations: {
				init: function() {
					this.registerAnimations();
					this.utils.watchScrollDirection();
					// this.observedAnims(); // Disabled in favor of elementorFrontend.waypoint().
				},
				registerAnimations: function () {
					var self = this;

					// Register animations here.
					var animations = [
						'stickyHeader', // Same name as function.
						// Bijoux-only.
						'cursorAnimation', // Same name as function.
					];

					animations.forEach( function( animation ) {
						self[ animation ].apply( self );
					} );
				},
				// A sticky header animation.
				stickyHeader: function () {
					var $target                = $( '.vamtam-sticky-header' ),
						topScrollOffsetTrigger = 300,
						_self                  = this;

					if ( ! $target.length ) {
						return;
					}

					if ( $target.length > 1 ) {
						// There should only be one sticky header.
						$target = $target[ 0 ];
					}

					( function () { // IIFE for closure so $target is available in rAF.
						var prevAnimState,
							isTransparentHeader = $( $target ).hasClass( 'vamtam-sticky-header--transparent-header' ),
							isFrontend          = ! window.elementorFrontend.isEditMode();

						// state: fixed, scrolled up (not visible).
						var fixedHiddenState = function () {
							$( $target ).removeClass( 'vamtam-sticky-header--fixed-shown' );
							if ( ! $( $target ).hasClass( 'vamtam-sticky-header--fixed-hidden' ) ) {
								$( $target ).addClass( 'vamtam-sticky-header--fixed-hidden' );
							}
							prevAnimState = 'fixedHiddenState';
						};

						// state: fixed, scrolled down (visible).
						var fixedShownState = function () {
							$( $target ).removeClass( 'vamtam-sticky-header--fixed-hidden' );
							if ( ! $( $target ).hasClass( 'vamtam-sticky-header--fixed-shown' ) ) {
								$( $target ).addClass( 'vamtam-sticky-header--fixed-shown' );
							}
							prevAnimState = 'fixedShownState';
						};

						// state: no animation.
						var noAnimState = function () {
							$( $target ).removeClass( 'vamtam-sticky-header--fixed-shown' );
							$( $target ).removeClass( 'vamtam-sticky-header--fixed-hidden' );
							prevAnimState = 'noAnimState';
						};

						const headerShouldAnimate = () => {
							// If a link inside the header is being hovered, we don't want to trigger the sticky header.
							if ( $( $target ).find( 'a:hover' ).length ) {
								return false;
							}
							// If a mega-menu belonging to the header is open, we don't want to trigger the sticky header.
							if ( $( '.vamtam-header-mega-menu:visible' ).length ) {
								return false;
							}

							return true;
						};

						// Initial phase

						if ( isFrontend ) {
							$( $target ).after( '<div class="vamtam-prevent-scroll-jumps"></div>' );
						}

						function preventScrollJumps() {
							if ( ! isFrontend ) {
								return;
							}
							// Apply a negative margin to prevent content jumps.
							var stickyHeight = $( $target ).innerHeight();
							$( $target ).css( 'margin-bottom', '-' + stickyHeight + 'px' );
							$( $target ).next( '.vamtam-prevent-scroll-jumps' ).css( 'padding-top', stickyHeight + 'px' );
						}

						if ( window.VAMTAM.isMaxDeviceWidth() ) {
							preventScrollJumps();
						}

						// If passed the trigger point it should always be at fixed hidden state.
						const initialScrollPosCheck = ( pageLoad = false ) => {
							if ( window.pageYOffset >= topScrollOffsetTrigger ) {
								fixedHiddenState();
							} else if ( ! pageLoad ) {
								// Sometimes the browser's onload scroll comes after the initialScrollPosCheck() so we check on page load jic. Happens mostly when initial scroll pos is after middle of page.
								window.addEventListener( 'load', function() {
									if ( ! prevAnimState ) {
										setTimeout( () => {
											initialScrollPosCheck( true );
										}, 10 );
									}
								} );
							}
						};
						initialScrollPosCheck();

						var scrollTimer = null, lastScrollYPause = window.scrollY, lastDirection; // Used to check if the user has scrolled up far enough to trigger the sticky header.
						window.addEventListener( 'scroll', function( e ) {
							if ( scrollTimer !== null ) {
								clearTimeout( scrollTimer );
							}

							// If the user hasn't scrolled for 500ms we use that as the new Y point.
							scrollTimer = setTimeout( function() {
								lastScrollYPause = window.scrollY;
						  	}, 500 );

							var anim = window.VAMTAM.debounce( function() {
								if ( e.target.nodeName === '#document' ) {

									if ( ! headerShouldAnimate() ) {

										// Don't animate, but go to fixedShown state for the transparent header.
										if ( isTransparentHeader ) {
											if ( prevAnimState !== 'fixedShownState' ) {
												fixedShownState();
											}
										}

										return;
									}

									var direction =  _self.utils.getScrollDirection();

									if ( lastDirection !== direction ) {
										lastScrollYPause = window.scrollY;
									}
									lastDirection = direction;

									const scrollDifference = Math.abs( window.scrollY - lastScrollYPause ); // Pixels.
									if ( scrollDifference < 80 && window.scrollY > 80 ) {
										return;
									}

									if ( direction === 'up' ) {
										if ( window.pageYOffset >= topScrollOffsetTrigger ) {
											if ( prevAnimState !== 'fixedShownState' ) {
												fixedShownState();
											}
										} else {
											if ( prevAnimState !== 'noAnimState' ) {
												noAnimState();
											}
										}
										return;
									}

									if ( direction === 'down' ) {
										if ( window.pageYOffset >= topScrollOffsetTrigger || isTransparentHeader ) { // Transparent header gets hidden right away.
											// Safe-guard for times when the opening of the cart can cause a scroll down and hide the menu (also sliding the cart upwards).
											var menuCardNotVisible = ! $( $target ).find( '.elementor-menu-cart--shown' ).length;
											if ( prevAnimState !== 'fixedHiddenState' && menuCardNotVisible ) {
												fixedHiddenState();
											}
										}
									}
								}
							}, 200 );

							if ( window.VAMTAM.isMaxDeviceWidth() ) {
								preventScrollJumps();
								requestAnimationFrame( anim );
							} else if ( prevAnimState !== 'noAnimState' ) {
								noAnimState();
							}
						}, { passive: true } );
					} )();
				},
				// Attaches observers to required anims and fires (adds vamtam-animate class & triggers vamtam:animate event)
				// them only when they are visible.
				observedAnims: function() {
					var observeClass    = 'vamtam-observe',
						animClass       = 'vamtam-animate',
						loopedAnimClass = 'vamtam-looped', // Whenever the el toggles to visible the anim will fire.
						animEls         = document.querySelectorAll( '.' + observeClass );

					if ( ! animEls.length ) {
						return;
					}

					var observer;

					var cb = function( iOEntries, observer ) {
						iOEntries.forEach( function( entry ) {
							var isVisible = false,
								el        = entry.target,
								$el       = el && $( el );

							if ( entry.isIntersecting ) {
								isVisible = true;
							}

							if ( isVisible ) {
								if ( ! $el.hasClass( animClass ) ) {
									$el.addClass( animClass );
									$el.trigger('vamtam:animate');
								}

								if ( ! $el.hasClass( loopedAnimClass ) ) {
									// If not looped, stop observing (anim fires only once).
									observer && observer.unobserve && observer.unobserve( el );
								}
							} else {
								if ( $el.hasClass( animClass ) ) {
									$el.removeClass( animClass );
								}
							}
						} );
					};

					animEls.forEach( function( el ) {
						var $el = $( el );
						$el.removeClass( animClass );

						if ( ! observer ) {
							observer = new IntersectionObserver( cb );
						}

						observer.observe( el );
					} );
				},
				// Scroll-based anims.
				scrollBasedAnims: function() {
					var scrollAnims = [
						'[data-settings*="growFromLeftScroll"]',
						'[data-settings*="growFromRightScroll"]',
					];

					var animEls = document.querySelectorAll( scrollAnims.join( ', ' ) );

					if ( ! animEls.length ) {
						return;
					}

					var observer, entries = {}, _this = this;

					var cb = function( iOEntries ) {
						iOEntries.forEach( function( entry ) {
							var currentScrollY       = entry.boundingClientRect.y,
								isInViewport         = entry.isIntersecting,
								observedEl           = entry.target,
								scrollPercentage     = Math.abs( parseFloat( ( entry.intersectionRatio * 100 ).toFixed( 2 ) ) ),
								prevScrollPercentage = entries[ observedEl.dataset.vamtam_anim_id ].lastScrollPercentage,
								lastScrollY          = entries[ observedEl.dataset.vamtam_anim_id ].lastScrollY,
								animateEl            = entries[ observedEl.dataset.vamtam_anim_id ].animateEl;

							var animate = function () {
								window.requestAnimationFrame( function() {
									animateEl.style.setProperty( '--vamtam-scroll-ratio', scrollPercentage + '%' );
								} );
							};

							if ( isInViewport && lastScrollY !== currentScrollY ) {
								if( _this.utils.getScrollDirection() === 'down') {
									if ( prevScrollPercentage < scrollPercentage ) {
										animate();
									}
								} else {
									animate();
								}
							}

							entries[ observedEl.dataset.vamtam_anim_id ].lastScrollY          = currentScrollY;
							entries[ observedEl.dataset.vamtam_anim_id ].lastScrollPercentage = scrollPercentage;
						} );
					};

					var buildThresholdList = function() {
						var thresholds = [],
							numSteps   = 50,
							i;

						for ( i = 1.0; i <= numSteps; i++ ) {
							var ratio = i / numSteps;
							thresholds.push( ratio );
						}

						thresholds.push( 0 );
						return thresholds;
					};

					const thresholds = buildThresholdList();

					animEls.forEach( function( el ) {
						if ( ! observer ) {
							var options = {
								root: null,
								rootMargin: "20% 0% 20% 0%",
								threshold: thresholds,
							};
							observer = new IntersectionObserver( cb, options );
						}

						// Init.
						el.style.setProperty( '--vamtam-scroll-ratio', '1%' );

						var observeEl;
						if ( el.classList.contains( 'elementor-widget' ) || el.classList.contains( 'elementor-column' ) ) {
							// For widgets we observe .elementor-widget-wrap
							// For columns we observe .elementor-row
							observeEl = el.parentElement;
							observeEl.setAttribute('data-vamtam_anim_id', el.dataset.id );
						} else {
							// Sections.
							// Add scroll anim wrapper.
							$( el ).before( '<div class="vamtam-scroll-anim-wrap" data-vamtam_anim_id="' + el.dataset.id + '"></div>' );
							var $wrap = $( el ).prev( '.vamtam-scroll-anim-wrap' );
							$( $wrap ).append( el );
							observeEl = $wrap[ 0 ];
						}

						entries[el.dataset.id] = {
							lastScrollY: '',
							lastScrollPercentage: '',
							observeEl: observeEl,
							animateEl: el,
						};

						observer.observe( observeEl );
					} );
				},
				// Bijoux-only.
				cursorAnimation: function() {
					const isEditor = window.elementorFrontend.isEditMode();

					if ( isEditor ||
						this.utils.isTouchDevice() ||
						$( 'body' ).filter( '.no-theme-cursor, .no-bijoux-cursor' ).length ||
						! ( 'elementorFrontendConfig' in window ) ||
						! window.elementorFrontendConfig.kit.vamtam_theme_has_theme_cursor )
					{
						return;
					}

					$( 'body' )
						.append( '<span id="mouseCircle" class="mouse-circle"></span>' )
						.addClass( 'has-mouse-circle' )
						.append( '<span id="mouseDot" class="mouse-dot"></span>' )
						.addClass( 'has-mouse-dot' );

					var $mouseCircle = $( '#mouseCircle' ),
						$mouseDot = $( '#mouseDot' ),
						mouseX = 0, mouseY = 0,
						xp = 0, yp = 0,
						timer;

					$( document ).on( 'mousemove mouseenter', function( e ) {
						clearTimeout(timer);

						if ( window.VAMTAM.isBelowMaxDeviceWidth() ) {
							return;
						}

						if ( ! $mouseCircle.hasClass( 'moving' ) ) {
							$mouseCircle.addClass( 'moving' );
						}

						mouseX = e.clientX;
						mouseY = e.clientY;

						// Position dot.
						$mouseDot.css( {
							transform: 'translate(' + mouseX +'px, ' + mouseY + 'px)',
						} );

						timer = setTimeout(function() {
							$mouseCircle.removeClass( 'moving' );
						}, 500 );
					} );

					// Position circle.
					setInterval( function() {
						xp += ( ( mouseX - xp ) / 6 );
						yp += ( ( mouseY - yp ) / 6 );
						$mouseCircle.css( {
							transform: 'translate(' + xp + 'px, ' + yp + 'px)',
						} );
					}, 20 );

					// Handler for links.
					var onLinkHover = function( e ) {
						let hoveringOverLink;

						if ( e.target.href ) {
							hoveringOverLink = true;
						} else {
							// Check if it's wrapped by a link (common-case).
							if ( $( e.target ).parent( 'a[href]' ).length ) {
								hoveringOverLink = true;
							}

							// < -- Custom Cases -- >
							// Button.
							if ( $( e.target ).closest( 'a.elementor-button-link[href]' ).length ) {
								hoveringOverLink = true;
							}
						}

						if ( hoveringOverLink ) {
							if ( ! $mouseCircle.hasClass( 'small' ) ) {
								window.requestAnimationFrame( function() {
									$mouseCircle.addClass( 'small' );
								} );
							}
						} else {
							if ( $mouseCircle.hasClass( 'small' ) ) {
								window.requestAnimationFrame( function() {
									$mouseCircle.removeClass( 'small' );
								} );
							}
						}
					};
					$( document ).on( 'mouseover', window.VAMTAM.debounce( onLinkHover, 100 ) );
				},
				// Common funcs used in custom animations.
				utils: {
					getAdminBarHeight: function () {
						return window.VAMTAM.adminBarHeight;
					},
					watchScrollDirection: function () {
						var watcher = function () {
							this.lastScrollTop = 0;
							this.utils = this;
							return {
								init: function () {
								},
								measure: function ( cpos ) {
									this.direction = cpos > this.lastScrollTop ? 'down' : 'up';
								}.bind( this ),
								mutate: function ( cpos ) {
									this.utils.getScrollDirection = function () {
										return this.direction;
									};
									this.lastScrollTop = cpos <= 0 ? 0 : cpos; // For Mobile or negative scrolling
								}.bind( this ),
							};
						}.bind( this );

						window.VAMTAM.addScrollHandler( watcher() );
					},
					isTouchDevice: function() {
						const prefixes = ' -webkit- -moz- -o- -ms- '.split( ' ' );

						const mq = function( query ) {
							return window.matchMedia( query ).matches;
						};

						if ( ( 'ontouchstart' in window ) || window.DocumentTouch && document instanceof DocumentTouch ) { // jshint ignore:line
							return true;
						}

						// include the 'heartz' as a way to have a non matching MQ to help terminate the join
						// https://git.io/vznFH
						var query = [ '(', prefixes.join( 'touch-enabled),(' ), 'heartz', ')' ].join( '' );

						return mq( query );
					},
				}
			},
		};

		window.VAMTAM.CUSTOM_ANIMATIONS.init();

		$( window ).ready( function () {
			window.VAMTAM.CUSTOM_ANIMATIONS.onDomReady();
		} );
	});
})( jQuery );
