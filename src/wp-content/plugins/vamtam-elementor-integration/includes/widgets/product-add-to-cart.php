<?php
namespace VamtamElementor\Widgets\WC_Product_Add_To_Cart;

// Extending the WC_Product_Add_To_Cart widget.

use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

// Is WC Widget.
if ( ! vamtam_has_woocommerce() ) {
	return;
}

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

function add_view_cart_section( $controls_manager, $widget ) {
	$widget->start_controls_section(
		'section_atc_view_cart_button_style',
		[
			'label' => __( 'View Cart', 'vamtam-elementor-integration' ),
			'tab' => $controls_manager::TAB_STYLE,
		]
	);

	$widget->add_control(
		'wc_style_warning_vc',
		[
			'type' => $controls_manager::RAW_HTML,
			'raw' => __( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'vamtam-elementor-integration' ),
			'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
		]
	);

	$widget->add_group_control(
		Group_Control_Typography::get_type(),
		[
			'name' => 'vc_button_typography',
			'selector' => '{{WRAPPER}} .cart .added_to_cart',
		]
	);

	$widget->add_group_control(
		Group_Control_Border::get_type(),
		[
			'name' => 'vc_button_border',
			'selector' => '{{WRAPPER}} .cart .added_to_cart',
			'exclude' => [ 'color' ],
		]
	);

	$widget->add_control(
		'vc_button_border_radius',
		[
			'label' => __( 'Border Radius', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::DIMENSIONS,
			'selectors' => [
				'{{WRAPPER}} .cart .added_to_cart' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]
	);

	$widget->add_control(
		'vc_button_padding',
		[
			'label' => __( 'Padding', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::DIMENSIONS,
			'size_units' => [ 'px', 'em' ],
			'selectors' => [
				'{{WRAPPER}} .cart .added_to_cart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]
	);

	$widget->start_controls_tabs( 'vc_button_style_tabs' );

	$widget->start_controls_tab( 'vc_button_style_normal',
		[
			'label' => __( 'Normal', 'vamtam-elementor-integration' ),
		]
	);

	$widget->add_control(
		'vc_button_text_color',
		[
			'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .cart .added_to_cart' => 'color: {{VALUE}}',
			],
		]
	);

	$widget->add_control(
		'vc_button_bg_color',
		[
			'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .cart .added_to_cart' => 'background-color: {{VALUE}}',
			],
		]
	);

	$widget->add_control(
		'vc_button_border_color',
		[
			'label' => __( 'Border Color', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .cart .added_to_cart' => 'border-color: {{VALUE}}',
			],
		]
	);

	$widget->end_controls_tab();

	$widget->start_controls_tab( 'vc_button_style_hover',
		[
			'label' => __( 'Hover', 'vamtam-elementor-integration' ),
		]
	);

	$widget->add_control(
		'vc_button_text_color_hover',
		[
			'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .cart .added_to_cart:hover' => 'color: {{VALUE}}',
			],
		]
	);

	$widget->add_control(
		'vc_button_bg_color_hover',
		[
			'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .cart .added_to_cart:hover' => 'background-color: {{VALUE}}',
			],
		]
	);

	$widget->add_control(
		'vc_button_border_color_hover',
		[
			'label' => __( 'Border Color', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .cart .added_to_cart:hover' => 'border-color: {{VALUE}}',
			],
		]
	);

	$widget->add_control(
		'vc_button_transition',
		[
			'label' => __( 'Transition Duration', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::SLIDER,
			'default' => [
				'size' => 0.2,
			],
			'range' => [
				'px' => [
					'max' => 2,
					'step' => 0.1,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .cart .added_to_cart' => 'transition: all {{SIZE}}s !important',
			],
		]
	);

	$widget->end_controls_tab();

	$widget->end_controls_tabs();

	$widget->end_controls_section();
}

function update_style_button_section_controls( $controls_manager, $widget ) {
	// Button Transition.
	\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'button_transition', [
		'selectors' => [
			'{{WRAPPER}} .cart button' => 'transition: all {{SIZE}}s !important',
		],
	] );
}

if ( vamtam_theme_supports( 'product-add-to-cart--fitness-vc-btn-style' ) ) {
	function add_use_theme_view_cart_btn_style_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'wc_style_warning_vc',
		] );
		// Use product btn theme style.
		$widget->add_control(
			'use_theme_vc_btn_style',
			[
				'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'theme-vc-btn-style',
				'default' => 'theme-vc-btn-style',
			]
		);
		$widget->end_injection();
	}
}

