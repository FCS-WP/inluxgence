<?php
namespace VamtamElementor\Widgets\CallToAction;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Utils;
use ElementorPro\Modules\CallToAction\Widgets\Call_To_Action as Elementor_CTA;

// Extending the Call To Action widget.

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

if ( vamtam_theme_supports( [ 'call-to-action--estudiar-cta-style', 'call-to-action--fitness-cta-style' ] ) ) {
	function render_content( $content, $widget ) {
		if ( 'call-to-action' === $widget->get_name() ) {
			$settings = $widget->get_settings();
			if ( ! empty( $settings['use_theme_cta_style'] ) ) {
				$icon = '<i aria-hidden="true" class="vamtamtheme- vamtam-theme-arrow-right"></i>';
				// Inject theme icon.
				$content = preg_replace( '/(<div class="elementor-cta__button-wrapper elementor-cta__content-item elementor-content-item[^>]*>[^~]*?)(<\/div>)/s', '$1' . $icon . '$2', $content );
			}
		}

		return $content;
	}
	add_filter( 'elementor/widget/render_content', __NAMESPACE__ . '\render_content', 10, 2 );

	function use_theme_cta_style_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'min-height',
			'at' => 'before',
		] );
		$widget->add_control(
			'use_theme_cta_style',
			[
				'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'theme-cta-style',
				'default' => 'theme-cta-style',
				'render_type' => 'template',
			]
		);
		$widget->end_injection();
	}
	function update_alignment_control( $controls_manager, $widget ) {
		// Alignment.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'alignment', [
			'prefix_class' => 'vamtam-elementor-cta-align-',
		] );
	}
	// Style - Box Section
	function box_style_before_section_end( $widget ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		use_theme_cta_style_control( $controls_manager, $widget );
		update_alignment_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/call-to-action/box_style/before_section_end', __NAMESPACE__ . '\box_style_before_section_end' );

	function update_button_color_controls( $controls_manager, $widget ) {
		// Button Color.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_color', [
			'selectors' => [
				'{{WRAPPER}} .elementor-cta__button ~ i.vamtamtheme-' => 'color: {{VALUE}};',
			],
		] );
		// Button Hover Color.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'button_color_hover', [
			'selectors' => [
				'{{WRAPPER}} .elementor-cta:hover .elementor-cta__button ~ i.vamtamtheme-' => 'color: {{VALUE}};',
			],
		] );
	}
	// Style - Content Section
	function section_content_style_before_section_end( $widget ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		update_button_color_controls( $controls_manager, $widget );
	}
	add_action( 'elementor/element/call-to-action/section_content_style/before_section_end', __NAMESPACE__ . '\section_content_style_before_section_end' );
}

if ( vamtam_theme_supports( 'call-to-action--graphic-element-hover-color' ) ) {
	function add_icon_hover_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'icon_primary_color',
		] );
		$widget->add_control(
			'icon_hover_color',
			[
				'label' => __( 'Hover Color', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-cta:hover .elementor-view-stacked .elementor-icon' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .elementor-cta:hover .elementor-view-stacked .elementor-icon svg' => 'stroke: {{VALUE}}',
					'{{WRAPPER}} .elementor-cta:hover .elementor-view-framed .elementor-icon, {{WRAPPER}} .elementor-cta:hover .elementor-view-default .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}}',
					'{{WRAPPER}} .elementor-cta:hover .elementor-view-framed .elementor-icon, {{WRAPPER}} .elementor-cta:hover .elementor-view-default .elementor-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'graphic_element' => 'icon',
				],
			]
		);
		$widget->end_injection();
	}

	// Style - Graphic Element Section
	function graphic_element_style_before_section_end( $widget ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_icon_hover_control( $controls_manager, $widget );
	}
	add_action( 'elementor/element/call-to-action/graphic_element_style/before_section_end', __NAMESPACE__ . '\graphic_element_style_before_section_end' );
}

