<?php
namespace VamtamElementor\Widgets\Tabs;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use \Elementor\Group_Control_Typography;

function add_disable_def_anim_control_content_tab( $controls_manager, $widget ) {
	$widget->add_control(
		'disable_def_anim',
		[
			'label' => __( 'Disable Default Tab Animation', 'vamtam-elementor-integration' ),
			'description' => __( 'Disables the default tab switching animation.', 'vamtam-elementor-integration' ),
			'type' => $controls_manager::SWITCHER,
			'prefix_class' => 'vamtam-has-',
			'return_value' => 'disable-def-anim',
		]
	);
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'tabs--estudiar-hr-tabs-style' ) ) {
	function add_use_theme_hr_tabs_style_controls( $controls_manager, $widget ) {
		// Use Theme Style.
		$widget->add_control(
			'use_theme_hr_tabs_style',
			[
				'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'theme-hr-tabs-style',
				'condition' => [
					'type' => 'horizontal',
				],
				'render_type' => 'template',
			]
		);
		// Overline Bg Color.
		$widget->add_control(
			'tab_title_overline_bg_color',
			[
				'label' => __( 'Overline Bg Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title > a::before' => '--vamtam-overline-bg-color: {{VALUE}};'
				],
				'condition' => [
					'use_theme_hr_tabs_style!' => '',
					'type' => 'horizontal',
				]
			]
		);
		// Overline Bg Hover Color.
		$widget->add_control(
			'tab_title_overline_bg_color_hover',
			[
				'label' => __( 'Overline Bg Hover Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title > a::before' => '--vamtam-overline-bg-hover-color: {{VALUE}};',
				],
				'condition' => [
					'use_theme_hr_tabs_style!' => '',
					'type' => 'horizontal',
				]
			]
		);
		// Max Width.
		$widget->add_control(
			'tabs_wrap_max_width',
			[
				'label' => __( 'Tabs Box Max Width', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-tabs-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'use_theme_hr_tabs_style!' => '',
					'type' => 'horizontal',
				]
			]
		);
		// Use Theme Animation.
		$widget->add_control(
			'use_theme_hr_tabs_anim',
			[
				'label' => __( 'Use Theme Animation', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'theme-hr-tabs-anim',
				'default' => 'theme-hr-tabs-anim',
				'condition' => [
					'use_theme_hr_tabs_style!' => '',
					'type' => 'horizontal',
				],
				'render_type' => 'template',
			]
		);
		// Transition Time.
		$widget->add_control(
			'theme_hr_tabs_anim_transition_time',
			[
				'label' => __( 'Transition Time (s)', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 10,
						'min' => 0.1,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-tabs .elementor-tab-content' => 'transition-duration: {{SIZE}}s;',
				],
				'condition' => [
					'use_theme_hr_tabs_anim!' => '',
					'use_theme_hr_tabs_style!' => '',
					'type' => 'horizontal',
				]
			]
		);
	}
}

// Content - Tabs section.
add_action( 'elementor/element/tabs/section_tabs/before_section_end', function( $widget, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	if ( current_theme_supports( 'vamtam-elementor-widgets', 'tabs--title-numbering' ) ) {
		add_title_numbering_controls_content_tab( $controls_manager, $widget );
	}
	add_disable_def_anim_control_content_tab( $controls_manager, $widget );
	if ( current_theme_supports( 'vamtam-elementor-widgets', 'tabs--estudiar-hr-tabs-style' ) ) {
		add_use_theme_hr_tabs_style_controls( $controls_manager, $widget );
	}
}, 10, 2 );

if ( current_theme_supports( 'vamtam-elementor-widgets', 'tabs--empty-title-button' ) ) {
	function add_empty_title_button_controls( $controls_manager, $widget ) {
		$widget->add_control(
			'empty_title_button',
			[
				'label' => __( 'Empty Title As Button', 'vamtam-elementor-integration' ),
				'description' => __( 'In absence of a tab title a button will be shown instead.', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'empty-title-button',
				'condition' => [
					'type' => 'vertical',
				],
				'render_type' => 'template',
			]
		);
		$widget->add_control(
			'nav_over_content',
			[
				'label' => __( 'Navigation Over Content', 'vamtam-elementor-integration' ),
				'description' => __( 'Draw navigation on top of the tab content. Use "Navigation Width" to position the navigation container.', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'nav-over-content',
				'condition' => [
					'type' => 'vertical',
					'empty_title_button!' => '',
				],
			]
		);
		$widget->add_control(
			'button_width',
			[
				'label' => __( 'Button Width', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 4,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.vamtam-has-empty-title-button .elementor-tab-title.vamtam-no-title a' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'type' => 'vertical',
					'empty_title_button!' => '',
				],
			]
		);
		$widget->add_control(
			'button_height',
			[
				'label' => __( 'Button Height', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 45,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.vamtam-has-empty-title-button .elementor-tab-title.vamtam-no-title a' => 'height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'type' => 'vertical',
					'empty_title_button!' => '',
				],
			]
		);
		$widget->add_responsive_control(
			'button_padding',
			[
				'label' => __( 'Button Padding', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}}.vamtam-has-empty-title-button .elementor-tab-title.vamtam-no-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'type' => 'vertical',
					'empty_title_button!' => '',
				],
			]
		);
	}

	// Content - Tabs section.
	add_action( 'elementor/element/tabs/section_tabs/before_section_end', function( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_empty_title_button_controls( $controls_manager, $widget );
	}, 10, 2 );

	function update_controls_style_tab( $controls_manager, $widget ) {
		// Tab Color.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'tab_color', [
			'selectors' => [
				'{{WRAPPER}}.vamtam-has-empty-title-button .elementor-tab-title.vamtam-no-title a' => 'background-color: {{VALUE}};',
			]
		] );
		// Tab Active Color.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'tab_active_color', [
			'selectors' => [
				'{{WRAPPER}}.vamtam-has-empty-title-button .elementor-tab-title.vamtam-no-title.elementor-active a' => 'background-color: {{VALUE}};',
			]
		] );
		// Navigation Width.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'navigation_width', [
			'condition' => [
				'nav_over_content' => '',
			]
		] );
	}

	function add_navigation_width_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'navigation_width',
		] );
		$widget->add_control(
			'vamtam_navigation_width',
			[
				'label' => __( 'Navigation Width', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-tabs-wrapper' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'type' => 'vertical',
					'nav_over_content!' => '',
				],
			]
		);
		$widget->end_injection();
	}
}

