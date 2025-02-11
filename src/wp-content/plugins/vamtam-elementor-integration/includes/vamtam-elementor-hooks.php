<?php
namespace VamtamElementor\ElementorHooks;

// Elementor actions.
add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(),      __NAMESPACE__ . '\register_widgets', 999999 );
add_action( 'elementor/editor/before_enqueue_scripts',   __NAMESPACE__ . '\enqueue_editor_scripts' );
add_action( 'elementor/frontend/before_enqueue_scripts', __NAMESPACE__ . '\frontend_before_enqueue_scripts' );
add_action( 'elementor/init', __NAMESPACE__ . '\elementor_init' );

// add_action( 'elementor/preview/enqueue_styles',          'Vamtam_Elementor_Widgets_Handler::enqueue_theme_editor_styles' );

// Elementor filters
add_filter( 'elementor/controls/animations/additional_animations', __NAMESPACE__ . '\vamtam_elementor_additional_animations' );
add_filter( 'elementor/controls/hover_animations/additional_animations', __NAMESPACE__ . '\vamtam_elementor_additional_hover_animations' );
add_filter( 'elementor/image_size/get_attachment_image_html', __NAMESPACE__ . '\vamtam_add_no_lazy_class_to_img_element', 11, 4 );

function elementor_init() {
	// Theme-dependant.
	set_experiments_default_state();
}

/*
	Sets all Stable features to their default value & disables all Ongoing experiments by default.
	Happens only once (based on option).
*/
function set_experiments_default_state() {
	if ( get_option( 'vamtam-set-experiments-default-state', false ) ) {
		return;
	}

	$exps     = \Elementor\Plugin::$instance->experiments;
	$features = $exps->get_features();

	foreach ( $features as $fname => $fdata ) {
		if ( $fdata['release_status'] === 'stable' ) {
			// Stable experiments.

			// Features to force-disable.
			$fdisable = [
				'additional_custom_breakpoints',
				'e_font_icon_svg'
			];

			if ( in_array( $fname, $fdisable ) ) {
				// Force-disable.
				update_option( 'elementor_experiment-' . $fname, $exps::STATE_INACTIVE );
				continue;
			}

			// Force default state.
			update_option( 'elementor_experiment-' . $fname, $exps::STATE_DEFAULT );

		} else {
			// Ongoing experiments.

			// Force-disable.
			update_option( 'elementor_experiment-' . $fname, $exps::STATE_INACTIVE );

			// Set it's current default state to inactive
			$exps->set_feature_default_state( $fname, $exps::STATE_INACTIVE );
		}
	}

	update_option( 'vamtam-set-experiments-default-state', true );
}


function vamtam_elementor_additional_animations( $additional_anims ) {
	if ( current_theme_supports( 'vamtam-elementor-widgets', 'widgets--horizontal-grow-anims' ) ) {
		if ( ! isset( $additional_anims[ 'Vamtam' ] ) ) {
			$additional_anims[ 'Vamtam' ] = [];
		}
		$additional_anims[ 'Vamtam' ] = $additional_anims[ 'Vamtam' ] + [
			'growFromLeft' => __( 'Grow From Left', 'vamtam-elementor-integration' ),
			'growFromRight' => __( 'Grow From Right', 'vamtam-elementor-integration' ),
		];
	}
	if ( current_theme_supports( 'vamtam-elementor-widgets', 'widgets--horizontal-grow-scroll-based-anims' ) ) {
		if ( ! isset( $additional_anims[ 'Vamtam' ] ) ) {
			$additional_anims[ 'Vamtam' ] = [];
		}
		$additional_anims[ 'Vamtam' ] = $additional_anims[ 'Vamtam' ] + [
			'growFromLeftScroll' => __( 'Grow From Left (Scroll Based)', 'vamtam-elementor-integration' ),
			'growFromRightScroll' => __( 'Grow From Right (Scroll Based)', 'vamtam-elementor-integration' ),
		];
	}
	if ( current_theme_supports( 'vamtam-elementor-widgets', 'image--grow-with-scale-anims' ) ) {
		if ( ! isset( $additional_anims[ 'Vamtam' ] ) ) {
			$additional_anims[ 'Vamtam' ] = [];
		}
		$additional_anims[ 'Vamtam' ] = $additional_anims[ 'Vamtam' ] + [
			'imageGrowWithScaleLeft' => __( 'Image - Grow With Scale (Left)', 'vamtam-elementor-integration' ),
			'imageGrowWithScaleRight' => __( 'Image - Grow With Scale (Right)', 'vamtam-elementor-integration' ),
			'imageGrowWithScaleTop' => __( 'Image - Grow With Scale (Top)', 'vamtam-elementor-integration' ),
			'imageGrowWithScaleBottom' => __( 'Image - Grow With Scale (Bottom)', 'vamtam-elementor-integration' ),
		];
	}
	return $additional_anims;
}

function vamtam_elementor_additional_hover_animations( $additional_hover_anims ) {
	if ( current_theme_supports( 'vamtam-elementor-widgets', 'form--prefix-grow-hover-anims' ) ) {
		$additional_hover_anims = $additional_hover_anims + [
			'prefix-grow' => __( 'Prefix Grow', 'vamtam-elementor-integration' ),
			'prefix-grow-alt' => __( 'Prefix Grow Alt', 'vamtam-elementor-integration' ),
		];
	}
	return $additional_hover_anims;
}

function register_widgets() {
	\Vamtam_Elementor_Widgets_Handler::instance();
}

function frontend_before_enqueue_scripts() {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	// Enqueue JS for Elementor (frontend).
	wp_enqueue_script(
		'vamtam-elementor-frontend',
		VAMTAM_ELEMENTOR_INT_URL . 'assets/js/vamtam-elementor-frontend' . $suffix . '.js',
		[
			'elementor-frontend', // dependency
		],
		\VamtamElementorIntregration::PLUGIN_VERSION,
		true //in footer
	);
}

function enqueue_editor_scripts() {
	// Enqueue JS for Elementor editor.
	wp_enqueue_script( 'vamtam-elementor', VAMTAM_ELEMENTOR_INT_URL . 'assets/js/vamtam-elementor.js', [], \VamtamElementorIntregration::PLUGIN_VERSION, true );
}

/*
	Add the no-lazy class to the <img> element, if the root widget element has it.
	We need this cause Elementor adds the Custom CSS classes to the root widget element
	but caching plugins need it directly on the <img> element in order not to lazy-load it.
*/
function vamtam_add_no_lazy_class_to_img_element( $html, $settings, $image_size_key, $image_key ) {
	if ( ! isset( $settings[ '_css_classes' ] ) || empty( $settings[ '_css_classes' ] ) || strpos( $settings[ '_css_classes' ], 'no-lazy' ) === false ) {
		return $html;
	}

	$attrClass = strpos( $html, "class=" );
	if ( $attrClass ) {
		$html = preg_replace( '/class="(.*)"/', 'class="' . 'no-lazy' . ' \1"', $html );
	} else {
		$html = preg_replace( '/src="(.*)"/', 'class="' . 'no-lazy' . '" src="\1"', $html );
	}

	return $html;
}