if ( vamtam_theme_supports( 'call-to-action--team-member-skin' ) ) {
	// Team Member Skin
	function vamtam_add_team_member_cta_skin( $widget ) {
		if ( did_action( 'vamtam_elementor/call-to-action/team-member/skin_registered' ) ) {
			// This is in case another theme feature requires to re-register (extend) the widget.
			// In that case the widget's action's will be added twice leading to control stack issues.
			return;
		}

		class Vamtam_CTA_Team_Member_Skin extends \Elementor\Skin_Base {
			public function get_id() {
				return 'team-member';
			}

			public function get_title() {
				return __( 'Team Member', 'vamtam-elementor-integration' );
			}

			protected function render_social_links() {
				$widget = $this->parent;
				$skin   = $this;

				$settings = $widget->get_settings_for_display();
				$i = 1;

				$fallback_defaults = [
					'fa fa-facebook',
					'fa fa-twitter',
					'fa fa-google-plus',
				];

				$migration_allowed = Icons_Manager::is_migration_allowed();

				// add old default
				if ( ! isset( $item['icon'] ) && ! $migration_allowed ) {
					$item['icon'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-check';
				}

				$migrated = isset( $item['__fa4_migrated']['select_social_icon'] );
				$is_new = ! isset( $item['icon'] ) && $migration_allowed;
				?>
				<div class="vamtam-tm-social-links-wrap">
					<ul class="vamtam-tm-social-links">
						<?php foreach ( $settings['team_member_social'] as $index => $item ) : ?>
							<?php
							$migrated = isset( $item['__fa4_migrated']['select_social_icon'] );
							$is_new = empty( $item['social_icon'] ) && $migration_allowed;
							$social = '';

							// add old default
							if ( empty( $item['social_icon'] ) && ! $migration_allowed ) {
								$item['social_icon'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-wordpress';
							}

							if ( ! empty( $item['social_icon'] ) ) {
								$social = str_replace( 'fa fa-', '', $item['social_icon'] );
							}

							if ( ( $is_new || $migrated ) && 'svg' !== $item['select_social_icon']['library'] ) {
								$social = explode( ' ', $item['select_social_icon']['value'], 2 );
								if ( empty( $social[1] ) ) {
									$social = '';
								} else {
									$social = str_replace( 'fa-', '', $social[1] );
								}
							}
							if ( 'svg' === $item['select_social_icon']['library'] ) {
								$social = '';
							}

							$widget->add_render_attribute( 'social-link', 'class', 'vamtam-tm-social-link' );
							$social_link_key = 'social-link' . $i;
							if ( ! empty( $item['social_link']['url'] ) ) {
								$widget->add_link_attributes( $social_link_key, $item['social_link'] );
							}
							?>
							<li>
								<?php
									//if ( $item['social_icon'] ) : ?>
										<a <?php echo $widget->get_render_attribute_string( $social_link_key ); ?>>
											<span class="vamtam-tm-social-icon-wrap">
												<span class="elementor-screen-only"><?php echo ucwords( $social ); ?></span>
												<span class="vamtam-tm-social-icon vamtam-icon">
												<?php
												if ( $is_new || $migrated ) {
													Icons_Manager::render_icon( $item['select_social_icon'], [ 'aria-hidden' => 'true' ] );
												} else { ?>
													<i class="<?php echo esc_attr( $item['social_icon'] ); ?>"></i>
												<?php } ?>
												</span>
											</span>
										</a>
									<?php //endif;
									?>
							</li>
							<?php $i++;
						endforeach; ?>
					</ul>
				</div>
				<?php
			}

			// Skin render().
			public function render() {
				$widget               = $this->parent;
				$skin                 = $this;
				$settings             = $widget->get_settings_for_display();
				$title_tag            = $settings['title_tag'];
				$wrapper_tag          = 'div';
				$button_tag           = 'a';
				$bg_image             = '';
				$content_animation    = $settings['content_animation'];
				$animation_class      = '';
				$print_bg             = true;
				$print_content        = true;
				$box_is_link          = 'box' === $settings['link_click'];
				$has_social_links     = 'yes' === $settings['member_social_links'];
				$has_theme_si_style   = ! empty( $settings['use_theme_cta_social_icons_style'] );
				$needs_separate_links = $box_is_link && $has_social_links && $has_theme_si_style && vamtam_theme_supports( 'call-to-action--fitness-cta-style' );

				if ( ! empty( $settings['bg_image']['id'] ) ) {
					$bg_image = Group_Control_Image_Size::get_attachment_image_src( $settings['bg_image']['id'], 'bg_image', $settings );
				} elseif ( ! empty( $settings['bg_image']['url'] ) ) {
					$bg_image = $settings['bg_image']['url'];
				}

				if ( empty( $bg_image ) && 'classic' == $settings['skin'] ) {
					$print_bg = false;
				}

				if ( empty( $settings['title'] ) && empty( $settings['description'] ) && empty( $settings['button'] ) && 'none' == $settings['graphic_element'] ) {
					$print_content = false;
				}

				$widget->add_render_attribute( 'background_image', 'style', [
					'background-image: url(' . $bg_image . ');',
				] );

				$widget->add_render_attribute( 'title', 'class', [
					'elementor-cta__title',
					'elementor-cta__content-item',
					'elementor-content-item',
				] );

				$widget->add_render_attribute( 'description', 'class', [
					'elementor-cta__description',
					'elementor-cta__content-item',
					'elementor-content-item',
				] );

				$widget->add_render_attribute( 'button', 'class', [
					'elementor-cta__button',
					'elementor-button',
					'elementor-size-' . $settings['button_size'],
				] );

				$widget->add_render_attribute( 'graphic_element', 'class',
					[
						'elementor-content-item',
						'elementor-cta__content-item',
					]
				);

				if ( 'icon' === $settings['graphic_element'] ) {
					$widget->add_render_attribute( 'graphic_element', 'class',
						[
							'elementor-icon-wrapper',
							'elementor-cta__icon',
						]
					);
					$widget->add_render_attribute( 'graphic_element', 'class', 'elementor-view-' . $settings['icon_view'] );
					if ( 'default' != $settings['icon_view'] ) {
						$widget->add_render_attribute( 'graphic_element', 'class', 'elementor-shape-' . $settings['icon_shape'] );
					}

					if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
						// add old default
						$settings['icon'] = 'fa fa-star';
					}

					if ( ! empty( $settings['icon'] ) ) {
						$widget->add_render_attribute( 'icon', 'class', $settings['icon'] );
					}
				} elseif ( 'image' === $settings['graphic_element'] && ! empty( $settings['graphic_image']['url'] ) ) {
					$widget->add_render_attribute( 'graphic_element', 'class', 'elementor-cta__image' );
				}

				if ( ! empty( $content_animation ) && 'cover' == $settings['skin'] ) {

					$animation_class = 'elementor-animated-item--' . $content_animation;

					$widget->add_render_attribute( 'title', 'class', $animation_class );

					$widget->add_render_attribute( 'graphic_element', 'class', $animation_class );

					$widget->add_render_attribute( 'description', 'class', $animation_class );

				}

				if ( ! empty( $settings['link']['url'] ) ) {
					$link_element = 'button';

					if ( $box_is_link ) {
						if ( $needs_separate_links) {
							$wrapper_tag  = 'div';
							$link_element = 'link';
							$widget->add_render_attribute( 'link', 'class', 'vamtam-cta-link' );
						} else {
							$wrapper_tag  = 'a';
							$link_element = 'wrapper';
						}
						$button_tag = 'span';
					}

					$widget->add_link_attributes( $link_element, $settings['link'] );
				}

				// $widget->add_inline_editing_attributes( 'title' );
				// $widget->add_inline_editing_attributes( 'description' );
				// $widget->add_inline_editing_attributes( 'button' );

				$migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
				$is_new   = empty( $settings['icon'] ) && Icons_Manager::is_migration_allowed();

				?>
				<<?php echo $wrapper_tag . ' ' . $widget->get_render_attribute_string( 'wrapper' ); ?> class="elementor-cta">
				<?php if ( $print_bg ) : ?>
					<div class="elementor-cta__bg-wrapper">
						<?php if ( $needs_separate_links ) : ?>
							<a <?php echo $widget->get_render_attribute_string( 'link' ); ?>>
						<?php endif; ?>
						<div class="elementor-cta__bg elementor-bg" <?php echo $widget->get_render_attribute_string( 'background_image' ); ?>></div>
						<div class="elementor-cta__bg-overlay"></div>
						<?php if ( $needs_separate_links ) : ?>
							</a>
							<?php $this->render_social_links(); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php if ( $print_content ) : ?>
					<?php if ( $needs_separate_links ) : ?>
						<a <?php echo $widget->get_render_attribute_string( 'link' ); ?>>
					<?php endif; ?>
					<div class="elementor-cta__content">
						<?php if ( 'image' === $settings['graphic_element'] && ! empty( $settings['graphic_image']['url'] ) ) : ?>
							<div <?php echo $widget->get_render_attribute_string( 'graphic_element' ); ?>>
								<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'graphic_image' ); ?>
							</div>
						<?php elseif ( 'icon' === $settings['graphic_element'] && ( ! empty( $settings['icon'] ) || ! empty( $settings['selected_icon'] ) ) ) : ?>
							<div <?php echo $widget->get_render_attribute_string( 'graphic_element' ); ?>>
								<div class="elementor-icon">
									<?php if ( $is_new || $migrated ) :
										Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
									else : ?>
										<i <?php echo $widget->get_render_attribute_string( 'icon' ); ?>></i>
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $settings['title'] ) ) : ?>
							<<?php echo $title_tag . ' ' . $widget->get_render_attribute_string( 'title' ); ?>>
								<?php echo $settings['title']; ?>
							</<?php echo $title_tag; ?>>
						<?php endif; ?>

						<?php if ( ! empty( $settings['description'] ) ) : ?>
							<div <?php echo $widget->get_render_attribute_string( 'description' ); ?>>
								<?php echo $settings['description']; ?>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $settings['button'] ) ) : ?>
							<div class="elementor-cta__button-wrapper elementor-cta__content-item elementor-content-item <?php echo $animation_class; ?>">
							<<?php echo $button_tag . ' ' . $widget->get_render_attribute_string( 'button' ); ?>>
								<?php echo $settings['button']; ?>
							</<?php echo $button_tag; ?>>
							</div>
						<?php endif; ?>
					</div>
					<?php if ( $needs_separate_links ) : ?>
						</a>
					<?php endif; ?>
				<?php endif; ?>
				<?php
				if ( ! empty( $settings['ribbon_title'] ) ) :
					$widget->add_render_attribute( 'ribbon-wrapper', 'class', 'elementor-ribbon' );

					if ( ! empty( $settings['ribbon_horizontal_position'] ) ) {
						$widget->add_render_attribute( 'ribbon-wrapper', 'class', 'elementor-ribbon-' . $settings['ribbon_horizontal_position'] );
					}
					?>
					<?php if ( $needs_separate_links ) : ?>
						<a <?php echo $widget->get_render_attribute_string( 'link' ); ?>>
					<?php endif; ?>
					<div <?php echo $widget->get_render_attribute_string( 'ribbon-wrapper' ); ?>>
						<div class="elementor-ribbon-inner"><?php echo $settings['ribbon_title']; ?></div>
					</div>
					<?php if ( $needs_separate_links ) : ?>
						</a>
					<?php endif; ?>
				<?php endif; ?>
				</<?php echo $wrapper_tag; ?>>

				<?php if ( $has_social_links && ! $needs_separate_links ) : ?>
					<?php $this->render_social_links(); ?>
				<?php endif; ?>
				<?php
			}

			// We can't use content_template on skins, we override using widget extension.
			// public function content_template() {}

			protected function _register_controls_actions() {
				// Content - Image Tab
				add_action( 'elementor/element/call-to-action/section_main_image/before_section_end', [ $this, 'section_main_image_before_section_end' ] );
				// Content - Ribbon Tab
				add_action( 'elementor/element/call-to-action/section_ribbon/after_section_end', [ $this, 'section_ribbon_after_section_end' ] );
				// Syle - Box Tab
				add_action( 'elementor/element/call-to-action/box_style/before_section_end', [ $this, 'section_box_style_before_section_end' ] );
				// Style - Content Tab
				add_action( 'elementor/element/call-to-action/section_content_style/after_section_end', [ $this, 'section_content_style_after_section_end' ] );

				// !! Important: Add this action on every custom skin to avoid issues with widget extensions. !!
				do_action( 'vamtam_elementor/call-to-action/team-member/skin_registered' );
			}

			// Content - Image Tab - Before Section end.
			public function section_main_image_before_section_end( $widget ) {
				$this->parent = $widget;
				$this->update_controls_content_tab( $widget );
			}

			protected function update_controls_content_tab( $widget ) {
				$controls_manager = \Elementor\Plugin::instance()->controls_manager;
				// Skin.
				\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'skin', [
					'label' => __( 'Style', 'vamtam-elementor-integration' ),
				] );
			}

			// Content - Ribbon Tab - Before Section end.
			public function section_ribbon_after_section_end( $widget ) {
				$this->parent = $widget;
				// $this->add_controls_style_tab( $widget );
				$this->add_social_icons_content_section( $widget );
			}

			/**
			 * Content Tab: Social Links
			 */
			public function add_social_icons_content_section( $widget ) {
				$controls_manager = \Elementor\Plugin::instance()->controls_manager;

				$widget->start_controls_section(
					'section_member_social_links',
					array(
						'label' => __( 'Social Links', 'vamtam-elementor-integration' ),
						'condition'   => array(
							'_skin' => 'team-member',
						),
					)
				);

				$widget->add_control(
					'member_social_links',
					array(
						'label'        => __( 'Show Social Links', 'vamtam-elementor-integration' ),
						'type'         => $controls_manager::SWITCHER,
						'default'      => 'yes',
						'label_on'     => __( 'Yes', 'vamtam-elementor-integration' ),
						'label_off'    => __( 'No', 'vamtam-elementor-integration' ),
						'return_value' => 'yes',
						'render_type' => 'template',
					)
				);

				$repeater = new \Elementor\Repeater();

				$repeater->add_control(
					'select_social_icon',
					array(
						'label'            => __( 'Social Icon', 'vamtam-elementor-integration' ),
						'type'             => $controls_manager::ICONS,
						'fa4compatibility' => 'social_icon',
						'recommended'      => array(
							'fa-brands' => array(
								'android',
								'apple',
								'behance',
								'bitbucket',
								'codepen',
								'delicious',
								'deviantart',
								'digg',
								'dribbble',
								'elementor',
								'facebook',
								'flickr',
								'foursquare',
								'free-code-camp',
								'github',
								'gitlab',
								'globe',
								'google-plus',
								'houzz',
								'instagram',
								'jsfiddle',
								'linkedin',
								'medium',
								'meetup',
								'mixcloud',
								'odnoklassniki',
								'pinterest',
								'product-hunt',
								'reddit',
								'shopping-cart',
								'skype',
								'slideshare',
								'snapchat',
								'soundcloud',
								'spotify',
								'stack-overflow',
								'steam',
								'stumbleupon',
								'telegram',
								'thumb-tack',
								'tripadvisor',
								'tumblr',
								'twitch',
								'twitter',
								'viber',
								'vimeo',
								'vk',
								'weibo',
								'weixin',
								'whatsapp',
								'wordpress',
								'xing',
								'yelp',
								'youtube',
								'500px',
							),
							'fa-solid'  => array(
								'envelope',
								'link',
								'rss',
							),
						),
						'render_type' => 'template',
					)
				);

				$repeater->add_control(
					'social_link',
					array(
						'label'       => __( 'Social Link', 'vamtam-elementor-integration' ),
						'type'        => $controls_manager::URL,
						'dynamic'     => array(
							'active' => true,
						),
						'label_block' => true,
						'placeholder' => __( 'Enter URL', 'vamtam-elementor-integration' ),
						'render_type' => 'template',
					)
				);

				$widget->add_control(
					'team_member_social',
					array(
						'label'       => __( 'Add Social Links', 'vamtam-elementor-integration' ),
						'type'        => $controls_manager::REPEATER,
						'default'     => array(
							array(
								'select_social_icon' => array(
									'value'   => 'fab fa-facebook',
									'library' => 'fa-brands',
								),
								'social_link'        => array(
									'url' => '#',
								),
							),
							array(
								'select_social_icon' => array(
									'value'   => 'fab fa-twitter',
									'library' => 'fa-brands',
								),
								'social_link'        => array(
									'url' => '#',
								),
							),
							array(
								'select_social_icon' => array(
									'value'   => 'fab fa-youtube',
									'library' => 'fa-brands',
								),
								'social_link'        => array(
									'url' => '#',
								),
							),
						),
						'fields'      => $repeater->get_controls(),
						'title_field' => '<# var migrated = "undefined" !== typeof __fa4_migrated, social = ( "undefined" === typeof social ) ? false : social; #>{{{ elementor.helpers.getSocialNetworkNameFromIcon( select_social_icon, social, true, migrated, true ) }}}',
						'condition'   => array(
							'member_social_links' => 'yes',
						),
						'render_type' => 'template',
					)
				);

				$widget->end_controls_section();
			}

			// Style - Box Tab - Before Section end.
			public function section_box_style_before_section_end( $widget ) {
				$this->parent = $widget;
				$this->update_style_controls_box_tab( $widget );
			}

			function update_style_controls_box_tab( $widget ) {
				$controls_manager = \Elementor\Plugin::instance()->controls_manager;
				// Alignment.
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'alignment', [
					'selectors' => [
						'{{WRAPPER}} .vamtam-tm-social-links' => 'text-align: {{VALUE}}',
					],
				] );
			}

			// Style - Content Tab - After Section end.
			public function section_content_style_after_section_end( $widget ) {
				$this->parent = $widget;
				$this->add_social_icons_style_section( $widget );
			}

			/**
			 * Style Tab: Social Links
			 */
			public function add_social_icons_style_section( $widget ) {
				$controls_manager = \Elementor\Plugin::instance()->controls_manager;

				$widget->start_controls_section(
					'section_member_social_links_style',
					array(
						'label' => __( 'Social Links', 'vamtam-elementor-integration' ),
						'tab'   => $controls_manager::TAB_STYLE,
						'condition'   => array(
							'_skin' => 'team-member',
							'member_social_links' => 'yes',
						),
					)
				);

				if ( vamtam_theme_supports( 'call-to-action--fitness-cta-style' ) ) {
					$widget->add_control(
						'use_theme_cta_social_icons_style',
						[
							'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
							'type' => $controls_manager::SWITCHER,
							'prefix_class' => 'vamtam-has-',
							'return_value' => 'theme-cta-social-icons-style',
							'default' => 'theme-cta-social-icons-style',
							'render_type' => 'template',
						]
					);
				}

				$widget->add_responsive_control(
					'member_icons_alignment',
					[
						'label' => __( 'Icons Alignment', 'vamtam-elementor-integration' ),
						'type' => Controls_Manager::CHOOSE,
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
						],
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} .vamtam-tm-social-links-wrap .vamtam-tm-social-links' => 'text-align: {{VALUE}}',
						],
					]
				);

				$widget->add_responsive_control(
					'member_icons_gap',
					array(
						'label'          => __( 'Icons Gap', 'vamtam-elementor-integration' ),
						'type'           => $controls_manager::SLIDER,
						'size_units'     => array( '%', 'px' ),
						'range'          => array(
							'px' => array(
								'max' => 60,
							),
						),
						'default'        => array(
							'size' => '20',
							'unit' => 'px',
						),
						'tablet_default' => array(
							'unit' => 'px',
						),
						'mobile_default' => array(
							'unit' => 'px',
						),
						'selectors'      => array(
							'{{WRAPPER}} .vamtam-tm-social-links li:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
						),
					)
				);

				$widget->add_responsive_control(
					'member_icon_size',
					array(
						'label'          => __( 'Icon Size', 'vamtam-elementor-integration' ),
						'type'           => $controls_manager::SLIDER,
						'size_units'     => array( 'px' ),
						'range'          => array(
							'px' => array(
								'max' => 100,
							),
						),
						'default'        => array(
							'size' => '20',
							'unit' => 'px',
						),
						'tablet_default' => array(
							'unit' => 'px',
						),
						'mobile_default' => array(
							'unit' => 'px',
						),
						'selectors'      => array(
							'{{WRAPPER}} .vamtam-tm-social-links .vamtam-tm-social-icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
						),
					)
				);

				$widget->start_controls_tabs( 'tabs_links_style' );

				$widget->start_controls_tab(
					'tab_links_normal',
					array(
						'label' => __( 'Normal', 'vamtam-elementor-integration' ),
					)
				);

				$widget->add_control(
					'member_links_icons_color',
					array(
						'label'     => __( 'Color', 'vamtam-elementor-integration' ),
						'type'      => $controls_manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}} .vamtam-tm-social-links .vamtam-tm-social-icon-wrap' => 'color: {{VALUE}};',
							'{{WRAPPER}} .vamtam-tm-social-links .vamtam-tm-social-icon-wrap svg' => 'fill: {{VALUE}};',
						),
					)
				);

				$widget->add_control(
					'member_links_bg_color',
					array(
						'label'     => __( 'Background Color', 'vamtam-elementor-integration' ),
						'type'      => $controls_manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}} .vamtam-tm-social-links .vamtam-tm-social-icon-wrap' => 'background-color: {{VALUE}};',
						),
					)
				);

				$widget->add_control(
					'member_links_bg_wrap_color',
					array(
						'label'     => __( 'Container Background Color', 'vamtam-elementor-integration' ),
						'type'      => $controls_manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}} .vamtam-tm-social-links-wrap' => 'background-color: {{VALUE}};',
						),
					)
				);

				$widget->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'        => 'member_links_border',
						'label'       => __( 'Border', 'vamtam-elementor-integration' ),
						'placeholder' => '1px',
						'default'     => '1px',
						'separator'   => 'before',
						'selector'    => '{{WRAPPER}} .vamtam-tm-social-links .vamtam-tm-social-icon-wrap',
					)
				);

				$widget->add_control(
					'member_links_border_radius',
					array(
						'label'      => __( 'Border Radius', 'vamtam-elementor-integration' ),
						'type'       => $controls_manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors'  => array(
							'{{WRAPPER}} .vamtam-tm-social-links .vamtam-tm-social-icon-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
						'condition'   => array(
							'member_links_border_border!' => '',
						),
					)
				);

				$widget->add_responsive_control(
					'member_links_padding',
					array(
						'label'      => __( 'Icon Padding', 'vamtam-elementor-integration' ),
						'type'       => $controls_manager::DIMENSIONS,
						'size_units' => array( 'px', 'em', '%' ),
						'separator'  => 'before',
						'selectors'  => array(
							'{{WRAPPER}} .vamtam-tm-social-links .vamtam-tm-social-icon-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$widget->add_responsive_control(
					'member_links_bg_wrap_padding',
					array(
						'label'      => __( 'Container Padding', 'vamtam-elementor-integration' ),
						'type'       => $controls_manager::DIMENSIONS,
						'size_units' => array( 'px', 'em', '%' ),
						'separator'  => 'before',
						'selectors'  => array(
							'{{WRAPPER}} .vamtam-tm-social-links-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$widget->end_controls_tab();

				$widget->start_controls_tab(
					'tab_links_hover',
					array(
						'label' => __( 'Hover', 'vamtam-elementor-integration' ),
					)
				);

				$widget->add_control(
					'member_links_icons_color_hover',
					array(
						'label'     => __( 'Color', 'vamtam-elementor-integration' ),
						'type'      => $controls_manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}} .vamtam-tm-social-links .vamtam-tm-social-icon-wrap:hover' => 'color: {{VALUE}};',
							'{{WRAPPER}} .vamtam-tm-social-links .vamtam-tm-social-icon-wrap:hover svg' => 'fill: {{VALUE}};',
						),
					)
				);

				$widget->add_control(
					'member_links_bg_color_hover',
					array(
						'label'     => __( 'Background Color', 'vamtam-elementor-integration' ),
						'type'      => $controls_manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}} .vamtam-tm-social-links .vamtam-tm-social-icon-wrap:hover' => 'background-color: {{VALUE}};',
						),
					)
				);

				$widget->add_control(
					'member_links_bg_wrap_color_hover',
					array(
						'label'     => __( 'Container Background Color', 'vamtam-elementor-integration' ),
						'type'      => $controls_manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}}:hover .vamtam-tm-social-links-wrap' => 'background-color: {{VALUE}};',
						),
					)
				);

				$widget->add_control(
					'member_links_border_color_hover',
					array(
						'label'     => __( 'Border Color', 'vamtam-elementor-integration' ),
						'type'      => $controls_manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}} .vamtam-tm-social-links .vamtam-tm-social-icon-wrap:hover' => 'border-color: {{VALUE}};',
						),
						'condition'   => array(
							'member_links_border_border!' => '',
						),
					)
				);

				$widget->end_controls_tab();

				$widget->end_controls_tabs();

				$widget->end_controls_section();
			}
		}

		$widget->add_skin( new Vamtam_CTA_Team_Member_Skin( $widget ) );
	}
	add_action( 'elementor/widget/call-to-action/skins_init', __NAMESPACE__ . '\vamtam_add_team_member_cta_skin' );
}

