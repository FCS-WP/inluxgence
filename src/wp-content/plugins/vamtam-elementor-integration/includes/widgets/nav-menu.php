<?php
namespace VamtamElementor\Widgets\NavMenu;

use \ElementorPro\Modules\NavMenu\Widgets\Nav_Menu as Elementor_Nav_Menu;

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

// Extending the Nav Menu widget.
function update_controls_style_tab_main_section( $controls_manager, $widget ) {
	// Menu item active color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'color_menu_item_active', [
		'selectors' => [
			'{{WRAPPER}} .elementor-nav-menu--main .current-menu-ancestor > .elementor-item' => '{{_RESET_}}',
		],
	] );

	// Menu item pointer active color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pointer_color_menu_item_active', [
		'selectors' => [
			implode( ',', [
				'{{WRAPPER}} .elementor-nav-menu--main:not(.e--pointer-framed) .current-menu-ancestor > .elementor-item:before',
				'{{WRAPPER}} .elementor-nav-menu--main:not(.e--pointer-framed) .current-menu-ancestor > .elementor-item:after',
			] ) => 'background-color: {{VALUE}}',
			implode( ',', [
				'{{WRAPPER}} .e--pointer-framed .current-menu-ancestor > .elementor-item:before',
				'{{WRAPPER}} .e--pointer-framed .current-menu-ancestor > .elementor-item:after',
			] ) => 'border-color: {{VALUE}}',
		],
	] );

	// Pointer width
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pointer_width', [
		'selectors' => [
			'{{WRAPPER}} .e--pointer-framed .current-menu-ancestor > a.elementor-item:before' => 'border-width: {{SIZE}}{{UNIT}} !important',
			'{{WRAPPER}} .e--pointer-framed.e--animation-draw .current-menu-ancestor > .elementor-item:before' => 'border-width: 0 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} !important',
			'{{WRAPPER}} .e--pointer-framed.e--animation-draw .current-menu-ancestor > .elementor-item:after' => 'border-width: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0 !important',
			'{{WRAPPER}} .e--pointer-framed.e--animation-corners .current-menu-ancestor > .elementor-item:before' => 'border-width: {{SIZE}}{{UNIT}} 0 0 {{SIZE}}{{UNIT}} !important',
			'{{WRAPPER}} .e--pointer-framed.e--animation-corners .current-menu-ancestor > .elementor-item:after' => 'border-width: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 !important',
			implode( ',', [
				'{{WRAPPER}} .e--pointer-underline .current-menu-ancestor > .elementor-item:after',
				'{{WRAPPER}} .e--pointer-overline .current-menu-ancestor > .elementor-item:before',
				'{{WRAPPER}} .e--pointer-double-line .current-menu-ancestor > .elementor-item:before',
				'{{WRAPPER}} .e--pointer-double-line .current-menu-ancestor > .elementor-item:after',
			] ) => 'height: {{SIZE}}{{UNIT}} !important',
		],
	] );

	if ( vamtam_theme_supports( 'nav-menu--underline-theme-pointer' ) ) {
		// Pointer width
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pointer_width', [
			'condition' => [
				'pointer' => [ 'underline-theme' ],
			],
		] );
	}

	if ( vamtam_theme_supports( 'nav-menu--circle-pointer' ) ) {
		// Pointer width
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pointer_width', [
			'selectors' => [
				// This is for making the pointer a circle.
				implode( ',', [
					'{{WRAPPER}} .e--pointer-underline .elementor-item:after',
					'{{WRAPPER}} .e--pointer-overline .elementor-item:before',
					'{{WRAPPER}} .e--pointer-double-line .elementor-item:before',
					'{{WRAPPER}} .e--pointer-double-line .elementor-item:after',
				] ) => 'width: {{SIZE}}{{UNIT}} !important',
			],
		] );
	}

	if ( vamtam_theme_supports( 'nav-menu--prefix-pointer' ) ) {
		update_color_menu_item_control( $controls_manager, $widget );
	}
}

