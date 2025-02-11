<?php
namespace VamtamElementor\Widgets\ProductsCategories;

// Extending the Products Categories widget.

// Is WC Widget.
if ( ! vamtam_has_woocommerce() ) {
	return;
}

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

// Add Switcher Control.
function add_show_count_control( $controls_manager, $widget ) {
	$widget->add_control(
		'show_cat_count',
		[
			'label' => __( 'Show Count', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::SWITCHER,
			'label_on' => __( 'Show', 'vamtam-elementor-integration' ),
			'label_off' => __( 'Hide', 'vamtam-elementor-integration' ),
			'prefix_class' => 'vamtam-no-count--',
			'return_value' => 'yes',
			'default' => apply_filters( 'vamtam_wc-categories_show_cat_count_default', 'yes' ),
		]
	);
}

function update_controls_style_tab( $controls_manager, $widget ) {
	$new_options = [
		'selectors' => [
			'{{WRAPPER}} ul.products li.product-category.product .vamtam-product-cat-content > .woocommerce-loop-category__title' => 'padding-top: {{SIZE}}{{UNIT}}',
		]
	];
	// Image Spacing.
	\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'image_spacing', $new_options );

	if ( current_theme_supports( 'vamtam-elementor-widgets', 'wc-categories--hide-title' ) ) {
		$new_options = [
			'condition' => [
				'hide_cat_title' => '',
			]
		];

		// Heading Title.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'heading_title_style', $new_options );
		// Title Color.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'title_color', $new_options );
		// Title Typography.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'title_typography', $new_options, \Elementor\Group_Control_Typography::get_type() );
	}

	if ( current_theme_supports( 'vamtam-elementor-widgets', 'wc-categories--bijoux-default-column-gap' ) ) {
		// Column gap.
		\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'column_gap', [
			'default'   => [
				'size' => 60,
			],
		] );
	}
}

if ( vamtam_theme_supports( 'general-products--use-theme-style' ) ) {
	function add_use_theme_layout_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'wc_style_warning',
			'at' => 'before',
		] );

		// Use theme layout for products.
		$widget->add_control(
			'use_product_cats_theme_style',
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

// Products section.
function section_products_style_before_section_end ( $widget, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	add_show_count_control( $controls_manager, $widget );
	update_controls_style_tab( $controls_manager, $widget);
	
	if ( vamtam_theme_supports( 'general-products--use-theme-style' ) ) {
		add_use_theme_layout_control( $controls_manager, $widget );
	}
}
add_action( 'elementor/element/wc-categories/section_products_style/before_section_end', __NAMESPACE__ . '\section_products_style_before_section_end', 10, 2 );

if ( current_theme_supports( 'vamtam-elementor-widgets', 'wc-categories--hide-title' ) ) {
	function add_hide_title_control( $controls_manager, $widget ) {
		$widget->add_control(
			'hide_cat_title',
			[
				'label' => __( 'Hide Category Title', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'hide-title',
			]
		);
	}

	function section_layout_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_hide_title_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/wc-categories/section_layout/before_section_end', __NAMESPACE__ . '\section_layout_before_section_end', 10, 2 );
}
