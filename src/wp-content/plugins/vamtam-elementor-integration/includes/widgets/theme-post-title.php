<?php
namespace VamtamElementor\Widgets\ThemePostTitle;

// Extending the Theme Post Title widget.

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

if ( vamtam_theme_supports( 'theme-post-title--fitness-style' ) ) {
	function add_use_theme_style_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => "title_color",
			'at' => "before",
		] );
		$widget->add_control(
			"use_theme_style",
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

	// Style - Title Section.
	function section_title_style_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_use_theme_style_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/theme-post-title/section_title_style/before_section_end', __NAMESPACE__ . '\section_title_style_before_section_end', 10, 2 );
}