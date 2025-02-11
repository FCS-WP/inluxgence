<?php
namespace VamtamElementor\Kits\Kit;

// Extending Elementor's Theme Styles (Kit).
function update_form_field_controls( $controls_manager, $kit ) {
	// Typography.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'form_field_typography', [
			'selectors' => [
				implode( ',', [
					'{{WRAPPER}} select',
					'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select',
					'{{WRAPPER}} .select2.select2-container .selection > .select2-selection[role="combobox"]',
				] ) => '{{_RESET_}}',
			],
		],
		\Elementor\Group_Control_Typography::get_type()
	);
	// Text Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'form_field_text_color', [
		'selectors' => [
			implode( ',', [
				'{{WRAPPER}} select',
				'{{WRAPPER}} .select2.select2-container .select2-selection[role="combobox"]',
				'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select',
				'{{WRAPPER}} .elementor-select-wrapper',
				'{{WRAPPER}} input:not([type="button"]):not([type="submit"])',
				'{{WRAPPER}} input:not([type="button"]):not([type="submit"])::placeholder',
				'{{WRAPPER}} textarea',
				'{{WRAPPER}} textarea::placeholder',
				'{{WRAPPER}} .elementor-field-textual',
				'{{WRAPPER}} .elementor-field-textual::placeholder',
			] ) => 'color: {{VALUE}}; caret-color: {{VALUE}};',
		],
	] );
	// Text Focus Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'form_field_focus_text_color', [
		'selectors' => [
			implode( ',', [
				'{{WRAPPER}} select:focus',
				'{{WRAPPER}} .select2.select2-container .select2-selection[role="combobox"]:focus',
				'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select:focus',
				'{{WRAPPER}} .elementor-select-wrapper:focus-within',
				'{{WRAPPER}} input:focus:not([type="button"]):not([type="submit"])',
				'{{WRAPPER}} input:focus:not([type="button"]):not([type="submit"])::placeholder',
				'{{WRAPPER}} textarea:focus',
				'{{WRAPPER}} textarea:focus::placeholder',
				'{{WRAPPER}} .elementor-field-textual:focus',
				'{{WRAPPER}} .elementor-field-textual:focus::placeholder',
			] ) => 'color: {{VALUE}}; caret-color: {{VALUE}};',
		],
	] );
	// Bg Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'form_field_background_color', [
		'selectors' => [
			implode( ',', [
				'{{WRAPPER}} select',
				'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select',
				'{{WRAPPER}} .select2.select2-container .select2-selection[role="combobox"]',
				'{{WRAPPER}} input[type="checkbox"] + label::before',
			] )	=> 'background-color: {{VALUE}} !important',
		],
	] );
	// Bg Focus Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'form_field_focus_background_color', [
		'selectors' => [
			implode( ',', [
				'{{WRAPPER}} select:focus',
				'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select:focus',
				'{{WRAPPER}} .select2.select2-container .select2-selection[role="combobox"]:focus',
				'{{WRAPPER}} input[type="checkbox"]:focus + label::before',
			] ) => 'background-color: {{VALUE}} !important',
		],
	] );
	// Border Radius.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'form_field_border_radius', [
		'selectors' => [
			implode( ',', [
				'{{WRAPPER}} select',
				'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select',
				'{{WRAPPER}} .select2.select2-container .select2-selection[role="combobox"]',
				'{{WRAPPER}} input[type="checkbox"] + label::before',
			] ) => '{{_RESET_}}',
		],
	] );
	// Border Radius Focus.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'form_field_focus_border_radius', [
		'selectors' => [
			implode( ',', [
				'{{WRAPPER}} select:focus',
				'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select:focus',
				'{{WRAPPER}} .select2.select2-container .select2-selection[role="combobox"]:focus',
				'{{WRAPPER}} input[type="checkbox"]:focus + label::before',
			] ) => '{{_RESET_}}',
		],
	] );
	// Transition Duration Focus.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'form_field_focus_transition_duration', [
		'selectors' => [
			implode( ',', [
				'{{WRAPPER}} select',
				'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select',
				'{{WRAPPER}} .elementor-select-wrapper:before',
				'{{WRAPPER}} .select2.select2-container .select2-selection[role="combobox"]',
				'{{WRAPPER}} input[type="checkbox"] + label::before',
			] ) => '{{_RESET_}}',
		],
	] );
	// Border.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'form_field_border', [
		'selectors' => [
			implode( ',', [
				'{{WRAPPER}} select',
				'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select',
				'{{WRAPPER}} .select2.select2-container .select2-selection[role="combobox"]',
				'{{WRAPPER}} input[type="checkbox"] + label::before',
			] ) => '{{_RESET_}}',
		],
	],
	\Elementor\Group_Control_Border::get_type()
	);
	// Border Focus.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'form_field_focus_border', [
		'selectors' => [
			implode( ',', [
				'{{WRAPPER}} select:focus',
				'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select:focus',
				'{{WRAPPER}} .select2.select2-container .select2-selection[role="combobox"]:focus',
				'{{WRAPPER}} input[type="checkbox"]:focus + label::before',
			] ) => '{{_RESET_}}',
		],
	],
	\Elementor\Group_Control_Border::get_type()
	);
	// Border Color Focus.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'form_field_focus_border_color', [
		'selectors' => [
			implode( ',', [
				'{{WRAPPER}} select:hover',
				'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select:hover',
				'{{WRAPPER}} .select2.select2-container .select2-selection[role="combobox"]:hover',
				'{{WRAPPER}} input:hover:not([type="button"]):not([type="submit"])',
				'{{WRAPPER}} textarea:hover',
				'{{WRAPPER}} .elementor-field-textual:hover',
				'{{WRAPPER}} input[type="checkbox"]:hover + label::before',
			] ) => '{{_RESET_}}',
		],
	] );
	// Box Shadow.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'form_field_box_shadow', [
		'selectors' => [
			implode( ',', [
				'{{WRAPPER}} select',
				'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select',
				'{{WRAPPER}} .select2.select2-container .select2-selection[role="combobox"]',
				'{{WRAPPER}} input[type="checkbox"] + label::before',
			] ) => '{{_RESET_}}',
		],
	],
	\Elementor\Group_Control_Box_Shadow::get_type()
	);
	// Box Shadow Focus.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'form_field_focus_box_shadow', [
		'selectors' => [
			implode( ',', [
				'{{WRAPPER}} select:focus',
				'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select:focus',
				'{{WRAPPER}} .select2.select2-container .select2-selection[role="combobox"]:focus',
				'{{WRAPPER}} input[type="checkbox"]:focus + label::before',
			] ) => '{{_RESET_}}',
		],
	],
	\Elementor\Group_Control_Box_Shadow::get_type()
	);
	// Padding.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'form_field_padding', [
		'selectors' => [
			implode( ',', [
				'{{WRAPPER}} select',
				'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select',
				'{{WRAPPER}} .select2.select2-container .select2-selection[role="combobox"]',
			] ) => '{{_RESET_}}',
		],
	] );
}

