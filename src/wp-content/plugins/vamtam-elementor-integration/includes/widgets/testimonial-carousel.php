<?php
namespace VamtamElementor\Widgets\TestimonialCarousel;

use \ElementorPro\Modules\Carousel\Widgets\Testimonial_Carousel as Elementor_Testimonial_Carousel;

// Extending the Testimonial Carousel widget.

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

if ( vamtam_theme_supports( [ 'testimonial-carousel--arrows-color-tabs', 'testimonial-carousel--caridad-slider-arrows', 'testimonial-carousel--estudiar-slider-arrows', 'testimonial-carousel--fitness-slider-arrows' ] ) ) {
	// Called frontend & editor (editor after element loses focus).
	function render_content( $content, $widget ) {
		if ( 'testimonial-carousel' === $widget->get_name() ) {
			$settings = $widget->get_settings();

			if ( vamtam_theme_supports( 'testimonial-carousel--caridad-slider-arrows' ) ) {
				if ( ! empty( $settings['use_theme_arrows_style'] ) ) {
					$content = str_replace(
						[
							'eicon-chevron-left',
							'eicon-chevron-right'
						],
						[
							'vamtamtheme- vamtam-theme-arrow-slide-left',
							'vamtamtheme- vamtam-theme-arrow-slide-right',
						],
						$content );
				}
			}
			if ( vamtam_theme_supports( [ 'testimonial-carousel--estudiar-slider-arrows', 'testimonial-carousel--fitness-slider-arrows' ] ) ) {
				if ( ! empty( $settings['use_theme_arrows_style'] ) ) {
					$content = str_replace(
						[
							'eicon-chevron-left',
							'eicon-chevron-right'
						],
						[
							'vamtamtheme- vamtam-theme-arrow-left',
							'vamtamtheme- vamtam-theme-arrow-right',
						],
						$content );
				}
			}
		}

		return $content;
	}
	add_filter( 'elementor/widget/render_content', __NAMESPACE__ . '\render_content', 10, 2 );

	function update_nav_section_arrows_color_control( $controls_manager, $widget ) {
		// Arrows Color.
		$widget->start_injection( [
			'of' => 'arrows_size',
		] );
		$widget->remove_control( 'arrows_color' );

		$widget->start_controls_tabs( 'arrows_color_tabs' );
		// Normal.
		$widget->start_controls_tab(
			'arrows_color_tabs_normal',
			[
				'label' => __( 'Normal', 'vamtam-elementor-integration' ),
				'condition' => [
					'show_arrows!' => '',
				],
			]
		);
		// Arrows Color.
		$widget->add_control(
			'arrows_color',
			[
				'label' => __( 'Arrows Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-swiper-button' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_arrows!' => '',
				],
			]
		);
		if ( vamtam_theme_supports( 'testimonial-carousel--caridad-slider-arrows' ) ) {
			// Circle Color.
			$widget->add_control(
				'arrows_circle_color',
				[
					'label' => __( 'Circle Color', 'vamtam-elementor-integration' ),
					'type' => $controls_manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .elementor-swiper-button::before' => 'border-color: {{VALUE}}',
					],
					'condition' => [
						'show_arrows!' => '',
						'use_theme_arrows_style!' => '',
					],
				]
			);
		}
		$widget->end_controls_tab();
		// Hover.
		$widget->start_controls_tab(
			'arrows_color_tabs_hover',
			[
				'label' => __( 'Hover', 'vamtam-elementor-integration' ),
				'condition' => [
					'show_arrows!' => '',
				],
			]
		);
		// Arrows Color Hover.
		$widget->add_control(
			'arrows_color_hover',
			[
				'label' => __( 'Arrows Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-swiper-button:hover' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_arrows!' => '',
				],
			]
		);
		if ( vamtam_theme_supports( 'testimonial-carousel--caridad-slider-arrows' ) ) {
			// Circle Hover Color.
			$widget->add_control(
				'arrows_circle_color_hover',
				[
					'label' => __( 'Circle Color', 'vamtam-elementor-integration' ),
					'type' => $controls_manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .elementor-swiper-button:hover::before' => 'border-color: {{VALUE}}',
					],
					'condition' => [
						'show_arrows!' => '',
						'use_theme_arrows_style!' => '',
					],
				]
			);
		}
		$widget->end_controls_tab();
		$widget->end_controls_tabs();
		$widget->end_injection();
	}

	if ( vamtam_theme_supports( [ 'testimonial-carousel--caridad-slider-arrows', 'testimonial-carousel--estudiar-slider-arrows', 'testimonial-carousel--fitness-slider-arrows' ] ) ) {
		function add_nav_section_controls( $controls_manager, $widget ) {
			$widget->start_injection( [
				'of' => 'heading_arrows',
			] );
			$widget->add_control(
				'use_theme_arrows_style',
				[
					'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
					'type' => $controls_manager::SWITCHER,
					'prefix_class' => 'vamtam-has-',
					'return_value' => 'theme-arrows-style',
					'condition' => [
						'show_arrows!' => '',
					],
					'render_type' => 'template',
				]
			);
			$widget->end_injection();
		}
	}

	// Style - Navigation section.
	add_action( 'elementor/element/testimonial-carousel/section_navigation/before_section_end', function( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		if ( vamtam_theme_supports( [ 'testimonial-carousel--arrows-color-tabs', 'testimonial-carousel--caridad-slider-arrows', 'testimonial-carousel--estudiar-slider-arrows' ] ) ) {
			update_nav_section_arrows_color_control( $controls_manager, $widget );
		}
		if ( vamtam_theme_supports( [ 'testimonial-carousel--caridad-slider-arrows', 'testimonial-carousel--estudiar-slider-arrows', 'testimonial-carousel--fitness-slider-arrows' ] ) ) {
			add_nav_section_controls( $controls_manager, $widget );
		}
	}, 10, 2 );
}

