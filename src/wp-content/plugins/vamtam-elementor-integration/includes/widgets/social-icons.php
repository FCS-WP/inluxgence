<?php
namespace VamtamElementor\Widgets\SocialIcons;

// Extending the Social Icons widget.

if ( current_theme_supports( 'vamtam-elementor-widgets', 'social-icons--custom-layout' ) ) {
	function add_layout_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'shape',
		] );
		$widget->add_control(
			'layout',
			[
				'label' => __( 'Layout', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal' => __( 'Horizontal', 'vamtam-elementor-integration' ),
					'vertical' => __( 'Vertical', 'vamtam-elementor-integration' ),
				],
				'prefix_class' => 'vamtam-layout-',
			]
		);
		$widget->end_injection();
	}

	function update_align_control( $controls_manager, $widget ) {
		// Align.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'align', [
			'condition' => [
				'layout!' => 'vertical',
			]
		] );
	}

	function add_vertical_align_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'align',
		] );
		$widget->add_responsive_control(
			'align_vertical',
			[
				'label' => __( 'Alignment', 'elementor' ),
				'type' => $controls_manager::CHOOSE,
				'options' => [
					'flex-start'    => [
						'title' => __( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => __( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}}.vamtam-layout-vertical .elementor-social-icons-wrapper' => 'align-items: {{VALUE}};',
				],
				'condition' => [
					'layout' => 'vertical',
				],
			]
		);
		$widget->end_injection();
	}

	// Content - Social Icon section
	function section_social_icon_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_layout_control( $controls_manager, $widget );
		update_align_control( $controls_manager, $widget );
		add_vertical_align_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/social-icons/section_social_icon/before_section_end', __NAMESPACE__ . '\section_social_icon_before_section_end', 10, 2 );

	function update_icon_spacing_control( $controls_manager, $widget ) {
		// Icon Spacing.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'icon_spacing', [
			'condition' => [
				'layout!' => 'vertical',
			]
		] );
	}

	function add_vertical_spacing_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'icon_spacing',
		] );
		$widget->add_responsive_control(
			'icon_spacing_vertical',
			[
				'label' => __( 'Spacing', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-social-icon:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'layout' => 'vertical',
				]
			]
		);
		$widget->end_injection();
	}

	// Style - Social Icon section
	function section_social_style_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_vertical_spacing_control( $controls_manager, $widget );
		update_icon_spacing_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/social-icons/section_social_style/before_section_end', __NAMESPACE__ . '\section_social_style_before_section_end', 10, 2 );
}