// Form Fields section
add_action( 'elementor/element/kit/section_form_fields/before_section_end', function( $kit, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	update_form_field_controls( $controls_manager, $kit );
}, 10, 2 );

function update_headings_controls( $controls_manager, $kit ) {
	// Headings 1-6.
	for ( $i = 1; $i < 7; $i++ ) {
		// Color.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, "h{$i}_color", [
			'selectors' => [
				"{{WRAPPER}} .font-h{$i}" => '{{_RESET_}}',
			],
		] );
		// Typography.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, "h{$i}_typography", [
				'selectors' => [
					"{{WRAPPER}} .font-h{$i}" => '{{_RESET_}}',
				],
			],
			\Elementor\Group_Control_Typography::get_type()
		);
	}
}

// Typography section
add_action( 'elementor/element/kit/section_typography/before_section_end', function( $kit, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	update_headings_controls( $controls_manager, $kit );
}, 10, 2 );


function update_buttons_section_controls( $controls_manager, $kit ) {
	$theme_btn_selectors = [
		'html .button',
		'html button',
		'html input[type=button]',
		'html input[type=submit]',
		'html .woocommerce.widget_shopping_cart .widget_shopping_cart_content .buttons a.button',
		'html .woocommerce a.button',
		'html .woocommerce a.button.loading',
		'html .woocommerce .cross-sells .add_to_cart_button',
		'html .woocommerce a.added_to_cart',
		'html .woocommerce button.button',
		'html .woocommerce input.button',
		'html .woocommerce.woocommerce-cart .cross-sells ul.products > li.product .button.add_to_cart_button',
		'html .woocommerce #respond input#submit',
		'html .woocommerce #content input.button',
		'html .woocommerce-page a.button',
		'html .woocommerce-page button.button',
		'html .woocommerce-page input.button',
		'html .woocommerce-page #respond input#submit',
		'html .woocommerce-page #content input.button',
		'html .woocommerce #respond input#submit.alt',
		'html .woocommerce a.button.alt',
		'html .woocommerce button.button.alt',
		'html .woocommerce button.button.alt.disabled',
		'html .woocommerce input.button.alt',
		'html .woocommerce .woocommerce-message .vamtam-close-notice-btn',
		'html a.comment-reply-link',
		'html .elementor-widget-woocommerce-menu-cart.elementor-element .elementor-button.elementor-button--checkout',
		'html .elementor-widget-woocommerce-menu-cart.elementor-element .elementor-button.elementor-button--view-cart',
	];


	$theme_btn_hover_selectors = implode( ',', array_map( function( $selector ) {
		return $selector . ':hover';
	}, $theme_btn_selectors ) );

	// Selected state of a multi-type form's button to have the same styles as the hover state.
	if ( is_plugin_active( 'give/give.php' ) ) {
		$theme_btn_hover_selectors .= ',html .give-form-wrap .give-form-type-multi #give-donation-level-button-wrap .give-default-level';
	}

	$theme_btn_selectors = implode( ',', $theme_btn_selectors );

	// Typography.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'button_typography', [
		'selector' => $theme_btn_selectors,
		],
		\Elementor\Group_Control_Typography::get_type()
	);
	// Text Shadow.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'button_text_shadow', [
		'selector' => $theme_btn_selectors,
		],
		\Elementor\Group_Control_Text_Shadow::get_type()
	);
	// Text Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'button_text_color', [
		'selectors' => [
			$theme_btn_selectors => '{{_RESET_}}',
		],
	] );
	// Bg Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'button_background_color', [
		'selectors' => [
			$theme_btn_selectors => '{{_RESET_}}',
		],
	] );
	// Box Shadow.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'button_box_shadow', [
		'selector' => $theme_btn_selectors,
		],
		\Elementor\Group_Control_Box_Shadow::get_type()
	);
	// Border.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'button_border', [
		'selector' => $theme_btn_selectors,
		],
		\Elementor\Group_Control_Border::get_type()
	);
	// Border Radius.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'button_border_radius', [
		'selectors' => [
			$theme_btn_selectors => '{{_RESET_}}',
		],
	] );
	// Text Color Hover.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'button_hover_text_color', [
		'selectors' => [
			$theme_btn_hover_selectors => '{{_RESET_}}'
		],
	] );
	// Bg Color Hover.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'button_hover_background_color', [
		'selectors' => [
			$theme_btn_hover_selectors => '{{_RESET_}}'
		],
	] );
	// Box Shadow Hover.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'button_hover_box_shadow', [
		'selector' => $theme_btn_hover_selectors,
		],
		\Elementor\Group_Control_Box_Shadow::get_type()
	);
	// Border Hover.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'button_hover_border', [
		'selector' => $theme_btn_hover_selectors,
		],
		\Elementor\Group_Control_Border::get_type()
	);
	// Border Radius Hover.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'button_hover_border_radius', [
		'selectors' => [
			$theme_btn_hover_selectors => '{{_RESET_}}'
		],
	] );
	// Padding.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'button_padding', [
		'selectors' => [
			$theme_btn_selectors => '{{_RESET_}}'
		],
	] );
}