// Style - Button Section (After).
function section_atc_button_style_after_section_end( $widget, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	update_style_button_section_controls( $controls_manager, $widget );
	add_view_cart_section( $controls_manager, $widget );

	if ( vamtam_theme_supports( 'product-add-to-cart--fitness-vc-btn-style' ) ) {
		add_use_theme_view_cart_btn_style_control( $controls_manager, $widget );
	}
}
add_action( 'elementor/element/woocommerce-product-add-to-cart/section_atc_button_style/after_section_end', __NAMESPACE__ . '\section_atc_button_style_after_section_end', 10, 2 );

if ( vamtam_theme_supports( 'product-add-to-cart--bijoux-button-type' ) ) {
	function add_button_style_section_controls( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'alignment',
		] );
		// Btn Type.
		$widget->add_control(
			'button_type',
			[
				'label' => __( 'Type', 'vamtam-elementor-widgets' ),
				'type' => $controls_manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'Default', 'vamtam-elementor-widgets' ),
					'bijoux-alt' => __( 'Bijoux Alt', 'vamtam-elementor-widgets' ),
				],
				'prefix_class' => 'vamtam-has-',
				'render_type' => 'template',
			]
		);
		$widget->end_injection();
		//Line Padding
		$widget->start_injection( [
			'of' => 'button_padding',
		] );
		$widget->add_responsive_control(
			'vamtam_prefix_padding',
			[
				'label' => __( 'Line Padding', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .vamtam-prefix' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'allowed_dimensions' => 'horizontal',
				'condition' => [
					'button_type' => 'bijoux-alt',
				]
			]
		);
		$widget->end_injection();
		$widget->start_injection( [
			'of' => 'button_border_color',
		] );
		// Line Color.
		$widget->add_control(
			'prefix_color',
			[
				'label' => __( 'Line Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .vamtam-prefix::before' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'button_type' => 'bijoux-alt',
				]
			]
		);
		$widget->end_injection();

		$widget->start_injection( [
			'of' => 'button_border_color_hover',
		] );
		// Line Color Hover.
		$widget->add_control(
			'prefix_color_hover',
			[
				'label' => __( 'Line Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .button:hover .vamtam-prefix::before' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'button_type' => 'bijoux-alt',
				]
			]
		);
		$widget->end_injection();
	}
	// Vamtam_Widget_Product_Add_To_Cart.
	function widgets_registered() {
		if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
			return;
		}

		if ( ! class_exists( '\ElementorPro\Modules\Woocommerce\Widgets\Product_Add_To_Cart' ) ) {
			return; // Elementor's autoloader acts weird sometimes.
		}

		class Vamtam_Widget_Product_Add_To_Cart extends \ElementorPro\Modules\Woocommerce\Widgets\Product_Add_To_Cart {
			public $extra_depended_scripts = [
				'vamtam-product-add-to-cart',
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
					'vamtam-product-add-to-cart',
					VAMTAM_ELEMENTOR_INT_URL . 'assets/js/widgets/product-add-to-cart/vamtam-product-add-to-cart' . $suffix . '.js',
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
		}

		// Replace current divider widget with our extended version.
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets_manager->unregister( 'woocommerce-product-add-to-cart' );
		$widgets_manager->register( new Vamtam_Widget_Product_Add_To_Cart );
	}
	add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
}

if ( vamtam_theme_supports( 'product-add-to-cart--fitness-btn-style' ) ) {
	// Called frontend & editor (editor after element loses focus).
	function render_content( $content, $widget ) {
		if ( 'woocommerce-product-add-to-cart' === $widget->get_name() ) {
			$settings = $widget->get_settings();

			if ( ! empty( $settings['use_theme_btn_style'] ) ) {
				$icon = '<i aria-hidden="true" class="vamtamtheme- vamtam-theme-arrow-right"></i>';
				// Inject theme icon.
				$content = preg_replace( '/(<button type="submit" name="add-to-cart"[^>]*>)([^~]*?)(<\/button>)/s', '$1' . $icon . '$2$3', $content );
				// same, but for variable products
				$content = preg_replace( '/(<button type="submit" class="single_add_to_cart_button button alt"[^>]*>)([^~]*?)(<\/button>)/s', '$1' . $icon . '$2$3', $content );
			}
			if ( ! empty( $settings['use_theme_vc_btn_style'] ) ) {
				// Add span text wrap.
				$content = preg_replace( '/(>)([^<]*?)(<\/button>)/s', '$1<span class="vamtam-adc-text">$2</span>$3', $content );
			}
		}

		return $content;
	}
	add_filter( 'elementor/widget/render_content', __NAMESPACE__ . '\render_content', 10, 2 );

	function use_theme_btn_style_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'wc_style_warning',
		] );
		$widget->add_control(
			'use_theme_btn_style',
			[
				'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'theme-btn-style',
				'default' => 'theme-btn-style',
				'render_type' => 'template',
			]
		);
		$widget->end_injection();
	}
}

