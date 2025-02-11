<?php
namespace VamtamElementor\Widgets\Slides;

use \ElementorPro\Modules\Slides\Widgets\Slides as Elementor_Slides;

// Extending the Slides widget.

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--arrows-position-overlap' ) ) {
	function update_nav_section_controls( $controls_manager, $widget ) {
		// Arrows Position.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'arrows_position', [
			'options' => [
				'overlap' => __( 'Overlap', 'vamtam-elementor-integration' ),
			]
		] );
		\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'arrows_position', [
			'default' => 'overlap',
		] );
		// Arrows Size.
		\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'arrows_size', [
			'default' => [
				'size' => 50,
			],
		] );
		// Arrows Horizontal Offset.
		$widget->start_injection( [
			'of' => 'arrows_position',
		] );
		$widget->add_responsive_control(
			'arrows_hr_offset',
			[
				'label' => __( 'Arrows Horizontal Offset', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-swiper-button-prev' => 'left: -{{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-swiper-button-next' => 'right: -{{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
					'arrows_position' => 'overlap',
				],
			]
		);
		$widget->end_injection();
	}
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--arrows-color-tabs' ) ||
	current_theme_supports( 'vamtam-elementor-widgets', 'slides--caridad-slider-arrows' ) ) {
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
					'navigation' => [ 'arrows', 'both' ],
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
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);
		if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--caridad-slider-arrows' ) ) {
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
						'navigation' => [ 'arrows', 'both' ],
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
					'navigation' => [ 'arrows', 'both' ],
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
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);
		if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--caridad-slider-arrows' ) ) {
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
						'navigation' => [ 'arrows', 'both' ],
					],
				]
			);
		}
		if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--arrows-color-tabs__hover-anim' ) ) {			// Arrows Hover Animation.
			$widget->add_control(
				'arrows_hover_animation',
				[
					'label' => __( 'Arrows Hover Animation', 'vamtam-elementor-integration' ),
					'type' => $controls_manager::HOVER_ANIMATION,
				]
			);
		}
		$widget->end_controls_tab();
		$widget->end_controls_tabs();
		$widget->end_injection();
	}
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--arrows-responsive-size' ) ) {
	function update_nav_section_arrows_size_control( $controls_manager, $widget ) {
		// Arrows Size.
		$widget->start_injection( [
			'of' => 'arrows_position',
		] );
		$widget->remove_control( 'arrows_size' );
		$widget->add_responsive_control(
			'arrows_size',
			[
				'label' => __( 'Arrows Size', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 60,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-swiper-button' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
			]
		);
		$widget->end_injection();
	}
}

if ( vamtam_theme_supports( [ 'slides--caridad-slider-arrows', 'slides--fitness-slider-arrows' ] ) ) {
		function add_nav_section_controls( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'heading_style_arrows',
		] );
		$widget->add_control(
			'use_theme_arrows_style',
			[
				'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'theme-arrows-style',
				'condition' => [
					'navigation' => [ 'arrows', 'both' ],
				],
				'render_type' => 'template',
			]
		);
		if ( vamtam_theme_supports( 'slides--caridad-slider-arrows' ) ) {
			$widget->add_control(
				'nav_horizontal_align',
				[
					'label' => __( 'Horizontal Alignment', 'vamtam-elementor-integration' ),
					'type' => $controls_manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => __( 'Left', 'vamtam-elementor-integration' ),
							'icon' => 'eicon-text-align-left',
						],
						'right' => [
							'title' => __( 'Right', 'vamtam-elementor-integration' ),
							'icon' => 'eicon-text-align-right',
						],
					],
					'prefix_class' => 'vamtam-nav-align-',
					'condition' => [
						'navigation' => [ 'arrows', 'both' ],
						'arrows_position!' => 'overlap',
						'use_theme_arrows_style!' => '',
					],
				]
			);
			$widget->end_injection();
		}
	}
}

if ( vamtam_theme_supports( [ 'slides--arrows-position-overlap', 'slides--arrows-responsive-size', 'slides--arrows-color-tabs', 'slides--caridad-slider-arrows', 'slides--fitness-slider-arrows' ] ) ) {
	// Style - Navigation section.
	add_action( 'elementor/element/slides/section_style_navigation/before_section_end', function( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--arrows-color-tabs' ) ||
			current_theme_supports( 'vamtam-elementor-widgets', 'slides--caridad-slider-arrows' ) ) {
			update_nav_section_arrows_color_control( $controls_manager, $widget );
		}
		if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--arrows-responsive-size' ) ) {
			update_nav_section_arrows_size_control( $controls_manager, $widget );
		}
		if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--arrows-position-overlap' ) ) {
			update_nav_section_controls( $controls_manager, $widget );
		}
		if ( vamtam_theme_supports( [ 'slides--caridad-slider-arrows', 'slides--fitness-slider-arrows' ] ) ) {
			add_nav_section_controls( $controls_manager, $widget );
		}
	}, 10, 2 );
}

