<?php
namespace VamtamElementor\Widgets\ImageBox;

// Extending the Image Box widget.

use Elementor\Group_Control_Typography;

function render_content( $content, $widget ) {
	if ( 'image-box' === $widget->get_name() ) {
		$settings = $widget->get_settings();

		if ( ! empty( $settings['link']['url'] ) ) {
			// Add vamtam-is-link class when the url option is set.
			$content = str_replace( 'elementor-image-box-wrapper"', 'elementor-image-box-wrapper vamtam-is-link"', $content );
		}
	}

	return $content;
}
// Called frontend & editor (editor after element loses focus).
add_filter( 'elementor/widget/render_content', __NAMESPACE__ . '\render_content', 10, 2 );

if ( current_theme_supports( 'vamtam-elementor-widgets', 'image-box--subtitle-field' ) ) {
	function add_subtitle_text_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'title_text',
		] );

		$widget->add_control(
			'subtitle_text',
			[
				'label' => __( 'Subtitle', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __( 'This is the subtitle', 'vamtam-elementor-integration' ),
				'placeholder' => __( 'Enter your subtitle', 'vamtam-elementor-integration' ),
				'label_block' => true,
			]
		);

		$widget->end_injection();
	}

	function add_subtitle_size_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'subtitle_size',
		] );

		$widget->add_control(
			'subtitle_size',
			[
				'label' => __( 'Subtitle HTML Tag', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h4',
			]
		);

		$widget->end_injection();
	}

	function update_title_text_control( $controls_manager, $widget ) {
		$new_options = [
			'label'   => __( 'Title', 'vamtam-elementor-integration' ),
			'default' => __( 'This is the title', 'vamtam-elementor-integration' ),
		];

		// Title Text.
		\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'title_text', $new_options );
	}

	function update_description_text_control( $controls_manager, $widget ) {
		$new_options = [
			'label'   => __( 'Description', 'vamtam-elementor-integration' ),
			'show_label' => true,
		];

		// Description Text.
		\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'description_text', $new_options );
	}
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'image-box--subtitle-field' ) ||
	current_theme_supports( 'vamtam-elementor-widgets', 'image-box--box-is-link' ) ) {
	// Content - Image Box section
	function section_image_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		if ( current_theme_supports( 'vamtam-elementor-widgets', 'image-box--subtitle-field' ) ) {
			add_subtitle_text_control( $controls_manager, $widget );
			add_subtitle_size_control( $controls_manager, $widget );
			update_title_text_control( $controls_manager, $widget );
			update_description_text_control( $controls_manager, $widget );
		}

		if ( current_theme_supports( 'vamtam-elementor-widgets', 'image-box--box-is-link' ) ) {
			add_box_is_link_control( $controls_manager, $widget );
		}
	}
	add_action( 'elementor/element/image-box/section_image/before_section_end', __NAMESPACE__ . '\section_image_before_section_end', 10, 2 );
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'image-box--masks-and-eye' ) ) {
	function add_vamtam_section_and_controls( $controls_manager, $widget ) {
		$widget->start_controls_section(
			'section_style_vamtam',
			[
				'label' => __( 'Vamtam', 'vamtam-elementor-integration' ),
				'tab'   => $controls_manager::TAB_STYLE,
			]
		);

		$widget->add_control(
			'use_bg_mask',
			[
				'label' => __( 'Use Theme\'s Background Mask', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'default' => '',
				'prefix_class' => 'vamtam-has-',
				'label_on' => __( 'Yes', 'vamtam-elementor-integration' ),
				'label_off' => __( 'No', 'vamtam-elementor-integration' ),
				'return_value' => 'bg-mask',
			]
		);
		$widget->add_control(
			'use_image_mask',
			[
				'label' => __( 'Use Theme\'s Image Mask', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'default' => '',
				'prefix_class' => 'vamtam-has-',
				'label_on' => __( 'Yes', 'vamtam-elementor-integration' ),
				'label_off' => __( 'No', 'vamtam-elementor-integration' ),
				'return_value' => 'image-mask',
			]
		);
		$widget->add_control(
			'show_eye',
			[
				'label' => __( 'Show eye', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'default' => '',
				'prefix_class' => 'vamtam-has-',
				'label_on' => __( 'Yes', 'vamtam-elementor-integration' ),
				'label_off' => __( 'No', 'vamtam-elementor-integration' ),
				'return_value' => 'eye',
				'render_type' => 'template'
			]
		);
		$widget->add_control(
			"eye_outer_color",
			[
				'label' => __( 'Eye Outer Circle Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'default' => 'var(--vamtam-accent-color-1)',
				'selectors' => [
					'{{WRAPPER}} .vamtam-eye .outer' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'show_eye!' => '',
				],
			]
		);
		$widget->add_control(
			"eye_inner_color",
			[
				'label' => __( 'Eye Inner Circle Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'default' => 'var(--vamtam-accent-color-5)',
				'selectors' => [
					'{{WRAPPER}} .vamtam-eye .inner' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'show_eye!' => '',
				],
			]
		);
		$widget->add_control(
			"eye_eye_color",
			[
				'label' => __( 'Eye Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'default' => 'var(--vamtam-accent-color-6)',
				'selectors' => [
					'{{WRAPPER}} .vamtam-eye .eye:after' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'show_eye!' => '',
				],
			]
		);
		$widget->add_control(
			"eye_initial_pos",
			[
				'label' => __( 'Eye Initial Position', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'default' => [
					'size' => 180,
					'unit' => 'deg',
				],
				'selectors' => [
					'{{WRAPPER}} .vamtam-eye .eye' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'show_eye!' => '',
				],
			]
		);
		$widget->add_control(
			'eye_random_movement',
			[
				'label' => __( 'Eye Random Movement', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'default' => '',
				'description' => __( 'The eye will move randomly when the cursor is not inside the area of the element.', 'vamtam-elementor-integration' ),
				'label_on' => __( 'Yes', 'vamtam-elementor-integration' ),
				'label_off' => __( 'No', 'vamtam-elementor-integration' ),
				'prefix_class' => '',
				'return_value' => 'eye-random',
				'render_type' => 'template',
				'condition' => [
					'show_eye!' => '',
				],
			]
		);
		$widget->add_control(
			"eye_random_movement_interval",
			[
				'label' => __( 'Random Movement Interval', 'vamtam-elementor-integration' ),
				'description' => __( 'The interval (in seconds) at which random movement will occur.', 'vamtam-elementor-integration' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 5,
				'max' => 30,
				'step' => 1,
				'default' => 10,
				'condition' => [
					'show_eye!' => '',
					'eye_random_movement!' => '',
				],
			]
		);

		$widget->end_controls_section();

	}

	// Style - Content section (After).
	function section_style_content_after_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_vamtam_section_and_controls( $controls_manager, $widget );
	}
	add_action( 'elementor/element/image-box/section_style_content/after_section_end', __NAMESPACE__ . '\section_style_content_after_section_end', 10, 2 );
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'image-box--box-is-link' ) ) {
	function add_box_is_link_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'position',
			'at' => 'before',
		] );

		$widget->add_control(
			'box_is_link',
			[
				'label' => __( 'Whole Box is Link', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'box-is-link',
				'render_type' => 'template',
			]
		);
		$widget->end_injection();
	}

	function add_style_controls_for_box_is_link( $controls_manager, $widget ) {
		// Title
		$widget->start_injection( [
			'of' => 'title_color',
			'at' => 'before',
		] );
		$widget->start_controls_tabs( 'title_tabs' );
		// Normal
		$widget->start_controls_tab(
			'title_tabs_normal',
			[
				'label' => __( 'Normal', 'vamtam-elementor-integration' ),
				'condition' => [
					'box_is_link!' => '',
				],
			]
		);
		// We have to remove and re-add existing controls so they can be properly inserted into the tabs.
		$widget->remove_control( 'title_color' );
		$widget->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-image-box-content .elementor-image-box-title' => 'color: {{VALUE}};',
				],
			]
		);
		$widget->remove_control( 'title_typography_typography' );
		$control_group = $controls_manager->get_control_groups( 'typography' );
		foreach ( $control_group->get_fields() as $key => $value ) {
			$control_id = 'title_typography_' . $key;
			if ( in_array( $key, [ 'font_size', 'line_height', 'letter_spacing' ] ) ) {
				foreach ( [ $control_id, $control_id . '_tablet', $control_id . '_mobile' ] as $device_cid  ) {
					$widget->remove_control( $device_cid );
				}
			} else {
				$widget->remove_control( $control_id );
			}
		}
		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .elementor-image-box-content .elementor-image-box-title',
			]
		);
		$widget->end_controls_tab();
		// Hover
		$widget->start_controls_tab(
			'title_tabs_hover',
			[
				'label' => __( 'Hover', 'vamtam-elementor-integration' ),
				'condition' => [
					'box_is_link!' => '',
				],
			]
		);
		$widget->add_control(
			'title_color_hover',
			[
				'label' => __( 'Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-image-box-content .elementor-image-box-title:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'box_is_link!' => '',
				],
			]
		);
		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography_hover',
				'selector' => '{{WRAPPER}} .elementor-image-box-content .elementor-image-box-title:hover',
				'condition' => [
					'box_is_link!' => '',
				],
			]
		);
		$widget->end_controls_tab();
		$widget->end_controls_tabs();
		$widget->end_injection();

		// Description
		$widget->start_injection( [
			'of' => 'description_color',
			'at' => 'before',
		] );
		$widget->start_controls_tabs( 'description_tabs' );
		// Normal
		$widget->start_controls_tab(
			'description_tabs_normal',
			[
				'label' => __( 'Normal', 'vamtam-elementor-integration' ),
				'condition' => [
					'box_is_link!' => '',
				],
			]
		);
		// We have to remove and re-add existing controls so they can be properly inserted into the tabs.
		$widget->remove_control( 'description_color' );
		$widget->add_control(
			'description_color',
			[
				'label' => __( 'Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-image-box-content .elementor-image-box-description' => 'color: {{VALUE}};',
				],
			]
		);
		$widget->remove_control( 'description_typography_typography' );
		$control_group = $controls_manager->get_control_groups( 'typography' );
		foreach ( $control_group->get_fields() as $key => $value ) {
			$control_id = 'description_typography_' . $key;
			if ( in_array( $key, [ 'font_size', 'line_height', 'letter_spacing' ] ) ) {
				foreach ( [ $control_id, $control_id . '_tablet', $control_id . '_mobile' ] as $device_cid  ) {
					$widget->remove_control( $device_cid );
				}
			} else {
				$widget->remove_control( $control_id );
			}
		}
		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .elementor-image-box-content .elementor-image-box-description',
			]
		);
		$widget->end_controls_tab();
		// Hover
		$widget->start_controls_tab(
			'description_tabs_hover',
			[
				'label' => __( 'Hover', 'vamtam-elementor-integration' ),
				'condition' => [
					'box_is_link!' => '',
				],
			]
		);
		$widget->add_control(
			'description_color_hover',
			[
				'label' => __( 'Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-image-box-content .elementor-image-box-description:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'box_is_link!' => '',
				],
			]
		);
		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography_hover',
				'selector' => '{{WRAPPER}} .elementor-image-box-content .elementor-image-box-description:hover',
				'condition' => [
					'box_is_link!' => '',
				],
			]
		);
		$widget->end_controls_tab();
		$widget->end_controls_tabs();
		$widget->end_injection();
	}

	// Style - Content section (Before).
	function section_style_content_before_section_end ( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_style_controls_for_box_is_link( $controls_manager, $widget );
	}
	add_action( 'elementor/element/image-box/section_style_content/before_section_end', __NAMESPACE__ . '\section_style_content_before_section_end', 10, 2 );
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'image-box--masks-and-eye' ) ||
	current_theme_supports( 'vamtam-elementor-widgets', 'image-box--subtitle-field' ) ||
	current_theme_supports( 'vamtam-elementor-widgets', 'image-box--box-is-link' ) ) {
	// Vamtam_Widget_Image_Box.
	function widgets_registered() {
		class Vamtam_Widget_Image_Box extends \Elementor\Widget_Image_Box {
			public $extra_depended_scripts = [
				'vamtam-image-box',
			];

			// Extend constructor.
			public function __construct($data = [], $args = null) {
				parent::__construct($data, $args);
				if ( current_theme_supports( 'vamtam-elementor-widgets', 'image-box--masks-and-eye' ) ) {
					$this->register_assets();

					$this->add_extra_script_depends();
				}
			}

			// Register the assets the widget depends on.
			public function register_assets() {
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_register_script(
					'vamtam-image-box',
					VAMTAM_ELEMENTOR_INT_URL . 'assets/js/widgets/image-box/vamtam-image-box' . $suffix . '.js',
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
			$settings = $this->get_settings_for_display();

			$has_content = ! \Elementor\Utils::is_empty( $settings['title_text'] ) || ! \Elementor\Utils::is_empty( $settings['description_text'] );

			$wrapper_tag             = 'div';
			$wrapper_link_attributes = '';
			$box_is_link             = isset( $settings['box_is_link'] ) && ! empty( $settings['box_is_link'] );

			if ( ! empty( $settings['link']['url'] ) ) {
				if ( $box_is_link ) {
					$wrapper_tag = 'a';
					$this->add_link_attributes( 'wrapper-link', $settings['link'] );
					$wrapper_link_attributes = $this->get_render_attribute_string( 'wrapper-link' );
				} else {
					$this->add_link_attributes( 'link', $settings['link'] );
				}
			}

			$html = '<'. $wrapper_tag . ' ' . $wrapper_link_attributes . ' class="elementor-image-box-wrapper">';

			if ( isset( $settings['subtitle_text'] ) && $has_content ) {
				$html .= '<div class="elementor-image-box-content">';

				if ( ! \Elementor\Utils::is_empty( $settings['title_text'] ) ) {
					$this->add_render_attribute( 'title_text', 'class', 'elementor-image-box-title' );

					$this->add_inline_editing_attributes( 'title_text', 'none' );

					$title_html = $settings['title_text'];

					if ( ! empty( $settings['link']['url'] && ! $box_is_link ) ) {
						$title_html = '<a ' . $this->get_render_attribute_string( 'link' ) . '>' . $title_html . '</a>';
					}

					$html .= sprintf( '<%1$s %2$s>%3$s</%1$s>', $settings['title_size'], $this->get_render_attribute_string( 'title_text' ), $title_html );
				}

				$html .= '</div>';
			}

			if ( ! empty( $settings['image']['url'] ) ) {
				$this->add_render_attribute( 'image', 'src', $settings['image']['url'] );
				$this->add_render_attribute( 'image', 'alt', \Elementor\Control_Media::get_image_alt( $settings['image'] ) );
				$this->add_render_attribute( 'image', 'title', \Elementor\Control_Media::get_image_title( $settings['image'] ) );

				if ( $settings['hover_animation'] ) {
					$this->add_render_attribute( 'image', 'class', 'elementor-animation-' . $settings['hover_animation'] );
				}

				$image_html = \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'image' );

				if ( ! empty( $settings['link']['url'] && ! $box_is_link ) ) {
					$image_html = '<a ' . $this->get_render_attribute_string( 'link' ) . '>' . $image_html . '</a>';
				}

				$html .= '<figure class="elementor-image-box-img">' . $image_html . '</figure>';
			}

			if ( $has_content ) {
				$html .= '<div class="elementor-image-box-content">';

				if ( ! isset( $settings['subtitle_text'] ) ) {
					if ( ! \Elementor\Utils::is_empty( $settings['title_text'] ) ) {
						$this->add_render_attribute( 'title_text', 'class', 'elementor-image-box-title' );

						$this->add_inline_editing_attributes( 'title_text', 'none' );

						$title_html = $settings['title_text'];

						if ( ! empty( $settings['link']['url'] && ! $box_is_link ) ) {
							$title_html = '<a ' . $this->get_render_attribute_string( 'link' ) . '>' . $title_html . '</a>';
						}

						$html .= sprintf( '<%1$s %2$s>%3$s</%1$s>', $settings['title_size'], $this->get_render_attribute_string( 'title_text' ), $title_html );
					}
				}

				if ( ! \Elementor\Utils::is_empty( $settings['description_text'] ) ) {
					$this->add_render_attribute( 'description_text', 'class', 'elementor-image-box-description' );

					$this->add_inline_editing_attributes( 'description_text' );

					$html .= sprintf( '<p %1$s>%2$s</p>', $this->get_render_attribute_string( 'description_text' ), $settings['description_text'] );
				}

				if ( isset( $settings['subtitle_size'] ) && isset( $settings['subtitle_text'] ) && ! \Elementor\Utils::is_empty( $settings['subtitle_text'] ) ) {
						$this->add_render_attribute( 'subtitle_text', 'class', 'elementor-image-box-title' );

						$this->add_inline_editing_attributes( 'subtitle_text', 'none' );

						$title_html = $settings['subtitle_text'];

						$html .= sprintf( '<%1$s %2$s>%3$s</%1$s>', $settings['subtitle_size'], $this->get_render_attribute_string( 'subtitle_text' ), $title_html );
					}

				$html .= '</div>';
			}

			if ( isset( $settings['show_eye'] ) && ! \Elementor\Utils::is_empty( $settings['show_eye'] ) ) {
				$this->add_render_attribute( 'show_eye', 'class', 'vamtam-eye' );

				if ( ! \Elementor\Utils::is_empty( $settings['eye_random_movement'] ) ) {
					$this->add_render_attribute( 'eye_random_movement_interval', 'data-eye-interval', $settings['eye_random_movement_interval'] );
				}


				$html .= "<div {$this->get_render_attribute_string( 'show_eye' )}>
							<span class=\"outer\">
								<span class=\"inner\">
									<span class=\"eye\" {$this->get_render_attribute_string( 'eye_random_movement_interval' )}></span>
								</span>
							</span>
						</div>";
			}

			$html .= '</' . $wrapper_tag . '>';

			echo $html;
			}

			// Extend content_template method.
			protected function content_template() {
			?>
			<#
			var	link        = ( settings.link.url && ! settings.box_is_link ) ? 'href="' + settings.link.url + '"' : '',
				wrapperLink = ( settings.link.url && settings.box_is_link ) ? 'href="' + settings.link.url + '"' : '',
				wrapperTag  = wrapperLink ? 'a' : 'div',
				html        = '<' + wrapperTag + ' ' + wrapperLink + ' class="elementor-image-box-wrapper">',
				hasContent  = !! ( settings.title_text || settings.description_text );

			if ( hasContent && settings.subtitle_text ) {
				html += '<div class="elementor-image-box-content">';

				if ( settings.title_text ) {
					var title_html = settings.title_text;

					if ( link ) {
						title_html = '<a href="' + settings.link.url + '">' + title_html + '</a>';
					}

					view.addRenderAttribute( 'title_text', 'class', 'elementor-image-box-title' );

					view.addInlineEditingAttributes( 'title_text', 'none' );

					html += '<' + settings.title_size  + ' ' + view.getRenderAttributeString( 'title_text' ) + '>' + title_html + '</' + settings.title_size  + '>';
				}

				html += '</div>';
			}

			if ( settings.image.url ) {
				var image = {
					id: settings.image.id,
					url: settings.image.url,
					size: settings.thumbnail_size,
					dimension: settings.thumbnail_custom_dimension,
					model: view.getEditModel()
				};

				var image_url = elementor.imagesManager.getImageUrl( image );

				var imageHtml = '<img src="' + image_url + '" class="elementor-animation-' + settings.hover_animation + '" />';

				if ( link ) {
					imageHtml = '<a href="' + settings.link.url + '">' + imageHtml + '</a>';
				}

				html += '<figure class="elementor-image-box-img">' + imageHtml + '</figure>';
			}

			if ( hasContent ) {
				html += '<div class="elementor-image-box-content">';

				if ( ! settings.subtitle_text ) {
					if ( settings.title_text ) {
						var title_html = settings.title_text;

						if ( link ) {
							title_html = '<a href="' + settings.link.url + '">' + title_html + '</a>';
						}

						view.addRenderAttribute( 'title_text', 'class', 'elementor-image-box-title' );

						view.addInlineEditingAttributes( 'title_text', 'none' );

						html += '<' + settings.title_size  + ' ' + view.getRenderAttributeString( 'title_text' ) + '>' + title_html + '</' + settings.title_size  + '>';
					}
				}

				if ( settings.description_text ) {
					view.addRenderAttribute( 'description_text', 'class', 'elementor-image-box-description' );

					view.addInlineEditingAttributes( 'description_text' );

					html += '<p ' + view.getRenderAttributeString( 'description_text' ) + '>' + settings.description_text + '</p>';
				}

				if ( settings.subtitle_size && settings.subtitle_text ) {
					var title_html = settings.subtitle_text;

					view.addRenderAttribute( 'subtitle_text', 'class', 'elementor-image-box-title' );

					view.addInlineEditingAttributes( 'subtitle_text', 'none' );

					html += '<' + settings.subtitle_size  + ' ' + view.getRenderAttributeString( 'subtitle_text' ) + '>' + title_html + '</' + settings.subtitle_size  + '>';
				}

				html += '</div>';
			}

			if ( settings.show_eye ) {
					view.addRenderAttribute( 'show_eye', 'class', 'vamtam-eye' );

					if ( settings.eye_random_movement ) {
						view.addRenderAttribute( 'eye_random_movement_interval', 'data-eye-interval', settings.eye_random_movement_interval );
					}

					html += '<div ' + view.getRenderAttributeString( 'show_eye' ) + '><span class="outer"><span class="inner"><span class="eye"' + view.getRenderAttributeString( 'eye_random_movement_interval' ) + '></span></span></span></div>';
				}

			html += '</' + wrapperTag + '>';

			print( html );
			#>
			<?php
			}
		}

		// Replace current image-box widget with our extended version.
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets_manager->unregister( 'image-box' );
		$widgets_manager->register( new Vamtam_Widget_Image_Box );
	}
	add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
}