if ( vamtam_theme_supports( 'call-to-action--class-skin' ) ) {
	// Class Skin
	function vamtam_add_class_cta_skin( $widget ) {
		if ( did_action( 'vamtam_elementor/call-to-action/class/skin_registered' ) ) {
			// This is in case another theme feature requires to re-register (extend) the widget.
			// In that case the widget's action's will be added twice leading to control stack issues.
			return;
		}

		class Vamtam_CTA_Class_Skin extends \Elementor\Skin_Base {
			public function get_id() {
				return 'class';
			}

			public function get_title() {
				return __( 'Class', 'vamtam-elementor-integration' );
			}

			protected function render_class_info() {
				$widget        = $this->parent;
				$skin          = $this;
				$settings      = $widget->get_settings_for_display();
				$show_duration = ! empty( $settings['show_class_duration'] );
				$show_price    = ! empty( $settings['show_class_price'] );


				$fallback_defaults = [
					'fa fa-stopwatch',
					'fa fa-wallet',
				];

				$migration_allowed = Icons_Manager::is_migration_allowed();

				// add old default
				if ( ! isset( $settings['icon'] ) && ! $migration_allowed ) {
					$settings['icon'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-check';
				}

				$duration_icon_migrated = isset( $settings['__fa4_migrated']['class_duration_selected_icon'] );
				$price_icon_migrated = isset( $settings['__fa4_migrated']['class_price_selected_icon'] );
				$is_new = ! isset( $settings['icon'] ) && $migration_allowed;
				?>
				<div class="vamtam-class-info-wrap">
					<ul class="vamtam-class-info">
					<?php if ( $show_duration ) : ?>
						<li>
							<span class="vamtam-class-info-content-wrap">
								<?php if ( ! empty( $settings['class_duration_selected_icon'] ) ) : ?>
									<span class="vamtam-class-info-icon vamtam-icon">
										<?php
										if ( $is_new || $duration_icon_migrated ) {
											Icons_Manager::render_icon( $settings['class_duration_selected_icon'], [ 'aria-hidden' => 'true' ] );
										} else { ?>
											<i class="<?php echo esc_attr( $settings['class_duration_selected_icon'] ); ?>"></i>
										<?php } ?>
									</span>
								<?php endif; ?>
								<span class="vamtam-class-info-text">
									<?php echo esc_html( $settings[ 'class_duration' ] ); ?>
								</span>
							</span>
						</li>
					<?php endif; ?>
					<?php if ( $show_price ) : ?>
						<li>
							<span class="vamtam-class-info-content-wrap">
								<?php if ( ! empty( $settings['class_price_selected_icon'] ) ) : ?>
									<span class="vamtam-class-info-icon vamtam-icon">
									<?php
										if ( $is_new || $price_icon_migrated ) {
											Icons_Manager::render_icon( $settings['class_price_selected_icon'], [ 'aria-hidden' => 'true' ] );
										} else { ?>
											<i class="<?php echo esc_attr( $settings['class_price_selected_icon'] ); ?>"></i>
										<?php } ?>
									</span>
								<?php endif; ?>
								<span class="vamtam-class-info-text">
									<?php echo esc_html( $settings[ 'class_price' ] ); ?>
								</span>
							</span>
						</li>
					<?php endif; ?>
					</ul>
				</div>
				<?php
			}

			// Skin render().
			public function render() {
				$widget             = $this->parent;
				$skin               = $this;
				$settings           = $widget->get_settings_for_display();
				$title_tag          = $settings['title_tag'];
				$wrapper_tag        = 'div';
				$button_tag         = 'a';
				$bg_image           = '';
				$content_animation  = $settings['content_animation'];
				$animation_class    = '';
				$print_bg           = true;
				$print_content      = true;
				$has_class_info     = 'yes' === $settings['show_class_info'];
				$has_theme_ci_style = ! empty( $settings['use_theme_cta_class_info_style'] );

				if ( ! empty( $settings['bg_image']['id'] ) ) {
					$bg_image = Group_Control_Image_Size::get_attachment_image_src( $settings['bg_image']['id'], 'bg_image', $settings );
				} elseif ( ! empty( $settings['bg_image']['url'] ) ) {
					$bg_image = $settings['bg_image']['url'];
				}

				if ( empty( $bg_image ) && 'classic' == $settings['skin'] ) {
					$print_bg = false;
				}

				if ( empty( $settings['title'] ) && empty( $settings['description'] ) && empty( $settings['button'] ) && 'none' == $settings['graphic_element'] ) {
					$print_content = false;
				}

				$widget->add_render_attribute( 'background_image', 'style', [
					'background-image: url(' . $bg_image . ');',
				] );

				$widget->add_render_attribute( 'title', 'class', [
					'elementor-cta__title',
					'elementor-cta__content-item',
					'elementor-content-item',
				] );

				$widget->add_render_attribute( 'description', 'class', [
					'elementor-cta__description',
					'elementor-cta__content-item',
					'elementor-content-item',
				] );

				$widget->add_render_attribute( 'button', 'class', [
					'elementor-cta__button',
					'elementor-button',
					'elementor-size-' . $settings['button_size'],
				] );

				$widget->add_render_attribute( 'graphic_element', 'class',
					[
						'elementor-content-item',
						'elementor-cta__content-item',
					]
				);

				if ( 'icon' === $settings['graphic_element'] ) {
					$widget->add_render_attribute( 'graphic_element', 'class',
						[
							'elementor-icon-wrapper',
							'elementor-cta__icon',
						]
					);
					$widget->add_render_attribute( 'graphic_element', 'class', 'elementor-view-' . $settings['icon_view'] );
					if ( 'default' != $settings['icon_view'] ) {
						$widget->add_render_attribute( 'graphic_element', 'class', 'elementor-shape-' . $settings['icon_shape'] );
					}

					if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
						// add old default
						$settings['icon'] = 'fa fa-star';
					}

					if ( ! empty( $settings['icon'] ) ) {
						$widget->add_render_attribute( 'icon', 'class', $settings['icon'] );
					}
				} elseif ( 'image' === $settings['graphic_element'] && ! empty( $settings['graphic_image']['url'] ) ) {
					$widget->add_render_attribute( 'graphic_element', 'class', 'elementor-cta__image' );
				}

				if ( ! empty( $content_animation ) && 'cover' == $settings['skin'] ) {

					$animation_class = 'elementor-animated-item--' . $content_animation;

					$widget->add_render_attribute( 'title', 'class', $animation_class );

					$widget->add_render_attribute( 'graphic_element', 'class', $animation_class );

					$widget->add_render_attribute( 'description', 'class', $animation_class );

				}

				if ( ! empty( $settings['link']['url'] ) ) {
					$link_element = 'button';

					if ( 'box' === $settings['link_click'] ) {
						$wrapper_tag = 'a';
						$button_tag = 'span';
						$link_element = 'wrapper';
					}

					$widget->add_link_attributes( $link_element, $settings['link'] );
				}

				// $widget->add_inline_editing_attributes( 'title' );
				// $widget->add_inline_editing_attributes( 'description' );
				// $widget->add_inline_editing_attributes( 'button' );

				$migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
				$is_new = empty( $settings['icon'] ) && Icons_Manager::is_migration_allowed();

				?>
				<<?php echo $wrapper_tag . ' ' . $widget->get_render_attribute_string( 'wrapper' ); ?> class="elementor-cta">
				<?php if ( $print_bg ) : ?>
					<div class="elementor-cta__bg-wrapper">
						<div class="elementor-cta__bg elementor-bg" <?php echo $widget->get_render_attribute_string( 'background_image' ); ?>></div>
						<div class="elementor-cta__bg-overlay"></div>
						<?php if ( $has_class_info && $has_theme_ci_style ) : ?>
							<?php $this->render_class_info(); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php if ( $print_content ) : ?>
					<div class="elementor-cta__content">
						<?php if ( 'image' === $settings['graphic_element'] && ! empty( $settings['graphic_image']['url'] ) ) : ?>
							<div <?php echo $widget->get_render_attribute_string( 'graphic_element' ); ?>>
								<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'graphic_image' ); ?>
							</div>
						<?php elseif ( 'icon' === $settings['graphic_element'] && ( ! empty( $settings['icon'] ) || ! empty( $settings['selected_icon'] ) ) ) : ?>
							<div <?php echo $widget->get_render_attribute_string( 'graphic_element' ); ?>>
								<div class="elementor-icon">
									<?php if ( $is_new || $migrated ) :
										Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
									else : ?>
										<i <?php echo $widget->get_render_attribute_string( 'icon' ); ?>></i>
									<?php endif; ?>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $settings['title'] ) ) : ?>
							<<?php echo $title_tag . ' ' . $widget->get_render_attribute_string( 'title' ); ?>>
								<?php echo $settings['title']; ?>
							</<?php echo $title_tag; ?>>
						<?php endif; ?>

						<?php if ( ! empty( $settings['description'] ) ) : ?>
							<div <?php echo $widget->get_render_attribute_string( 'description' ); ?>>
								<?php echo $settings['description']; ?>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $settings['button'] ) ) : ?>
							<div class="elementor-cta__button-wrapper elementor-cta__content-item elementor-content-item <?php echo $animation_class; ?>">
							<<?php echo $button_tag . ' ' . $widget->get_render_attribute_string( 'button' ); ?>>
								<?php echo $settings['button']; ?>
							</<?php echo $button_tag; ?>>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php
				if ( ! empty( $settings['ribbon_title'] ) ) :
					$widget->add_render_attribute( 'ribbon-wrapper', 'class', 'elementor-ribbon' );

					if ( ! empty( $settings['ribbon_horizontal_position'] ) ) {
						$widget->add_render_attribute( 'ribbon-wrapper', 'class', 'elementor-ribbon-' . $settings['ribbon_horizontal_position'] );
					}
					?>
					<div <?php echo $widget->get_render_attribute_string( 'ribbon-wrapper' ); ?>>
						<div class="elementor-ribbon-inner"><?php echo $settings['ribbon_title']; ?></div>
					</div>
				<?php endif; ?>
				<?php if ( $has_class_info && ! $has_theme_ci_style ) : ?>
					<?php $this->render_class_info(); ?>
				<?php endif; ?>
				</<?php echo $wrapper_tag; ?>>
				<?php
			}

			// We can't use content_template on skins, we override using widget extension.
			// public function content_template() {}

			protected function _register_controls_actions() {
				// Content - Image Tab
				add_action( 'elementor/element/call-to-action/section_main_image/before_section_end', [ $this, 'section_main_image_before_section_end' ] );
				// Content - Ribbon Tab
				add_action( 'elementor/element/call-to-action/section_ribbon/after_section_end', [ $this, 'section_ribbon_after_section_end' ] );
				// Syle - Box Tab
				add_action( 'elementor/element/call-to-action/box_style/before_section_end', [ $this, 'section_box_style_before_section_end' ] );
				// Style - Content Tab
				add_action( 'elementor/element/call-to-action/section_content_style/after_section_end', [ $this, 'section_content_style_after_section_end' ] );

				// !! Important: Add this action on every custom skin to avoid issues with widget extensions. !!
				do_action( 'vamtam_elementor/call-to-action/class/skin_registered' );
			}

			// Content - Image Tab - Before Section end.
			public function section_main_image_before_section_end( $widget ) {
				$this->parent = $widget;
				$this->update_controls_content_tab( $widget );
			}

			protected function update_controls_content_tab( $widget ) {
				$controls_manager = \Elementor\Plugin::instance()->controls_manager;
				// Skin.
				\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'skin', [
					'label' => __( 'Style', 'vamtam-elementor-integration' ),
				] );
			}

			// Content - Ribbon Tab - Before Section end.
			public function section_ribbon_after_section_end( $widget ) {
				$this->parent = $widget;
				$this->add_class_info_content_section( $widget );
			}

			/**
			 * Content Tab: Social Links
			 */
			public function add_class_info_content_section( $widget ) {
				$controls_manager = \Elementor\Plugin::instance()->controls_manager;

				$widget->start_controls_section(
					'section_class_info',
					array(
						'label' => __( 'Class Info', 'vamtam-elementor-integration' ),
						'condition'   => array(
							'_skin' => 'class',
						),
					)
				);

				$widget->add_control(
					'show_class_info',
					array(
						'label'        => __( 'Show Class Info', 'vamtam-elementor-integration' ),
						'type'         => $controls_manager::SWITCHER,
						'default'      => 'yes',
						'label_on'     => __( 'Yes', 'vamtam-elementor-integration' ),
						'label_off'    => __( 'No', 'vamtam-elementor-integration' ),
						'return_value' => 'yes',
						'render_type' => 'template',
					)
				);

				$widget->add_control(
					'heading_class_duration',
					[
						'type' => Controls_Manager::HEADING,
						'label' => __( 'Duration', 'vamtam-elementor-integration' ),
						'condition' => [
							'show_class_info!' => '',
						],
						'separator' => 'before',
					]
				);

				$widget->add_control(
					'show_class_duration',
					array(
						'label'        => __( 'Show Duration', 'vamtam-elementor-integration' ),
						'type'         => $controls_manager::SWITCHER,
						'default'      => 'yes',
						'label_on'     => __( 'Yes', 'vamtam-elementor-integration' ),
						'label_off'    => __( 'No', 'vamtam-elementor-integration' ),
						'return_value' => 'yes',
						'render_type' => 'template',
						'condition' => [
							'show_class_info!' => '',
						],
					)
				);

				$widget->add_control(
					'class_duration',
					[
						'label' => __( 'Text', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::TEXT,
						'default' => __( '60MIN', 'vamtam-elementor-integration' ),
						'condition' => [
							'show_class_info!' => '',
							'show_class_duration!' => '',
						],
						'render_type' => 'template',
					]
				);

				$widget->add_control(
					'class_duration_selected_icon',
					[
						'label' => __( 'Icon', 'elementor-pro' ),
						'type' => Controls_Manager::ICONS,
						'fa4compatibility' => 'icon',
						'default' => [
							'value' => 'fas fa-stopwatch',
							'library' => 'fa-solid',
						],
						'condition' => [
							'show_class_info!' => '',
							'show_class_duration!' => '',
						],
						'render_type' => 'template',
					]
				);

				$widget->add_control(
					'heading_class_price',
					[
						'type' => Controls_Manager::HEADING,
						'label' => __( 'Price', 'vamtam-elementor-integration' ),
						'condition' => [
							'show_class_info!' => '',
						],
						'separator' => 'before',
						'render_type' => 'template',
					]
				);

				$widget->add_control(
					'show_class_price',
					array(
						'label'        => __( 'Show Price', 'vamtam-elementor-integration' ),
						'type'         => $controls_manager::SWITCHER,
						'default'      => 'yes',
						'label_on'     => __( 'Yes', 'vamtam-elementor-integration' ),
						'label_off'    => __( 'No', 'vamtam-elementor-integration' ),
						'return_value' => 'yes',
						'render_type' => 'template',
						'condition' => [
							'show_class_info!' => '',
						],
					)
				);

				$widget->add_control(
					'class_price',
					[
						'label' => __( 'Text', 'vamtam-elementor-integration' ),
						'type' => $controls_manager::TEXT,
						'default' => __( '20$/MO', 'vamtam-elementor-integration' ),
						'condition' => [
							'show_class_info!' => '',
							'show_class_price!' => '',
						],
						'render_type' => 'template',
					]
				);

				$widget->add_control(
					'class_price_selected_icon',
					[
						'label' => __( 'Icon', 'elementor-pro' ),
						'type' => Controls_Manager::ICONS,
						'fa4compatibility' => 'icon',
						'default' => [
							'value' => 'fas fa-wallet',
							'library' => 'fa-solid',
						],
						'condition' => [
							'show_class_info!' => '',
							'show_class_price!' => '',
						],
						'render_type' => 'template',
					]
				);

				$widget->end_controls_section();
			}

			// Style - Box Tab - Before Section end.
			public function section_box_style_before_section_end( $widget ) {
				$this->parent = $widget;
				$this->update_style_controls_box_tab( $widget );
			}

			function update_style_controls_box_tab( $widget ) {
				$controls_manager = \Elementor\Plugin::instance()->controls_manager;
				// Alignment.
				\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'alignment', [
					'selectors' => [
						'{{WRAPPER}} .vamtam-class-info' => 'text-align: {{VALUE}}',
					],
				] );
			}

			// Style - Content Tab - After Section end.
			public function section_content_style_after_section_end( $widget ) {
				$this->parent = $widget;
				$this->add_class_info_style_section( $widget );
			}

			/**
			 * Style Tab: Social Links
			 */
			public function add_class_info_style_section( $widget ) {
				$controls_manager = \Elementor\Plugin::instance()->controls_manager;

				$widget->start_controls_section(
					'section_class_info_style',
					array(
						'label' => __( 'Class Info', 'vamtam-elementor-integration' ),
						'tab'   => $controls_manager::TAB_STYLE,
						'condition'   => array(
							'_skin' => 'class',
							'show_class_info!' => '',
						),
					)
				);

				if ( vamtam_theme_supports( 'call-to-action--fitness-cta-style' ) ) {
					$widget->add_control(
						'use_theme_cta_class_info_style',
						[
							'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
							'type' => $controls_manager::SWITCHER,
							'prefix_class' => 'vamtam-has-',
							'return_value' => 'theme-cta-class-info-style',
							'default' => 'theme-cta-class-info-style',
							'render_type' => 'template',
						]
					);
				}

				$widget->add_responsive_control(
					'class_info_icons_alignment',
					[
						'label' => __( 'Icons Alignment', 'vamtam-elementor-integration' ),
						'type' => Controls_Manager::CHOOSE,
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
						],
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} .vamtam-class-info-wrap .vamtam-class-info' => 'text-align: {{VALUE}}',
						],
					]
				);

				$widget->add_responsive_control(
					'class_info_content_gap',
					array(
						'label'          => __( 'Content Gap', 'vamtam-elementor-integration' ),
						'type'           => $controls_manager::SLIDER,
						'size_units'     => array( '%', 'px' ),
						'range'          => array(
							'px' => array(
								'max' => 60,
							),
						),
						'default'        => array(
							'size' => '20',
							'unit' => 'px',
						),
						'tablet_default' => array(
							'unit' => 'px',
						),
						'mobile_default' => array(
							'unit' => 'px',
						),
						'selectors'      => array(
							'{{WRAPPER}} .vamtam-class-info li:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
						),
					)
				);

				$widget->add_responsive_control(
					'class_info_icons_gap',
					array(
						'label'          => __( 'Icons Gap', 'vamtam-elementor-integration' ),
						'type'           => $controls_manager::SLIDER,
						'size_units'     => array( '%', 'px' ),
						'range'          => array(
							'px' => array(
								'max' => 60,
							),
						),
						'default'        => array(
							'size' => '10',
							'unit' => 'px',
						),
						'tablet_default' => array(
							'unit' => 'px',
						),
						'mobile_default' => array(
							'unit' => 'px',
						),
						'selectors'      => array(
							'{{WRAPPER}} .vamtam-class-info .vamtam-class-info-icon.vamtam-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
						),
					)
				);

				$widget->add_responsive_control(
					'class_info_icon_size',
					array(
						'label'          => __( 'Icon Size', 'vamtam-elementor-integration' ),
						'type'           => $controls_manager::SLIDER,
						'size_units'     => array( 'px' ),
						'range'          => array(
							'px' => array(
								'max' => 100,
							),
						),
						'default'        => array(
							'size' => '20',
							'unit' => 'px',
						),
						'tablet_default' => array(
							'unit' => 'px',
						),
						'mobile_default' => array(
							'unit' => 'px',
						),
						'selectors'      => array(
							'{{WRAPPER}} .vamtam-class-info .vamtam-class-info-icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
						),
					)
				);

				$widget->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'label'          => __( 'Text Typography', 'vamtam-elementor-integration' ),
						'name' => 'class_info_text_typography',
						'selector' => '{{WRAPPER}} .vamtam-class-info-text',
					]
				);

				$widget->start_controls_tabs( 'tabs_class_style' );

				$widget->start_controls_tab(
					'tab_class_info_normal',
					array(
						'label' => __( 'Normal', 'vamtam-elementor-integration' ),
					)
				);

				$widget->add_control(
					'class_info_text_color',
					array(
						'label'     => __( 'Text Color', 'vamtam-elementor-integration' ),
						'type'      => $controls_manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}} .vamtam-class-info .vamtam-class-info-text' => 'color: {{VALUE}};',
						),
					)
				);

				$widget->add_control(
					'class_info_icons_color',
					array(
						'label'     => __( 'Icons Color', 'vamtam-elementor-integration' ),
						'type'      => $controls_manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}} .vamtam-class-info .vamtam-class-info-icon' => 'color: {{VALUE}};',
							'{{WRAPPER}} .vamtam-class-info .vamtam-class-info-icon svg' => 'fill: {{VALUE}};',
						),
					)
				);

				$widget->add_control(
					'class_info_content_bg_color',
					array(
						'label'     => __( 'Content Background Color', 'vamtam-elementor-integration' ),
						'type'      => $controls_manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}} .vamtam-class-info .vamtam-class-info-content-wrap' => 'background-color: {{VALUE}};',
						),
					)
				);

				$widget->add_control(
					'class_info_bg_wrap_color',
					array(
						'label'     => __( 'Container Background Color', 'vamtam-elementor-integration' ),
						'type'      => $controls_manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}} .vamtam-class-info-wrap' => 'background-color: {{VALUE}};',
						),
					)
				);

				$widget->add_group_control(
					Group_Control_Border::get_type(),
					array(
						'name'        => 'class_info_border',
						'label'       => __( 'Border', 'vamtam-elementor-integration' ),
						'placeholder' => '1px',
						'default'     => '1px',
						'separator'   => 'before',
						'selector'    => '{{WRAPPER}} .vamtam-class-info .vamtam-class-info-content-wrap',
					)
				);

				$widget->add_control(
					'class_info_border_radius',
					array(
						'label'      => __( 'Border Radius', 'vamtam-elementor-integration' ),
						'type'       => $controls_manager::DIMENSIONS,
						'size_units' => array( 'px', '%' ),
						'selectors'  => array(
							'{{WRAPPER}} .vamtam-class-info .vamtam-class-info-content-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
						'condition'   => array(
							'class_info_border_border!' => '',
						),
					)
				);

				$widget->add_responsive_control(
					'class_info_padding',
					array(
						'label'      => __( 'Content Padding', 'vamtam-elementor-integration' ),
						'type'       => $controls_manager::DIMENSIONS,
						'size_units' => array( 'px', 'em', '%' ),
						'separator'  => 'before',
						'selectors'  => array(
							'{{WRAPPER}} .vamtam-class-info .vamtam-class-info-content-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$widget->add_responsive_control(
					'class_info_bg_wrap_padding',
					array(
						'label'      => __( 'Container Padding', 'vamtam-elementor-integration' ),
						'type'       => $controls_manager::DIMENSIONS,
						'size_units' => array( 'px', 'em', '%' ),
						'separator'  => 'before',
						'selectors'  => array(
							'{{WRAPPER}} .vamtam-class-info-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						),
					)
				);

				$widget->end_controls_tab();

				$widget->start_controls_tab(
					'tab_class_hover',
					array(
						'label' => __( 'Hover', 'vamtam-elementor-integration' ),
					)
				);

				$widget->add_control(
					'class_info_text_color_hover',
					array(
						'label'     => __( 'Text Color', 'vamtam-elementor-integration' ),
						'type'      => $controls_manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}} .vamtam-class-info .vamtam-class-info-content-wrap:hover .vamtam-class-info-text' => 'color: {{VALUE}};',
						),
					)
				);

				$widget->add_control(
					'class_info_icons_color_hover',
					array(
						'label'     => __( 'Icons Color', 'vamtam-elementor-integration' ),
						'type'      => $controls_manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}} .vamtam-class-info .vamtam-class-info-content-wrap:hover .vamtam-class-info-icon' => 'color: {{VALUE}};',
							'{{WRAPPER}} .vamtam-class-info .vamtam-class-info-content-wrap:hover .vamtam-class-info-icon svg' => 'fill: {{VALUE}};',
						),
					)
				);

				$widget->add_control(
					'class_info_content_bg_color_hover',
					array(
						'label'     => __( 'Content Background Color', 'vamtam-elementor-integration' ),
						'type'      => $controls_manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}} .vamtam-class-info .vamtam-class-info-content-wrap:hover' => 'background-color: {{VALUE}};',
						),
					)
				);

				$widget->add_control(
					'class_info_bg_wrap_color_hover',
					array(
						'label'     => __( 'Container Background Color', 'vamtam-elementor-integration' ),
						'type'      => $controls_manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}}:hover .vamtam-class-info-wrap' => 'background-color: {{VALUE}};',
						),
					)
				);

				$widget->add_control(
					'class_info_border_color_hover',
					array(
						'label'     => __( 'Border Color', 'vamtam-elementor-integration' ),
						'type'      => $controls_manager::COLOR,
						'default'   => '',
						'selectors' => array(
							'{{WRAPPER}} .vamtam-class-info .vamtam-class-info-content-wrap:hover' => 'border-color: {{VALUE}};',
						),
						'condition'   => array(
							'class_info_border_border!' => '',
						),
					)
				);

				$widget->end_controls_tab();

				$widget->end_controls_tabs();

				$widget->end_controls_section();
			}
		}

		$widget->add_skin( new Vamtam_CTA_Class_Skin( $widget ) );
	}
	add_action( 'elementor/widget/call-to-action/skins_init', __NAMESPACE__ . '\vamtam_add_class_cta_skin' );
}

