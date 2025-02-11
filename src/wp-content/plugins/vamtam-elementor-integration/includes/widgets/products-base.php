<?php
namespace VamtamElementor\Widgets\ProductsBase;

// Extending the Products_Base widget class (related/archives/normal products widgets).

// Is WC Widget.
if ( ! vamtam_has_woocommerce() ) {
	return;
}

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

function update_controls_style_tab_products_section( $controls_manager, $widget ) {
	// Image Spacing.
	\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'image_spacing', [
		'selectors' => [
			'{{WRAPPER}} .elementor-widget-container ul.products li.product .vamtam-product-content .woocommerce-loop-product__title' => 'padding-top: {{SIZE}}{{UNIT}}',
		]
	] );
	$new_options = [
		'selectors' => [
			'{{WRAPPER}}.elementor-wc-products .added_to_cart' => '{{_RESET_}}',
		]
	];
	$new_hover_options = [
		'selectors' => [
			'{{WRAPPER}}.elementor-wc-products .added_to_cart:hover' => '{{_RESET_}}',
		]
	];
	// Btn Text Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_text_color', $new_options );
	// Btn Hover Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_hover_color', $new_hover_options );
	// Btn Bg Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_background_color', $new_options );
	// Btn Bg Hover Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_hover_background_color', $new_hover_options );
	// Btn Border Hover Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_hover_border_color', $new_hover_options );
	// Button Border.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_border', $new_options , \Elementor\Group_Control_Border::get_type() );
	// Button Typography.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_typography', $new_options, \Elementor\Group_Control_Typography::get_type() );
	// Button Text Padding.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_text_padding', $new_options );
	// Button Border Radius.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_border_radius', $new_options );
	// Button Spacing.
	\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'button_spacing', [
		'selectors' => [
			'{{WRAPPER}} .elementor-widget-container ul.products li.product .vamtam-product-content' => 'padding-bottom: {{SIZE}}{{UNIT}}',
		],
	] );

	// Increase specificity of View cart selectors so they override the Button ones, if needed.
	$new_options = [
		'selectors' => [
			'{{WRAPPER}}.elementor-wc-products .products .product .added_to_cart' => '{{_RESET_}}',
		]
	];
	// View Cart Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'view_cart_color', $new_options );
	// View Cart Typography.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'view_cart_typography', $new_options, \Elementor\Group_Control_Typography::get_type() );
}

function add_controls_style_tab_products_section( $controls_manager, $widget ) {
	add_content_controls( $controls_manager, $widget );
	add_title_min_height_controls( $controls_manager, $widget );
}

