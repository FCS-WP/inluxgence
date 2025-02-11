<?php
namespace VamtamElementor\Widgets\PostInfo;

// Extending the Post Info widget.

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

if ( vamtam_theme_supports( 'post-info--fitness-text-style' ) ) {
	function add_use_theme_text_style_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => "text_indent",
			'at' => "before",
		] );
		$widget->add_control(
			"use_theme_text_style",
			[
				'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'theme-text-style',
				'default' => 'theme-text-style',
			]
		);
		$widget->end_injection();
	}

	// Style - Title Section.
	function section_text_style_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_use_theme_text_style_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/post-info/section_text_style/before_section_end', __NAMESPACE__ . '\section_text_style_before_section_end', 10, 2 );
}