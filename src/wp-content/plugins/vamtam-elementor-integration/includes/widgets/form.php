<?php
namespace VamtamElementor\Widgets\Form;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;

// Extending the Form widget.

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

if ( vamtam_theme_supports( 'form--bijoux-button-type' ) ) {
	function render_content( $content, $widget ) {
		if ( 'form' === $widget->get_name() ) {
			$settings = $widget->get_settings();

			if ( ! empty( $settings['button_type'] ) && $settings['button_type'] === 'bijoux-alt' ) {
				// Add element required for bijoux alt btn type (prefix).
				$content = str_replace(
					'<span class="elementor-button-text">',
					'<span class="vamtam-prefix"></span><span class="elementor-button-text">',
					$content
				);
			}
		}
		return $content;
	}
	// Called frontend & editor (editor after element loses focus).
	add_filter( 'elementor/widget/render_content', __NAMESPACE__ . '\render_content', 10, 2 );

	function add_button_section_controls( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'button_size',
			'at' => 'before',
		] );
		$widget->add_control(
			'button_type',
			[
				'label' => __( 'Type', 'vamtam-elementor-widgets' ),
				'type' => $controls_manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'Default', 'vamtam-elementor-widgets' ),
					'bijoux-alt' => __( 'Bijoux Alt', 'vamtam-elementor-widgets' ),
				],
				'prefix_class' => 'vamtam-has-',
				'render_type' => 'template',
			]
		);
		$widget->end_injection();
	}
	// Content - Buttons section
	function section_buttons_content_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_button_section_controls( $controls_manager, $widget );
	}
	add_action( 'elementor/element/form/section_buttons/before_section_end', __NAMESPACE__ . '\section_buttons_content_before_section_end', 10, 2 );

	function add_button_style_section_controls( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'heading_next_submit_button',
		] );
		// Line Color.
		$widget->add_control(
			'prefix_color',
			[
				'label' => __( 'Line Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .vamtam-prefix::before' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'button_type' => 'bijoux-alt',
				]
			]
		);
		$widget->end_injection();

		$widget->start_injection( [
			'of' => 'heading_next_submit_button_hover',
		] );
		// Line Color Hover.
		$widget->add_control(
			'prefix_color_hover',
			[
				'label' => __( 'Line Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-button:hover .vamtam-prefix::before' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'button_type' => 'bijoux-alt',
				]
			]
		);
		$widget->end_injection();
		//Line Padding
		$widget->start_injection( [
			'of' => 'button_text_padding',
		] );
		$widget->add_responsive_control(
			'vamtam_prefix_padding',
			[
				'label' => __( 'Line Padding', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .vamtam-prefix' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'allowed_dimensions' => 'horizontal',
				'condition' => [
					'button_type' => 'bijoux-alt',
				]
			]
		);
		$widget->end_injection();
	}
	// Style - Buttons section
	function section_button_style_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_button_style_section_controls( $controls_manager, $widget );
	}
	add_action( 'elementor/element/form/section_button_style/before_section_end', __NAMESPACE__ . '\section_button_style_before_section_end', 10, 2 );

	// Vamtam_Widget_Form.
	function widgets_registered() {
		if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
			return;
		}

		class Vamtam_Widget_Form extends \ElementorPro\Modules\Forms\Widgets\Form {
			// Override.
			protected function content_template() {
				$submit_text = esc_html__( 'Submit', 'vamtam-elementor-integration' );
				?>
				<form class="elementor-form" id="{{settings.form_id}}" name="{{settings.form_name}}">
					<div class="elementor-form-fields-wrapper elementor-labels-{{settings.label_position}}">
						<#
							for ( var i in settings.form_fields ) {
								var item = settings.form_fields[ i ];
								item = elementor.hooks.applyFilters( 'elementor_pro/forms/content_template/item', item, i, settings );

								var options = item.field_options ? item.field_options.split( '\n' ) : [],
									itemClasses = _.escape( item.css_classes ),
									labelVisibility = '',
									placeholder = '',
									required = '',
									inputField = '',
									multiple = '',
									fieldGroupClasses = 'elementor-field-group elementor-column elementor-field-type-' + item.field_type,
									printLabel = settings.show_labels && ! [ 'hidden', 'html', 'step' ].includes( item.field_type );

								fieldGroupClasses += ' elementor-col-' + ( ( '' !== item.width ) ? item.width : '100' );

								if ( item.width_tablet ) {
									fieldGroupClasses += ' elementor-md-' + item.width_tablet;
								}

								if ( item.width_mobile ) {
									fieldGroupClasses += ' elementor-sm-' + item.width_mobile;
								}

								if ( item.required ) {
									required = 'required';
									fieldGroupClasses += ' elementor-field-required';

									if ( settings.mark_required ) {
										fieldGroupClasses += ' elementor-mark-required';
									}
								}

								if ( item.placeholder ) {
									placeholder = 'placeholder="' + _.escape( item.placeholder ) + '"';
								}

								if ( item.allow_multiple ) {
									multiple = ' multiple';
									fieldGroupClasses += ' elementor-field-type-' + item.field_type + '-multiple';
								}

								switch ( item.field_type ) {
									case 'step':
										inputField = `<div
											class="e-field-step elementor-hidden"
											data-label="${ item.field_label }"
											data-previousButton="${ item.previous_button || '' }"
											data-nextButton="${ item.next_button || '' }"
											data-iconUrl="${ 'svg' === item.selected_icon.library && item.selected_icon.value ? item.selected_icon.value.url : '' }"
											data-iconLibrary="${ 'svg' !== item.selected_icon.library && item.selected_icon.value ? item.selected_icon.value : '' }"></div>`;
										break;
									case 'html':
										inputField = item.field_html;
										break;

									case 'textarea':
										inputField = '<textarea class="elementor-field elementor-field-textual elementor-size-' + settings.input_size + ' ' + itemClasses + '" name="form_field_' + i + '" id="form_field_' + i + '" rows="' + item.rows + '" ' + required + ' ' + placeholder + '>' + item.field_value + '</textarea>';
										break;

									case 'select':
										if ( options ) {
											var size = '';
											if ( item.allow_multiple && item.select_size ) {
												size = ' size="' + item.select_size + '"';
											}
											inputField = '<div class="elementor-field elementor-select-wrapper ' + itemClasses + '">';
											inputField += '<select class="elementor-field-textual elementor-size-' + settings.input_size + '" name="form_field_' + i + '" id="form_field_' + i + '" ' + required + multiple + size + ' >';
											for ( var x in options ) {
												var option_value = options[ x ];
												var option_label = options[ x ];
												var option_id = 'form_field_option' + i + x;

												if ( options[ x ].indexOf( '|' ) > -1 ) {
													var label_value = options[ x ].split( '|' );
													option_label = label_value[0];
													option_value = label_value[1];
												}

												view.addRenderAttribute( option_id, 'value', option_value );
												if ( item.field_value.split( ',' ) .indexOf( option_value ) ) {
													view.addRenderAttribute( option_id, 'selected', 'selected' );
												}
												inputField += '<option ' + view.getRenderAttributeString( option_id ) + '>' + option_label + '</option>';
											}
											inputField += '</select></div>';
										}
										break;

									case 'radio':
									case 'checkbox':
										if ( options ) {
											var multiple = '';

											if ( 'checkbox' === item.field_type && options.length > 1 ) {
												multiple = '[]';
											}

											inputField = '<div class="elementor-field-subgroup ' + itemClasses + ' ' + item.inline_list + '">';

											for ( var x in options ) {
												var option_value = options[ x ];
												var option_label = options[ x ];
												var option_id = 'form_field_' + item.field_type + i + x;
												if ( options[x].indexOf( '|' ) > -1 ) {
													var label_value = options[x].split( '|' );
													option_label = label_value[0];
													option_value = label_value[1];
												}

												view.addRenderAttribute( option_id, {
													value: option_value,
													type: item.field_type,
													id: 'form_field_' + i + '-' + x,
													name: 'form_field_' + i + multiple
												} );

												if ( option_value ===  item.field_value ) {
													view.addRenderAttribute( option_id, 'checked', 'checked' );
												}

												inputField += '<span class="elementor-field-option"><input ' + view.getRenderAttributeString( option_id ) + ' ' + required + '> ';
												inputField += '<label for="form_field_' + i + '-' + x + '">' + option_label + '</label></span>';

											}

											inputField += '</div>';
										}
										break;

									case 'text':
									case 'email':
									case 'url':
									case 'password':
									case 'number':
									case 'search':
										itemClasses = 'elementor-field-textual ' + itemClasses;
										inputField = '<input size="1" type="' + item.field_type + '" value="' + item.field_value + '" class="elementor-field elementor-size-' + settings.input_size + ' ' + itemClasses + '" name="form_field_' + i + '" id="form_field_' + i + '" ' + required + ' ' + placeholder + ' >';
										break;
									default:
										inputField = elementor.hooks.applyFilters( 'elementor_pro/forms/content_template/field/' + item.field_type, '', item, i, settings );
								}

								if ( inputField ) {
									#>
									<div class="{{ fieldGroupClasses }}">

										<# if ( printLabel && item.field_label ) { #>
											<label class="elementor-field-label" for="form_field_{{ i }}" {{{ labelVisibility }}}>{{{ item.field_label }}}</label>
										<# } #>

										{{{ inputField }}}
									</div>
									<#
								}
							}


							var buttonClasses = 'elementor-field-group elementor-column elementor-field-type-submit e-form__buttons';

							buttonClasses += ' elementor-col-' + ( ( '' !== settings.button_width ) ? settings.button_width : '100' );

							if ( settings.button_width_tablet ) {
								buttonClasses += ' elementor-md-' + settings.button_width_tablet;
							}

							if ( settings.button_width_mobile ) {
								buttonClasses += ' elementor-sm-' + settings.button_width_mobile;
							}

							var iconHTML = elementor.helpers.renderIcon( view, settings.selected_button_icon, { 'aria-hidden': true }, 'i' , 'object' ),
								migrated = elementor.helpers.isIconMigrated( settings, 'selected_button_icon' );

							#>

							<div class="{{ buttonClasses }}">
								<button id="{{ settings.button_css_id }}" type="submit" class="elementor-button elementor-size-{{ settings.button_size }} elementor-button-{{ settings.button_type }} elementor-animation-{{ settings.button_hover_animation }}">
									<span>
										<# if ( settings.button_type === 'bijoux-alt' ) { #>
											<span class="vamtam-prefix"></span>
										<# } #>
										<# if ( settings.button_icon || settings.selected_button_icon ) { #>
											<span class="elementor-button-icon elementor-align-icon-{{ settings.button_icon_align }}">
												<# if ( iconHTML && iconHTML.rendered && ( ! settings.button_icon || migrated ) ) { #>
													{{{ iconHTML.value }}}
												<# } else { #>
													<i class="{{ settings.button_icon }}" aria-hidden="true"></i>
												<# } #>
												<span class="elementor-screen-only"><?php echo $submit_text; ?></span>
											</span>
										<# } #>

										<# if ( settings.button_text ) { #>
											<span class="elementor-button-text">{{{ settings.button_text }}}</span>
										<# } #>
									</span>
								</button>
							</div>
					</div>
				</form>
				<?php
			}
		}

		// Replace current divider widget with our extended version.
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets_manager->unregister( 'form' );
		$widgets_manager->register( new Vamtam_Widget_Form );
	}
	add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
}