if ( vamtam_theme_supports( 'nav-menu--individual-nav-items' ) ) {
	function get_nav_menu_top_level_items_count( $menu_name ) {
		$menu_items      = wp_get_nav_menu_items( $menu_name );
		$top_level_items = 0;

		foreach ( $menu_items as $key => $menu_item ) {
			if ( $menu_item->menu_item_parent != 0 ) {
				continue;
			} else {
				++$top_level_items;
			}
		}

		return $top_level_items;
	}

	function add_individual_nav_items_color_controls( $controls_manager, $widget ) {

		$widget->add_control(
			'individual_nav_items_colors',
			[
				'label' => __( 'Individual Nav Items Colors', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'description' => __( 'Use different colors for each of the menu items. These colors have priority over the general ones.', 'vamtam-elementor-integration' ),
				'return_value' => 'yes',
			]
		);

		$widget->start_controls_tabs( 'individual_nav_items_colors_tabs' );

		$widget->start_controls_tab(
			'tab_individual_nav_items_colors',
			[
				'label' => __( 'Normal', 'vamtam-elementor-integration' ),
				'condition' => [
					'individual_nav_items_colors' => 'yes',
				],
			]

		);

		// Get menu items (top-level).
		$top_level_items_count = 12; // until we find a solution to the getSettings() problem: https://github.com/elementor/elementor/issues/8698

		for ( $i = 0; $i < $top_level_items_count; $i++ ) {
			$widget->add_control(
				"nav_item_{$i}_color_label",
				[
					'type' => $controls_manager::RAW_HTML,
					'raw' => sprintf( __( 'Item %d', 'vamtam-elementor-integration' ), $i + 1 ),
					'content_classes' => 'elementor-control-field-title',
					'condition' => [
						'individual_nav_items_colors' => 'yes',
					],
				]
			);
			$widget->add_control(
				"nav_item_{$i}_text_color",
				[
					'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
					'type' => $controls_manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .elementor-nav-menu--main > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-item' => 'color: {{VALUE}}',
					],
					'condition' => [
						'individual_nav_items_colors' => 'yes',
					],
				]
			);
			$widget->add_control(
				"nav_item_{$i}_color_hr",
				[
					'type' => $controls_manager::DIVIDER,
					'condition' => [
						'individual_nav_items_colors' => 'yes',
					],
				]
			);
		}

		$widget->end_controls_tab();

		$widget->start_controls_tab(
			'tab_individual_nav_items_colors_hover',
			[
				'label' => __( 'Hover', 'vamtam-elementor-integration' ),
				'condition' => [
					'individual_nav_items_colors' => 'yes',
				],
				'description' => __( 'Use different colors for each of the menu items.', 'vamtam-elementor-integration' ),

			]
		);

		for ( $i = 0; $i < $top_level_items_count; $i++ ) {
			$widget->add_control(
				"nav_item_{$i}_color_hover_label",
				[
					'type' => $controls_manager::RAW_HTML,
					'raw' => sprintf( __( 'Item %d', 'vamtam-elementor-integration' ), $i + 1 ),
					'content_classes' => 'elementor-control-field-title',
					'condition' => [
						'individual_nav_items_colors' => 'yes',
					],
				]
			);
			$widget->add_control(
				"nav_item_{$i}_text_color_hover",
				[
					'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
					'type' => $controls_manager::COLOR,
					'default' => '',
					'selectors' => [
						implode( ',', [
							'{{WRAPPER}} .elementor-nav-menu--main > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-item:hover',
							'{{WRAPPER}} .elementor-nav-menu--main  > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-item.elementor-item-active',
							'{{WRAPPER}} .elementor-nav-menu--main  > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-item.highlighted',
							'{{WRAPPER}} .elementor-nav-menu--main  > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-item:focus',
						] ) => 'color: {{VALUE}}',
					],
					'condition' => [
						'individual_nav_items_colors' => 'yes',
					],
				]
			);
			$widget->add_control(
				"nav_item_{$i}_pointer_color_hover",
				[
					'label' => sprintf( __( 'Pointer Color', 'vamtam-elementor-integration' ), $i + 1 ),
					'type' => $controls_manager::COLOR,
					'default' => '',
					'selectors' => [
						implode( ',', [
							'{{WRAPPER}} .elementor-nav-menu--main:not(.e--pointer-framed)  > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-item:before',
							'{{WRAPPER}} .elementor-nav-menu--main:not(.e--pointer-framed) > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-item:after',
						] ) => 'background-color: {{VALUE}}',
						implode( ',', [
							'{{WRAPPER}} .e--pointer-framed > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-item:before',
							'{{WRAPPER}} .e--pointer-framed > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-item:after',
						] ) => 'border-color: {{VALUE}}',
					],
					'condition' => [
						'individual_nav_items_colors' => 'yes',
						'pointer!' => [ 'none', 'text' ],
					],
				]
			);
			$widget->add_control(
				"nav_item_{$i}_color_hover_hr",
				[
					'type' => $controls_manager::DIVIDER,
					'condition' => [
						'individual_nav_items_colors' => 'yes',
					],
				]
			);
		}

		$widget->end_controls_tab();

		$widget->start_controls_tab(
			'tab_individual_nav_items_colors_active',
			[
				'label' => __( 'Active', 'vamtam-elementor-integration' ),
				'condition' => [
					'individual_nav_items_colors' => 'yes',
				],
			]
		);

		for ( $i = 0; $i < $top_level_items_count; $i++ ) {
			$widget->add_control(
				"nav_item_{$i}_color_active_label",
				[
					'type' => $controls_manager::RAW_HTML,
					'raw' => sprintf( __( 'Item %d', 'vamtam-elementor-integration' ), $i + 1 ),
					'content_classes' => 'elementor-control-field-title',
					'condition' => [
						'individual_nav_items_colors' => 'yes',
					],
				]
			);
			$widget->add_control(
				"nav_item_{$i}_text_color_active",
				[
					'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
					'type' => $controls_manager::COLOR,
					'default' => '',
					'selectors' => [
						implode( ',', [
							'{{WRAPPER}} .elementor-nav-menu--main > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-item.elementor-item-active',
							'{{WRAPPER}} .elementor-nav-menu--main > .elementor-nav-menu > .current-menu-ancestor.menu-item:nth-child(' . ( $i + 1 ) . ') > .elementor-item',
						] ) => 'color: {{VALUE}}',
					],
					'condition' => [
						'individual_nav_items_colors' => 'yes',
					],
				]
			);
			$widget->add_control(
				"nav_item_{$i}_pointer_color_active",
				[
					'label' => sprintf( __( 'Pointer Color', 'vamtam-elementor-integration' ), $i + 1 ),
					'type' => $controls_manager::COLOR,
					'default' => '',
					'selectors' => [
						implode( ',', [
							'{{WRAPPER}} .elementor-nav-menu--main:not(.e--pointer-framed) > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-item.elementor-item-active:before',
							'{{WRAPPER}} .elementor-nav-menu--main:not(.e--pointer-framed)  > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-item.elementor-item-active:after',
						] ) => 'background-color: {{VALUE}}',
						implode( ',', [
							'{{WRAPPER}} .e--pointer-framed > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-item.elementor-item-active:before',
							'{{WRAPPER}} .e--pointer-framed  > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-item.elementor-item-active:after',
						] ) => 'border-color: {{VALUE}}',
						implode( ',', [
							'{{WRAPPER}} .elementor-nav-menu--main:not(.e--pointer-framed) > .elementor-nav-menu > .current-menu-ancestor.menu-item:nth-child(' . ( $i + 1 ) . ') > .elementor-item:before',
							'{{WRAPPER}} .elementor-nav-menu--main:not(.e--pointer-framed) > .elementor-nav-menu > .current-menu-ancestor.menu-item:nth-child(' . ( $i + 1 ) . ') > .elementor-item:after',
						] ) => 'background-color: {{VALUE}}',
						implode( ',', [
							'{{WRAPPER}} .e--pointer-framed > .elementor-nav-menu > .current-menu-ancestor.menu-item:nth-child(' . ( $i + 1 ) . ') > .elementor-item:before',
							'{{WRAPPER}} .e--pointer-framed > .elementor-nav-menu > .current-menu-ancestor.menu-item:nth-child(' . ( $i + 1 ) . ') > .elementor-item:after',
						] ) => 'border-color: {{VALUE}}',
					],
					'condition' => [
						'individual_nav_items_colors' => 'yes',
						'pointer!' => [ 'none', 'text' ],
					],
				]
			);
			$widget->add_control(
				"nav_item_{$i}_color_active_hr",
				[
					'type' => $controls_manager::DIVIDER,
					'condition' => [
						'individual_nav_items_colors' => 'yes',
					],
				]
			);
		}

		$widget->end_controls_tab();

		$widget->end_controls_tabs();
	}

	function add_individual_dropdown_color_controls( $controls_manager, $widget ) {

		$widget->add_control(
			'individual_dropdown_colors',
			[
				'label' => __( 'Individual Dropdown Colors', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'description' => __( 'Use different colors for each of the dropdowns. These colors have priority over the general ones.', 'vamtam-elementor-integration' ),
				'return_value' => 'yes',
			]
		);

		$widget->start_controls_tabs( 'individual_dropdown_colors_tabs' );

		$widget->start_controls_tab(
			'tab_individual_dropdown_colors',
			[
				'label' => __( 'Normal', 'vamtam-elementor-integration' ),
				'condition' => [
					'individual_dropdown_colors' => 'yes',
				],
			]

		);

		$top_level_items_count = 12; // until we find a solution to the getSettings() problem: https://github.com/elementor/elementor/issues/8698

		for ( $i = 0; $i < $top_level_items_count; $i++ ) {
			$widget->add_control(
				"dropdown_{$i}_color_label",
				[
					'type' => $controls_manager::RAW_HTML,
					'raw' => sprintf( __( 'Item %d', 'vamtam-elementor-integration' ), $i + 1 ),
					'content_classes' => 'elementor-control-field-title',
					'condition' => [
						'individual_dropdown_colors' => 'yes',
					],
				]
			);
			$widget->add_control(
				"dropdown_{$i}_text_color",
				[
					'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
					'type' => $controls_manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .elementor-nav-menu--main > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-nav-menu--dropdown a,' => 'color: {{VALUE}}',
					],
					'condition' => [
						'individual_dropdown_colors' => 'yes',
					],
				]
			);
			$widget->add_control(
				"dropdown_{$i}_bg_color",
				[
					'label' => sprintf( __( 'Background Color', 'vamtam-elementor-integration' ), $i + 1 ),
					'type' => $controls_manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .elementor-nav-menu--main > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-nav-menu--dropdown' => 'background-color: {{VALUE}}',
					],
					'condition' => [
						'individual_dropdown_colors' => 'yes',
					],
				]
			);
			$widget->add_control(
				"dropdown_{$i}_color_hr",
				[
					'type' => $controls_manager::DIVIDER,
					'condition' => [
						'individual_dropdown_colors' => 'yes',
					],
				]
			);
		}

		$widget->end_controls_tab();

		$widget->start_controls_tab(
			'tab_individual_dropdown_colors_hover',
			[
				'label' => __( 'Hover', 'vamtam-elementor-integration' ),
				'condition' => [
					'individual_dropdown_colors' => 'yes',
				],
				'description' => __( 'Use different colors for each of the menu items.', 'vamtam-elementor-integration' ),

			]
		);

		for ( $i = 0; $i < $top_level_items_count; $i++ ) {
			$widget->add_control(
				"dropdown_{$i}_color_hover_label",
				[
					'type' => $controls_manager::RAW_HTML,
					'raw' => sprintf( __( 'Item %d', 'vamtam-elementor-integration' ), $i + 1 ),
					'content_classes' => 'elementor-control-field-title',
					'condition' => [
						'individual_dropdown_colors' => 'yes',
					],
				]
			);
			$widget->add_control(
				"dropdown_{$i}_text_color_hover",
				[
					'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
					'type' => $controls_manager::COLOR,
					'default' => '',
					'selectors' => [
						implode( ',', [
							'{{WRAPPER}} .elementor-nav-menu--main > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-nav-menu--dropdown a:hover',
							'{{WRAPPER}}  .elementor-nav-menu--main > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-nav-menu--dropdown a.elementor-item-active',
							'{{WRAPPER}}  .elementor-nav-menu--main > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-nav-menu--dropdown a.highlighted',
						] ) => 'color: {{VALUE}}',
					],
					'condition' => [
						'individual_dropdown_colors' => 'yes',
					],
				]
			);
			$widget->add_control(
				"dropdown_{$i}_bg_color_hover",
				[
					'label' => sprintf( __( 'Background Color', 'vamtam-elementor-integration' ), $i + 1 ),
					'type' => $controls_manager::COLOR,
					'default' => '',
					'selectors' => [
						implode( ',', [
							'{{WRAPPER}} .elementor-nav-menu--main > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-nav-menu--dropdown a:hover',
							'{{WRAPPER}}  .elementor-nav-menu--main > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-nav-menu--dropdown a.elementor-item-active',
							'{{WRAPPER}}  .elementor-nav-menu--main > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-nav-menu--dropdown a.highlighted',
					] ) => 'background-color: {{VALUE}}',
					],
					'condition' => [
						'individual_dropdown_colors' => 'yes',
					],
				]
			);
			$widget->add_control(
				"dropdown_{$i}_color_hover_hr",
				[
					'type' => $controls_manager::DIVIDER,
					'condition' => [
						'individual_dropdown_colors' => 'yes',
					],
				]
			);
		}

		$widget->end_controls_tab();

		$widget->start_controls_tab(
			'tab_individual_dropdown_colors_active',
			[
				'label' => __( 'Active', 'vamtam-elementor-integration' ),
				'condition' => [
					'individual_dropdown_colors' => 'yes',
				],
			]
		);

		for ( $i = 0; $i < $top_level_items_count; $i++ ) {
			$widget->add_control(
				"dropdown_{$i}_color_active_label",
				[
					'type' => $controls_manager::RAW_HTML,
					'raw' => sprintf( __( 'Item %d', 'vamtam-elementor-integration' ), $i + 1 ),
					'content_classes' => 'elementor-control-field-title',
					'condition' => [
						'individual_dropdown_colors' => 'yes',
					],
				]
			);
			$widget->add_control(
				"dropdown_{$i}_text_color_active",
				[
					'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
					'type' => $controls_manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .elementor-nav-menu--main > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-nav-menu--dropdown a.elementor-item-active' => 'color: {{VALUE}}',
					],
					'condition' => [
						'individual_dropdown_colors' => 'yes',
					],
				]
			);
			$widget->add_control(
				"dropdown_{$i}_bg_color_active",
				[
					'label' => sprintf( __( 'Background Color', 'vamtam-elementor-integration' ), $i + 1 ),
					'type' => $controls_manager::COLOR,
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .elementor-nav-menu--main > .elementor-nav-menu > .menu-item:nth-child(' . ( $i + 1 ) . ') .elementor-nav-menu--dropdown a.elementor-item-active' => 'background-color: {{VALUE}}',
					],
					'condition' => [
						'individual_dropdown_colors' => 'yes',
					],
				]
			);
			$widget->add_control(
				"dropdown_{$i}_color_active_hr",
				[
					'type' => $controls_manager::DIVIDER,
					'condition' => [
						'individual_dropdown_colors' => 'yes',
					],
				]
			);
		}

		$widget->end_controls_tab();

		$widget->end_controls_tabs();
	}
}