// On mobile, currently, there is no <a> inside the tab-title so the default tab_active_color rule doesnt apply.
// TODO: Remove when fixed: https://github.com/elementor/elementor/issues/13188
function active_tab_color_fix( $controls_manager, $widget ) {
	// Tab Active Color.
	\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'tab_active_color', [
		'selectors' => [
			'(mobile) {{WRAPPER}} .elementor-tab-mobile-title.elementor-active' => 'color: {{VALUE}};',
		]
	] );
}

// Style - Tabs section.
add_action( 'elementor/element/tabs/section_tabs_style/before_section_end', function( $widget, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	active_tab_color_fix( $controls_manager, $widget );
	if ( current_theme_supports( 'vamtam-elementor-widgets', 'tabs--empty-title-button' ) ) {
		update_controls_style_tab( $controls_manager, $widget );
		add_navigation_width_control( $controls_manager, $widget );
	}
}, 10, 2 );

if ( current_theme_supports( 'vamtam-elementor-widgets', 'tabs--title-numbering' ) ) {
	// Extending the Tabs widget.
	function add_title_numbering_controls_content_tab( $controls_manager, $widget ) {
		$widget->add_control(
			'use_title_numbering',
			[
				'label' => __( 'Title Numbering', 'vamtam-elementor-integration' ),
				'description' => __( 'When using title numbering, the tab\'s title up to the first space will be used as the numbering.', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'condition' => [
					'type' => 'vertical',
				],
			]
		);
		$widget->add_control(
			'use_title_numbering_shape',
			[
				'label' => __( 'Use Numbering shape', 'vamtam-elementor-integration' ),
				'description' => __( 'Applied on active tab.', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'numbering-bg',
				'condition' => [
					'use_title_numbering' => 'yes',
					'type' => 'vertical',
				],
			]
		);
	}

	function add_title_numbering_controls_style_tab( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'heading_content',
			'at' => 'before',
		] );

		$widget->add_control(
			'numbering_title',
			[
				'label' => __( 'Title Numbering', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'use_title_numbering' => 'yes',
					'type' => 'vertical',
				],
			]
		);
		$widget->add_control(
			'numbering_color',
			[
				'label' => __( 'Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title .title-numbering' => 'color: {{VALUE}};',
				],
				'condition' => [
					'use_title_numbering' => 'yes',
					'type' => 'vertical',
				],
			]
		);
		$widget->add_control(
			'numbering_active_color',
			[
				'label' => __( 'Active Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-tab-title.elementor-active .title-numbering' => 'color: {{VALUE}};',
				],
				'condition' => [
					'use_title_numbering' => 'yes',
					'type' => 'vertical',
				],
			]
		);
		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'numbering_typography',
				'selector' => '{{WRAPPER}} .elementor-tab-title .title-numbering',
				'condition' => [
					'use_title_numbering' => 'yes',
					'type' => 'vertical',
				],
			]
		);

		$widget->end_injection();
	}

	// Style - Tabs section.
	add_action( 'elementor/element/tabs/section_tabs_style/before_section_end', function( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_title_numbering_controls_style_tab( $controls_manager, $widget );
	}, 10, 2 );
}

