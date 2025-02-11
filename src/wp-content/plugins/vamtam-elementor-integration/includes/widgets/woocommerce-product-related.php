<?php
namespace VamtamElementor\Widgets\RelatedProducts;

use \ElementorPro\Modules\Woocommerce\Widgets\Product_Related as Elementor_Product_Related;
// Extending the WC Related Products widget.

// Is WC Widget.
if ( ! vamtam_has_woocommerce() ) {
	return;
}

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

if ( vamtam_theme_supports( 'woocommerce-product-related--fitness-related-style' ) ) {
	function use_theme_btn_style_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'wc_style_warning',
		] );
		$widget->add_control(
			'use_theme_style',
			[
				'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'theme-style',
				'default' => 'theme-style',
			]
		);
		$widget->end_injection();
	}
	function section_products_style_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		use_theme_btn_style_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/woocommerce-product-related/section_products_style/before_section_end', __NAMESPACE__ . '\section_products_style_before_section_end', 10, 2 );
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'products-base--product-image-anims' ) ||
	current_theme_supports( 'vamtam-elementor-widgets', 'products-base--hide-categories' ) ) {
	// Vamtam_Widget_Related_Products.
	function widgets_registered() {
		if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
			return;
		}

		if ( ! class_exists( '\ElementorPro\Modules\Woocommerce\Widgets\Product_Related' ) ) {
			return; // Elementor's autoloader acts weird sometimes.
		}

		class Vamtam_Widget_Related_Products extends Elementor_Product_Related {
			public $extra_depended_scripts = [
				'vamtam-products-base',
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
					'vamtam-products-base',
					VAMTAM_ELEMENTOR_INT_URL . 'assets/js/widgets/products-base/vamtam-products-base' . $suffix . '.js',
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
				$this->vamtam_before_render( $settings );

				wc_set_loop_prop( 'columns', $settings['columns'] );

				parent::render();
				$this->vamtam_after_render( $settings );
			}

			public function vamtam_before_render( $settings ) {
				if ( current_theme_supports( 'vamtam-elementor-widgets', 'products-base--hide-categories' ) ) {
					$hide_product_categories = isset( $settings['hide_product_categories'] ) && ! empty( $settings['hide_product_categories'] );

					if ( $hide_product_categories ) {
						$GLOBALS['vamtam_wc_products_hide_categories'] = true;
					}
				}
			}

			public function vamtam_after_render( $settings ) {
				if ( current_theme_supports( 'vamtam-elementor-widgets', 'products-base--hide-categories' ) ) {
					$hide_product_categories = isset( $settings['hide_product_categories'] ) && ! empty( $settings['hide_product_categories'] );
					if ( $hide_product_categories ) {
						unset( $GLOBALS['vamtam_wc_products_hide_categories'] );
					}
				}
			}
		}

		// Replace current products widget with our extended version.
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets_manager->unregister( 'woocommerce-product-related' );
		$widgets_manager->register( new Vamtam_Widget_Related_Products );
	}
	add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
}