if ( vamtam_theme_supports( 'nav-menu--dropdown-min-width' ) ) {
	function add_dropdown_min_width_control( $controls_manager, $widget ) {
		$widget->add_responsive_control(
			'dropdown_min_width',
			[
				'label' => __( 'Min Width', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
					'em' => [
						'max' => 30,
					],
				],
				'selectors' => [
					implode( ',', [
						'{{WRAPPER}} .elementor-nav-menu--main > .elementor-nav-menu > li > .elementor-nav-menu--dropdown',
						'{{WRAPPER}} .elementor-nav-menu__container.elementor-nav-menu--dropdown,'
					] ) => 'min-width: {{SIZE}}{{UNIT}} !important',
				],
			]
		);
	}
}

if ( vamtam_theme_supports( 'nav-menu--dropdown-left-distance' ) ) {
	function add_dropdown_left_distance_control( $controls_manager, $widget ) {
		$widget->add_responsive_control(
			'dropdown_left_distance',
			[
				'label' => __( 'Horizontal Position', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors' => [
					implode( ',', [
						'{{WRAPPER}} .elementor-nav-menu--main > .elementor-nav-menu > li > .elementor-nav-menu--dropdown',
						'{{WRAPPER}} .elementor-nav-menu__container.elementor-nav-menu--dropdown',
					] ) => 'margin-left: {{SIZE}}{{UNIT}} !important',
				],
			]
		);
	}
}