// Vamtam_Widget_Tabs.
function widgets_registered() {
	class Vamtam_Widget_Tabs extends \Elementor\Widget_Tabs {
		public $extra_depended_scripts = [
			'vamtam-tabs',
		];

		public function get_script_depends() {
			return [
				'vamtam-tabs',
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
				'vamtam-tabs',
				VAMTAM_ELEMENTOR_INT_URL . 'assets/js/widgets/tabs/vamtam-tabs' . $suffix . '.js',
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

		// Extend render method.
		protected function render() {
			if ( vamtam_theme_supports( [ 'tabs--estudiar-hr-tabs-style', 'tabs--title-numbering', 'tabs--empty-title-button', 'tabs--bijoux-tabs-mega-menu-title-line-prefix' ] ) ) {
				// These features were built with the old markup (not a11y-friendly) in mind.
				// We force it to avoid breaking existing themes.
				$settings               = $this->get_settings_for_display();
				$tabs                   = $settings['tabs'];
				$id_int                 = substr( $this->get_id_int(), 0, 3 );
				$use_title_numbering    = vamtam_theme_supports( 'tabs--title-numbering' ) ? $settings['use_title_numbering'] === 'yes' : '';
				$use_empty_title_button = vamtam_theme_supports( 'tabs--empty-title-button' ) ? ! empty( $settings['empty_title_button'] ) : '';
				$use_title_line_prefix  = vamtam_theme_supports( 'tabs--bijoux-tabs-mega-menu-title-line-prefix' ) && $settings['type'] === 'vertical' && strpos( $settings['_css_classes'], 'vamtam-bijoux-tabs-mega-menu' ) !== false;
				?>
				<div class="elementor-tabs" role="tablist">
					<div class="elementor-tabs-wrapper">
						<?php
						foreach ( $tabs as $index => $item ) :
							$tab_count = $index + 1;

							$tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );
							$classes               = [ 'elementor-tab-title', 'elementor-tab-desktop-title' ];
							if ( $use_empty_title_button && empty( $item['tab_title'] ) ) {
								$classes[] = 'vamtam-no-title';
							}
							$this->add_render_attribute( $tab_title_setting_key, [
								'id' => 'elementor-tab-title-' . $id_int . $tab_count,
								'class' => $classes,
								'data-tab' => $tab_count,
								'role' => 'tab',
								'aria-controls' => 'elementor-tab-content-' . $id_int . $tab_count,
							] );
							?>
							<div <?php echo $this->get_render_attribute_string( $tab_title_setting_key ); ?>>
								<?php if ( $use_title_numbering ) : ?>
									<span class="title-numbering"><?php echo substr( $item['tab_title'], 0, strpos( $item['tab_title'], ' ' ) ); ?></span>
									<a href="">
										<?php if ( $use_title_line_prefix ) : ?>
											<span class="vamtam-prefix"></span>
										<?php endif; ?>
										<?php echo substr( $item['tab_title'], strpos( $item['tab_title'], ' ' ) + 1 ); ?>
									</a>
								<?php else : ?>
									<a href="">
										<?php if ( $use_title_line_prefix ) : ?>
											<span class="vamtam-prefix"></span>
										<?php endif; ?>
										<?php echo $item['tab_title']; ?>
									</a>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="elementor-tabs-content-wrapper">
						<?php
						foreach ( $tabs as $index => $item ) :
							$tab_count = $index + 1;

							$tab_content_setting_key = $this->get_repeater_setting_key( 'tab_content', 'tabs', $index );

							$tab_title_mobile_setting_key = $this->get_repeater_setting_key( 'tab_title_mobile', 'tabs', $tab_count );

							$this->add_render_attribute( $tab_content_setting_key, [
								'id' => 'elementor-tab-content-' . $id_int . $tab_count,
								'class' => [ 'elementor-tab-content', 'elementor-clearfix' ],
								'data-tab' => $tab_count,
								'role' => 'tabpanel',
								'aria-labelledby' => 'elementor-tab-title-' . $id_int . $tab_count,
							] );

							$this->add_render_attribute( $tab_title_mobile_setting_key, [
								'class' => [ 'elementor-tab-title', 'elementor-tab-mobile-title' ],
								'data-tab' => $tab_count,
								'role' => 'tab',
							] );

							$this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced' );
							?>
							<div <?php echo $this->get_render_attribute_string( $tab_title_mobile_setting_key ); ?>><?php echo $item['tab_title']; ?></div>
							<div <?php echo $this->get_render_attribute_string( $tab_content_setting_key ); ?>><?php echo $this->parse_text_editor( $item['tab_content'] ); ?></div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php
			} else {
				parent::render();
			}
		}

		// Extend content_template method.
		protected function content_template() {
			if ( vamtam_theme_supports( [ 'tabs--estudiar-hr-tabs-style', 'tabs--title-numbering', 'tabs--empty-title-button', 'tabs--bijoux-tabs-mega-menu-title-line-prefix' ] ) ) {
				// These features were built with the old markup (not a11y-friendly) in mind.
				// We force it to avoid breaking existing themes.
				$use_title_line_prefix = vamtam_theme_supports( 'tabs--bijoux-tabs-mega-menu-title-line-prefix' );
				?>
				<div class="elementor-tabs" role="tablist">
					<#
					if ( settings.tabs ) {
						var tabindex = view.getIDInt().toString().substr( 0, 3 );
						#>
						<div class="elementor-tabs-wrapper">
							<#
							_.each( settings.tabs, function( item, index ) {
								var tabCount = index + 1;
								var classes  = 'elementor-tab-title elementor-tab-desktop-title';
								if ( settings.empty_title_button && ! item.tab_title ) {
									classes += ' vamtam-no-title';
								}
								#>
								<div id="elementor-tab-title-{{ tabindex + tabCount }}" class="{{ classes }}" data-tab="{{ tabCount }}" role="tab" aria-controls="elementor-tab-content-{{ tabindex + tabCount }}">
									<# if ( settings.use_title_numbering === 'yes' ) { #>
										<span class="title-numbering">{{{ item.tab_title.substr( 0, item.tab_title.indexOf( ' ' ) ) }}}</span>
										<a href="">
											<?php if ( $use_title_line_prefix ) : ?>
												<span class="vamtam-prefix"></span>
											<?php endif; ?>
											{{{ item.tab_title.substr( item.tab_title.indexOf( ' ' ) + 1 ) }}}
										</a>
									<# } else { #>
										<a href="">
											<?php if ( $use_title_line_prefix ) : ?>
												<span class="vamtam-prefix"></span>
											<?php endif; ?>
											{{{ item.tab_title }}}
										</a>
									<# } #>
								</div>
							<# } ); #>
						</div>
						<div class="elementor-tabs-content-wrapper">
							<#
							_.each( settings.tabs, function( item, index ) {
								var tabCount = index + 1,
									tabContentKey = view.getRepeaterSettingKey( 'tab_content', 'tabs',index );

								view.addRenderAttribute( tabContentKey, {
									'id': 'elementor-tab-content-' + tabindex + tabCount,
									'class': [ 'elementor-tab-content', 'elementor-clearfix', 'elementor-repeater-item-' + item._id ],
									'data-tab': tabCount,
									'role' : 'tabpanel',
									'aria-labelledby' : 'elementor-tab-title-' + tabindex + tabCount
								} );

								view.addInlineEditingAttributes( tabContentKey, 'advanced' );
								#>
								<div class="elementor-tab-title elementor-tab-mobile-title" data-tab="{{ tabCount }}" role="tab">{{{ item.tab_title }}}</div>
								<div {{{ view.getRenderAttributeString( tabContentKey ) }}}>{{{ item.tab_content }}}</div>
							<# } ); #>
						</div>
					<# } #>
				</div>
				<?php
			} else {
				parent::content_template();
			}
		}
	}

	// Replace current tabs widget with our extended version.
	$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
	$widgets_manager->unregister( 'tabs' );
	$widgets_manager->register( new Vamtam_Widget_Tabs );
}
add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