if ( vamtam_theme_supports( [ 'call-to-action--team-member-skin', 'call-to-action--class-skin' ] ) ) {
	// Vamtam_Widget_CTA.
	function widgets_registered() {
		// Is Pro Widget.
		if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
			return;
		}

		if ( ! class_exists( '\ElementorPro\Modules\CallToAction\Widgets\Call_To_Action' ) ) {
			return; // Elementor's autoloader acts weird sometimes.
		}

		class Vamtam_Widget_CTA extends Elementor_CTA {
			protected function content_template() {
					if ( vamtam_theme_supports( 'call-to-action--fitness-cta-style' ) ) {
						?>
						<#
							var has_fitness_cta_style = true;
						#>
						<?php
					}
				?>
				<#
					if ( settings._skin === 'team-member' ) {
						var wrapperTag           = 'div',
							buttonTag            = 'a',
							contentAnimation     = settings.content_animation,
							animationClass,
							btnSizeClass         = 'elementor-size-' + settings.button_size,
							printBg              = true,
							printContent         = true,
							iconHTML             = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
							migrated             = elementor.helpers.isIconMigrated( settings, 'selected_icon' ),
							box_is_link          = 'box' === settings.link_click,
							has_social_links     = 'yes' === settings.member_social_links,
							has_theme_si_style   = settings.use_theme_cta_social_icons_style,
							needs_separate_links = box_is_link && has_social_links && has_theme_si_style && has_fitness_cta_style;

							if ( box_is_link ) {
								if ( needs_separate_links) {
									wrapperTag = 'div';
									view.addRenderAttribute( 'link', 'class', 'vamtam-cta-link' );
									view.addRenderAttribute( 'link', 'href', '#' );
								} else {
									wrapperTag = 'a';
									view.addRenderAttribute( 'wrapper', 'href', '#' );
								}
								buttonTag = 'span';
							}

						if ( '' !== settings.bg_image.url ) {
							var bg_image = {
								id: settings.bg_image.id,
								url: settings.bg_image.url,
								size: settings.bg_image_size,
								dimension: settings.bg_image_custom_dimension,
								model: view.getEditModel()
							};

							var bgImageUrl = elementor.imagesManager.getImageUrl( bg_image );
						}

						if ( ! bg_image && 'classic' == settings.skin ) {
							printBg = false;
						}

						if ( ! settings.title && ! settings.description && ! settings.button && 'none' == settings.graphic_element ) {
							printContent = false;
						}

						if ( 'icon' === settings.graphic_element ) {
							var iconWrapperClasses = 'elementor-icon-wrapper';
								iconWrapperClasses += ' elementor-cta__image';
								iconWrapperClasses += ' elementor-view-' + settings.icon_view;
							if ( 'default' !== settings.icon_view ) {
								iconWrapperClasses += ' elementor-shape-' + settings.icon_shape;
							}
							view.addRenderAttribute( 'graphic_element', 'class', iconWrapperClasses );

						} else if ( 'image' === settings.graphic_element && '' !== settings.graphic_image.url ) {
							var image = {
								id: settings.graphic_image.id,
								url: settings.graphic_image.url,
								size: settings.graphic_image_size,
								dimension: settings.graphic_image_custom_dimension,
								model: view.getEditModel()
							};

							var imageUrl = elementor.imagesManager.getImageUrl( image );
							view.addRenderAttribute( 'graphic_element', 'class', 'elementor-cta__image' );
						}

						if ( contentAnimation && 'cover' === settings.skin ) {

							var animationClass = 'elementor-animated-item--' + contentAnimation;

							view.addRenderAttribute( 'title', 'class', animationClass );

							view.addRenderAttribute( 'description', 'class', animationClass );

							view.addRenderAttribute( 'graphic_element', 'class', animationClass );
						}

						view.addRenderAttribute( 'background_image', 'style', 'background-image: url(' + bgImageUrl + ');' );
						view.addRenderAttribute( 'title', 'class', [ 'elementor-cta__title', 'elementor-cta__content-item', 'elementor-content-item' ] );
						view.addRenderAttribute( 'description', 'class', [ 'elementor-cta__description', 'elementor-cta__content-item', 'elementor-content-item' ] );
						view.addRenderAttribute( 'button', 'class', [ 'elementor-cta__button', 'elementor-button', btnSizeClass ] );
						view.addRenderAttribute( 'graphic_element', 'class', [ 'elementor-cta__content-item', 'elementor-content-item' ] );


						view.addInlineEditingAttributes( 'title' );
						view.addInlineEditingAttributes( 'description' );
						view.addInlineEditingAttributes( 'button' );

						function member_social_links() {
							var iconsHTML = {};
							#>
							<div class="vamtam-tm-social-links-wrap">
								<ul class="vamtam-tm-social-links">
									<# _.each( settings.team_member_social, function( item, index ) {
										var migrated = elementor.helpers.isIconMigrated( item, 'select_social_icon' );
											social = elementor.helpers.getSocialNetworkNameFromIcon( item.select_social_icon, item.social_icon, false, migrated );
										#>
										<li>
											<# if ( item.social_icon || item.select_social_icon ) { #>
													<span class="vamtam-tm-social-icon-wrap">
														<span class="vamtam-tm-social-icon vamtam-icon">
															<span class="elementor-screen-only">{{{ social }}}</span>
															<#
																iconsHTML[ index ] = elementor.helpers.renderIcon( view, item.select_social_icon, {}, 'i', 'object' );
																if ( ( ! item.social_icon || migrated ) && iconsHTML[ index ] && iconsHTML[ index ].rendered ) { #>
																	{{{ iconsHTML[ index ].value }}}
																<# } else { #>
																	<i class="{{ item.social_icon }}"></i>
																<# }
															#>
														</span>
													</span>
											<# } #>
										</li>
									<# } ); #>
								</ul>
							</div>
						<# } #>

						<{{ wrapperTag }} class="elementor-cta" {{{ view.getRenderAttributeString( 'wrapper' ) }}}>

						<# if ( printBg ) { #>
							<div class="elementor-cta__bg-wrapper">
								<# if ( needs_separate_links ) { #>
									<a {{{ view.getRenderAttributeString( 'link' ) }}}>
								<# } #>
								<div class="elementor-cta__bg elementor-bg" {{{ view.getRenderAttributeString( 'background_image' ) }}}></div>
								<div class="elementor-cta__bg-overlay"></div>
								<# if ( needs_separate_links ) { #>
									</a>
									<# member_social_links(); #>
								<# } #>
							</div>
						<# } #>

						<# if ( printContent ) {
							if ( needs_separate_links ) { #>
								<a {{{ view.getRenderAttributeString( 'link' ) }}}>
							<# } #>
							<div class="elementor-cta__content">
								<# if ( 'image' === settings.graphic_element && '' !== settings.graphic_image.url ) { #>
									<div {{{ view.getRenderAttributeString( 'graphic_element' ) }}}>
										<img src="{{ imageUrl }}">
									</div>
								<#  } else if ( 'icon' === settings.graphic_element && ( settings.icon || settings.selected_icon ) ) { #>
									<div {{{ view.getRenderAttributeString( 'graphic_element' ) }}}>
										<div class="elementor-icon">
											<# if ( iconHTML && iconHTML.rendered && ( ! settings.icon || migrated ) ) { #>
												{{{ iconHTML.value }}}
											<# } else { #>
												<i class="{{ settings.icon }}"></i>
											<# } #>
										</div>
									</div>
								<# } #>

								<# if ( settings.title ) { #>
									<{{ settings.title_tag }} {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ settings.title }}}</{{ settings.title_tag }}>
								<# } #>

								<# if ( settings.description ) { #>
									<div {{{ view.getRenderAttributeString( 'description' ) }}}>{{{ settings.description }}}</div>
								<# } #>

								<# if ( settings.button ) { #>
									<div class="elementor-cta__button-wrapper elementor-cta__content-item elementor-content-item {{ animationClass }}">
										<{{ buttonTag }} href="#" {{{ view.getRenderAttributeString( 'button' ) }}}>{{{ settings.button }}}</{{ buttonTag }}>
									</div>
								<# } #>
							</div>

							<# if ( needs_separate_links ) { #>
								</a>
							<# } #>
						<# } #>

						<# if ( settings.ribbon_title ) {
							var ribbonClasses = 'elementor-ribbon';

							if ( settings.ribbon_horizontal_position ) {
								ribbonClasses += ' elementor-ribbon-' + settings.ribbon_horizontal_position;
							} #>
							<# if ( needs_separate_links ) { #>
								<a {{{ view.getRenderAttributeString( 'link' ) }}}>
							<# } #>
							<div class="{{ ribbonClasses }}">
								<div class="elementor-ribbon-inner">{{{ settings.ribbon_title }}}</div>
							</div>
							<# if ( needs_separate_links ) { #>
								</a>
							<# } #>
						<# } #>

						</{{ wrapperTag }}>

						<# if ( has_social_links && ! needs_separate_links ) {
							member_social_links();
						}
					} else if ( settings._skin === 'class' ) {
							var wrapperTag           = 'div',
								buttonTag            = 'a',
								contentAnimation     = settings.content_animation,
								animationClass,
								btnSizeClass         = 'elementor-size-' + settings.button_size,
								printBg              = true,
								printContent         = true,
								iconHTML             = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
								migrated             = elementor.helpers.isIconMigrated( settings, 'selected_icon' ),
								durationIconHTML     = elementor.helpers.renderIcon( view, settings.class_duration_selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
								durationIconMigrated = elementor.helpers.isIconMigrated( settings, 'class_duration_selected_icon' ),
								priceIconHTML        = elementor.helpers.renderIcon( view, settings.class_price_selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
								priceIconMigrated    = elementor.helpers.isIconMigrated( settings, 'class_price_selected_icon' ),
								has_class_info       = 'yes' === settings.show_class_info,
								has_theme_ci_style   = settings.use_theme_cta_class_info_style;

							if ( 'box' === settings.link_click ) {
								wrapperTag = 'a';
								buttonTag = 'span';
								view.addRenderAttribute( 'wrapper', 'href', '#' );
							}

							if ( '' !== settings.bg_image.url ) {
								var bg_image = {
									id: settings.bg_image.id,
									url: settings.bg_image.url,
									size: settings.bg_image_size,
									dimension: settings.bg_image_custom_dimension,
									model: view.getEditModel()
								};

								var bgImageUrl = elementor.imagesManager.getImageUrl( bg_image );
							}

							if ( ! bg_image && 'classic' == settings.skin ) {
								printBg = false;
							}

							if ( ! settings.title && ! settings.description && ! settings.button && 'none' == settings.graphic_element ) {
								printContent = false;
							}

							if ( 'icon' === settings.graphic_element ) {
								var iconWrapperClasses = 'elementor-icon-wrapper';
									iconWrapperClasses += ' elementor-cta__image';
									iconWrapperClasses += ' elementor-view-' + settings.icon_view;
								if ( 'default' !== settings.icon_view ) {
									iconWrapperClasses += ' elementor-shape-' + settings.icon_shape;
								}
								view.addRenderAttribute( 'graphic_element', 'class', iconWrapperClasses );

							} else if ( 'image' === settings.graphic_element && '' !== settings.graphic_image.url ) {
								var image = {
									id: settings.graphic_image.id,
									url: settings.graphic_image.url,
									size: settings.graphic_image_size,
									dimension: settings.graphic_image_custom_dimension,
									model: view.getEditModel()
								};

								var imageUrl = elementor.imagesManager.getImageUrl( image );
								view.addRenderAttribute( 'graphic_element', 'class', 'elementor-cta__image' );
							}

							if ( contentAnimation && 'cover' === settings.skin ) {

								var animationClass = 'elementor-animated-item--' + contentAnimation;

								view.addRenderAttribute( 'title', 'class', animationClass );

								view.addRenderAttribute( 'description', 'class', animationClass );

								view.addRenderAttribute( 'graphic_element', 'class', animationClass );
							}

							view.addRenderAttribute( 'background_image', 'style', 'background-image: url(' + bgImageUrl + ');' );
							view.addRenderAttribute( 'title', 'class', [ 'elementor-cta__title', 'elementor-cta__content-item', 'elementor-content-item' ] );
							view.addRenderAttribute( 'description', 'class', [ 'elementor-cta__description', 'elementor-cta__content-item', 'elementor-content-item' ] );
							view.addRenderAttribute( 'button', 'class', [ 'elementor-cta__button', 'elementor-button', btnSizeClass ] );
							view.addRenderAttribute( 'graphic_element', 'class', [ 'elementor-cta__content-item', 'elementor-content-item' ] );


							view.addInlineEditingAttributes( 'title' );
							view.addInlineEditingAttributes( 'description' );
							view.addInlineEditingAttributes( 'button' );

							function render_class_info() {
								var show_duration = settings.show_class_duration,
									show_price    = settings.show_class_price;
								#>
								<div class="vamtam-class-info-wrap">
									<ul class="vamtam-class-info">
									<# if ( show_duration ) { #>
										<li>
											<span class="vamtam-class-info-content-wrap">
												<# if ( settings.class_duration_selected_icon && settings.class_duration_selected_icon.value ) { #>
													<span class="vamtam-class-info-icon vamtam-icon">
														<# if ( durationIconHTML && durationIconHTML.rendered && ( ! settings.class_duration_selected_icon.value || durationIconMigrated ) ) { #>
															{{{ durationIconHTML.value }}}
														<# } else { #>
															<i class="{{ settings.class_duration_selected_icon.value }}"></i>
														<# } #>
													</span>
												<# } #>
												<span class="vamtam-class-info-text">
													{{{ settings.class_duration }}}
												</span>
											</span>
										</li>
									<# } #>
									<# if ( show_price ) { #>
										<li>
											<span class="vamtam-class-info-content-wrap">
												<# if ( settings.class_price_selected_icon && settings.class_price_selected_icon.value ) { #>
													<span class="vamtam-class-info-icon vamtam-icon">
														<# if ( priceIconHTML && priceIconHTML.rendered && ( ! settings.class_price_selected_icon.value || priceIconMigrated ) ) { #>
															{{{ priceIconHTML.value }}}
														<# } else { #>
															<i class="{{ settings.class_price_selected_icon.value }}"></i>
														<# } #>
													</span>
												<# } #>
												<span class="vamtam-class-info-text">
													{{{ settings.class_price }}}
												</span>
											</span>
										</li>
									<# } #>
									</ul>
								</div>
							<# } #>

						<{{ wrapperTag }} class="elementor-cta" {{{ view.getRenderAttributeString( 'wrapper' ) }}}>

						<# if ( printBg ) { #>
							<div class="elementor-cta__bg-wrapper">
								<div class="elementor-cta__bg elementor-bg" {{{ view.getRenderAttributeString( 'background_image' ) }}}></div>
								<div class="elementor-cta__bg-overlay"></div>
								<# if ( has_class_info && has_theme_ci_style ) {
									render_class_info();
								} #>
							</div>
						<# } #>
						<# if ( printContent ) { #>
							<div class="elementor-cta__content">
								<# if ( 'image' === settings.graphic_element && '' !== settings.graphic_image.url ) { #>
									<div {{{ view.getRenderAttributeString( 'graphic_element' ) }}}>
										<img src="{{ imageUrl }}">
									</div>
								<#  } else if ( 'icon' === settings.graphic_element && ( settings.icon || settings.selected_icon ) ) { #>
									<div {{{ view.getRenderAttributeString( 'graphic_element' ) }}}>
										<div class="elementor-icon">
											<# if ( iconHTML && iconHTML.rendered && ( ! settings.icon || migrated ) ) { #>
												{{{ iconHTML.value }}}
											<# } else { #>
												<i class="{{ settings.icon }}"></i>
											<# } #>
										</div>
									</div>
								<# } #>
								<# if ( settings.title ) { #>
									<{{ settings.title_tag }} {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ settings.title }}}</{{ settings.title_tag }}>
								<# } #>

								<# if ( settings.description ) { #>
									<div {{{ view.getRenderAttributeString( 'description' ) }}}>{{{ settings.description }}}</div>
								<# } #>

								<# if ( settings.button ) { #>
									<div class="elementor-cta__button-wrapper elementor-cta__content-item elementor-content-item {{ animationClass }}">
										<{{ buttonTag }} href="#" {{{ view.getRenderAttributeString( 'button' ) }}}>{{{ settings.button }}}</{{ buttonTag }}>
									</div>
								<# } #>
							</div>
						<# } #>
						<# if ( settings.ribbon_title ) {
							var ribbonClasses = 'elementor-ribbon';

							if ( settings.ribbon_horizontal_position ) {
								ribbonClasses += ' elementor-ribbon-' + settings.ribbon_horizontal_position;
							} #>
							<div class="{{ ribbonClasses }}">
								<div class="elementor-ribbon-inner">{{{ settings.ribbon_title }}}</div>
							</div>
						<# } #>
						<# if ( has_class_info && ! has_theme_ci_style ) {
							render_class_info();
						} #>
						</{{ wrapperTag }}>
					<# } else { #>
						<#
							// Parent content_template().
							// TODO: Find a better way to conditionally add the parent content_template when using a skin.
							// This is a copy from Elementor's CTA implementation (needs to follow Elemento's updates).

							var wrapperTag = 'div',
								buttonTag = 'a',
								contentAnimation = settings.content_animation,
								animationClass,
								btnSizeClass = 'elementor-size-' + settings.button_size,
								printBg = true,
								printContent = true,
								iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
								migrated = elementor.helpers.isIconMigrated( settings, 'selected_icon' );

							if ( 'box' === settings.link_click ) {
								wrapperTag = 'a';
								buttonTag = 'span';
								view.addRenderAttribute( 'wrapper', 'href', '#' );
							}

							if ( '' !== settings.bg_image.url ) {
								var bg_image = {
									id: settings.bg_image.id,
									url: settings.bg_image.url,
									size: settings.bg_image_size,
									dimension: settings.bg_image_custom_dimension,
									model: view.getEditModel()
								};

								var bgImageUrl = elementor.imagesManager.getImageUrl( bg_image );
							}

							if ( ! bg_image && 'classic' == settings.skin ) {
								printBg = false;
							}

							if ( ! settings.title && ! settings.description && ! settings.button && 'none' == settings.graphic_element ) {
								printContent = false;
							}

							if ( 'icon' === settings.graphic_element ) {
								var iconWrapperClasses = 'elementor-icon-wrapper';
									iconWrapperClasses += ' elementor-cta__image';
									iconWrapperClasses += ' elementor-view-' + settings.icon_view;
								if ( 'default' !== settings.icon_view ) {
									iconWrapperClasses += ' elementor-shape-' + settings.icon_shape;
								}
								view.addRenderAttribute( 'graphic_element', 'class', iconWrapperClasses );

							} else if ( 'image' === settings.graphic_element && '' !== settings.graphic_image.url ) {
								var image = {
									id: settings.graphic_image.id,
									url: settings.graphic_image.url,
									size: settings.graphic_image_size,
									dimension: settings.graphic_image_custom_dimension,
									model: view.getEditModel()
								};

								var imageUrl = elementor.imagesManager.getImageUrl( image );
								view.addRenderAttribute( 'graphic_element', 'class', 'elementor-cta__image' );
							}

							if ( contentAnimation && 'cover' === settings.skin ) {

								var animationClass = 'elementor-animated-item--' + contentAnimation;

								view.addRenderAttribute( 'title', 'class', animationClass );

								view.addRenderAttribute( 'description', 'class', animationClass );

								view.addRenderAttribute( 'graphic_element', 'class', animationClass );
							}

							view.addRenderAttribute( 'background_image', 'style', 'background-image: url(' + bgImageUrl + ');' );
							view.addRenderAttribute( 'title', 'class', [ 'elementor-cta__title', 'elementor-cta__content-item', 'elementor-content-item' ] );
							view.addRenderAttribute( 'description', 'class', [ 'elementor-cta__description', 'elementor-cta__content-item', 'elementor-content-item' ] );
							view.addRenderAttribute( 'button', 'class', [ 'elementor-cta__button', 'elementor-button', btnSizeClass ] );
							view.addRenderAttribute( 'graphic_element', 'class', [ 'elementor-cta__content-item', 'elementor-content-item' ] );


							view.addInlineEditingAttributes( 'title' );
							view.addInlineEditingAttributes( 'description' );
							view.addInlineEditingAttributes( 'button' );
						#>

						<{{ wrapperTag }} class="elementor-cta" {{{ view.getRenderAttributeString( 'wrapper' ) }}}>

						<# if ( printBg ) { #>
							<div class="elementor-cta__bg-wrapper">
								<div class="elementor-cta__bg elementor-bg" {{{ view.getRenderAttributeString( 'background_image' ) }}}></div>
								<div class="elementor-cta__bg-overlay"></div>
							</div>
						<# } #>
						<# if ( printContent ) { #>
							<div class="elementor-cta__content">
								<# if ( 'image' === settings.graphic_element && '' !== settings.graphic_image.url ) { #>
									<div {{{ view.getRenderAttributeString( 'graphic_element' ) }}}>
										<img src="{{ imageUrl }}">
									</div>
								<#  } else if ( 'icon' === settings.graphic_element && ( settings.icon || settings.selected_icon ) ) { #>
									<div {{{ view.getRenderAttributeString( 'graphic_element' ) }}}>
										<div class="elementor-icon">
											<# if ( iconHTML && iconHTML.rendered && ( ! settings.icon || migrated ) ) { #>
												{{{ iconHTML.value }}}
											<# } else { #>
												<i class="{{ settings.icon }}"></i>
											<# } #>
										</div>
									</div>
								<# } #>
								<# if ( settings.title ) { #>
									<{{ settings.title_tag }} {{{ view.getRenderAttributeString( 'title' ) }}}>{{{ settings.title }}}</{{ settings.title_tag }}>
								<# } #>

								<# if ( settings.description ) { #>
									<div {{{ view.getRenderAttributeString( 'description' ) }}}>{{{ settings.description }}}</div>
								<# } #>

								<# if ( settings.button ) { #>
									<div class="elementor-cta__button-wrapper elementor-cta__content-item elementor-content-item {{ animationClass }}">
										<{{ buttonTag }} href="#" {{{ view.getRenderAttributeString( 'button' ) }}}>{{{ settings.button }}}</{{ buttonTag }}>
									</div>
								<# } #>
							</div>
						<# } #>
						<# if ( settings.ribbon_title ) {
							var ribbonClasses = 'elementor-ribbon';

							if ( settings.ribbon_horizontal_position ) {
								ribbonClasses += ' elementor-ribbon-' + settings.ribbon_horizontal_position;
							} #>
							<div class="{{ ribbonClasses }}">
								<div class="elementor-ribbon-inner">{{{ settings.ribbon_title }}}</div>
							</div>
						<# } #>
						</{{ wrapperTag }}>
				<# } #>
				<?php
			}
		}

		// Replace current heading widget with our extended version.
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets_manager->unregister( 'call-to-action' );
		$widgets_manager->register( new Vamtam_Widget_CTA );
	}
	add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
}
