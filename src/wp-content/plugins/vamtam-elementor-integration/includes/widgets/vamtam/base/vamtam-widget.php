<?php
/**
 * Vamtam Elementor Widget.
 *
 * @package Vamtam Elementor Integration
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Common Widget
 *
 * @since 0.0.1
 */
abstract class Vamtam_Elementor_Widget extends Elementor\Widget_Base {

	/**
	 * Get categories
	 *
	 * @since 0.0.1
	 */
	public function get_categories() {
		return [ 'vamtam-widgets' ];
	}

	/**
	 * Add a placeholder for the widget in the elementor editor
	 *
	 * @access public
	 * @since 1.3.11
	 *
	 * @return void
	 */
	public function render_editor_placeholder( $args = array() ) {

		if ( ! \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
			return;
		}

		$defaults = [
			'title' => $this->get_title(),
			'body' 	=> __( 'This is a placeholder for this widget and is visible only in the editor.', 'vamtam-elementor-integration' ),
		];

		$args = wp_parse_args( $args, $defaults );

		$this->add_render_attribute([
			'placeholder' => [
				'class' => 'vamtam-editor-placeholder',
			],
			'placeholder-title' => [
				'class' => 'vamtam-editor-placeholder-title',
			],
			'placeholder-content' => [
				'class' => 'vamtam-editor-placeholder-content',
			],
		]);

		?><div <?php echo $this->get_render_attribute_string( 'placeholder' ); ?>>
			<h4 <?php echo $this->get_render_attribute_string( 'placeholder-title' ); ?>>
				<?php echo $args['title']; ?>
			</h4>
			<div <?php echo $this->get_render_attribute_string( 'placeholder-content' ); ?>>
				<?php echo $args['body']; ?>
			</div>
		</div><?php
	}
}