if ( vamtam_theme_supports( [ 'product-add-to-cart--bijoux-button-type', 'product-add-to-cart--fitness-btn-style' ] ) ) {
	// Style - Buttons section
	function section_atc_button_style_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		if ( vamtam_theme_supports( 'product-add-to-cart--bijoux-button-type' ) ) {
			add_button_style_section_controls( $controls_manager, $widget );
		}
		if ( vamtam_theme_supports( 'product-add-to-cart--fitness-btn-style' ) ) {
			use_theme_btn_style_control( $controls_manager, $widget );
		}
	}
	add_action( 'elementor/element/woocommerce-product-add-to-cart/section_atc_button_style/before_section_end', __NAMESPACE__ . '\section_atc_button_style_before_section_end', 10, 2 );
}

function variations_controls_selector_fixes( $controls_manager, $widget ) {
	// Space Between.
	\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'variations_space_between', [
		'selectors' => [
			'.woocommerce {{WRAPPER}} form.cart table.variations tr:not(:last-child) > td' => 'padding-bottom: {{SIZE}}{{UNIT}}',
		],
	] );
	// Select Background Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'variations_select_bg_color', [
		'selectors' => [
			'.woocommerce {{WRAPPER}} form.cart table.variations td.value select' => 'background-color: {{VALUE}}!important',
		],
	] );
	// Select Border Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'variations_select_border_color', [
		'selectors' => [
			'.woocommerce {{WRAPPER}} form.cart table.variations td.value select' => '{{_RESET_}}',
		],
	] );
	// Select Border Radius.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'variations_select_border_radius', [
		'selectors' => [
			'.woocommerce {{WRAPPER}} form.cart table.variations td.value select' => '{{_RESET_}}',
		],
	] );
}
function use_theme_ajax_handler_for_variations_control( $controls_manager, $widget ) {
	$widget->add_control(
		'disable_theme_ajax_vars',
		[
			'label' => __( 'Disable Theme\'s Ajax Handler', 'vamtam-elementor-integration' ),
			'description' => __( 'Due to the vast amount of Variable Product implementations provided by 3rd party plugins, you can disable the theme\'s Ajax add-to-cart handler for variables, if you experience any problems/collisions (reverts to default WC behavior).', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::SWITCHER,
			'prefix_class' => 'vamtam-has-',
			'return_value' => 'disable-theme-ajax-vars',
			'separator' => 'before',
		]
	);
}
function add_custom_variations_controls( $controls_manager, $widget ) {
	// Variations Price Heading
	$widget->add_control(
		'vamtam_variations_price_heading',
		[
			'label' => esc_html__( 'Price', 'elementor-pro' ),
			'type' => $controls_manager::HEADING,
			'separator' => 'before',
		]
	);

	// Variations Price Color
	$widget->add_control(
		'vamtam_variations_price_color',
		[
			'label' => esc_html__( 'Color', 'elementor-pro' ),
			'type' => $controls_manager::COLOR,
			'selectors' => [
				'.woocommerce {{WRAPPER}} .woocommerce-variation .woocommerce-variation-price .price' => 'color: {{VALUE}}',
			],
		]
	);

	// Variations Price Typography
	$widget->add_group_control(
		Group_Control_Typography::get_type(),
		[
			'name' => 'vamtam_variations_price_typography',
			'selector' => '.woocommerce {{WRAPPER}} .woocommerce-variation .woocommerce-variation-price .price',
		]
	);
}
// Style - Variations Section (Before).
function section_variations_style_before_section_end( $widget, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	variations_controls_selector_fixes( $controls_manager, $widget );
	add_custom_variations_controls( $controls_manager, $widget );
	use_theme_ajax_handler_for_variations_control( $controls_manager, $widget );
}
add_action( 'elementor/element/woocommerce-product-add-to-cart/section_atc_variations_style/before_section_end', __NAMESPACE__ . '\section_variations_style_before_section_end', 10, 2 );

function update_spacing_control( $controls_manager, $widget ) {
	// Spacing.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'spacing', [
		'selectors' => [
			'body:not(.rtl) {{WRAPPER}} .quantity ~ .added_to_cart' => 'margin-left: {{SIZE}}{{UNIT}}',
			'body.rtl {{WRAPPER}} .quantity ~ .added_to_cart' => 'margin-right: {{SIZE}}{{UNIT}}',
		],
	] );
}
// Style - Quantity Section (Before).
function section_atc_quantity_style_before_section_end( $widget, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	update_spacing_control( $controls_manager, $widget );
}
add_action( 'elementor/element/woocommerce-product-add-to-cart/section_atc_quantity_style/before_section_end', __NAMESPACE__ . '\section_atc_quantity_style_before_section_end', 10, 2 );
