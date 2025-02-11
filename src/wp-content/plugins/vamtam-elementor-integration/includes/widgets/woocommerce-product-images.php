<?php
namespace VamtamElementor\Widgets\ProductImages;

use \ElementorPro\Modules\Woocommerce\Widgets\Product_Images as Elementor_Product_Images;
// Extending the WC Product Images widget.

// Is WC Widget.
if ( ! vamtam_has_woocommerce() ) {
	return;
}

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

if ( vamtam_theme_supports( 'woocommerce-product-images--disable-image-link' ) ) {
	function add_disable_image_link_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'sale_flash',
		] );

		$widget->add_control(
			'disable_image_link',
		[
				'label' => __( 'Disable Image Link', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'disable-image-link',
				'description' => __( 'Disables opening the image on a new tab or Elementor lightbox. Doesn\'t disable WC\'s lightbox (if enabled).', 'vamtam-elementor-integration' ),
			]
		);

		$widget->end_injection();
	}
}

if ( vamtam_theme_supports( 'woocommerce-product-images--full-sized-gallery' ) ) {
	function add_full_sized_gallery_controls( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'sale_flash',
		] );

		$widget->add_control(
			'use_full_sized_gallery',
			[
				'label' => __( 'Display as Full Size Gallery', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'full-sized-gallery',
				'render_type' => 'template',
			]
		);

		$widget->add_control(
			'disable_on_mobile_browsers',
			[
				'label' => __( 'Disable On Mobile Browsers', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'default' => 'vamtam-mbrowser-no-fsg',
				'return_value' => 'vamtam-mbrowser-no-fsg',
				'prefix_class' => '',
				'condition' => [
					'use_full_sized_gallery!' => '',
				]
			]
		);

		$widget->add_control(
			'vamtam_image_spacing',
			[
				'label' => __( 'Image Spacing', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce-product-gallery__wrapper,{{WRAPPER}} .woocommerce-product-gallery--vamtam__wrapper' => 'grid-gap: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'use_full_sized_gallery!' => '',
				]
			]
		);

		$widget->end_injection();
	}
	function full_sized_gallery_thumb_size( $sizes ) {
		if ( isset( $GLOBALS['product_images_use_full_sized_gallery'] ) && $GLOBALS['product_images_use_full_sized_gallery'] === true ) {
			return 'woocommerce_single';
		}
		return $sizes;
	}
	add_filter( 'woocommerce_gallery_thumbnail_size', __NAMESPACE__ . '\full_sized_gallery_thumb_size', 100 );
	function full_sized_gallery_thumb_cols( $cols ) {
		if ( isset( $GLOBALS['product_images_use_full_sized_gallery'] ) && $GLOBALS['product_images_use_full_sized_gallery'] === true ) {
			$cols = 1;
		}
		return $cols;
	}
	add_filter( 'woocommerce_product_thumbnails_columns', __NAMESPACE__ . '\full_sized_gallery_thumb_cols', 100 );
	function full_sized_gallery_flex_slider( $boolean ) {
		if ( isset( $GLOBALS['product_images_use_full_sized_gallery'] ) && $GLOBALS['product_images_use_full_sized_gallery'] === true ) {
			return false;
		}
		return $boolean;
	}
	add_filter( 'woocommerce_single_product_flexslider_enabled', __NAMESPACE__ . '\full_sized_gallery_flex_slider', 100 );
}

