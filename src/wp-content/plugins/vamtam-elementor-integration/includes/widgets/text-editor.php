<?php
namespace VamtamElementor\Widgets\TextEditor;

// Extending the Text Editor widget.

// Vamtam_Widget_Text_Editor.
function widgets_registered() {
	class Vamtam_Widget_Text_Editor extends \Elementor\Widget_Text_Editor {
		public $extra_depended_scripts = [
			'vamtam-text-editor',
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
				'vamtam-text-editor',
				VAMTAM_ELEMENTOR_INT_URL . 'assets/js/widgets/text-editor/vamtam-text-editor' . $suffix . '.js',
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

	// Replace current text-editor widget with our extended version.
	$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
	$widgets_manager->unregister( 'text-editor' );
	$widgets_manager->register( new Vamtam_Widget_Text_Editor );
}
add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
