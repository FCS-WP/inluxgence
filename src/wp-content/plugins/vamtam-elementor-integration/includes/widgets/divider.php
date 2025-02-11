<?php
namespace VamtamElementor\Widgets\Divider;

// Extending the Divider widget.

// Vamtam_Widget_Divider.
function widgets_registered() {
	class Vamtam_Widget_Divider extends \Elementor\Widget_Divider {
		// Extend svg_to_data_uri method.
		public function svg_to_data_uri( $svg ) {
			// If svg data URI contains an accent color, replace it with the actual value,
			// as css vars don't work inside data URIs.
			if ( preg_match('/(var\(--vamtam-accent-color-(\d)\))/',$svg, $matches) ) {
				global $vamtam_theme;
				$accents = $vamtam_theme[ 'accent-color' ];
				$svg = str_replace( $matches[0], $accents[ $matches[2] ], $svg );
			}
			return str_replace(
				[ '<', '>', '"', '#' ],
				[ '%3C', '%3E', "'", '%23' ],
				$svg
			);
		}
	}

	// Replace current divider widget with our extended version.
	$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
	$widgets_manager->unregister( 'divider' );
	$widgets_manager->register( new Vamtam_Widget_Divider );
}
add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
