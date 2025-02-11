<?php
namespace VamtamElementor\Widgets\Blockquote;

// Extending the Blockquote widget.

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

function update_button_typography_control( $controls_manager, $widget ) {
	// Button Typography
	\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'button_typography', [
		'selector' => '{{WRAPPER}} .elementor-blockquote__tweet-button span',
		],
		\Elementor\Group_Control_Typography::get_type()
	);
}

// Style - Button section
function section_button_style_before_section_end( $widget, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	update_button_typography_control( $controls_manager, $widget );
}
add_action( 'elementor/element/blockquote/section_button_style/before_section_end', __NAMESPACE__ . '\section_button_style_before_section_end', 10, 2 );
