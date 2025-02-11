<?php
namespace VamtamElementor\Widgets\Accordion;

// Extending the Accordion widget.

if ( current_theme_supports( 'vamtam-elementor-widgets', 'accordion--estudiar-border-style' ) ) {
	function use_theme_border_style_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'border_width',
		] );
		$widget->add_control(
			'use_theme_border_style',
			[
				'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'theme-border-style',
				'default' => 'theme-border-style',
				'label_on' => __( 'Yes', 'vamtam-elementor-integration' ),
				'label_off' => __( 'No', 'vamtam-elementor-integration' ),
				'selectors' => [
					'{{WRAPPER}} .elementor-accordion .elementor-accordion-item .elementor-tab-title' => 'border-width: {{border_width.SIZE}}{{border_width.UNIT}};',
					'{{WRAPPER}} .elementor-accordion .elementor-accordion-item .elementor-tab-content.elementor-active' => 'border-width: {{border_width.SIZE}}{{border_width.UNIT}};',
				],
			]
		);
		$widget->end_injection();
	}

	function section_title_style_before_section_end( $widget ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		use_theme_border_style_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/accordion/section_title_style/before_section_end', __NAMESPACE__ . '\section_title_style_before_section_end' );
}