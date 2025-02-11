<?php
namespace VamtamElementor\Widgets\Image;

// Extending the Image widget.

if ( current_theme_supports( 'vamtam-elementor-widgets', 'image--grow-with-scale-anims' ) ) {

	// Style - Image Section.
	function section_style_image_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		// Width.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'width', [
			'selectors' => [
				'{{WRAPPER}} .vamtam-image-wrapper' => 'width: {{SIZE}}{{UNIT}};',
			],
		] );
		// Max Width.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'space', [
			'selectors' => [
				'{{WRAPPER}} .vamtam-image-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
			]
		] );
	}
	add_action( 'elementor/element/image/section_style_image/before_section_end', __NAMESPACE__ . '\section_style_image_before_section_end', 10, 2 );

	// Vamtam_Widget_Image.
	function widgets_registered() {
		class Vamtam_Widget_Image extends \Elementor\Widget_Image {
			public $extra_depended_scripts = [
				'vamtam-image',
			];

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
					'vamtam-image',
					VAMTAM_ELEMENTOR_INT_URL . 'assets/js/widgets/image/vamtam-image' . $suffix . '.js',
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

			protected function render() {
				$settings = $this->get_settings_for_display();

				if ( empty( $settings['image']['url'] ) ) {
					return;
				}

				$has_grow_scale_anim = in_array( $settings['_animation'], [ 'imageGrowWithScaleLeft', 'imageGrowWithScaleRight', 'imageGrowWithScaleTop', 'imageGrowWithScaleBottom' ] );

				$has_caption = $this->has_caption( $settings );

				$this->add_render_attribute( 'wrapper', 'class', 'elementor-image' );

				if ( ! empty( $settings['shape'] ) ) {
					$this->add_render_attribute( 'wrapper', 'class', 'elementor-image-shape-' . $settings['shape'] );
				}

				$link = $this->get_link_url( $settings );

				if ( $link ) {
					$this->add_link_attributes( 'link', $link );

					if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
						$this->add_render_attribute( 'link', [
							'class' => 'elementor-clickable',
						] );
					}

					if ( 'custom' !== $settings['link_to'] ) {
						$this->add_lightbox_data_attributes( 'link', $settings['image']['id'], $settings['open_lightbox'] );
					}
				} ?>
				<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
					<?php if ( $has_grow_scale_anim ) : ?>
						<div class="vamtam-image-wrapper">
					<?php endif; ?>
					<?php if ( $has_caption ) : ?>
						<figure class="wp-caption">
					<?php endif; ?>
					<?php if ( $link ) : ?>
							<a <?php echo $this->get_render_attribute_string( 'link' ); ?>>
					<?php endif; ?>
						<?php echo \Elementor\Group_Control_Image_Size::get_attachment_image_html( $settings ); ?>
					<?php if ( $link ) : ?>
							</a>
					<?php endif; ?>
					<?php if ( $has_caption ) : ?>
							<figcaption class="widget-image-caption wp-caption-text"><?php echo $this->get_caption( $settings ); ?></figcaption>
					<?php endif; ?>
					<?php if ( $has_caption ) : ?>
						</figure>
					<?php endif; ?>
					<?php if ( $has_grow_scale_anim ) : ?>
						</div>
					<?php endif; ?>
				</div>
				<?php
			}

			/**
			 * Render image widget output in the editor.
			 *
			 * Written as a Backbone JavaScript template and used to generate the live preview.
			 *
			 * @since 2.9.0
			 * @access protected
			 */
			protected function content_template() {
				?>
				<# if ( settings.image.url ) {
					var image = {
						id: settings.image.id,
						url: settings.image.url,
						size: settings.image_size,
						dimension: settings.image_custom_dimension,
						model: view.getEditModel()
					};

					var image_url = elementor.imagesManager.getImageUrl( image );

					if ( ! image_url ) {
						return;
					}

					var hasGrowScaleAnim = [ 'imageGrowWithScaleLeft', 'imageGrowWithScaleRight', 'imageGrowWithScaleTop', 'imageGrowWithScaleBottom' ].includes( settings._animation );

					var hasCaption = function() {
						if( ! settings.caption_source || 'none' === settings.caption_source ) {
							return false;
						}
						return true;
					}

					var ensureAttachmentData = function( id ) {
						if ( 'undefined' === typeof wp.media.attachment( id ).get( 'caption' ) ) {
							wp.media.attachment( id ).fetch().then( function( data ) {
								view.render();
							} );
						}
					}

					var getAttachmentCaption = function( id ) {
						if ( ! id ) {
							return '';
						}
						ensureAttachmentData( id );
						return wp.media.attachment( id ).get( 'caption' );
					}

					var getCaption = function() {
						if ( ! hasCaption() ) {
							return '';
						}
						return 'custom' === settings.caption_source ? settings.caption : getAttachmentCaption( settings.image.id );
					}

					var link_url;

					if ( 'custom' === settings.link_to ) {
						link_url = settings.link.url;
					}

					if ( 'file' === settings.link_to ) {
						link_url = settings.image.url;
					}

					#><div class="elementor-image{{ settings.shape ? ' elementor-image-shape-' + settings.shape : '' }}"><#
					var imgClass = '';

					if ( '' !== settings.hover_animation ) {
						imgClass = 'elementor-animation-' + settings.hover_animation;
					}

					if ( hasGrowScaleAnim ) {
						#><div class="vamtam-image-wrapper"><#
					}

					if ( hasCaption() ) {
						#><figure class="wp-caption"><#
					}

					if ( link_url ) {
							#><a class="elementor-clickable" data-elementor-open-lightbox="{{ settings.open_lightbox }}" href="{{ link_url }}"><#
					}
								#><img src="{{ image_url }}" class="{{ imgClass }}" /><#

					if ( link_url ) {
							#></a><#
					}

					if ( hasCaption() ) {
							#><figcaption class="widget-image-caption wp-caption-text">{{{ getCaption() }}}</figcaption><#
					}

					if ( hasCaption() ) {
						#></figure><#
					}

					if ( hasGrowScaleAnim ) {
						#></div><#
					}

					#></div><#
				} #>
				<?php
			}

			/**
			 * Check if the current widget has caption
			 *
			 * @access private
			 * @since 2.3.0
			 *
			 * @param array $settings
			 *
			 * @return boolean
			 */
			private function has_caption( $settings ) {
				return ( ! empty( $settings['caption_source'] ) && 'none' !== $settings['caption_source'] );
			}

			/**
			 * Get the caption for current widget.
			 *
			 * @access private
			 * @since 2.3.0
			 * @param $settings
			 *
			 * @return string
			 */
			private function get_caption( $settings ) {
				$caption = '';
				if ( ! empty( $settings['caption_source'] ) ) {
					switch ( $settings['caption_source'] ) {
						case 'attachment':
							$caption = wp_get_attachment_caption( $settings['image']['id'] );
							break;
						case 'custom':
							$caption = ! \Elementor\Utils::is_empty( $settings['caption'] ) ? $settings['caption'] : '';
					}
				}
				return $caption;
			}

			/**
			 * Retrieve image widget link URL.
			 *
			 * @since 1.0.0
			 * @access protected
			 *
			 * @param array $settings
			 *
			 * @return array|string|false An array/string containing the link URL, or false if no link.
			 */
			protected function get_link_url( $settings ) {
				switch ( $settings['link_to'] ) {
					case 'none':
						return false;

					case 'custom':
						return ( ! empty( $settings['link']['url'] ) ) ? $settings['link'] : false;

					case 'site_url':
						return [ 'url' => \ElementorPro\Plugin::elementor()->dynamic_tags->get_tag_data_content( null, 'site-url' ) ?? '' ];

					default:
						return [ 'url' => $settings['image']['url'] ];
				}
			}

		}

		// Replace current image widget with our extended version.
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets_manager->unregister( 'image' );
		$widgets_manager->register( new Vamtam_Widget_Image );
	}
	add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
}