if ( vamtam_theme_supports( 'nav-menu--custom-dropdown-divider' ) ) {
	function custom_dropdown_divider_controls( $controls_manager, $widget ) {
		// --- Add Controls --- //

		$widget->start_injection( [
			'of' => 'heading_dropdown_divider',
		] );
		$widget->add_control(
			'use_custom_divider',
			[
				'label' => __( 'Use Theme\'s Divider', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'default' => '',
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'custom-divider',
			]
		);
		$widget->add_control(
			"custom_divider_color",
			[
				'label' => __( 'Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--dropdown li:not(:last-child):after' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'use_custom_divider!' => '',
				],
			]
		);
		$widget->add_control(
			"custom_divider_width",
			[
				'label' => __( 'Width', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'size_units' => [ '%' ],
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--dropdown li:not(:last-child):after' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'use_custom_divider!' => '',
				],
			]
		);
		$widget->add_control(
			"custom_divider_height",
			[
				'label' => __( 'Height', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'size_units' => [ 'px' ],
				'default' => [
					'size' => 1,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--dropdown li:not(:last-child):after' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'use_custom_divider!' => '',
				],
			]
		);
		$widget->end_injection();

		// --- Update Controls --- //

		$new_options = [
			'condition' => [
				'use_custom_divider' => '',
			]
		];
		// Border Type.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'dropdown_divider', $new_options, \Elementor\Group_Control_Border::get_type() );
		// Border Width.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'dropdown_divider_width', $new_options, \Elementor\Group_Control_Border::get_type() );
	}
}

if ( vamtam_theme_supports( 'nav-menu--hover-dropdown-text-decoration' ) ) {
	function add_hover_dropdown_text_decoration_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'background_color_dropdown_item_hover',
		] );
		$widget->add_control(
			'vamtam_hover_text_decoration',
			[
				'label' => __( 'Text Decoration', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'Default', 'vamtam-elementor-integration' ),
					'underline' => __( 'Underline', 'vamtam-elementor-integration' ),
					'overline' => __( 'Overline', 'vamtam-elementor-integration' ),
					'line-through' => __( 'Line Through', 'vamtam-elementor-integration' ),
					'none' => __( 'None', 'vamtam-elementor-integration' ),
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--dropdown a:hover,
					{{WRAPPER}} .elementor-nav-menu--dropdown a.elementor-item-active,
					{{WRAPPER}} .elementor-nav-menu--dropdown a.highlighted,
					{{WRAPPER}} .elementor-menu-toggle:hover' => 'text-decoration: {{VALUE}}',
				],
			]
		);
		$widget->end_injection();
	}
}

