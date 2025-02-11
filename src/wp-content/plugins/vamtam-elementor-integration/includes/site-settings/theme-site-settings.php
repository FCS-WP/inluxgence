<?php

namespace VamtamElementor\SiteSettings\ThemeSettings;

use \Elementor\Controls_Manager;
use \Elementor\Core\Kits\Documents\Tabs;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Settings_Theme_Settings extends Tabs\Tab_Base {

	public function get_id() {
		return 'settings-theme-settings';
	}

	public function get_title() {
		return __( 'Theme Settings', 'vamtam-elementor-integration' );
	}

	public function get_group() {
		return 'settings';
	}

	public function get_icon() {
		return 'eicon-settings';
	}

	protected function register_tab_controls() {
		$this->add_theme_settings_section( $this );
	}

    protected function add_theme_settings_section( $kit ) {
        $kit->start_controls_section(
            'section_' . $this->get_id(),
			[
				'label' => $this->get_title(),
				'tab' => $this->get_id(),
			]
        );

        $theme_prefix  = 'vamtam_theme_';
        $kit->add_control(
            "{$theme_prefix}save_notice",
            [
                'type' => Controls_manager::RAW_HTML,
                'raw' => __( '<h3><strong>After saving your changes, a page refresh will be needed for the changes to take effect.</strong/></h3>', 'vamtam-elementor-integration' ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
            ]
        );

        $this->add_general_controls( $kit );

        $kit->end_controls_section();
    }

	protected function add_general_controls( $kit ) {
        $theme_prefix  = 'vamtam_theme_';

        $kit->add_control(
            'section_theme_settings_general_heading',
            [
                'type' => Controls_manager::HEADING,
                'label' => __( 'General', 'vamtam-elementor-integration' ),
                'separator' => 'before',
            ]
        );

        $kit->add_control(
            "{$theme_prefix}has_theme_cursor",
			[
				'label' => __( 'Use Theme Cursor', 'vamtam-elementor-integration' ),
				'description' => __( 'Toggles the theme\'s custom cursor implementation.', 'vamtam-elementor-integration' ),
				'type' => Controls_manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
        );
    }
}

function add_theme_settings_tab( $kit ) {
	if ( vamtam_theme_supports( 'site-settings--theme-settings' ) ) {
		$kit->register_tab( 'settings-theme-settings', Settings_Theme_Settings::class );
	}
}

add_action( 'elementor/kit/register_tabs', __NAMESPACE__ . '\add_theme_settings_tab', 10 );