if ( vamtam_theme_supports( 'testimonial-carousel--disable-slide-to-click' ) ) {
	function add_disable_slide_to_click_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'width',
		] );
		$widget->add_control(
			'disable_slide_to_click',
			[
				'label' => __( 'Disable Slide To Click', 'vamtam-elementor-integration' ),
				'description' => __( 'If disabled, click on a slide will <strong>not</strong> produce transition to this slide', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'frontend_available' => true,
			]
		);
		$widget->end_injection();
	}

	// Content - Slides section - Before Section End.
	function section_slides_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_disable_slide_to_click_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/testimonial-carousel/section_slides/before_section_end', __NAMESPACE__ . '\section_slides_before_section_end', 10, 2 );
}

// Vamtam_Testimonial_Carousel.
function widgets_registered() {

	if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
		return;
	}

	if ( ! class_exists( '\ElementorPro\Modules\Carousel\Widgets\Testimonial_Carousel' ) ) {
		return; // Elementor's autoloader acts weird sometimes.
	}

	class Vamtam_Testimonial_Carousel extends Elementor_Testimonial_Carousel {
		public $extra_depended_scripts = [
			'vamtam-testimonial-carousel',
		];

		public function get_script_depends(): array {
			return [
				'vamtam-testimonial-carousel',
			];
		}

		// Extend constructor.
		public function __construct($data = [], $args = null) {
			parent::__construct($data, $args);

			$this->register_assets();

			$this->add_extra_script_depends();
		}

		// Register the assets the widget depends on.
		public function register_assets() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script(
				'vamtam-testimonial-carousel',
				VAMTAM_ELEMENTOR_INT_URL . 'assets/js/widgets/testimonial-carousel/vamtam-testimonial-carousel' . $suffix . '.js',
				[
					'elementor-frontend',
				],
				\VamtamElementorIntregration::PLUGIN_VERSION,
				true
			);
		}

		// Assets the widget depends upon.
		public function add_extra_script_depends() {
			// Scripts
			foreach ( $this->extra_depended_scripts as $script ) {
				$this->add_script_depends( $script );
			}
		}
	}

	// Replace current testimonial-carousel widget with our extended version.
	$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
	$widgets_manager->unregister( 'testimonial-carousel' );
	$widgets_manager->register( new Vamtam_Testimonial_Carousel );
}
add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