// Repeater control.
function update_slides_repeater_control( $controls_manager, $widget ) {
	$control_id      = 'slides';
	$field_to_update = 'link';
	$control_data    = $controls_manager->get_control_from_stack( $widget->get_unique_name(), $control_id );

	if ( is_wp_error( $control_data ) ) {
		return;
	}

	// We can access and modify the repeater fields as an array directly
	$control_data['fields'][ $field_to_update ]['dynamic'] = [
		'active' => true,
	];

	$widget->update_control( $control_id, $control_data );
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--force-slide-stretch' ) ) {
	function add_force_slide_strecth_control( $controls_manager, $widget ) {
		$widget->add_control(
			'force_slide_stretch',
			[
				'label' => __( 'Force Slide Stretch', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .swiper-slide-contents' => 'flex-basis: 100%;',
				],
				'description' => __( 'The heigth setting might need to be re-adjusted for the slide to show properly.', 'vamtam-elementor-integration' ),
			]
		);
	}
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--force-contain-slide' ) ) {
	function add_force_contain_slide_control( $controls_manager, $widget ) {
		$widget->add_control(
			'force_contain_slide',
			[
				'label' => __( 'Force Contain Slide', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .swiper-slide' => 'width: 100% !important;',
				],
				'description' => __( 'Forces slides widget never to exceed container\'s width.', 'vamtam-elementor-integration' ),
			]
		);
	}
}

// Content - Slides section.
add_action( 'elementor/element/slides/section_slides/before_section_end', function( $widget, $args ) {
	$controls_manager = \Elementor\Plugin::instance()->controls_manager;
	update_slides_repeater_control( $controls_manager, $widget );
	if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--force-slide-stretch' ) ) {
		add_force_slide_strecth_control( $controls_manager, $widget );
	}
	if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--force-contain-slide' ) ) {
		add_force_contain_slide_control( $controls_manager, $widget );
	}
}, 10, 2 );

// Making the dynamic tags work with slide's link field (step 1 of 2).
add_action( 'elementor/frontend/widget/before_render', function ( \Elementor\Element_Base $element ) {
	if ( 'slides' === $element->get_name() ) {
		$settings = $element->get_settings_for_display();

		foreach ( $settings['slides'] as $key => $value) {
			// Tag the link elements that we'll need to update.
			$shouldTag = ! empty( $value['__dynamic__']['link'] ) && strpos( $value['link']['url'], '%23elementor-action') === 0;
			if ( $shouldTag ) {
				$element->add_render_attribute( "slide_link{$key}", 'href', "#vt-{$key}#", true );
			}
		}
	}
} );

// Making the dynamic tags work with slide's link field (step 2 of 2).
add_filter( 'elementor/widget/render_content', function( $content, $widget ) {
	if ( 'slides' === $widget->get_name() ) {
		$settings = $widget->get_settings_for_display();

		foreach ( $settings['slides'] as $key => $value) {
			// Replace the href attr of the tagged link elements with the proper action string.
			if ( ! empty( $value['__dynamic__']['link'] ) ) {
				$content = preg_replace(
					'/#vt-' . $key . '#[^"]*/',
					$value['link']['url'],
					$content
				);
			}
		}
	}

	return $content;
}, 10, 2 );