// TODO: remove this feature when/if they add support for @media (hover: hover) for their hover styles.
if ( vamtam_theme_supports( 'nav-menu--toggle-sticky-hover-state-on-touch-fix' ) ) {
	function update_dropdown_text_hover_selector( $controls_manager, $widget ) {
		// Text Color.
		\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'color_dropdown_item_hover', [
			'selectors' => [
				'{{WRAPPER}} .elementor-nav-menu--dropdown a:hover,
				{{WRAPPER}} .elementor-nav-menu--dropdown a.elementor-item-active,
				{{WRAPPER}} .elementor-nav-menu--dropdown a.highlighted,
				body:not(.e--ua-isTouchDevice) {{WRAPPER}} .elementor-menu-toggle:hover,
				body.e--ua-isTouchDevice {{WRAPPER}} .elementor-menu-toggle.elementor-active:hover' => 'color: {{VALUE}}',
			],
		] );
	}

	function update_toggle_hover_selectors( $controls_manager, $widget ) {
		// Toggle Hover Color.
		\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'toggle_color_hover', [
			'selectors' => [
				'body:not(.e--ua-isTouchDevice) {{WRAPPER}} div.elementor-menu-toggle:hover' => 'color: {{VALUE}}', // Harder selector to override text color control
				'body:not(.e--ua-isTouchDevice) {{WRAPPER}} div.elementor-menu-toggle:hover svg' => 'fill: {{VALUE}}',
				'body.e--ua-isTouchDevice {{WRAPPER}} div.elementor-menu-toggle.elementor-active:hover' => 'color: {{VALUE}}', // Harder selector to override text color control
				'body.e--ua-isTouchDevice {{WRAPPER}} div.elementor-menu-toggle.elementor-active:hover svg' => 'fill: {{VALUE}}',
			],
		] );

		// Toggle Bg Hover Color.
		\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'toggle_background_color_hover', [
			'selectors' => [
				'body:not(.e--ua-isTouchDevice) {{WRAPPER}} .elementor-menu-toggle:hover' => 'background-color: {{VALUE}}',
				'body.e--ua-isTouchDevice {{WRAPPER}} .elementor-active.elementor-menu-toggle:hover' => 'background-color: {{VALUE}}',
			],
		] );
	}
	// Style - Toggle section
	function section_style_toggle_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		update_toggle_hover_selectors( $controls_manager, $widget );
	}
	add_action( 'elementor/element/nav-menu/style_toggle/before_section_end', __NAMESPACE__ . '\section_style_toggle_before_section_end', 10, 2 );
}

