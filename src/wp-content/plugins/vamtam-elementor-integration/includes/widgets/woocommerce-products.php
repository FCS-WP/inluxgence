<?php
namespace VamtamElementor\Widgets\Products;

use \ElementorPro\Modules\Woocommerce\Classes\Products_Renderer;
use \ElementorPro\Modules\Woocommerce\Classes\Current_Query_Renderer;
use \ElementorPro\Modules\Woocommerce\Widgets\Products as ElementorProducts;
// Extending the WC Products widget.

// Is WC Widget.
if ( ! vamtam_has_woocommerce() ) {
	return;
}

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'woocommerce-products--hide-price' ) ) {
	function update_controls_style_tab( $controls_manager, $widget ) {
		$new_options = [
			'condition' => [
				'hide_product_price' => '',
			]
		];

		// Heading Price.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'heading_price_style', $new_options );
		// Heading Old Price.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'heading_old_price_style', $new_options );
		// Price Color.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'price_color', $new_options );
		// Old Price Color.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'old_price_color', $new_options );
		// Price Typography.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'price_typography', $new_options, \Elementor\Group_Control_Typography::get_type() );
		// Old Price Typography.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'old_price_typography', $new_options, \Elementor\Group_Control_Typography::get_type() );
	}

	function add_hide_price_control( $controls_manager, $widget ) {
		$widget->add_control(
			'hide_product_price',
			[
				'label' => __( 'Hide Product Price', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'hide-price',
				'render_type' => 'template',
			]
		);
	}

	function section_products_style_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		update_controls_style_tab( $controls_manager, $widget );
	}
	add_action( 'elementor/element/woocommerce-products/section_products_style/before_section_end', __NAMESPACE__ . '\section_products_style_before_section_end', 10, 2 );
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'woocommerce-products--hide-price' ) ) {
	function section_content_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		if ( current_theme_supports( 'vamtam-elementor-widgets', 'woocommerce-products--hide-price' ) ) {
			add_hide_price_control( $controls_manager, $widget );
		}
	}
	add_action( 'elementor/element/woocommerce-products/section_content/before_section_end', __NAMESPACE__ . '\section_content_before_section_end', 10, 2 );
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'woocommerce-products--table-layout' ) ) {

	function vamtam_add_table_layout_skin( $widget ) {
		if ( did_action( 'vamtam_elementor/woocommerce-products/products_table_layout/skin_registered' ) ) {
			// This is in case another theme feature requires to re-register (extend) the widget.
			// In that case the widget's action's will be added twice leading to control stack issues.
			return;
		}

		class Vamtam_Products_Renderer extends Products_Renderer {
			public function get_attributes() {
				return $this->attributes;
			}
			public function get_query_results() {
				return parent::get_query_results();
			}
			public function get_type() {
				return $this->type;
			}
		}

		class Vamtam_Products_Table_Layout extends \Elementor\Skin_Base {
			public function get_id() {
				return 'products_table_layout';
			}

			public function get_title() {
				return __( 'Table Layout', 'vamtam-elementor-integration' );
			}

			public function render() {
				$widget = $this->parent;
				$skin   = $this;

				if ( WC()->session ) {
					wc_print_notices();
				}

				// For Products_Renderer.
				if ( ! isset( $GLOBALS['post'] ) ) {
					$GLOBALS['post'] = null; // WPCS: override ok.
				}

				$settings = $widget->get_settings();
				$settings['columns'] = 1; // On table layout only the rows count should matter for the posts_per_page.

				$hide_product_price      = current_theme_supports( 'vamtam-elementor-widgets', 'woocommerce-products--hide-price' ) && isset( $settings['hide_product_price'] ) && ! empty( $settings['hide_product_price'] );
				$hide_product_categories = current_theme_supports( 'vamtam-elementor-widgets', 'products-base--hide-categories' ) && isset( $settings['hide_product_categories'] ) && ! empty( $settings['hide_product_categories'] );

				if ( $hide_product_price ) {
					$GLOBALS['vamtam_wc_products_hide_price'] = true;
				}

				if ( $hide_product_categories ) {
					$GLOBALS['vamtam_wc_products_hide_categories'] = true;
				}

				$products_renderer = $skin->get_shortcode_object( $settings );
				$content           = $this->get_products_table_layout_content( $products_renderer, $settings );

				if ( $hide_product_price ) {
					unset( $GLOBALS['vamtam_wc_products_hide_price'] );
				}

				if ( $hide_product_categories ) {
					unset( $GLOBALS['vamtam_wc_products_hide_categories'] );
				}

				if ( $content ) {
					echo $content;
				} elseif ( $widget->get_settings( 'nothing_found_message' ) ) {
					echo '<div class="elementor-nothing-found elementor-products-nothing-found">' . esc_html( $widget->get_settings( 'nothing_found_message' ) ) . '</div>';
				}

			}

			protected function _register_controls_actions() {
				add_action( 'elementor/element/woocommerce-products/section_content/before_section_end', [ $this, 'section_content_before_section_end' ] );
				add_action( 'elementor/element/woocommerce-products/section_products_style/before_section_end', [ $this, 'section_products_style_before_section_end' ] );
				add_action( 'elementor/element/woocommerce-products/section_products_style/after_section_end', [ $this, 'section_products_style_after_section_end' ] );
				add_action( 'elementor/element/woocommerce-products/section_design_box/before_section_end', [ $this, 'section_design_box_before_section_end' ] );
				add_action( 'elementor/element/woocommerce-products/sale_flash_style/after_section_end', [ $this, 'sale_flash_style_after_section_end' ] );

				// !! Important: Add this action on every custom skin to avoid issues with widget extensions. !!
				do_action( 'vamtam_elementor/woocommerce-products/products_table_layout/skin_registered' );
			}

			public function section_content_before_section_end( $widget ) {
				$this->parent = $widget;
				$this->update_controls_content_tab( $widget );
				$this->add_controls_content_tab( $widget );
			}
			protected function update_controls_content_tab( $widget ) {
				$controls_manager = \Elementor\Plugin::instance()->controls_manager;
				// Rows.
				\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'rows', ['min' => -1 ] );
			}
			protected function add_controls_content_tab( $widget ) {
				$controls_manager = \Elementor\Plugin::instance()->controls_manager;
				$widget->start_injection( [
					'of' => 'columns',
					'at' => 'after',
				] );
				// Columns option should always exist (Product Renderer),
				// so we add an alert for table layout instead of removing it.
				$widget->add_control(
					'columns_info_table_layout',
					[
						'type' => $controls_manager::RAW_HTML,
						'raw' => __( 'Columns are not taken into account for Table layout.', 'vamtam-elementor-integration' ),
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
						'condition' => [
							'_skin' => 'products_table_layout',
						]
					]
				);
				$widget->end_injection();
			}

			public function section_products_style_before_section_end( $widget ) {
				$controls_manager = \Elementor\Plugin::instance()->controls_manager;
				$this->hide_products_style_controls( $controls_manager, $widget );
				$this->update_products_style_controls( $controls_manager, $widget );
				$this->add_products_style_controls( $controls_manager, $widget );
			}
			protected function hide_products_style_controls( $controls_manager, $widget ) {
				$new_options = [
					'condition' => [
						'_skin!' => 'products_table_layout',
					]
				];
				// Column gap.
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'column_gap', $new_options );
				// Row gap.
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'row_gap', $new_options );
				// Image.
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'heading_image_style', $new_options );
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'image_border', $new_options, \Elementor\Group_Control_Border::get_type() );
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'image_border_radius', $new_options );
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'image_spacing', $new_options );
				// Rating.
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'heading_rating_style', $new_options );
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'star_color', $new_options );
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'empty_star_color', $new_options );
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'star_size', $new_options );
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'rating_spacing', $new_options );
			}
			protected function update_products_style_controls( $controls_manager, $widget ) {
				// Alignment.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products .product td' => 'text-align: {{VALUE}}',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'align', $new_options );

				// Title Color.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products .product .woocommerce-loop-product__title' => 'color: {{VALUE}}',
						'{{WRAPPER}}.elementor-wc-products .products .product .woocommerce-loop-category__title' => 'color: {{VALUE}}',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'title_color', $new_options );

				// Title Typography.
				$new_options = [
					'selector' => [
						'{{WRAPPER}}.elementor-wc-products .products .product .woocommerce-loop-product__title, ' .
						'{{WRAPPER}}.elementor-wc-products .products .product .woocommerce-loop-category__title',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'title_typography', $new_options, \Elementor\Group_Control_Typography::get_type() );

				// Title Spacing.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products .product .woocommerce-loop-product__title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
						'{{WRAPPER}}.elementor-wc-products .products .product .woocommerce-loop-category__title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'title_spacing', $new_options );

				// Price Color.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products .product .price' => 'color: {{VALUE}}',
						'{{WRAPPER}}.elementor-wc-products .products .product .price ins' => 'color: {{VALUE}}',
						'{{WRAPPER}}.elementor-wc-products .products .product .price ins .amount' => 'color: {{VALUE}}',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'price_color', $new_options );

				// Price Typography.
				$new_options = [
					'selector' => '{{WRAPPER}}.elementor-wc-products .products .product .price',
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'price_typography', $new_options, \Elementor\Group_Control_Typography::get_type() );

				// Regular Price Color.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products .product .price del' => 'color: {{VALUE}}',
						'{{WRAPPER}}.elementor-wc-products .products .product .price del .amount' => 'color: {{VALUE}}',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'old_price_color', $new_options );

				// Regular Price Typography.
				$new_options = [
					'selector' => '{{WRAPPER}}.elementor-wc-products .products .product .price del .amount, ' .
									'{{WRAPPER}}.elementor-wc-products .products .product .price del',
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'old_price_typography', $new_options, \Elementor\Group_Control_Typography::get_type() );

				// Button Text Color.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products .product .button' => 'color: {{VALUE}};',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_text_color', $new_options );

				// Button Bg Color.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products .product .button' => 'background-color: {{VALUE}};',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_background_color', $new_options );

				// Button Border Color.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products .product .button' => 'border-color: {{VALUE}};',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_border_color', $new_options );

				// Button Typography.
				$new_options = [
					'selector' => '{{WRAPPER}}.elementor-wc-products .products .product .button',
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_typography', $new_options, \Elementor\Group_Control_Typography::get_type() );

				// Button Hover Text Color.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products .product .button:hover' => 'color: {{VALUE}};',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_hover_color', $new_options );

				// Button Hover Bg Color.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products .product .button:hover' => 'background-color: {{VALUE}};',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_hover_background_color', $new_options );

				// Button Hover Border Color.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products .product .button:hover' => 'border-color: {{VALUE}};',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_hover_border_color', $new_options );

				// Button Border.
				$new_options = [
					'selector' => '{{WRAPPER}}.elementor-wc-products .products .product .button',
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_border', $new_options, \Elementor\Group_Control_Border::get_type() );

				// Button Border Radius.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products .product .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_border_radius', $new_options );

				// Button Text Padding.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products .product .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_text_padding', $new_options );

				// Button Spacing.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products .product .button' => 'margin-top: {{SIZE}}{{UNIT}}',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_spacing', $new_options );
			}
			protected function add_products_style_controls( $controls_manager, $widget ) {
				$widget->start_injection( [
					'of' => 'align',
				] );

				$widget->add_responsive_control(
					'vamtam_vertical_align',
					[
						'label' => __( 'Vertical Alignment', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::CHOOSE,
						'default' => 'middle',
						'options' => [
							'top' => [
								'title' => __( 'Top', 'vamtam-elementor-integration' ),
								'icon' => 'eicon-v-align-top',
							],
							'middle' => [
								'title' => __( 'Center', 'vamtam-elementor-integration' ),
								'icon' => 'eicon-v-align-middle',
							],
							'bottom' => [
								'title' => __( 'Bottom', 'vamtam-elementor-integration' ),
								'icon' => 'eicon-v-align-bottom',
							],
						],
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products .product td' => 'vertical-align: {{VALUE}}',
						],
						'condition' => [
							'_skin' => 'products_table_layout',
						]
					]
				);
				$widget->end_injection();

				// Title.
				$widget->start_injection( [
					'of' => 'title_color',
					'at' => 'before',
				] );

				$widget->add_control(
					'title_column_header_text',
					[
						'label' => __( 'Header Text', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::TEXT,
						'default' => __( 'Title', 'vamtam-elementor-integration' ),
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->add_responsive_control(
					'title_column_width',
					[
						'label' => __( 'Column Width', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::SLIDER,
						'size_units' => [ '%', 'px' ],
						'default' => [
							'unit' => '%',
							'size' => 40,
						],
						'range' => [
							'%' => [
								'min' => 5,
								'max' => 50,
							],
							'px' => [
								'min' => 0,
								'max' => 200,
							],
						],
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .table-layout .vamtam-headers th.vamtam-title' => 'width: {{SIZE}}{{UNIT}}',
						],
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->add_control(
					'title_is_link',
					[
						'label' => __( 'Title Links To Product', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::SWITCHER,
						'prefix_class' => 'vamtam-has-',
						'return_value' => 'title-is-link',
						'render_type' => 'template',
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->end_injection();

				// Price.
				$widget->start_injection( [
					'of' => 'price_color',
					'at' => 'before',
				] );

				$widget->add_control(
					'price_column_header_text',
					[
						'label' => __( 'Header Text', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::TEXT,
						'default' => __( 'Price', 'vamtam-elementor-integration' ),
						'condition' => [
							'_skin' => 'products_table_layout',
							'hide_product_price' => '',
						],
					]
				);

				$widget->add_responsive_control(
					'price_column_width',
					[
						'label' => __( 'Column Width', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::SLIDER,
						'size_units' => [ '%', 'px' ],
						'default' => [
							'unit' => '%',
							'size' => 20,
						],
						'range' => [
							'%' => [
								'min' => 5,
								'max' => 50,
							],
							'px' => [
								'min' => 0,
								'max' => 200,
							],
						],
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .table-layout .vamtam-headers th.vamtam-price' => 'width: {{SIZE}}{{UNIT}}',
						],
						'condition' => [
							'_skin' => 'products_table_layout',
							'hide_product_price' => '',
						],
					]
				);

				$widget->end_injection();

				// Quantity.
				$widget->start_injection( [
					'of' => 'heading_button_style',
					'at' => 'before',
				] );

				$widget->add_control(
					'heading_quantity_style',
					[
						'label' => __( 'Quantity', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::HEADING,
						'separator' => 'before',
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->add_control(
					'quantity_column_header_text',
					[
						'label' => __( 'Header Text', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::TEXT,
						'default' => __( 'Quantity', 'vamtam-elementor-integration' ),
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->add_responsive_control(
					'quantity_column_width',
					[
						'label' => __( 'Column Width', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::SLIDER,
						'size_units' => [ '%', 'px' ],
						'default' => [
							'unit' => '%',
							'size' => 20,
						],
						'range' => [
							'%' => [
								'min' => 5,
								'max' => 50,
							],
							'px' => [
								'min' => 0,
								'max' => 200,
							],
						],
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .table-layout .vamtam-headers th.vamtam-quantity' => 'width: {{SIZE}}{{UNIT}}',
						],
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->add_group_control(
					\Elementor\Group_Control_Border::get_type(), [
						'name' => 'quantity_border',
						'exclude' => [ 'color' ],
						'selector' => '{{WRAPPER}}.elementor-wc-products .products.table-layout .product .quantity input',
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->add_control(
					'quantity_text_padding',
					[
						'label' => __( 'Text Padding', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::DIMENSIONS,
						'size_units' => [ 'px', 'em', '%' ],
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout .product .quantity input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->start_controls_tabs( 'tabs_quantity_style' );

				$widget->start_controls_tab(
					'tab_quantity_normal',
					[
						'label' => __( 'Normal', 'vamtam-elementor-integration' ),
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]

				);

				$widget->add_control(
					'quantity_text_color',
					[
						'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'default' => '',
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout .product .quantity input' => 'color: {{VALUE}};',
						],
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->add_control(
					'quantity_background_color',
					[
						'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout .product .quantity input' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->add_control(
					'quantity_border_color',
					[
						'label' => __( 'Border Color', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout .product .quantity input' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'quantity_typography',
						'selector' => '{{WRAPPER}}.elementor-wc-products .products.table-layout .product .quantity input',
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->end_controls_tab();

				$widget->start_controls_tab(
					'tab_quantity_hover',
					[
						'label' => __( 'Hover', 'vamtam-elementor-integration' ),
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->add_control(
					'quantity_hover_color',
					[
						'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout .product .quantity input:hover' => 'color: {{VALUE}};',
						],
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->add_control(
					'quantity_hover_background_color',
					[
						'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout .product .quantity input:hover' => 'background-color: {{VALUE}};',
						],
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->add_control(
					'quantity_hover_border_color',
					[
						'label' => __( 'Border Color', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout .product .quantity input:hover' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->end_controls_tab();

				$widget->end_controls_tabs();

				$widget->end_injection();

				// Button.
				$widget->start_injection( [
					'of' => 'heading_button_style',
				] );

				$widget->add_control(
					'button_column_header_text',
					[
						'label' => __( 'Header Text', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::TEXT,
						'default' => __( '', 'vamtam-elementor-integration' ),
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->add_responsive_control(
					'button_column_width',
					[
						'label' => __( 'Column Width', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::SLIDER,
						'size_units' => [ '%', 'px' ],
						'default' => [
							'unit' => '%',
							'size' => 20,
						],
						'range' => [
							'%' => [
								'min' => 5,
								'max' => 50,
							],
							'px' => [
								'min' => 0,
								'max' => 200,
							],
						],
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .table-layout .vamtam-headers th.vamtam-button' => 'width: {{SIZE}}{{UNIT}}',
						],
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->add_control(
					'adc_triggers_menu_cart',
					[
						'label' => __( 'Button Opens Menu Cart', 'vamtam-elementor-integration' ),
						'description' => __( 'If exists, menu cart opens after "Add To Cart" button is clicked.', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::SWITCHER,
						'prefix_class' => 'vamtam-has-',
						'return_value' => 'adc-triggers-menu-cart',
						'default' => 'adc-triggers-menu-cart',
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);

				$widget->end_injection();

			}
			public function section_products_style_after_section_end( $widget ) {
				$controls_manager = \Elementor\Plugin::instance()->controls_manager;
				$this->add_table_section( $controls_manager, $widget );
			}
			protected function add_table_section( $controls_manager, $widget ) {
				$widget->start_controls_section(
					'section_table_style',
					[
						'label' => __( 'Table', 'vamtam-elementor-integration' ),
						'tab' => $controls_manager::TAB_STYLE,
						'condition' => [
							'_skin' => 'products_table_layout',
						],
					]
				);
				// Headers.
				$widget->add_control(
					'table_headers_heading',
					[
						'label' => __( 'Headers', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::HEADING,
					]
				);

				$widget->add_control(
					'table_hide_headers',
					[
						'label' => __( 'Hide Headers', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::SWITCHER,
						'prefix_class' => 'vamtam-has-',
						'return_value' => 'no-headers',
					]
				);

				$widget->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' => 'headers_typography',
						'selector' => '{{WRAPPER}}.elementor-wc-products .products.table-layout tr.vamtam-headers th',
					]
				);

				// Borders.
				$widget->add_control(
					'table_borders_heading',
					[
						'label' => __( 'Borders', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::HEADING,
						'separator' => 'before',
					]
				);
				$widget->add_control(
					'table_header_border_width',
					[
						'label' => __( 'Header Border Width', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::DIMENSIONS,
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 50,
							],
						],
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout tr.vamtam-headers th' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						],
					]
				);
				$widget->add_control(
					'table_row_border_width',
					[
						'label' => __( 'Row Border Width', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::DIMENSIONS,
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 50,
							],
						],
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout tr' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						],
					]
				);
				$widget->add_control(
					'table_cell_border_width',
					[
						'label' => __( 'Cell Border Width', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::DIMENSIONS,
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 50,
							],
						],
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout td' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						],
					]
				);
				$widget->add_control(
					'table_border_collapse',
					[
						'label' => __( 'Border Collapse', 'vamtam-elementor-integration' ),
						'description' => __( 'Whether table borders should collapse into a single border or be separated.', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::SELECT,
						'default' => '',
						'options' => [
							'' => __( 'Default', 'vamtam-elementor-integration' ),
							'separate' => __( 'Separate', 'vamtam-elementor-integration' ),
							'collapse' => __( 'Collapse', 'vamtam-elementor-integration' ),
						],
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout' => 'border-collapse:{{VALUE}}',
						],
					]
				);
				// Colors.
				$widget->add_control(
					'table_colors_heading',
					[
						'label' => __( 'Colors', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::HEADING,
						'separator' => 'before',
					]
				);

				$widget->add_control(
					'table_use_zebra',
					[
						'label' => __( 'Zebra Striped', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::SWITCHER,
						'prefix_class' => 'vamtam-has-',
						'return_value' => 'zebra-stripes',
					]
				);

				$widget->start_controls_tabs( 'table_style_tabs' );

				$widget->start_controls_tab( 'table_colors_normal',
					[
						'label' => __( 'Normal', 'vamtam-elementor-integration' ),
					]
				);

				$widget->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'table_row_box_shadow',
						'selector' => '{{WRAPPER}}.elementor-wc-products .products.table-layout tr',
					]
				);

				$widget->add_control(
					'table_row_bg_color',
					[
						'label' => __( 'Row Background Color', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout tr' => 'background-color: {{VALUE}}',
						],
						'condition' => [
							'table_use_zebra' => ''
						]
					]
				);

				$widget->add_control(
					'table_row_bg_color_even',
					[
						'label' => __( 'Row Background Color (Even)', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout tr:nth-child(even)' => 'background-color: {{VALUE}}',
						],
						'condition' => [
							'table_use_zebra!' => ''
						]
					]
				);

				$widget->add_control(
					'table_row_bg_color_odd',
					[
						'label' => __( 'Row Background Color (Odd)', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout tr:nth-child(odd)' => 'background-color: {{VALUE}}',
						],
						'condition' => [
							'table_use_zebra!' => ''
						]
					]
				);

				$widget->add_control(
					'table_header_color',
					[
						'label' => __( 'Header Color', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout tr.vamtam-headers th' => 'color: {{VALUE}}',
						],
					]
				);

				$widget->add_control(
					'table_header_bg_color',
					[
						'label' => __( 'Header Background Color', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout tr.vamtam-headers' => 'background-color: {{VALUE}}',
						],
					]
				);

				$widget->add_control(
					'table_border_color',
					[
						'label' => __( 'Border Color', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'selectors' => [
							implode( ',', [
								'{{WRAPPER}}.elementor-wc-products .products.table-layout tr',
								'{{WRAPPER}}.elementor-wc-products .products.table-layout td',
								'{{WRAPPER}}.elementor-wc-products .products.table-layout th',
							] ) => 'border-color: {{VALUE}}',
						],
					]
				);

				$widget->end_controls_tab();

				$widget->start_controls_tab( 'table_colors_hover',
					[
						'label' => __( 'Hover', 'vamtam-elementor-integration' ),
					]
				);

				$widget->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'table_row_shadow_hover',
						'selector' => '{{WRAPPER}}.elementor-wc-products .products.table-layout tr:hover'
					]
				);

				$widget->add_control(
					'table_row_bg_color_hover',
					[
						'label' => __( 'Row Background Color', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout tr:hover' => 'background-color: {{VALUE}}',
						],
						'condition' => [
							'table_use_zebra' => ''
						]
					]
				);

				$widget->add_control(
					'table_row_bg_color_even_hover',
					[
						'label' => __( 'Row Background Color (Even)', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout tr:nth-child(even):hover' => 'background-color: {{VALUE}}',
						],
						'condition' => [
							'table_use_zebra!' => ''
						]
					]
				);

				$widget->add_control(
					'table_row_bg_color_odd_hover',
					[
						'label' => __( 'Row Background Color (Odd)', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout tr:nth-child(odd):hover' => 'background-color: {{VALUE}}',
						],
						'condition' => [
							'table_use_zebra!' => ''
						]
					]
				);

				$widget->add_control(
					'table_header_color_hover',
					[
						'label' => __( 'Header Color', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout tr.vamtam-headers:hover th' => 'color: {{VALUE}}',
						],
					]
				);

				$widget->add_control(
					'table_header_bg_color_hover',
					[
						'label' => __( 'Header Background Color', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'selectors' => [
							'{{WRAPPER}}.elementor-wc-products .products.table-layout tr.vamtam-headers:hover' => 'background-color: {{VALUE}}',
						],
					]
				);

				$widget->end_controls_tab();

				$widget->end_controls_tabs();

				$widget->end_controls_section();
			}
			public function section_design_box_before_section_end( $widget ) {
				$controls_manager = \Elementor\Plugin::instance()->controls_manager;
				// Border Width.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products.table-layout' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'box_border_width', $new_options );

				// Border Radius.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products.table-layout' => 'border-radius: {{SIZE}}{{UNIT}}',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'box_border_radius', $new_options );

				// Box Padding.
				$new_options = [
					'condition' => [
						'_skin!' => 'products_table_layout',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'box_padding', $new_options );

				// Box Shadow.
				$new_options = [
					'selector' => '{{WRAPPER}}.elementor-wc-products .products.table-layout',
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'box_shadow', $new_options, \Elementor\Group_Control_Box_Shadow::get_type() );

				// Bg Color.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products.table-layout' => 'background-color: {{VALUE}}',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'box_bg_color', $new_options );

				// Border Color.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products.table-layout' => 'border-color: {{VALUE}}',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'box_border_color', $new_options );

				// Box Shadow Hover.
				$new_options = [
					'selector' => '{{WRAPPER}}.elementor-wc-products .products.table-layout:hover',
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'box_shadow_hover', $new_options, \Elementor\Group_Control_Box_Shadow::get_type() );

				// Bg Color Hover.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products.table-layout:hover' => 'background-color: {{VALUE}}',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'box_bg_color_hover', $new_options );

				// Border Color Hover.
				$new_options = [
					'selectors' => [
						'{{WRAPPER}}.elementor-wc-products .products.table-layout:hover' => 'border-color: {{VALUE}}',
					],
				];
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'box_border_color_hover', $new_options );
			}

			public function sale_flash_style_after_section_end( $widget ) {
				$controls_manager = \Elementor\Plugin::instance()->controls_manager;
				// Hide section.
				$new_options = [
					'condition' => [
						'_skin!' => 'products_table_layout',
					]
				];
				// Sale Flash section.
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'sale_flash_style', $new_options );
			}

			protected function get_shortcode_object( $settings ) {
				if ( 'current_query' === $settings[ Products_Renderer::QUERY_CONTROL_NAME . '_post_type' ] ) {
					$type = 'current_query';
					return new Current_Query_Renderer( $settings, $type );
				}
				$type = 'products';
				return new Vamtam_Products_Renderer( $settings, $type );
			}

			protected function get_products_table_layout_content( $products_renderer, array $settings ) {
				ob_start();
				$this->render_products_table_layout( $products_renderer, $settings );
				return ob_get_clean();
			}

			protected function render_products_table_layout( $products_renderer, array $settings ) {
				$query_args = $products_renderer->get_query_args();

				if ( empty( $query_args ) ) {
					return;
				}

				$attrs    = $products_renderer->get_attributes();
				$products = $products_renderer->get_query_results();

				// Setup the loop.
				wc_setup_loop(
					array(
						'columns'      => $attrs['columns'],
						'name'         => $products_renderer->get_type(),
						'is_shortcode' => true,
						'is_search'    => false,
						'is_paginated' => wc_string_to_bool( $attrs['paginate'] ),
						'total'        => $products->total,
						'total_pages'  => $products->total_pages,
						'per_page'     => $products->per_page,
						'current_page' => $products->current_page,
					)
				);

				if ( ! empty( $settings['show_result_count'] ) ) {
					woocommerce_result_count();
				}
				if ( ! empty( $settings['allow_order'] && ! is_front_page() ) ) {
					woocommerce_catalog_ordering();
				}
				?>
					<table class="products vamtam-wc table-layout">
						<tbody>
							<?php
								$this->render_products_table_headers( $settings );
								$loop = new \WP_Query( $query_args );
								if ( $loop->have_posts() ) {
									while ( $loop->have_posts() ) : $loop->the_post();
										$this->render_products_table_row( $settings );
									endwhile;
								} else {
									echo __( 'No products found' );
								}
								wp_reset_postdata();
							?>
							</tbody>
					</table>
				<?php
				if ( ! empty( $settings['paginate'] ) ) {
					woocommerce_pagination();
				}
			}

			protected function render_products_table_headers( array $settings ) {
				?>
					<tr class="vamtam-headers">
							<th class="vamtam-title">
								<?php echo esc_html( $settings['title_column_header_text'] ); ?>
							</th>
							<?php if ( empty( $settings['hide_product_price'] ) ) : ?>
								<th class="vamtam-price">
									<?php echo esc_html( $settings['price_column_header_text'] ); ?>
								</th>
							<?php endif; ?>
							<th class="vamtam-quanity">
								<?php echo esc_html( $settings['quantity_column_header_text'] ); ?>
							</th>
							<th class="vamtam-button">
								<?php echo esc_html( $settings['button_column_header_text'] ); ?>
							</th>
					</tr>
				<?php
			}

			protected function render_products_table_row( array $settings ) {
				// Ensure visibility.
				global $product;
				if ( empty( $product ) || ! $product->is_visible() ) {
					return;
				}
				?>
				<tr <?php wc_product_class( 'vamtam-product', $product ); ?>>
						<td>
							<?php if ( ! empty( $settings['title_is_link'] ) ) : ?>
								<a href="<?php echo esc_attr( get_permalink( $product->get_id() ) ); ?>">
									<?php woocommerce_template_loop_product_title(); ?>
								</a>
							<?php else : ?>
								<?php woocommerce_template_loop_product_title(); ?>
							<?php endif; ?>
						</td>
						<?php if ( empty( $settings['hide_product_price'] ) ) : ?>
							<td>
								<?php woocommerce_template_loop_price(); ?>
							</td>
						<?php endif; ?>
						<td>
							<?php woocommerce_quantity_input(); ?>
						</td>
						<td>
							<?php
								add_filter( 'woocommerce_loop_add_to_cart_args', [ __CLASS__, 'vamtam_woocommerce_loop_add_to_cart_args_table_layout' ], 10, 2 );
								woocommerce_template_loop_add_to_cart();
								remove_filter( 'woocommerce_loop_add_to_cart_args', [ __CLASS__, 'vamtam_woocommerce_loop_add_to_cart_args_table_layout' ], 10, 2 );
							?>
						</td>
				</tr>
				<?php
			}

			public static function vamtam_woocommerce_loop_add_to_cart_args_table_layout ( $args, $product ) {
				// Remove ajax_add_to_cart class. We use our own js handler.
				$args['class'] = str_replace( 'ajax_add_to_cart', '', $args['class'] );
				return $args;
			}
		}

		$widget->add_skin( new Vamtam_Products_Table_Layout( $widget ) );
	}

	add_action( 'elementor/widget/woocommerce-products/skins_init', __NAMESPACE__ . '\vamtam_add_table_layout_skin' );
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'woocommerce-products--hide-price' ) ||
	current_theme_supports( 'vamtam-elementor-widgets', 'products-base--product-image-anims' ) ) {
	// Vamtam_Widget_Products.
	function widgets_registered() {
		if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
			return;
		}

		if ( ! class_exists( '\ElementorPro\Modules\Woocommerce\Widgets\Products' ) ) {
			return; // Elementor's autoloader acts weird sometimes.
		}

		class Vamtam_Widget_Products extends ElementorProducts {
			public $extra_depended_scripts = [
				'vamtam-products-base',
			];

			// Extend constructor.
			public function __construct($data = [], $args = null) {
				parent::__construct($data, $args);

				if ( current_theme_supports( 'vamtam-elementor-widgets', 'products-base--product-image-anims' ) ) {
					$this->register_assets();

					$this->add_extra_script_depends();
				}
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

			// Extend render method.
			protected function render() {
				if ( WC()->session ) {
					wc_print_notices();
				}

				// For Products_Renderer.
				if ( ! isset( $GLOBALS['post'] ) ) {
					$GLOBALS['post'] = null; // WPCS: override ok.
				}

				$settings                = $this->get_settings();
				$hide_product_price      = current_theme_supports( 'vamtam-elementor-widgets', 'woocommerce-products--hide-price' ) && isset( $settings['hide_product_price'] ) && ! empty( $settings['hide_product_price'] );
				$hide_product_categories = current_theme_supports( 'vamtam-elementor-widgets', 'products-base--hide-categories' ) && isset( $settings['hide_product_categories'] ) && ! empty( $settings['hide_product_categories'] );

				if ( $hide_product_price ) {
					$GLOBALS['vamtam_wc_products_hide_price'] = true;
				}

				if ( $hide_product_categories ) {
					$GLOBALS['vamtam_wc_products_hide_categories'] = true;
				}

				$shortcode = $this->get_shortcode_object( $settings );

				$content = $shortcode->get_content();

				if ( $hide_product_price ) {
					unset( $GLOBALS['vamtam_wc_products_hide_price'] );
				}

				if ( $hide_product_categories ) {
					unset( $GLOBALS['vamtam_wc_products_hide_categories'] );
				}

				if ( $content ) {
					$content = str_replace( '<ul class="products', '<ul class="products elementor-grid', $content );

					echo $content;
				} elseif ( $this->get_settings( 'nothing_found_message' ) ) {
					echo '<div class="elementor-nothing-found elementor-products-nothing-found">' . esc_html( $this->get_settings( 'nothing_found_message' ) ) . '</div>';
				}
			}
		}

		// Replace current products widget with our extended version.
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;

		if ( ! wp_doing_cron() && ! wp_doing_ajax() && ! isset( $_GET['elementor_updater'] ) ) {
			$widgets_manager->unregister( 'woocommerce-products' );
			$widgets_manager->register( new Vamtam_Widget_Products );
		}
	}
	add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
}