// Vamtam_Widget_Slides.
function widgets_registered() {

	if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
		return;
	}

	if ( ! class_exists( '\ElementorPro\Modules\Slides\Widgets\Slides' ) ) {
		return; // Elementor's autoloader acts weird sometimes.
	}

	class Vamtam_Widget_Slides extends Elementor_Slides {
		public $extra_depended_scripts = [
			'vamtam-slides',
		];

		public function get_script_depends() {
			return [
				'imagesloaded',
				'vamtam-slides',
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
				'vamtam-slides',
				VAMTAM_ELEMENTOR_INT_URL . 'assets/js/widgets/slides/vamtam-slides' . $suffix . '.js',
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

		// Override.
		protected function render() {
			$settings = $this->get_settings_for_display();

			if ( empty( $settings['slides'] ) ) {
				return;
			}

			$title_tag = \Elementor\Utils::validate_html_tag( $settings['slides_title_tag'] );
			$description_tag = \Elementor\Utils::validate_html_tag( $settings['slides_description_tag'] );

			$this->add_render_attribute( 'button', 'class', [ 'elementor-button', 'elementor-slide-button' ] );

			if ( ! empty( $settings['button_size'] ) ) {
				$this->add_render_attribute( 'button', 'class', 'elementor-size-' . $settings['button_size'] );
			}

			$slides = [];
			$slide_count = 0;

			foreach ( $settings['slides'] as $slide ) {
				$slide_html = '';
				$btn_attributes = '';
				$slide_attributes = '';
				$slide_element = 'div';
				$btn_element = 'div';

				if ( ! empty( $slide['link']['url'] ) ) {
					$this->add_link_attributes( 'slide_link' . $slide_count, $slide['link'] );

					if ( 'button' === $slide['link_click'] ) {
						$btn_element = 'a';
						$btn_attributes = $this->get_render_attribute_string( 'slide_link' . $slide_count );
					} else {
						$slide_element = 'a';
						$slide_attributes = $this->get_render_attribute_string( 'slide_link' . $slide_count );
					}
				}

				$slide_html .= '<' . $slide_element . ' class="swiper-slide-inner" ' . $slide_attributes . '>';

				$slide_html .= '<div class="swiper-slide-contents">';

				if ( $slide['heading'] ) {
					$slide_html .= '<' . $title_tag . ' class="elementor-slide-heading">' . $slide['heading'] . '</' . $title_tag . '>';
				}

				if ( $slide['description'] ) {
					$slide_html .= '<' . $description_tag . ' class="elementor-slide-description">' . $slide['description'] . '</' . $description_tag . '>';
				}

				if ( $slide['button_text'] ) {
					$slide_html .= '<' . $btn_element . ' ' . $btn_attributes . ' ' . $this->get_render_attribute_string( 'button' ) . '>' . $slide['button_text'] . '</' . $btn_element . '>';
				}

				$slide_html .= '</div></' . $slide_element . '>';

				if ( 'yes' === $slide['background_overlay'] ) {
					$slide_html = '<div class="elementor-background-overlay"></div>' . $slide_html;
				}

				$ken_class = '';

				if ( $slide['background_ken_burns'] ) {
					$ken_class = ' elementor-ken-burns elementor-ken-burns--' . $slide['zoom_direction'];
				}

				$slide_html = '<div class="swiper-slide-bg' . esc_attr( $ken_class ) . '" role="img"></div>' . $slide_html;

				$slides[] = '<div class="elementor-repeater-item-' . esc_attr( $slide['_id'] ) . ' swiper-slide">' . $slide_html . '</div>';
				$slide_count++;
			}

			$prev = 'left';
			$next = 'right';
			$direction = is_rtl() ? 'rtl' : 'ltr';

			if ( is_rtl() ) {
				$prev = 'right';
				$next = 'left';
			}

			$prev_arrow_class = "eicon-chevron-{$prev}";
			$next_arrow_class = "eicon-chevron-{$next}";
			if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--bijoux-slider-arrows' ) ) {
				// Use different slider arrows.
				$prev_arrow_class = "vamtamtheme- vamtam-theme-arrow-{$prev}";
				$next_arrow_class = "vamtamtheme- vamtam-theme-arrow-{$next}";
			}
			if ( ! empty( $settings['use_theme_arrows_style'] ) && current_theme_supports( 'vamtam-elementor-widgets', 'slides--caridad-slider-arrows' ) ) {
				// Use different slider arrows.
				$prev_arrow_class = "vamtamtheme- vamtam-theme-arrow-slide-{$prev}";
				$next_arrow_class = "vamtamtheme- vamtam-theme-arrow-slide-{$next}";
			}
			if ( vamtam_theme_supports( 'slides--fitness-slider-arrows' ) ) {
				// Use different slider arrows.
				$prev_arrow_class = "vamtamtheme- vamtam-theme-arrow-{$prev}";
				$next_arrow_class = "vamtamtheme- vamtam-theme-arrow-{$next}";
			}


			if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--arrows-color-tabs__hover-anim' ) ) {
				$prev_arrow_classes = $prev_arrow_class;
				if ( ! empty( $settings['arrows_hover_animation'] ) ) {
					$this->add_render_attribute( 'prev_arrow_classes', 'class', $prev_arrow_classes . " elementor-animation-{$settings[ 'arrows_hover_animation' ]}" );
				} else {
					$this->add_render_attribute( 'prev_arrow_classes', 'class', $prev_arrow_classes );
				}
				$next_arrow_classes = $next_arrow_class;
				if ( ! empty( $settings['arrows_hover_animation'] ) ) {
					$this->add_render_attribute( 'next_arrow_classes', 'class', $next_arrow_classes . " elementor-animation-{$settings[ 'arrows_hover_animation' ]}" );
				} else {
					$this->add_render_attribute( 'next_arrow_classes', 'class', $next_arrow_classes );
				}
			}

			$show_dots = ( in_array( $settings['navigation'], [ 'dots', 'both' ] ) );
			$show_arrows = ( in_array( $settings['navigation'], [ 'arrows', 'both' ] ) );

			$slides_count = count( $settings['slides'] );
			$swiper_class = \ElementorPro\Plugin::elementor()->experiments->is_feature_active( 'e_swiper_latest' ) ? 'swiper' : 'swiper-container';
			?>
			<div class="elementor-swiper">
				<div class="elementor-slides-wrapper elementor-main-swiper <?php echo esc_attr( $swiper_class ); ?>" dir="<?php \Elementor\Utils::print_unescaped_internal_string( $direction ); ?>" data-animation="<?php echo esc_attr( $settings['content_animation'] ); ?>">
					<div class="swiper-wrapper elementor-slides">
						<?php // PHPCS - Slides for each is safe. ?>
						<?php echo implode( '', $slides ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<?php if ( 1 < $slides_count ) : ?>
						<?php if ( $show_dots ) : ?>
							<div class="swiper-pagination"></div>
						<?php endif; ?>
						<?php if ( $show_arrows ) : ?>
							<div class="elementor-swiper-button elementor-swiper-button-prev" role="button" tabindex="0">
								<?php if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--arrows-color-tabs__hover-anim' ) ) : ?>
									<i <?php echo $this->get_render_attribute_string( 'prev_arrow_classes' ); ?> aria-hidden="true"></i>
								<?php else : ?>
									<i class="<?php echo $prev_arrow_class; ?>" aria-hidden="true"></i>
								<?php endif; ?>
								<span class="elementor-screen-only"><?php echo esc_html__( 'Previous', 'vamtam-elementor-integration' ); ?></span>
							</div>
							<div class="elementor-swiper-button elementor-swiper-button-next <?php echo esc_attr( $this->get_render_attribute_string( 'arrows_anim' ) ); ?>" role="button" tabindex="0">
								<?php if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--arrows-color-tabs__hover-anim' ) ) : ?>
									<i <?php echo $this->get_render_attribute_string( 'next_arrow_classes' ); ?> aria-hidden="true"></i>
								<?php else : ?>
									<i class="<?php echo $next_arrow_class; ?>" aria-hidden="true"></i>
								<?php endif; ?>
								<span class="elementor-screen-only"><?php echo esc_html__( 'Next', 'vamtam-elementor-integration' ); ?></span>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
			<?php
		}

		// Override.
		protected function content_template() {
			?>
			<#
				var direction        = elementorFrontend.config.is_rtl ? 'rtl' : 'ltr',
					next             = elementorFrontend.config.is_rtl ? 'left' : 'right',
					prev             = elementorFrontend.config.is_rtl ? 'right' : 'left',
					navi             = settings.navigation,
					showDots         = ( 'dots' === navi || 'both' === navi ),
					showArrows       = ( 'arrows' === navi || 'both' === navi ),
					buttonSize       = settings.button_size,
					titleTag         = elementor.helpers.validateHTMLTag( settings.slides_title_tag ),
					descriptionTag   = elementor.helpers.validateHTMLTag( settings.slides_description_tag ),
					prev_arrow_class = 'eicon-chevron-' + prev,
					next_arrow_class = 'eicon-chevron-' + next;
			#>
			<?php
				if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--bijoux-slider-arrows' ) ) {
					// Use different slider arrows.
					?>
					<#
						prev_arrow_class = 'vamtamtheme- vamtam-theme-arrow-' + prev;
						next_arrow_class = 'vamtamtheme- vamtam-theme-arrow-' + next;
					#>
					<?php
				}
				if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--caridad-slider-arrows' ) ) {
					// Use different slider arrows.
					?>
					<#
						if ( settings.use_theme_arrows_style ) {
							prev_arrow_class = 'vamtamtheme- vamtam-theme-arrow-slide-' + prev;
							next_arrow_class = 'vamtamtheme- vamtam-theme-arrow-slide-' + next;
						}
					#>
					<?php
				}
				if ( vamtam_theme_supports( 'slides--fitness-slider-arrows' ) ) {
					// Use different slider arrows.
					?>
					<#
						if ( settings.use_theme_arrows_style ) {
							prev_arrow_class = 'vamtamtheme- vamtam-theme-arrow-' + prev;
							next_arrow_class = 'vamtamtheme- vamtam-theme-arrow-' + next;
						}
					#>
					<?php
				}
				if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--arrows-color-tabs__hover-anim' ) ) {
					?>
					<#
						var prev_arrow_classes = prev_arrow_class;
						if ( settings.arrows_hover_animation ) {
							view.addRenderAttribute( 'prev_arrow_classes', 'class', prev_arrow_classes + ' elementor-animation-' + settings.arrows_hover_animation );
						} else {
							view.addRenderAttribute( 'prev_arrow_classes', 'class', prev_arrow_classes );
						}
						var next_arrow_classes = next_arrow_class;
						if ( settings.arrows_hover_animation ) {
							view.addRenderAttribute( 'next_arrow_classes', 'class', next_arrow_classes + ' elementor-animation-' + settings.arrows_hover_animation );
						} else {
							view.addRenderAttribute( 'next_arrow_classes', 'class', next_arrow_classes );
						}
					#>
				<?php
			}
			?>
			<div class="elementor-swiper">
				<div class="elementor-slides-wrapper elementor-main-swiper {{ elementorFrontend.config.swiperClass }}" dir="{{ direction }}" data-animation="{{ settings.content_animation }}">
					<div class="swiper-wrapper elementor-slides">
						<# jQuery.each( settings.slides, function( index, slide ) { #>
							<div class="elementor-repeater-item-{{ _.escape( slide._id ) }} swiper-slide">
								<#
								var kenClass = '';

								if ( '' != slide.background_ken_burns ) {
									kenClass = ' elementor-ken-burns elementor-ken-burns--' + _.escape( slide.zoom_direction );
								}
								#>
								<div class="swiper-slide-bg{{ kenClass }}" role="img"></div>
								<# if ( 'yes' === slide.background_overlay ) { #>
								<div class="elementor-background-overlay"></div>
								<# } #>
								<div class="swiper-slide-inner">
									<div class="swiper-slide-contents">
										<# if ( slide.heading ) { #>
											<{{ titleTag }} class="elementor-slide-heading">{{{ slide.heading }}}</{{ titleTag }}>
										<# }
										if ( slide.description ) { #>
											<{{descriptionTag}} class="elementor-slide-description">{{{ slide.description }}}</{{descriptionTag}}>
										<# }
										if ( slide.button_text ) { #>
											<div class="elementor-button elementor-slide-button elementor-size-{{ buttonSize }}">{{{ slide.button_text }}}</div>
										<# } #>
									</div>
								</div>
							</div>
						<# } ); #>
					</div>
					<# if ( 1 < settings.slides.length ) { #>
						<# if ( showDots ) { #>
							<div class="swiper-pagination"></div>
						<# } #>
						<# if ( showArrows ) { #>
							<div class="elementor-swiper-button elementor-swiper-button-prev" role="button" tabindex="0">
								<?php if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--arrows-color-tabs__hover-anim' ) ) : ?>
									<i {{{ view.getRenderAttributeString( 'prev_arrow_classes' ) }}} aria-hidden="true"></i>
								<?php else: ?>
									<i class="{{ prev_arrow_class }}" aria-hidden="true"></i>
								<?php endif; ?>
								<span class="elementor-screen-only"><?php echo esc_html__( 'Previous', 'vamtam-elementor-integration' ); ?></span>
							</div>
							<div class="elementor-swiper-button elementor-swiper-button-next" role="button" tabindex="0">
								<?php if ( current_theme_supports( 'vamtam-elementor-widgets', 'slides--arrows-color-tabs__hover-anim' ) ) : ?>
									<i {{{ view.getRenderAttributeString( 'next_arrow_classes' ) }}} aria-hidden="true"></i>
								<?php else: ?>
									<i class="{{ next_arrow_class }}" aria-hidden="true"></i>
								<?php endif; ?>
								<span class="elementor-screen-only"><?php echo esc_html__( 'Next', 'vamtam-elementor-integration' ); ?></span>
							</div>
						<# } #>
					<# } #>
				</div>
			</div>
			<?php
		}
	}

	// Replace current slides widget with our extended version.
	$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
	$widgets_manager->unregister( 'slides' );
	$widgets_manager->register( new Vamtam_Widget_Slides );
}
add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