// Style - Dropdown section
function section_style_dropdown_before_section_end( $widget, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	if ( vamtam_theme_supports( 'nav-menu--individual-nav-items' ) ) {
		add_individual_dropdown_color_controls( $controls_manager, $widget );
	}
	if ( vamtam_theme_supports( 'nav-menu--dropdown-left-distance' ) ) {
		add_dropdown_left_distance_control( $controls_manager, $widget );
	}
	if ( vamtam_theme_supports( 'nav-menu--dropdown-min-width' ) ) {
		add_dropdown_min_width_control( $controls_manager, $widget );
	}
	if ( vamtam_theme_supports( 'nav-menu--custom-dropdown-divider' ) ) {
		custom_dropdown_divider_controls( $controls_manager, $widget );
	}
	if ( vamtam_theme_supports( 'nav-menu--hover-dropdown-text-decoration' ) ) {
		add_hover_dropdown_text_decoration_control( $controls_manager, $widget );
	}
	if ( vamtam_theme_supports( 'nav-menu--toggle-sticky-hover-state-on-touch-fix' ) ) {
		update_dropdown_text_hover_selector( $controls_manager, $widget );
	}
}
add_action( 'elementor/element/nav-menu/section_style_dropdown/before_section_end', __NAMESPACE__ . '\section_style_dropdown_before_section_end', 10, 2 );

// Style - Main Menu section
function section_style_main_menu_before_section_end( $widget, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	update_controls_style_tab_main_section( $controls_manager, $widget );

	if ( vamtam_theme_supports( 'nav-menu--individual-nav-items' ) ) {
		add_individual_nav_items_color_controls( $controls_manager, $widget );
	}
}
add_action( 'elementor/element/nav-menu/section_style_main-menu/before_section_end', __NAMESPACE__ . '\section_style_main_menu_before_section_end', 10, 2 );

