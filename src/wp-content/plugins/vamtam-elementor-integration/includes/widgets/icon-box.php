<?php
namespace VamtamElementor\Widgets\IconBox;

use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;


// Extending the Icon Box widget.

// Called frontend & editor (editor after element loses focus).
function render_content( $content, $widget ) {
	if ( 'icon-box' === $widget->get_name() ) {
		$settings = $widget->get_settings();

		if ( ! empty( $settings['link']['url'] ) ) {
			// Add vamtam-is-link class when the url option is set.
			$content = str_replace( 'elementor-icon-box-wrapper"', 'elementor-icon-box-wrapper vamtam-is-link"', $content );
		}
	}

	return $content;
}
add_filter( 'elementor/widget/render_content', __NAMESPACE__ . '\render_content', 10, 2 );

if ( current_theme_supports( 'vamtam-elementor-widgets', 'icon-box--amorph-shape' ) ) {
	function update_shape_control( $controls_manager, $widget ) {
		// Shape.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'shape', [
			'options' => [
				'vamtam-amorph' => __( 'Vamtam Amorph', 'vamtam-elementor-integration' ),
			],
		] );
	}
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'icon-box--box-is-link' ) ) {
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
					'{{WRAPPER}} .elementor-icon-box-title' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
			]
		);
		// Title Typography.
		\Vamtam_Elementor_Utils::remove_group_control( $controls_manager, $widget, 'title_typography', Group_Control_Typography::get_type() );

		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .elementor-icon-box-title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
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
					'{{WRAPPER}} .elementor-icon-box-title:hover' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
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
				'selector' => '{{WRAPPER}} .elementor-icon-box-title:hover',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
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
					'{{WRAPPER}} .elementor-icon-box-description' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);
		// Description Typography.
		\Vamtam_Elementor_Utils::remove_group_control( $controls_manager, $widget, 'description_typography', Group_Control_Typography::get_type() );
		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .elementor-icon-box-description',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
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
					'{{WRAPPER}} .elementor-icon-box-description:hover' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
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
				'selector' => '{{WRAPPER}} .elementor-icon-box-description:hover',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'condition' => [
					'box_is_link!' => '',
				],
			]
		);
		$widget->end_controls_tab();
		$widget->end_controls_tabs();
		$widget->end_injection();
	}

	// Style - Content section
	function section_style_content_before_section_end ( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_style_controls_for_box_is_link( $controls_manager, $widget );
	}
	add_action( 'elementor/element/icon-box/section_style_content/before_section_end', __NAMESPACE__ . '\section_style_content_before_section_end', 10, 2 );
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'icon-box--amorph-shape' ) ||
	current_theme_supports( 'vamtam-elementor-widgets', 'icon-box--box-is-link' ) ) {
	// Content - Icon section
	function section_icon_before_section_end ( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		if ( current_theme_supports( 'vamtam-elementor-widgets', 'icon-box--box-is-link' ) ) {
			add_box_is_link_control( $controls_manager, $widget );
		}

		if ( current_theme_supports( 'vamtam-elementor-widgets', 'icon-box--amorph-shape' ) ) {
			update_shape_control( $controls_manager, $widget );
		}
	}
	add_action( 'elementor/element/icon-box/section_icon/before_section_end', __NAMESPACE__ . '\section_icon_before_section_end', 10, 2 );
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'icon-box--box-is-link' ) ) {
	// Vamtam_Widget_Icon_Box.
	function widgets_registered() {
		class Vamtam_Widget_Icon_Box extends \Elementor\Widget_Icon_Box {

			// Extend render method.
			protected function render() {
				$settings = $this->get_settings_for_display();

				$this->add_render_attribute( 'icon', 'class', [ 'elementor-icon', 'elementor-animation-' . $settings['hover_animation'] ] );

				$wrapper_tag             = 'div';
				$icon_tag                = 'span';
				$wrapper_link_attributes = '';
				$link_attributes         = '';
				$box_is_link             = isset( $settings['box_is_link'] ) && ! empty( $settings['box_is_link'] );

				if ( ! isset( $settings['icon'] ) && ! \Elementor\Icons_Manager::is_migration_allowed() ) {
					// add old default
					$settings['icon'] = 'fa fa-star';
				}

				$has_icon = ! empty( $settings['icon'] );

				if ( ! empty( $settings['link']['url'] ) ) {
					if ( $box_is_link ) {
						$wrapper_tag = 'a';
						$this->add_link_attributes( 'wrapper-link', $settings['link'] );
						$wrapper_link_attributes = $this->get_render_attribute_string( 'wrapper-link' );
					} else {
						$icon_tag = 'a';
						$this->add_link_attributes( 'link', $settings['link'] );
						$link_attributes = $this->get_render_attribute_string( 'link' );
					}
				}

				if ( $has_icon ) {
					$this->add_render_attribute( 'i', 'class', $settings['icon'] );
					$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
				}

				$icon_attributes = $this->get_render_attribute_string( 'icon' );

				$this->add_render_attribute( 'description_text', 'class', 'elementor-icon-box-description' );

				$this->add_inline_editing_attributes( 'title_text', 'none' );
				$this->add_inline_editing_attributes( 'description_text' );
				if ( ! $has_icon && ! empty( $settings['selected_icon']['value'] ) ) {
					$has_icon = true;
				}
				$migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
				$is_new = ! isset( $settings['icon'] ) && \Elementor\Icons_Manager::is_migration_allowed();
				?>
				<<?php echo $wrapper_tag . ' ' . $wrapper_link_attributes; ?> class="elementor-icon-box-wrapper">
					<?php if ( $has_icon ) : ?>
					<div class="elementor-icon-box-icon">
						<<?php echo implode( ' ', [ $icon_tag, $icon_attributes, $link_attributes ] ); ?>>
						<?php
						if ( $is_new || $migrated ) {
							\Elementor\Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
						} elseif ( ! empty( $settings['icon'] ) ) {
							?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
						}
						?>
						</<?php echo $icon_tag; ?>>
					</div>
					<?php endif; ?>
					<div class="elementor-icon-box-content">
						<<?php echo $settings['title_size']; ?> class="elementor-icon-box-title">
							<<?php echo implode( ' ', [ $icon_tag, $link_attributes ] ); ?><?php echo $this->get_render_attribute_string( 'title_text' ); ?>><?php echo $settings['title_text']; ?></<?php echo $icon_tag; ?>>
						</<?php echo $settings['title_size']; ?>>
						<?php if ( ! \Elementor\Utils::is_empty( $settings['description_text'] ) ) : ?>
						<p <?php echo $this->get_render_attribute_string( 'description_text' ); ?>><?php echo $settings['description_text']; ?></p>
						<?php endif; ?>
					</div>
				</<?php echo $wrapper_tag; ?>>
				<?php
			}

			// Extend content_template method.
			protected function content_template() {
				?>
				<#
				var	link        = ( settings.link.url && ! settings.box_is_link ) ? 'href="' + settings.link.url + '"' : '',
					wrapperLink = ( settings.link.url && settings.box_is_link ) ? 'href="' + settings.link.url + '"' : '',
					iconTag     = ( link && ! settings.box_is_link ) ? 'a' : 'span',
					wrapperTag  = wrapperLink ? 'a' : 'div',
					iconHTML    = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
					migrated    = elementor.helpers.isIconMigrated( settings, 'selected_icon' );

				view.addRenderAttribute( 'description_text', 'class', 'elementor-icon-box-description' );

				view.addInlineEditingAttributes( 'title_text', 'none' );
				view.addInlineEditingAttributes( 'description_text' );
				#>
				<{{{ wrapperTag + ' ' + wrapperLink }}} class="elementor-icon-box-wrapper">
					<# if ( settings.icon || settings.selected_icon ) { #>
					<div class="elementor-icon-box-icon">
						<{{{ iconTag + ' ' + link }}} class="elementor-icon elementor-animation-{{ settings.hover_animation }}">
							<# if ( iconHTML && iconHTML.rendered && ( ! settings.icon || migrated ) ) { #>
								{{{ iconHTML.value }}}
								<# } else { #>
									<i class="{{ settings.icon }}" aria-hidden="true"></i>
								<# } #>
						</{{{ iconTag }}}>
					</div>
					<# } #>
					<div class="elementor-icon-box-content">
						<{{{ settings.title_size }}} class="elementor-icon-box-title">
							<{{{ iconTag + ' ' + link }}} {{{ view.getRenderAttributeString( 'title_text' ) }}}>{{{ settings.title_text }}}</{{{ iconTag }}}>
						</{{{ settings.title_size }}}>
						<# if ( settings.description_text ) { #>
						<p {{{ view.getRenderAttributeString( 'description_text' ) }}}>{{{ settings.description_text }}}</p>
						<# } #>
					</div>
				</{{{ wrapperTag }}}>
				<?php
			}
		}

		// Replace current icon-box widget with our extended version.
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets_manager->unregister( 'icon-box' );
		$widgets_manager->register( new Vamtam_Widget_Icon_Box );
	}
	add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
}
