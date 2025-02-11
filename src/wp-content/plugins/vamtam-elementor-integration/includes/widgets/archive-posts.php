<?php
namespace VamtamElementor\Widgets\ArchivePosts;

// Extending the Archive Posts widget.

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'archive-posts.classic--box-section' ) ) {
	function add_box_section( $widget ) {
		$widget->start_controls_section(
			'archive_classic_section_design_box',
			[
				'label' => __( 'Box', 'vamtam-elementor-integration' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => 'archive_classic',
				]
			]
		);

		$widget->add_control(
			'archive_classic_box_border_width',
			[
				'label' => __( 'Border Width', 'vamtam-elementor-integration' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$widget->add_control(
			'archive_classic_box_border_radius',
			[
				'label' => __( 'Border Radius', 'vamtam-elementor-integration' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$widget->add_control(
			'archive_classic_box_padding',
			[
				'label' => __( 'Padding', 'vamtam-elementor-integration' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$widget->add_control(
			'archive_classic_content_padding',
			[
				'label' => __( 'Content Padding', 'vamtam-elementor-integration' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post__text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'separator' => 'after',
			]
		);

		$widget->start_controls_tabs( 'archive_classic_bg_effects_tabs' );

		$widget->start_controls_tab( 'archive_classic_style_normal',
			[
				'label' => __( 'Normal', 'vamtam-elementor-integration' ),
			]
		);

		$widget->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'archive_classic_box_shadow',
				'selector' => '{{WRAPPER}} .elementor-post',
			]
		);

		$widget->add_control(
			'archive_classic_box_bg_color',
			[
				'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-post' => 'background-color: {{VALUE}}',
				],
			]
		);

		$widget->add_control(
			'archive_classic_box_border_color',
			[
				'label' => __( 'Border Color', 'vamtam-elementor-integration' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-post' => 'border-color: {{VALUE}}',
				],
			]
		);

		$widget->end_controls_tab();

		$widget->start_controls_tab( 'archive_classic_style_hover',
			[
				'label' => __( 'Hover', 'vamtam-elementor-integration' ),
			]
		);

		$widget->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'archive_classic_box_shadow_hover',
				'selector' => '{{WRAPPER}} .elementor-post:hover',
			]
		);

		$widget->add_control(
			'archive_classic_box_bg_color_hover',
			[
				'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-post:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$widget->add_control(
			'archive_classic_box_border_color_hover',
			[
				'label' => __( 'Border Color', 'vamtam-elementor-integration' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-post:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$widget->add_control(
			'archive_classic_content_hover_color',
			[
				'label' => __( 'Content Color', 'vamtam-elementor-integration' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					// Title.
					'{{WRAPPER}} .elementor-post:hover .elementor-post__title, {{WRAPPER}} .elementor-post:hover .elementor-post__title a' => 'color: {{VALUE}};',
					// Meta.
					'{{WRAPPER}} .elementor-post:hover .elementor-post__meta-data' => 'color: {{VALUE}};',
					// Excerpt.	
					'{{WRAPPER}} .elementor-post:hover .elementor-post__excerpt p' => 'color: {{VALUE}};',
				],
			]
		);

		$widget->end_controls_tab();

		$widget->end_controls_tabs();

		$widget->end_controls_section();
	}

	// Style - Advanced Section (Classic Layout) - After Section End.
	function section_design_content_after_section_end( $widget, $args ) {
		add_box_section( $widget );
	}
	add_action( 'elementor/element/archive-posts/archive_classic_section_design_layout/after_section_end', __NAMESPACE__ . '\section_design_content_after_section_end', 10, 2 );
}