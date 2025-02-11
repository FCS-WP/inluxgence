<?php
namespace VamtamElementor\Widgets\SearchForm;

// Extending the Search Form widget.

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

if ( vamtam_theme_supports( 'search-form--disable-scroll' ) ) {

	function add_vamtam_disable_scroll_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'skin',
		] );
		$widget->add_control(
			'vamtam_disable_scroll',
			[
				'label' => __( 'Disable page scroll', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'disable-scroll',
				'condition' => [
					'skin' => 'full_screen',
				]
			]
		);
		$widget->end_injection();
	}

	// Content - After section end
	function search_content_after_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_vamtam_disable_scroll_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/search-form/search_content/after_section_end', __NAMESPACE__ . '\search_content_after_section_end', 10, 2 );

	// Vamtam_Widget_Search_Form.
	function widgets_registered() {

		if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
			return;
		}

		if ( ! class_exists( '\ElementorPro\Modules\ThemeElements\Widgets\Search_Form' ) ) {
			return; // Elementor's autoloader acts weird sometimes.
		}

		class Vamtam_Widget_Search_Form extends \ElementorPro\Modules\ThemeElements\Widgets\Search_Form {
			public $extra_depended_scripts = [
				'vamtam-search-form',
			];

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
					'vamtam-search-form',
					VAMTAM_ELEMENTOR_INT_URL . 'assets/js/widgets/search-form/vamtam-search-form' . $suffix . '.js',
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
		// Replace current search-form widget with our extended version.
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets_manager->unregister( 'search-form' );
		$widgets_manager->register( new Vamtam_Widget_Search_Form );
	}
	add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
}

if ( vamtam_theme_supports( 'search-form--estudiar-form-style' ) ) {
	function render_content( $content, $widget ) {
		if ( 'search-form' === $widget->get_name() ) {
			$settings = $widget->get_settings();
			if ( ! empty( $settings['use_theme_form_style'] ) ) {
				// Inject theme icon.
				$content = str_replace( 'fa fa-search', 'vamtamtheme- vamtam-theme-search', $content );
			}
		}

		return $content;
	}
	add_filter( 'elementor/widget/render_content', __NAMESPACE__ . '\render_content', 10, 2 );

	function add_use_theme_form_style_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'skin',
		] );
		$widget->add_control(
			'use_theme_form_style',
			[
				'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'theme-form-style',
				'render_type' => 'template',
				'condition' => [
					'skin' => 'classic',
				],
			]
		);
		$widget->end_injection();
	}

	// Content - Before section end
	function search_content_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_use_theme_form_style_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/search-form/search_content/before_section_end', __NAMESPACE__ . '\search_content_before_section_end', 10, 2 );
}