if ( vamtam_theme_supports( [ 'woocommerce-product-images--disable-image-link', 'woocommerce-product-images--full-sized-gallery'] ) ) {
	// Vamtam_Widget_Product_Images.
	function widgets_registered() {
		if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
			return;
		}

		if ( ! class_exists( '\ElementorPro\Modules\Woocommerce\Widgets\Product_Images' ) ) {
			return; // Elementor's autoloader acts weird sometimes.
		}

		class Vamtam_Widget_Product_Images extends Elementor_Product_Images {
			public $extra_depended_scripts = [
				'vamtam-woocommerce-product-images',
			];

			// Extend constructor.
			public function __construct($data = [], $args = null) {
				parent::__construct($data, $args);

				$this->register_assets();

				$this->add_extra_script_depends();
			}

			// Register the assets the widget depends on.
			public function register_assets() {
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_register_script(
					'vamtam-woocommerce-product-images',
					VAMTAM_ELEMENTOR_INT_URL . 'assets/js/widgets/woocommerce-product-images/vamtam-woocommerce-product-images' . $suffix . '.js',
					[
						'elementor-frontend',
					],
					\VamtamElementorIntregration::PLUGIN_VERSION,
					true
				);
			}

			// Assets the widget depends upon.
			public function add_extra_script_depends() {
				// Scripts
				foreach ( $this->extra_depended_scripts as $script ) {
					$this->add_script_depends( $script );
				}
			}

			// Override.
			public function render() {
				$settings = $this->get_settings_for_display();
				global $product;

				$product = wc_get_product();

				if ( empty( $product ) ) {
					return;
				}

				if ( vamtam_theme_supports( 'woocommerce-product-images--full-sized-gallery' ) ) {
					if ( ! empty( $settings[ 'use_full_sized_gallery' ] ) ) {
						$GLOBALS['product_images_use_full_sized_gallery'] = true;
					}

					if ( wp_is_mobile() && ! empty( $settings[ 'disable_on_mobile_browsers' ] ) ) {
						unset( $GLOBALS['product_images_use_full_sized_gallery'] );
						$this->add_render_attribute( '_wrapper', 'class', 'vamtam-mobile-gallery' );
					}
				}

				if ( 'yes' === $settings['sale_flash'] ) {
					wc_get_template( 'loop/sale-flash.php' );
				}

				wc_get_template( 'single-product/product-image.php' );

				if ( vamtam_theme_supports( 'woocommerce-product-images--full-sized-gallery' ) ) {
					if ( isset( $settings[ 'use_full_sized_gallery' ] ) && ! empty( $settings[ 'use_full_sized_gallery' ] ) ) {
						unset( $GLOBALS['product_images_use_full_sized_gallery'] );

						// That's a hack to not allow WC's flexslider to be applied for this gallery.
						// We fix this on the widget's js handler onInit.
						?>
						<script>
						jQuery( document ).ready( function () {
							const $fsGalls = jQuery( '.vamtam-has-full-sized-gallery' );
							jQuery.each( $fsGalls, function ( i, widget ) {
								const $galEl = jQuery( widget ).find( 'div.woocommerce-product-gallery' ),
									mBrNoFsg = jQuery( widget ).hasClass( 'vamtam-mbrowser-no-fsg' );
								if ( window.VAMTAM.isMobileBrowser && mBrNoFsg ) {
									jQuery( widget ).addClass('vamtam-mobile-gallery');
									return;
								} else {
									$galEl.removeClass( 'woocommerce-product-gallery' ).addClass( 'woocommerce-product-gallery--vamtam' );
								}
							} );
						} );
						</script>
						<?php
					}

				}

				// On render widget from Editor - trigger the init manually.
				if ( wp_doing_ajax() ) {
					?>
					<script>
						setTimeout(() => {
							jQuery( '.woocommerce-product-gallery' ).each( function() {
								jQuery( this ).wc_product_gallery();
							} );
						}, 200);
					</script>
					<?php
				}
			}
		}

		// Replace current products widget with our extended version.
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets_manager->unregister( 'woocommerce-product-images' );
		$widgets_manager->register( new Vamtam_Widget_Product_Images );
	}
	add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
}

function section_product_gallery_style_before_section_end( $widget, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	if ( vamtam_theme_supports( 'woocommerce-product-images--disable-image-link' ) ) {
		add_disable_image_link_control( $controls_manager, $widget );
	}
	if ( vamtam_theme_supports( 'woocommerce-product-images--full-sized-gallery' ) ) {
		add_full_sized_gallery_controls( $controls_manager, $widget );
	}
}
add_action( 'elementor/element/woocommerce-product-images/section_product_gallery_style/before_section_end', __NAMESPACE__ . '\section_product_gallery_style_before_section_end', 10, 2 );
