<?php
namespace VamtamElementor\Widgets\Posts;

// Extending the Posts widget.

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

if ( vamtam_theme_supports( [ 'posts--thumb-shape', 'posts.classic--estudiar-read-more-style' ] ) ) {
	// Called frontend & editor (editor after element loses focus).
	function render_content( $content, $widget ) {
		if ( 'posts' === $widget->get_name() ) {
			$settings = $widget->get_settings();

			if ( vamtam_theme_supports( 'posts--thumb-shape' ) ) {
				$shape = apply_filters( 'vamtam_posts_widget_thumb_shape', '<div class="vamtam-shape"></div>' );
				// Inject thumb shape container.
				$content = str_replace(
					'elementor-post__thumbnail">',
					'elementor-post__thumbnail">' . $shape,
					$content
				);
			}

			if ( vamtam_theme_supports( 'posts.classic--estudiar-read-more-style' ) ) {
				if ( $settings['_skin'] === 'classic' && ! empty( $settings['use_theme_read_more_style'] ) ) {
					$icon = '<i aria-hidden="true" class="vamtamtheme- vamtam-theme-arrow-right"></i>';
					// Inject theme icon.
					$content = preg_replace('/(<a[^>]+class="elementor-post__read-more"[^>]*>)/s', '$1' . $icon, $content );
				}
			}
		}

		return $content;
	}
	add_filter( 'elementor/widget/render_content', __NAMESPACE__ . '\render_content', 10, 2 );
}

if ( vamtam_theme_supports( 'posts.classic--responsive-box-padding' ) ) {
	function update_box_padding_control( $controls_manager, $widget ) {
		$widget->remove_control('classic_box_padding');

		$widget->start_injection( [
			'of' => 'classic_box_border_radius',
		] );

		$widget->add_responsive_control(
			'classic_box_padding',
			[
				'label' => __( 'Padding', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition' => [
					'_skin' => 'classic',
				]
			]
		);

		$widget->end_injection();
	}
}

if ( vamtam_theme_supports( 'posts--caridad-read-more' ) ) {
	function add_read_more_controls_for_skin( $controls_manager, $widget, $skin ) {
		$widget->start_injection( [
			'of' => "{$skin}_heading_readmore_style",
		] );
		$widget->add_control(
			"{$skin}_use_theme_read_more_style",
			[
				'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'theme-read-more-style',
				'default' => 'theme-read-more-style',
				'condition' => [
					'_skin' => $skin,
					"{$skin}_show_read_more" => 'yes',
				]
			]
		);
		$widget->end_injection();
	}
	// Style - Content Section (Cards Layout).
	function section_cards_design_content_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_read_more_controls_for_skin( $controls_manager, $widget, 'cards' );
	}
	add_action( 'elementor/element/posts/cards_section_design_content/before_section_end', __NAMESPACE__ . '\section_cards_design_content_before_section_end', 10, 2 );
	// Style - Content Section (Classic Layout).
	function section_classic_design_content_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_read_more_controls_for_skin( $controls_manager, $widget, 'classic' );
	}
	add_action( 'elementor/element/posts/classic_section_design_content/before_section_end', __NAMESPACE__ . '\section_classic_design_content_before_section_end', 10, 2 );
}

if ( vamtam_theme_supports( 'posts.classic--estudiar-read-more-style' ) ) {
	function add_theme_read_more_style_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'classic_show_read_more',
		] );
		$widget->add_control(
			'use_theme_read_more_style',
			[
				'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'theme-read-more-style',
				'condition' => [
					'classic_show_read_more!' => '',
					'_skin' => 'classic',
				],
				'render_type' => 'template',
			]
		);
		$widget->end_injection();
	}

	// Content - Layout Section (Classic Layout).
	function section_design_layout_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_theme_read_more_style_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/posts/classic_section_design_layout/before_section_end', __NAMESPACE__ . '\section_design_layout_before_section_end', 10, 2 );
}

if ( vamtam_theme_supports( 'posts.classic--content-hover-color' ) ) {
	function add_content_hover_color_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'classic_box_border_color_hover',
		] );
		$widget->add_control(
			'classic_content_hover_color',
			[
				'label' => __( 'Content Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'selectors' => [
					// Title.
					'{{WRAPPER}} .elementor-post:hover .elementor-post__title, {{WRAPPER}} .elementor-post:hover .elementor-post__title a' => 'color: {{VALUE}};',
					// Meta.
					'{{WRAPPER}} .elementor-post:hover .elementor-post__meta-data' => 'color: {{VALUE}};',
					// Excerpt.	
					'{{WRAPPER}} .elementor-post:hover .elementor-post__excerpt p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'_skin' => 'classic',
				]
			]
		);
		$widget->end_injection();
	}
}

if ( vamtam_theme_supports( [ 'posts.classic--responsive-box-padding', 'posts.classic--content-hover-color' ] ) ) {
	// Style - Box Section (Classic Layout).
	function section_design_box_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		if ( vamtam_theme_supports( 'posts.classic--responsive-box-padding' ) ) {
			update_box_padding_control( $controls_manager, $widget );
		}
		if ( vamtam_theme_supports( 'posts.classic--content-hover-color' ) ) {
			add_content_hover_color_control( $controls_manager, $widget );
		}
	}
	add_action( 'elementor/element/posts/classic_section_design_box/before_section_end', __NAMESPACE__ . '\section_design_box_before_section_end', 10, 2 );
}