<?php
namespace VamtamElementor\Widgets\Heading;

// Extending the Heading widget.
if ( current_theme_supports( 'vamtam-elementor-widgets', 'heading--caridad-heading-skin' ) ) {
	// Caridad Skin
	function vamtam_add_caridad_heading_skin( $widget ) {
		if ( did_action( 'vamtam_elementor/heading/heading_caridad/skin_registered' ) ) {
			// This is in case another theme feature requires to re-register (extend) the widget.
			// In that case the widget's action's will be added twice leading to control stack issues.
			return;
		}

		class Vamtam_Heading_Caridad_Skin extends \Elementor\Skin_Base {
			public function get_id() {
				return 'heading_caridad';
			}

			public function get_title() {
				return __( 'Caridad', 'vamtam-elementor-integration' );
			}

			// Skin render().
			public function render() {
				$widget = $this->parent;
				$skin   = $this;

				$settings = $widget->get_settings_for_display();

				if ( '' === $settings['title'] ) {
					return;
				}

				$widget->add_render_attribute( 'title', 'class', 'elementor-heading-title' );

				if ( ! empty( $settings['size'] ) ) {
					$widget->add_render_attribute( 'title', 'class', 'elementor-size-' . $settings['size'] );
				}
				if ( ! empty( $settings['link']['url'] ) ) {
					$widget->add_link_attributes( 'url', $settings['link'] );
				}

				$lines = explode( PHP_EOL, $settings['title'] );

				echo "<{$settings['header_size']} class='vamtam-heading-wrapper'>";
					foreach ( $lines as $line ) {
						$line = trim( $line );

						if ( empty( $line ) ) {
							continue;
						}

						if ( ! empty( $settings['link']['url'] ) ) {
							$line = sprintf( '<a %1$s>%2$s</a>', $widget->get_render_attribute_string( 'url' ), $line );
						}

						$line_html = "<div {$widget->get_render_attribute_string( 'title' )}>{$line}</div>";

						echo $line_html;
					}
				echo "</{$settings['header_size']}>";
			}
			// content_template() we override using widget extension.

			protected function _register_controls_actions() {
				add_action( 'elementor/element/heading/section_title/before_section_end', [ $this, 'section_title_before_section_end' ] );
				add_action( 'elementor/element/heading/section_title_style/before_section_end', [ $this, 'section_title_style_before_section_end' ] );

				// !! Important: Add this action on every custom skin to avoid issues with widget extensions. !!
				do_action( 'vamtam_elementor/heading/heading_caridad/skin_registered' );
			}

			// Content - Before Section end.
			public function section_title_before_section_end( $widget ) {
				$this->parent = $widget;
				$this->add_controls_content_tab( $widget );
				$this->update_controls_content_tab( $widget );
			}

			protected function add_controls_content_tab( $widget ) {
				$controls_manager = \Elementor\Plugin::instance()->controls_manager;
				$widget->start_injection( [
					'of' => 'title',
					'at' => 'before',
				] );
				// Notice.
				$widget->add_control(
					'vamtam_title_notice',
					[
						'raw' => __( 'Use new lines to indicate a new sentence block.', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::RAW_HTML,
						'content_classes' => 'elementor-descriptor',
						'condition' => [
							'_skin' => 'heading_caridad',
						]
					]
				);
				$widget->end_injection();
				$widget->start_injection( [
					'of' => 'align',
				] );
				// Align.
				$widget->add_responsive_control(
					'vamtam_align',
					[
						'label' => __( 'Alignment', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::CHOOSE,
						'default' => '',
						'options' => [
							'left' => [
								'title' => __( 'Left', 'vamtam-elementor-integration' ),
								'icon' => 'eicon-text-align-left',
							],
							'center' => [
								'title' => __( 'Center', 'vamtam-elementor-integration' ),
								'icon' => 'eicon-text-align-center',
							],
							'right' => [
								'title' => __( 'Right', 'vamtam-elementor-integration' ),
								'icon' => 'eicon-text-align-right',
							],
							'justify' => [
								'title' => __( 'Justified', 'elementor' ),
								'icon' => 'eicon-text-align-justify',
							],
						],
						'prefix_class' => 'vamtam%s-align-',
						'selectors_dictionary' => [
							'left' => 'flex-start',
							'right' => 'flex-end',
							'justify' => 'flex-start',
						],
						'selectors' => [
							'{{WRAPPER}} .vamtam-heading-wrapper' => 'align-items: {{VALUE}};',
						],
						'condition' => [
							'_skin' => 'heading_caridad',
						]
					]
				);
				$widget->end_injection();
			}
			protected function update_controls_content_tab( $widget ) {
				$controls_manager = \Elementor\Plugin::instance()->controls_manager;
				// Align.
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'align', [
					'condition' => [
						'_skin!' => 'heading_caridad',
					],
				] );
			}

			// Style - Before Section end.
			public function section_title_style_before_section_end( $widget ) {
				$this->parent = $widget;
				$this->add_controls_style_tab( $widget );
			}

			protected function add_controls_style_tab( $widget ) {
				$controls_manager = \Elementor\Plugin::instance()->controls_manager;
				$widget->start_injection( [
					'of' => 'title_color',
				] );
				// Bg Color.
				$widget->add_control(
					'vamtam_title_bg_color',
					[
						'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .vamtam-heading-wrapper > *' => 'background-color: {{VALUE}}',
						],
						'condition' => [
							'_skin' => 'heading_caridad',
						]
					]
				);
				// Spacing
				$widget->add_control(
					'vamtam_title_spacing',
					[
						'label' => __( 'Spacing', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::SLIDER,
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'max' => 100,
								'min' => 0,
							],
						],
						'default' => [
							'size' => 10,
						],
						'selectors' => [
							'{{WRAPPER}} .vamtam-heading-wrapper > :not(:first-child)' => 'margin-top: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'_skin' => 'heading_caridad',
						]
					]
				);
				$widget->end_injection();
			}
		}

		$widget->add_skin( new Vamtam_Heading_Caridad_Skin( $widget ) );
	}
	add_action( 'elementor/widget/heading/skins_init', __NAMESPACE__ . '\vamtam_add_caridad_heading_skin' );

	// Vamtam_Widget_Heading.
	function widgets_registered() {
		class Vamtam_Widget_Heading extends \Elementor\Widget_Heading {
			// Override.
			protected function content_template() {
				?>
				<#
				if ( settings._skin === 'heading_caridad' ) {
					var html  = '';
					var lines = settings.title.split( '\n' );

					html += '<div class="vamtam-heading-wrapper">';

					lines.forEach( line => {
						line = line.trim();

						if ( ! line ) {
							return;
						}

						if ( '' !== settings.link.url ) {
							line = '<a href="' + settings.link.url + '">' + line + '</a>';
						}

						view.addRenderAttribute( 'title', 'class', [ 'elementor-heading-title', 'elementor-size-' + settings.size ] );

						var line_html = '<' + settings.header_size  + ' ' + view.getRenderAttributeString( 'title' ) + '>' + line + '</' + settings.header_size + '>';

						html += line_html;
					} );
					html += '</div>';
					print( html );
				} else {
					var title = settings.title;

					if ( '' !== settings.link.url ) {
						title = '<a href="' + settings.link.url + '">' + title + '</a>';
					}

					view.addRenderAttribute( 'title', 'class', [ 'elementor-heading-title', 'elementor-size-' + settings.size ] );

					view.addInlineEditingAttributes( 'title' );

					var title_html = '<' + settings.header_size  + ' ' + view.getRenderAttributeString( 'title' ) + '>' + title + '</' + settings.header_size + '>';

					print( title_html );
				}
				#>
				<?php
			}
		}

		// Replace current heading widget with our extended version.
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets_manager->unregister( 'heading' );
		$widgets_manager->register( new Vamtam_Widget_Heading );
	}
	add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
}