// Buttons section
add_action( 'elementor/element/kit/section_buttons/before_section_end', function( $kit, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	update_buttons_section_controls( $controls_manager, $kit );
}, 10, 2 );

function add_wc_buttons_section_controls( $controls_manager, $kit ) {
	$wc_button_selectors = [
		'html .woocommerce.woocommerce-page .cart-collaterals .wc-proceed-to-checkout .checkout-button',
		'html .woocommerce.woocommerce-page .vamtam-cart-collaterals .coupon button[name=apply_coupon]',
		'html .woocommerce.woocommerce-page .checkout_coupon .button[name=apply_coupon]',
		'html .woocommerce.woocommerce-page #place_order',
		'html .woocommerce.woocommerce-page .woocommerce-form-login .woocommerce-form-login__submit',
		'html .woocommerce.woocommerce-page .woocommerce-Button.button',
		'html .woocommerce.woocommerce-page .woocommerce-button.button',
		'html .woocommerce.woocommerce-page.woocommerce-cart .cross-sells ul.products > li.product .button.add_to_cart_button',
	];

	$wc_button_hover_selectors = implode( ',', array_map( function( $selector ) {
		return $selector . ':hover';
	}, $wc_button_selectors ) );

	$wc_button_selectors = implode( ',', $wc_button_selectors );

	$kit->start_controls_section(
		'section_wc_buttons',
		[
			'label' => __( 'WooCommerce Buttons', 'vamtam-elementor-integration' ),
			'tab' => 'theme-style-buttons', // !! Important: This tells the editor on which tab to insert the section. !!
		]
	);

	$kit->add_control(
		'wc_buttons_notice',
		[
			'type' => $controls_manager::RAW_HTML,
			'raw' => __( 'These options affect buttons <strong>only</strong> at standard WooCommerce pages like:<ul style="padding:10px;"><li>- The Cart page,</li><li>- The Checkout page</li><li>- The My Account Page</li></ul> e.t.c.', 'vamtam-elementor-integration' ),
			'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
		]
	);

	$kit->add_group_control(
		\Elementor\Group_Control_Typography::get_type(),
		[
			'label' => __( 'Typography', 'vamtam-elementor-integration' ),
			'name' => 'wc_button_typography',
			'selector' => $wc_button_selectors,
		]
	);

	$kit->add_group_control(
		\Elementor\Group_Control_Text_Shadow::get_type(),
		[
			'name' => 'wc_button_text_shadow',
			'selector' => $wc_button_selectors,
		]
	);

	$kit->start_controls_tabs( 'tabs_wc_button_style' );

	$kit->start_controls_tab(
		'tab_wc_button_normal',
		[
			'label' => __( 'Normal', 'vamtam-elementor-integration' ),
		]
	);

	$kit->add_control(
		'wc_button_text_color',
		[
			'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::COLOR,
			'dynamic' => [],
			'selectors' => [
				$wc_button_selectors => 'color: {{VALUE}};',
			],
		]
	);

	$kit->add_control(
		'wc_button_background_color',
		[
			'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::COLOR,
			'dynamic' => [],
			'selectors' => [
				$wc_button_selectors => 'background-color: {{VALUE}};',
			],
		]
	);

	$kit->add_group_control(
		\Elementor\Group_Control_Box_Shadow::get_type(),
		[
			'name' => 'wc_button_box_shadow',
			'selector' => $wc_button_selectors,
		]
	);

	$kit->add_group_control(
		\Elementor\Group_Control_Border::get_type(),
		[
			'name' => 'wc_button_border',
			'selector' => $wc_button_selectors,
			'fields_options' => [
				'color' => [
					'dynamic' => [],
				],
			],
		]
	);

	$kit->add_control(
		'wc_button_border_radius',
		[
			'label' => __( 'Border Radius', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				$wc_button_selectors => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]
	);

	$kit->end_controls_tab();

	$kit->start_controls_tab(
		'tab_wc_button_hover',
		[
			'label' => __( 'Hover', 'vamtam-elementor-integration' ),
		]
	);

	$kit->add_control(
		'wc_button_hover_text_color',
		[
			'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::COLOR,
			'dynamic' => [],
			'selectors' => [
				$wc_button_hover_selectors => 'color: {{VALUE}};',
			],
		]
	);

	$kit->add_control(
		'wc_button_hover_background_color',
		[
			'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::COLOR,
			'dynamic' => [],
			'selectors' => [
				$wc_button_hover_selectors => 'background-color: {{VALUE}};',
			],
		]
	);

	$kit->add_group_control(
		\Elementor\Group_Control_Box_Shadow::get_type(),
		[
			'name' => 'wc_button_hover_box_shadow',
			'selector' => $wc_button_hover_selectors,
		]
	);

	$kit->add_group_control(
		\Elementor\Group_Control_Border::get_type(),
		[
			'name' => 'wc_button_hover_border',
			'selector' => $wc_button_hover_selectors,
			'fields_options' => [
				'color' => [
					'dynamic' => [],
				],
			],
		]
	);

	$kit->add_control(
		'wc_button_hover_border_radius',
		[
			'label' => __( 'Border Radius', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors' => [
				$wc_button_hover_selectors => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]
	);

	$kit->end_controls_tab();

	$kit->end_controls_tabs();

	$kit->add_responsive_control(
		'wc_button_padding',
		[
			'label' => __( 'Padding', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors' => [
				$wc_button_selectors => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'separator' => 'before',
		]
	);

	$kit->end_controls_section();
}

// Buttons section
add_action( 'elementor/element/kit/section_buttons/after_section_end', function( $kit, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	add_wc_buttons_section_controls( $controls_manager, $kit );
}, 10, 2 );

function update_images_controls( $controls_manager, $kit ) {
	$product_imgs = '.elementor-wc-products.vamtam-has-theme-widget-styles ul.products li.product:not(.product-category) > a img:not([class*=elementor-animation])';
	$blog_imgs    = '.vamtam-has-theme-widget-styles .elementor-posts-container .elementor-post__thumbnail img';
	// Transition duration.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $kit, 'image_hover_transition', [
		'selectors' => [
			"{{WRAPPER}} {$product_imgs}, {{WRAPPER}} {$blog_imgs}" => 'transition-duration: {{SIZE}}s !important',
		],
	] );
}

// Images section
add_action( 'elementor/element/kit/section_images/after_section_end', function( $kit, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	update_images_controls( $controls_manager, $kit );
}, 10, 2 );