if ( vamtam_theme_supports( 'form--fitness-form-style' ) ) {
	function add_use_theme_form_style_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'column_gap',
			'at' => 'before',
		] );
		$widget->add_control(
			'use_theme_form_style',
			[
				'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'theme-form-style',
				'default' => 'theme-form-style',
				'render_type' => 'template',
			]
		);
		$widget->end_injection();
	}
	// Style - Form section
	function section_form_style_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_use_theme_form_style_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/form/section_form_style/before_section_end', __NAMESPACE__ . '\section_form_style_before_section_end', 10, 2 );
}

if ( vamtam_theme_supports( 'form--updated-fields-style-section' ) ) {
	function add_new_field_style_section( $controls_manager, $widget ) {
		// Remove prev section_field_style section.
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'section_field_style' );

		// We also need to remove it's fields (so we can re-declare them)
		// Text Color.
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'field_text_color' );
		// Typography.
		\Vamtam_Elementor_Utils::remove_group_control( $controls_manager, $widget, 'field_typography', \Elementor\Group_Control_Typography::get_type() );
		// Bg Color.
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'field_background_color' );
		// Border Color.
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'field_border_color' );
		// Border Width.
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'field_border_width' );
		// Border Radius.
		\Vamtam_Elementor_Utils::remove_control( $controls_manager, $widget, 'field_border_radius' );


		// Start new section.
		$widget->start_controls_section(
			'section_field_styles',
			[
				'label' => __( 'Field', 'vamtam-elementor-integration' ),
				'tab' => $controls_manager::TAB_STYLE,
			]
		);

		// Typography.
		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'field_typography',
				'selector' => '{{WRAPPER}} .elementor-field-group .elementor-field, {{WRAPPER}} .elementor-field-subgroup label',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);
		// Padding
		$widget->add_responsive_control(
			'field_padding',
			[
				'label' => __( 'Padding', 'vamtam-elementor-integration' ),
				'description' => __( 'Applies to fields & labels.', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group .elementor-field, {{WRAPPER}} .elementor-field-group .elementor-field-label, {{WRAPPER}} .elementor-field-subgroup label' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};',
				],
				'allowed_dimensions' => 'horizontal',
			]
		);

		$widget->start_controls_tabs( 'vamtam_field_tabs' );
		// Normal.
		$widget->start_controls_tab(
			'vamtam_field_tabs_normal',
			[
				'label' => __( 'Normal', 'vamtam-elementor-integration' ),
			]
		);
		// Text Color.
		$widget->add_control(
			'field_text_color',
			[
				'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group .elementor-field,
					{{WRAPPER}} .elementor-field-group .elementor-field::placeholder' => 'color: {{VALUE}}; caret-color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);
		// Bg Color.
		$widget->add_control(
			'field_background_color',
			[
				'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper),
					{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select' => 'background-color: {{VALUE}};',
				],
			]
		);
		// Box Shadow.
		$widget->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'field_box_shadow',
				'selector' => '{{WRAPPER}} input:not([type="button"]):not([type="submit"]), {{WRAPPER}} textarea, {{WRAPPER}} .elementor-field-textual',
			]
		);
		// Border.
		$widget->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'field_border',
				'selector' => '{{WRAPPER}} input:not([type="button"]):not([type="submit"]), {{WRAPPER}} textarea, {{WRAPPER}} .elementor-field-textual',
				'fields_options' => [
					'color' => [
						'dynamic' => [],
						'selectors' => [
							'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper),
							{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select' => 'border-color: {{VALUE}};',
							'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper::before' => 'color: {{VALUE}};',
						],
					],
					'width' => [
						'selectors' => [
							'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper),
							{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					],
				],
			]
		);
		// Border Radius.
		$widget->add_control(
			'field_border_radius',
			[
				'label' => __( 'Border Radius', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper),
					{{WRAPPER}} .elementor-field-group .elementor-select-wrapper select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$widget->end_controls_tab();
		// Hover
		$widget->start_controls_tab(
			'vamtam_field_tabs_hover',
			[
				'label' => __( 'Hover', 'vamtam-elementor-integration' ),
			]
		);
		// Hover Text Color.
		$widget->add_control(
			'field_text_color_hover',
			[
				'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group .elementor-field:hover,
					{{WRAPPER}} .elementor-field-group .elementor-field:hover::placeholder' => 'color: {{VALUE}}; caret-color: {{VALUE}};',
				],
			]
		);
		// Hover Bg Color.
		$widget->add_control(
			'field_background_color_hover',
			[
				'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper):hover,
					{{WRAPPER}} .elementor-field-group .elementor-select-wrapper:hover select' => 'background-color: {{VALUE}};',
				],
			]
		);
		// Hover Box Shadow.
		$widget->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'field_box_shadow_hover',
				'selector' => '{{WRAPPER}} input:hover:not([type="button"]):not([type="submit"]), {{WRAPPER}} textarea:hover, {{WRAPPER}} .elementor-field-textual:hover',
			]
		);
		// Hover Border.
		$widget->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'field_border_hover',
				'selector' => '{{WRAPPER}} input:hover:not([type="button"]):not([type="submit"]), {{WRAPPER}} textarea:hover, {{WRAPPER}} .elementor-field-textual:hover',
				'fields_options' => [
					'color' => [
						'dynamic' => [],
						'selectors' => [
							'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper):hover,
							{{WRAPPER}} .elementor-field-group .elementor-select-wrapper:hover select' => 'border-color: {{VALUE}};',
							'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper:hover::before' => 'color: {{VALUE}};',
						],
					],
					'width' => [
						'selectors' => [
							'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper):hover,
							{{WRAPPER}} .elementor-field-group .elementor-select-wrapper:hover select' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					],
				],
			]
		);
		// Hover Border Radius.
		$widget->add_control(
			'field_border_radius_hover',
			[
				'label' => __( 'Border Radius', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper):hover,
					{{WRAPPER}} .elementor-field-group .elementor-select-wrapper:hover select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		// Hover Transition Duration.
		$widget->add_control(
			'field_hover_transition_duration',
			[
				'label' => __( 'Transition Duration', 'vamtam-elementor-integration' ) . ' (ms)',
				'type' => $controls_manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} input:not([type="button"]):not([type="submit"]),
					{{WRAPPER}} textarea,
					{{WRAPPER}} .elementor-field-textual' => 'transition: {{SIZE}}ms',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3000,
					],
				],
			]
		);
		$widget->end_controls_tab();
		// Focus
		$widget->start_controls_tab(
			'vamtam_field_tabs_focus',
			[
				'label' => __( 'Focus', 'vamtam-elementor-integration' ),
			]
		);
		// Focus Text Color.
		$widget->add_control(
			'field_text_color_focus',
			[
				'label' => __( 'Text Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group .elementor-field:focus,
					{{WRAPPER}} .elementor-field-group .elementor-field:focus::placeholder' => 'color: {{VALUE}}; caret-color: {{VALUE}};',
				],
			]
		);
		// Focus Bg Color.
		$widget->add_control(
			'field_background_color_focus',
			[
				'label' => __( 'Background Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper):focus,
					{{WRAPPER}} .elementor-field-group .elementor-select-wrapper:focus select' => 'background-color: {{VALUE}};',
				],
			]
		);
		// Focus Box Shadow.
		$widget->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'field_box_shadow_focus',
				'selector' => '{{WRAPPER}} input:focus:not([type="button"]):not([type="submit"]), {{WRAPPER}} textarea:focus, {{WRAPPER}} .elementor-field-textual:focus',
			]
		);
		// Focus Border.
		$widget->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'field_border_focus',
				'selector' => '{{WRAPPER}} input:focus:not([type="button"]):not([type="submit"]), {{WRAPPER}} textarea:focus, {{WRAPPER}} .elementor-field-textual:focus',
				'fields_options' => [
					'color' => [
						'dynamic' => [],
						'selectors' => [
							'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper):focus,
							{{WRAPPER}} .elementor-field-group .elementor-select-wrapper:focus select' => 'border-color: {{VALUE}};',
							'{{WRAPPER}} .elementor-field-group .elementor-select-wrapper:focus::before' => 'color: {{VALUE}};',
						],
					],
					'width' => [
						'selectors' => [
							'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper):focus,
							{{WRAPPER}} .elementor-field-group .elementor-select-wrapper:focus select' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					],
				],
			]
		);
		// Focus Border Radius.
		$widget->add_control(
			'field_border_radius_focus',
			[
				'label' => __( 'Border Radius', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-field-group:not(.elementor-field-type-upload) .elementor-field:not(.elementor-select-wrapper):focus,
					{{WRAPPER}} .elementor-field-group .elementor-select-wrapper:focus select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$widget->end_controls_tab();
		$widget->end_controls_tabs();

		$widget->end_controls_section();
	}
	// Style - Fields section
	function section_field_style_after_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_new_field_style_section( $controls_manager, $widget );
	}
	add_action( 'elementor/element/form/section_field_style/after_section_end', __NAMESPACE__ . '\section_field_style_after_section_end', 10, 2 );
}