function add_content_controls( $controls_manager, $widget ) {
	$widget->add_control(
		'heading_content',
		[
			'label' => __( 'Content', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::HEADING,
			'separator' => 'before',
		]
	);
	$widget->start_controls_tabs( 'content_style_tabs' );
	$widget->start_controls_tab( 'content_style_normal',
		[
			'label' => __( 'Normal', 'vamtam-elementor-integration' ),
		]
	);
	$widget->add_control(
		'content_bg_color',
		[
			'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::COLOR,
			'default' => '',
			'selectors' => [
				'{{WRAPPER}} .elementor-widget-container ul.products li.product .vamtam-product-content' => 'background-color: {{VALUE}};',
			],
		]
	);
	$widget->end_controls_tab();
	$widget->start_controls_tab( 'content_style_hover',
		[
			'label' => __( 'Hover', 'vamtam-elementor-integration' ),
		]
	);
	$widget->add_control(
		'content_bg_color_hover',
		[
			'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::COLOR,
			'default' => '',
			'selectors' => [
				'{{WRAPPER}} .elementor-widget-container ul.products li.product .vamtam-product-content:hover' => 'background-color: {{VALUE}};',
			],
		]
	);
	$widget->end_controls_tab();
	$widget->end_controls_tabs();
}

function add_title_min_height_controls( $controls_manager, $widget ) {
	$widget->start_injection( [
		'of' => 'title_spacing',
	] );
	// Use Title Min-Height.
	$widget->add_control(
		'has_title_min_height',
		[
			'label' => __( 'Use Title Min Height', 'vamtam-elementor-integration' ),
			'description' => __( 'Use this option to equalize any differences caused by inconsistent title names.', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::SWITCHER,
			'prefix_class' => 'vamtam-has-',
			'return_value' => 'title-min-height',
		]
	);
	// Title Min-Height.
	$widget->add_responsive_control(
		'title_min_height',
		[
			'label' => __( 'Min Height', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::SLIDER,
			'size_units' => [ 'px' ],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .elementor-widget-container ul.products li.product .vamtam-product-content .woocommerce-loop-product__title' => 'min-height: {{SIZE}}{{UNIT}}',
			],
			'condition' => [
				'has_title_min_height!' => '',
			],
		]
	);
	$widget->end_injection();
}

if ( vamtam_theme_supports( 'general-products--use-theme-style' ) ) {
	function add_use_theme_layout_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'wc_style_warning',
			'at' => 'before',
		] );

		// Use theme layout for products.
		$widget->add_control(
			'use_products_theme_style',
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
}

if ( vamtam_theme_supports( 'products-base--bijoux-products-layout' ) ) {
	function update_controls_style_tab_products_section_bijoux_layout( $controls_manager, $widget ) {
		// Image Spacing.
		\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'image_spacing', [
			'selectors' => [
				'{{WRAPPER}} .elementor-widget-container ul.products li.product .vamtam-product-content' => 'padding-top: {{SIZE}}{{UNIT}}',
			]
		] );
	}

	function add_controls_style_tab_products_section_bijoux_layout( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'heading_rating_style',
			'at' => 'before',
		] );

		$widget->add_control(
			'heading_meta_style',
			[
				'label' => __( 'Meta', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::HEADING,
				'separator' => 'before',
			]
		);

		$widget->add_control(
			'meta_color',
			[
				'label' => __( 'Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}}.elementor-wc-products ul.products li.product .vamtam-product-tags' => 'color: {{VALUE}}',
					'{{WRAPPER}}.elementor-wc-products ul.products li.product .vamtam-product-tags a' => 'color: {{VALUE}}',
				],
			]
		);

		$widget->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'meta_typography',
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}}.elementor-wc-products ul.products li.product .vamtam-product-tags',
			]
		);

		$widget->add_responsive_control(
			'meta_spacing',
			[
				'label' => __( 'Spacing', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 5,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.elementor-wc-products ul.products li.product .vamtam-product-tags' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$widget->end_injection();
	}

	function remove_controls_style_tab_products_section_bijoux_layout( $controls_manager, $widget ) {
		// Alignment.
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'align' );

		// Button Controls.
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'heading_button_style' );
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'tabs_button_style' );
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'tab_button_normal' );
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'tab_button_hover' );
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'button_text_color' );
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'button_hover_color' );
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'button_background_color' );
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'button_hover_background_color' );
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'button_border_color' );
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'button_hover_border_color' );
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'button_border_radius' );
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'button_spacing' );
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'button_text_padding' );
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'button_typography', \Elementor\Group_Control_Typography::get_type() );
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'button_border', \Elementor\Group_Control_Border::get_type() );

		// View Cart Controls.
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'heading_view_cart_style' );
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'view_cart_color' );
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'view_cart_typography', \Elementor\Group_Control_Typography::get_type() );

	}
}

if ( vamtam_theme_supports( 'products-base--product-image-anims' ) ) {
	function add_controls_style_tab_products_section_image_anims( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'heading_image_style',
		] );

		// Use product image anims.
		$widget->add_control(
			'has_product_image_anims',
			[
				'label' => __( 'Animate Product Image', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'product-image-anims',
				'render_type' => 'template',
			]
		);

		$widget->add_responsive_control(
			'product_image_animation',
			[
				'label' => __( 'Entrance Animation', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::ANIMATION,
				'frontend_available' => true,
				'condition' => [
					'has_product_image_anims!' => '',
				],
			]
		);

		$widget->add_control(
			'product_image_hover_animation',
			[
				'label' => __( 'Hover Animation', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::HOVER_ANIMATION,
				'frontend_available' => true,
				'condition' => [
					'has_product_image_anims!' => '',
				],
			]
		);

		$widget->end_injection();
	}
}

if ( vamtam_theme_supports( 'products-base--product-image-theme-anim' ) ) {
	function add_controls_style_tab_products_section_image_theme_anim_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'heading_image_style',
		] );
		// Use product image theme anim.
		$widget->add_control(
			'use_product_image_theme_anim',
			[
				'label' => __( 'Use Theme Animation', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'product-image-theme-anim',
				'default' => 'product-image-theme-anim',
			]
		);
		$widget->end_injection();
	}
}

if ( vamtam_theme_supports( 'products-base--product-btn-fitness-style' ) ) {
	function add_controls_style_tab_products_section_btn_theme_style_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'heading_button_style',
		] );
		// Use product btn theme style.
		$widget->add_control(
			'use_product_btn_theme_style',
			[
				'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'product-btn-theme-style',
				'default' => 'product-btn-theme-style',
			]
		);
		$widget->end_injection();
	}
}

