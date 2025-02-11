<?php
namespace VamtamElementor\Widgets\PostNavigation;

// Extending the Post Navigation widget.

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

if ( vamtam_theme_supports( 'post-navigation--fitness-label-style' ) ) {
	function add_use_theme_label_style_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => "tabs_label_style",
			'at' => "before",
		] );
		$widget->add_control(
			"use_theme_label_style",
			[
				'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'theme-label-style',
				'default' => 'theme-label-style',
			]
		);
		$widget->end_injection();
	}

	// Style - Label Section.
	function label_style_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_use_theme_label_style_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/post-navigation/label_style/before_section_end', __NAMESPACE__ . '\label_style_before_section_end', 10, 2 );
}