if ( vamtam_theme_supports( [ 'nav-menu--line-through-pointer', 'nav-menu--prefix-pointer', 'nav-menu--bijoux-menu-toggle', 'nav-menu--underline-theme-pointer' ] ) ) {
	// Called frontend & editor (editor after element loses focus).
	function render_content( $content, $widget ) {
		if ( 'nav-menu' === $widget->get_name() ) {
			$settings = $widget->get_settings();

			if ( 'line-through' === $settings['pointer'] ) {
				if ( vamtam_theme_supports( 'nav-menu--line-through-pointer' ) ) {
					// Line-through is essentially underline with some style tweaks.
					// By doing it this way, we don't have to define all the selectors
					// and account for all use cases explicitly for line-through.
					$content = str_replace( 'e--pointer-line-through', 'e--pointer-underline e--pointer-line-through', $content );
					return $content;
				}
			}

			if ( 'prefix' === $settings['pointer'] ) {
				if ( vamtam_theme_supports( 'nav-menu--prefix-pointer' ) ) {
					// Prefix is essentially underline with some style tweaks.
					// By doing it this way, we don't have to define all the selectors
					// and account for all use cases explicitly for prefix.
					$content = str_replace( 'e--pointer-prefix', 'e--pointer-underline e--pointer-prefix', $content );
					return $content;
				}
			}

			// Theme-dependent.
			if ( 'underline-theme' === $settings['pointer'] ) {
				if ( vamtam_theme_supports( 'nav-menu--underline-theme-pointer' ) ) {
					// Underline-Theme is essentially underline with some style tweaks.
					// By doing it this way, we don't have to define all the selectors
					// and account for all use cases explicitly for underline-theme.
					$content = str_replace( 'e--pointer-underline-theme', 'e--pointer-underline e--pointer-underline-theme', $content );
					return $content;
				}
			}

			if ( vamtam_theme_supports( 'nav-menu--bijoux-menu-toggle' ) ) {
				if ( strpos( $settings['_css_classes'], 'vamtam-bijoux-menu-toggle' ) !== false ) {
					$content = str_replace(
						'<i class="eicon-menu-bar"',
						'<i class="eicon-menu-bar vamtamtheme- vamtam-theme-side-menu"',
						$content );
					return $content;
				}
			}
		}
		return $content;
	}
	add_filter( 'elementor/widget/render_content', __NAMESPACE__ . '\render_content', 10, 2 );

	function update_pointer_control( $controls_manager, $widget ) {
		if ( vamtam_theme_supports( 'nav-menu--line-through-pointer' ) ) {
			// Pointer.
			\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pointer', [
				'options' => [
					'line-through' => __( 'Line Through', 'vamtam-elementor-integration' ),
				],
			] );
		}

		if ( vamtam_theme_supports( 'nav-menu--prefix-pointer' ) ) {
			// Pointer.
			\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pointer', [
				'options' => [
					'prefix' => __( 'Prefix', 'vamtam-elementor-integration' ),
				],
			] );
		}

		if ( vamtam_theme_supports( 'nav-menu--underline-theme-pointer' ) ) {
			// Pointer.
			\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'pointer', [
				'options' => [
					'underline-theme' => __( 'Underline (Theme)', 'vamtam-elementor-integration' ),
				],
			] );
		}
	}

	if ( vamtam_theme_supports( [ 'nav-menu--prefix-pointer', 'nav-menu--line-through-pointer' ] ) ) {
		function update_color_menu_item_control( $controls_manager, $widget ) {
			// Menu item color.
			\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'color_menu_item', [
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--main.e--pointer-prefix .elementor-item:not(:hover):after' => 'background-color: {{VALUE}}',
				],
			] );
		}

		function add_vamtam_animation_line_control( $controls_manager, $widget ) {
			$widget->start_injection( [
				'of' => 'animation_line',
			] );

			if ( vamtam_theme_supports( 'nav-menu--prefix-pointer' ) ) {
				$widget->add_control(
					'animation_line_vamtam_prefix',
					[
						'label' => __( 'Animation', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::SELECT,
						'default' => 'prefix-grow',
						'options' => [
							'prefix-grow' => __( 'Prefix Grow', 'vamtam-elementor-integration' ),
							'fade' => __( 'Fade', 'vamtam-elementor-integration' ),
							'slide' => __( 'Slide', 'vamtam-elementor-integration' ),
							'grow' => __( 'Grow', 'vamtam-elementor-integration' ),
							'drop-in' => __( 'Drop In', 'vamtam-elementor-integration' ),
							'drop-out' => __( 'Drop Out', 'vamtam-elementor-integration' ),
							'none' => __( 'None', 'vamtam-elementor-integration' ),
						],
						'condition' => [
							'layout!' => 'dropdown',
							'pointer' => 'prefix',
						],
					]
				);
			}

			if ( vamtam_theme_supports( 'nav-menu--line-through-pointer' ) ) {
				$widget->add_control(
					'animation_line_vamtam_line_through',
					[
						'label' => __( 'Animation', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::SELECT,
						'default' => 'horizontal-grow',
						'options' => [
							'horizontal-grow' => __( 'Horizontal Grow', 'vamtam-elementor-integration' ),
							'fade' => __( 'Fade', 'vamtam-elementor-integration' ),
							'grow' => __( 'Grow', 'vamtam-elementor-integration' ),
							'none' => __( 'None', 'vamtam-elementor-integration' ),
						],
						'condition' => [
							'layout!' => 'dropdown',
							'pointer' => 'line-through',
						],
					]
				);
			}

			$widget->end_injection();
		}
	}

	if ( vamtam_theme_supports( [ 'nav-menu--prefix-pointer', 'nav-menu--pointer-anim-bounce', 'nav-menu--underline-theme-pointer' ] ) ) {
		function update_animation_line_control( $controls_manager, $widget ) {
			if ( vamtam_theme_supports( 'nav-menu--prefix-pointer' ) ) {
				// Animation Line.
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'animation_line', [
					'condition' => [
						'pointer!' => 'prefix',
					],
				] );
			}

			if ( vamtam_theme_supports( 'nav-menu--pointer-anim-bounce' ) ) {
				// Animation Line.
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'animation_line', [
					'options' => [
						'bounce' => __( 'Bounce', 'vamtam-elementor-integration' ),
					],
				] );
			}

			if ( vamtam_theme_supports( 'nav-menu--underline-theme-pointer' ) ) {
				// Underline-Theme.
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'animation_line', [
					'condition' => [
						'pointer' => [ 'underline-theme' ],
					],
				] );
			}
		}
	}
}