// Products Button section (add_to_cart, view_cart).
function section_products_style_before_section_end( $widget, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	update_controls_style_tab_products_section( $controls_manager, $widget );
	add_controls_style_tab_products_section( $controls_manager, $widget );

	if ( vamtam_theme_supports( 'general-products--use-theme-style' ) ) {
		add_use_theme_layout_control( $controls_manager, $widget );
	}

	if ( vamtam_theme_supports( 'products-base--bijoux-products-layout' ) ) {
		update_controls_style_tab_products_section_bijoux_layout( $controls_manager, $widget );
		add_controls_style_tab_products_section_bijoux_layout( $controls_manager, $widget );
		remove_controls_style_tab_products_section_bijoux_layout( $controls_manager, $widget );
	}
	if ( vamtam_theme_supports( 'products-base--product-image-anims' ) ) {
		add_controls_style_tab_products_section_image_anims( $controls_manager, $widget );
	}
	if ( vamtam_theme_supports( 'products-base--product-image-theme-anim' ) ) {
		add_controls_style_tab_products_section_image_theme_anim_control( $controls_manager, $widget );
	}
	if ( vamtam_theme_supports( 'products-base--product-btn-fitness-style' ) ) {
		add_controls_style_tab_products_section_btn_theme_style_control( $controls_manager, $widget );
	}
}
add_action( 'elementor/element/woocommerce-product-related/section_products_style/before_section_end', __NAMESPACE__ . '\section_products_style_before_section_end', 10, 2 );
add_action( 'elementor/element/woocommerce-product-upsell/section_products_style/before_section_end', __NAMESPACE__ . '\section_products_style_before_section_end', 10, 2 );
add_action( 'elementor/element/wc-archive-products/section_products_style/before_section_end', __NAMESPACE__ . '\section_products_style_before_section_end', 10, 2 );
add_action( 'elementor/element/woocommerce-products/section_products_style/before_section_end', __NAMESPACE__ . '\section_products_style_before_section_end', 10, 2 );

function update_controls_style_tab_pagination_section( $controls_manager, $widget ) {
	$new_options = [
		'selectors' => [
			'{{WRAPPER}} .navigation.vamtam-pagination-wrapper' => '{{_RESET_}}',
		]
	];
	// Pagination Spacing.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_spacing', $new_options );
	// Pagination Typography.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_typography', $new_options, \Elementor\Group_Control_Typography::get_type() );
	// Pagination Padding.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_padding', [
		'selectors' => [
			'{{WRAPPER}} .navigation.vamtam-pagination-wrapper .page-numbers' => 'line-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
		]
	] );
	$new_options = [
		'selectors' => [
			'{{WRAPPER}} .navigation.vamtam-pagination-wrapper .page-numbers' => '{{_RESET_}}',
		]
	];
	// Pagination Border Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_border_color', $new_options );
	// Pagination Link Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_link_color', $new_options );
	// Pagination Link Bg Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_link_bg_color', $new_options );
	$new_options = [
		'selectors' => [
			'{{WRAPPER}} .navigation.vamtam-pagination-wrapper .page-numbers:hover' => '{{_RESET_}}',
		]
	];
	// Pagination Link Hover Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_link_color_hover', $new_options );
	// Pagination Link Bg Hover Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_link_bg_color_hover', $new_options );
	$new_options = [
		'selectors' => [
			'{{WRAPPER}} .navigation.vamtam-pagination-wrapper .page-numbers.current' => '{{_RESET_}}',
		]
	];
	// Pagination Link Active Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_link_color_active', $new_options );
	// Pagination Link Bg Active Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pagination_link_bg_color_active', $new_options );
}

// Products Archive Pagination section.
function section_pagination_style_before_section_end( $widget, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	update_controls_style_tab_pagination_section( $controls_manager, $widget );
}
add_action( 'elementor/element/wc-archive-products/section_pagination_style/before_section_end', __NAMESPACE__ . '\section_pagination_style_before_section_end', 10, 2 );

// Hide Product Category.
if ( vamtam_theme_supports( 'products-base--hide-categories' ) ) {
	function add_hide_categories_control( $controls_manager, $widget ) {
		$widget->add_control(
			'hide_product_categories',
			[
				'label' => __( 'Hide Product Categories', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'hide-category',
				'render_type' => 'template',
			]
		);
	}
	// Content Section - Before Section End.
	function section_content_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_hide_categories_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/wc-archive-products/section_content/before_section_end', __NAMESPACE__ . '\section_content_before_section_end', 11, 2 );
	add_action( 'elementor/element/woocommerce-product-related/section_related_products_content/before_section_end', __NAMESPACE__ . '\section_content_before_section_end', 11, 2 );
	add_action( 'elementor/element/woocommerce-product-upsell/section_upsell_content/before_section_end', __NAMESPACE__ . '\section_content_before_section_end', 11, 2 );
	add_action( 'elementor/element/woocommerce-products/section_content/before_section_end', __NAMESPACE__ . '\section_content_before_section_end', 11, 2 );
}