if ( vamtam_theme_supports( 'nav-menu--disable-scroll-on-mobile' ) ) {
	function add_disable_scroll_on_mobile_control( $controls_manager, $widget ) {
		$widget->add_control(
			'vamtam_disable_scroll_on_mobile',
			[
				'label' => __( 'Disable Page Scroll', 'vamtam-elementor-integration' ),
				'description' => __( 'Disables the page scroll when the mobile dropdown menu is toggled.', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'mobile-disable-scroll',
				'default' => 'mobile-disable-scroll',
			]
		);
		if ( vamtam_theme_supports( 'nav-menu--mobile-menu-overlay' ) ) {
			$widget->add_control(
				'vamtam_mobile_menu_overlay',
				[
					'label' => __( 'Use Overlay', 'vamtam-elementor-integration' ),
					'description' => __( 'Applies an overlay that covers the area beneath the toggled dropdown menu.', 'vamtam-elementor-integration' ),
					'type' => $controls_manager::SWITCHER,
					'prefix_class' => '',
					'return_value' => 'vamtam-overlay-trigger',
					'condition' => [
						'vamtam_disable_scroll_on_mobile!' => '',
					]
				]
			);
		}
		$widget->add_control(
			'vamtam_mobile_menu_use_max_height',
			[
				'label' => __( 'Enforce Max Height', 'vamtam-elementor-integration' ),
				'description' => __( 'Use this option if the mobile dropdown menu is getting cut and items become unreachable.', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'mobile-menu-max-height',
				'condition' => [
					'vamtam_disable_scroll_on_mobile!' => '',
				]
			]
		);
		$widget->add_responsive_control(
			'vamtam_mobile_menu_max_height',
			[
				'label' => esc_html__( 'Max Height (vh)', 'vamtam-elementor-integration' ),
				'description' => __( 'Adjust this value until all items are scroll-reachable. Test this on the frontend as on the editor there can be elements that take up space which are hidden on the frontend.<i>Please ignore the desktop variant of this option as its value is not used.</i>', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'devices' => [ 'desktop', 'tablet', 'mobile' ], // 'desktop' we don't really need (and is not applied anywhere), but it breaks the editor when the "Additional Custom Breakpoints" feature is enabled.
				'size_units' => [ 'vh' ],
				'range' => [
					'vh' => [
						'min' => 50,
						'max' => 100,
					],
				],
				'tablet_default' => [
					'size' => 80,
					'unit' => 'vh',
				],
				'mobile_default' => [
					'size' => 80,
					'unit' => 'vh',
				],
				'selectors' => [
					'{{WRAPPER}}' => "--vamtam-mobile-menu-max-height: {{SIZE}}vh",
				],
				'condition' => [
					'vamtam_disable_scroll_on_mobile!' => '',
					'vamtam_mobile_menu_use_max_height!' => '',
				],
			]
		);
	}
}

if ( vamtam_theme_supports( [ 'nav-menu--disable-scroll-on-mobile', 'nav-menu--toggle-diff-icon-dimensions' ] ) ) {
	// Vamtam_Widget_Nav_Menu.
	function widgets_registered() {
		if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
			return;
		}

		if ( ! class_exists( '\ElementorPro\Modules\NavMenu\Widgets\Nav_Menu' ) ) {
			return; // Elementor's autoloader acts weird sometimes.
		}

		class Vamtam_Widget_Nav_Menu extends Elementor_Nav_Menu {
			public $extra_depended_scripts = [
				'vamtam-nav-menu',
			];

			/*
				We override the get_script_depends method directly because Elementor's
				Elementor_Nav_Menu class returns the array directly, like so:

					public function get_script_depends() {
						return [ 'smartmenus' ];
					}

				If this changes, we should update this and probably filter the script in the
				add_extra_script_depends method.
			*/

			public function get_script_depends() {
				return [
					'smartmenus',
					'vamtam-nav-menu',
				];
			}

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
					'vamtam-nav-menu',
					VAMTAM_ELEMENTOR_INT_URL . 'assets/js/widgets/nav-menu/vamtam-nav-menu' . $suffix . '.js',
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

		// Replace current products widget with our extended version.
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets_manager->unregister( 'nav-menu' );
		$widgets_manager->register( new Vamtam_Widget_Nav_Menu );
	}
	add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
}

function add_prefix_for_toggle_active_state( $controls_manager, $widget ) {
	// Icon Active State.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'toggle_icon_active', [
		'render_type' => 'template',
		'frontend_available' => true,
	] );
}

// Content - Layout section
function section_section_layout_before_section_end( $widget, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	if ( vamtam_theme_supports( [ 'nav-menu--line-through-pointer', 'nav-menu--prefix-pointer', 'nav-menu--underline-theme-pointer' ] ) ) {
		update_pointer_control( $controls_manager, $widget );
	}
	if ( vamtam_theme_supports( [ 'nav-menu--prefix-pointer', 'nav-menu--pointer-anim-bounce' ] ) ) {
		update_animation_line_control( $controls_manager, $widget );
	}
	if ( vamtam_theme_supports( [ 'nav-menu--prefix-pointer', 'nav-menu--line-through-pointer' ] ) ) {
		add_vamtam_animation_line_control( $controls_manager, $widget );
	}
	if ( vamtam_theme_supports( 'nav-menu--disable-scroll-on-mobile' ) ) {
		add_disable_scroll_on_mobile_control( $controls_manager, $widget );
	}
	add_prefix_for_toggle_active_state( $controls_manager, $widget );
}
add_action( 'elementor/element/nav-menu/section_layout/before_section_end', __NAMESPACE__ . '\section_section_layout_before_section_end', 10, 2 );
