<?php
namespace VamtamElementor\Widgets\AnimatedHeadline;

use \ElementorPro\Modules\AnimatedHeadline\Widgets\Animated_Headline as Elementor_Animated_Headline;
use \ElementorPro\Core\Utils as ProUtils;
use \Elementor\Utils;

// Extending the Animated Headline widget.

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

if ( current_theme_supports( 'vamtam-elementor-widgets', 'animated-headline--blurred-letters' ) ) {

	function update_content_tab_controls( $controls_manager, $widget ) {
		// Style.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'headline_style', [
			'options' => [
				'blurred_letters' => __( 'Blurred Letters', 'vamtam-elementor-integration' ),
			]
		] );

		$new_options = [
			'condition' => [
				'headline_style!' => 'blurred_letters',
			]
		];
		// Before Text.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'before_text', $new_options );
		// After Text.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'after_text', $new_options );
		// Infinite Loop.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'loop', [
			'condition' => [
				'headline_style!' => 'blurred_letters',
			]
		] );

	}

	function add_content_tab_controls( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'before_text',
		] );
		$widget->add_control(
			'vamtam_text',
			[
				'label' => __( 'Heading Text', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::TEXT,
				'default' => __( 'Placeholder Text', 'vamtam-elementor-integration' ),
				'condition' => [
					'headline_style' => 'blurred_letters',
				],
				'dynamic' => [
					'active' => true,
				],
				'frontend_available' => true,
			]
		);
		$widget->add_control(
			'vamtam_randomize',
			[
				'type' => \Elementor\Controls_Manager::BUTTON,
				'label' => __( 'Randomize', 'vamtam-elementor-integration' ),
				'button_type' => 'default',
				'text' => __( '<i class="fas fa-dice" style="margin:0;"></i>', 'vamtam-elementor-integration' ),
				'event' => 'vamtam:animated-headline:randomize',
				'condition' => [
					'headline_style' => 'blurred_letters',
				],
				'render_type' => 'template',
			]
		);
		$widget->add_control(
			'vamtam_text_options',
			[
				'label' => __( 'Blurred Text Options', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::TEXTAREA,
				'default' => "1|0\n2|1\n3|2\n4|3\n7|4\n10|5\n15|6",
				'condition' => [
					'headline_style' => 'blurred_letters',
				],
				'placeholder' => __( 'letter_index|animation_delay', 'vamtam-elementor-integration' ),
				'description' => sprintf( __( 'Set the numeric index and animation delay (in seconds) of each letter you want to animate. Each pair in a separate line. Separate letter index from the animation delay using %s character.', 'vamtam-elementor-integration' ), '<code>|</code>' ),
				'classes' => 'elementor-control-direction-ltr',
			]
		);
		$widget->add_control(
			'vamtam_animation_speed',
			[
				'label' => __( 'Animation Speed', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SLIDER,
				'description' => __( 'Sets the duration (in seconds) of the blur animation for all letters.', 'vamtam-elementor-integration' ),
				'selectors' => [
					'{{WRAPPER}} .vamtam-letter.blurred' => 'animation-duration: {{SIZE}}s !important',
					'{{WRAPPER}} .vamtam-letter:not(.blurred)' => 'animation-duration: {{SIZE}}s !important',
				],
				'condition' => [
					'headline_style' => 'blurred_letters',
				],
				'range' => [
					'px' => [
						'max' => 10,
						'min' => 0,
						'step' => 0.01,
					],
				],
				'render_type' => 'template',
			]
		);
		$widget->end_injection();
	}

	function text_elements_before_section_end( $widget ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		update_content_tab_controls( $controls_manager, $widget );
		add_content_tab_controls( $controls_manager, $widget );
	}
	add_action( 'elementor/element/vamtam-animated-headline/text_elements/before_section_end', __NAMESPACE__ . '\text_elements_before_section_end' );

	function update_style_tab_controls( $controls_manager, $widget ) {
		// Text Color.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'title_color', [
			'selectors' => [
				'{{WRAPPER}} .vamtam-word :not(.blurred)' => 'color: {{VALUE}}',
			],
		] );
		// Text Transform.
		\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'title_typography_text_transform', [
			'default' => 'none',
			'options' => [
				'none' => __( 'Default', 'vamtam-elementor-integration' ),
				'uppercase' => _x( 'Uppercase', 'Typography Control', 'vamtam-elementor-integration' ),
				'lowercase' => _x( 'Lowercase', 'Typography Control', 'vamtam-elementor-integration' ),
			],
		] );
		// Animated Text Color.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'words_color', [
			'selectors' => [
				'{{WRAPPER}} .vamtam-word .blurred' => 'color: {{VALUE}}',
			],
		] );
		// Animated Text Typography.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'words_typography', [
				'selector' => '{{WRAPPER}} .vamtam-word .blurred',
			],
			\Elementor\Group_Control_Typography::get_type()
		);
	}

	function section_style_text_before_section_end( $widget ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		update_style_tab_controls( $controls_manager, $widget );
	}
	add_action( 'elementor/element/vamtam-animated-headline/section_style_text/before_section_end', __NAMESPACE__ . '\section_style_text_before_section_end' );

	// Vamtam_Widget_Animated_Headline.
	function widgets_registered() {
		// Is Pro Widget.
		if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
			return;
		}

		if ( ! class_exists( '\ElementorPro\Modules\AnimatedHeadline\Widgets\Animated_Headline' ) ) {
			return; // Elementor's autoloader acts weird sometimes.
		}

		class Vamtam_Widget_Animated_Headline extends Elementor_Animated_Headline {
			// The name change is so the widget's default javascript handler
			// doesn't register and run before we can edit it. Issue: https://github.com/elementor/elementor/issues/11671
			// We woudn't need this if we could do that: https://github.com/elementor/elementor/issues/11686
			public function get_name() {
				return 'vamtam-animated-headline';
			}

			public $extra_depended_scripts = [
				'vamtam-animated-headline',
			];

			/*
				When inline css mode is active (check elementor's is_inline_css_mode())
				we need to inline the parent widget's css. That's cause of the widget's
				name change.
			*/
			public function get_inline_css_depends() {
				return [ 'animated-headline' ];
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
					'vamtam-animated-headline',
					VAMTAM_ELEMENTOR_INT_URL . 'assets/js/widgets/animated-headline/vamtam-animated-headline' . $suffix . '.js',
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
				$tag      = '';

				// validate_html_tag() was transferred from Pro to Core.
				if ( method_exists( '\Elementor\Utils', 'validate_html_tag' ) ) {
				$tag = Utils::validate_html_tag( $settings['tag'] );
				} elseif ( method_exists( '\ElementorPro\Core\Utils', 'validate_html_tag' ) ) {
					$tag = ProUtils::validate_html_tag( $settings['tag'] );
				}

				$this->add_render_attribute( 'headline', 'class', 'elementor-headline' );

				if ( 'rotate' === $settings['headline_style'] ) {
					$this->add_render_attribute( 'headline', 'class', 'elementor-headline-animation-type-' . $settings['animation_type'] );

					$is_letter_animation = in_array( $settings['animation_type'], [ 'typing', 'swirl', 'blinds', 'wave' ] );

					if ( $is_letter_animation ) {
						$this->add_render_attribute( 'headline', 'class', 'elementor-headline-letters' );
					}
				}

				if ( ! empty( $settings['link']['url'] ) ) {
					$this->add_link_attributes( 'url', $settings['link'] );

					echo '<a ' . $this->get_render_attribute_string( 'url' ) . '>';
				}

				$is_blurred_letters = $settings['headline_style'] === 'blurred_letters';
				$text_options       = $is_blurred_letters ? $this->get_normalized_text_options( $settings['vamtam_text_options'] ) : [];
				$has_open_word      = false;
				?>
				<<?php echo $tag; ?> <?php echo $this->get_render_attribute_string( 'headline' ); ?>>
					<?php if ( $is_blurred_letters ) : ?>
						<?php
							$settings['vamtam_text'] = html_entity_decode( $settings['vamtam_text'] );
						?>
						<?php foreach ( preg_split( "//u", $settings['vamtam_text'], -1, PREG_SPLIT_NO_EMPTY ) as $index => $letter ) : ?>
							<?php
								if ( ! $has_open_word ) {
									echo '<span class="vamtam-word">';
									$has_open_word = true;
								}

								if ( ctype_space( $letter ) ) {
									if ( $has_open_word ) {
										echo '</span>';
										$has_open_word = false;
									}
									echo esc_html( $letter ); // Whitespace.
									continue;
								}
								$letter_has_anim = array_key_exists( $index + 1, $text_options );
							?>
							<?php if ( $letter_has_anim ) : ?>
								<span class="vamtam-letter blurred" data-delay="<?php echo esc_attr( $text_options[ $index + 1 ] ); ?>"><?php echo esc_html( $letter ); ?></span>
							<?php else : ?>
								<span class="vamtam-letter"><?php echo esc_html( $letter ); ?></span>
							<?php endif; ?>
						<?php endforeach; ?>
						<?php
							if ( $has_open_word ) {
								echo '</span>';
								$has_open_word = false;
							}
						?>
					<?php else : ?>
						<?php if ( ! empty( $settings['before_text'] ) ) : ?>
							<span class="elementor-headline-plain-text elementor-headline-text-wrapper"><?php echo $settings['before_text']; ?></span>
						<?php endif; ?>
						<span class="elementor-headline-dynamic-wrapper elementor-headline-text-wrapper">
						<?php if ( 'rotate' === $settings['headline_style'] && $settings['rotating_text'] ) :
							$rotating_text = explode( "\n", $settings['rotating_text'] );
							foreach ( $rotating_text as $key => $text ) :
								$status_class = 1 > $key ? 'elementor-headline-text-active' : ''; ?>
							<span class="elementor-headline-dynamic-text <?php echo $status_class; ?>">
								<?php echo str_replace( ' ', '&nbsp;', $text ); ?>
							</span>
						<?php endforeach; ?>
						<?php elseif ( 'highlight' === $settings['headline_style'] && ! empty( $settings['highlighted_text'] ) ) : ?>
							<span class="elementor-headline-dynamic-text elementor-headline-text-active"><?php echo $settings['highlighted_text']; ?></span>
						<?php endif ?>
						</span>
						<?php if ( ! empty( $settings['after_text'] ) ) : ?>
							<span class="elementor-headline-plain-text elementor-headline-text-wrapper"><?php echo $settings['after_text']; ?></span>
							<?php endif; ?>
					<?php endif; ?>
				</<?php echo $tag; ?>>
				<?php

				if ( ! empty( $settings['link']['url'] ) ) {
					echo '</a>';
				}
			}

			// Exntend content_template method.
			protected function content_template() {
				?>
				<#
				function get_normalized_text_options( text_options ) {
					if ( ! text_options  ) {
						return {};
					}

					var text_options_arr = text_options.split( "\n" ),
						normalized       = {};

					if ( ! text_options_arr.length ) {
						return {};
					}

					text_options_arr.forEach( function( prop ) {
						var prop_key_value = prop.split( '|' ) || [],
							prop_key = prop_key_value && prop_key_value.length && prop_key_value[ 0 ] ? prop_key_value[ 0 ].trim() : '',
							prop_value = prop_key_value && prop_key_value.length && prop_key_value[ 1 ] ? prop_key_value[ 1 ].trim() : '';

						if ( ! prop_key || ! prop_key_value ) {
							return;
						}
						normalized[ prop_key ] = prop_value;
					} );

					return normalized;
				}

				function randomizeBlurredLetters( view ) {
					var letters      = settings.vamtam_text,
						lettersArr   = [],
						durationsArr = [],
						text_options = '';

					function genRand( min, max, decimalPlaces ) {
						const rand  = Math.random() < 0.5 ? ( ( 1-Math.random() ) * ( max - min ) + min ) : ( Math.random() * ( max-min ) + min );  // could be min or max or anything in between
						const power = Math.pow( 10, decimalPlaces );
						return Math.floor( rand * power ) / power;
					}

					// Unique random letters.
					while( lettersArr.length < genRand( 1, letters.length, 0 ) ){
						const r = genRand( 1, letters.length, 0 );
						if ( lettersArr.indexOf( r ) === -1 ) {
							lettersArr.push( r );
							durationsArr.push( genRand( 0, 0.5, 2 ) );
						}
					}

					// Generate the final string.
					for ( let i = 0; i < lettersArr.length; i++ ) {
						text_options = text_options + lettersArr[ i ] + '|' + durationsArr[ i ] + '\n';
					}

					// Update control value.
					view._parent.model.setSetting( 'vamtam_text_options', text_options );

					// Trigger control update (to update the UI).
					jQuery( view.el ).next( '.elementor-control-vamtam_text_options' ).find( 'textarea' ).trigger( 'input' );
				}

				const validateHTMLTag = function(tag) {
					var validHTMLTags = ['article', 'aside', 'div', 'footer', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'header', 'main', 'nav', 'p', 'section', 'span'];
					return validHTMLTags.includes(tag.toLowerCase()) ? tag : 'div';
				}

				var headlineClasses = 'elementor-headline',
					tag = validateHTMLTag( settings.tag );

				if ( 'rotate' === settings.headline_style ) {
					headlineClasses += ' elementor-headline-animation-type-' + settings.animation_type;

					var isLetterAnimation = -1 !== [ 'typing', 'swirl', 'blinds', 'wave' ].indexOf( settings.animation_type );

					if ( isLetterAnimation ) {
						headlineClasses += ' elementor-headline-letters';
					}
				}

				var is_blurred_letters = settings.headline_style === 'blurred_letters',
					text_options       = is_blurred_letters ? get_normalized_text_options( settings.vamtam_text_options ) : [],
					has_open_word      = false;

				if ( is_blurred_letters ) {
					// vamtam_randomize (onClick).
					elementor.channels.editor.on( 'vamtam:animated-headline:randomize', randomizeBlurredLetters );
				}

				if ( settings.link.url ) { #>
					<a href="#">
				<# } #>
						<{{{ tag }}} class="{{{ headlineClasses }}}">
							<# if ( is_blurred_letters ) { #>
								<# _.each( settings.vamtam_text, function( letter, index ) { #>
									<# if ( ! has_open_word ) { #>
										<span class="vamtam-word">
										<# has_open_word = true; #>
									<# } #>
									<# if ( letter.trim() == '' ) { #>
										<# if ( has_open_word ) { #>
											</span>
											<# has_open_word = false; #>
										<# } #>
										<# {{{ letter }}} // Whitespace. #>
										<# return; #>
									<# } #>
									<# var letter_has_anim = text_options[ index + 1 ]; #>
									<# if ( letter_has_anim ) { #>
										<span class="vamtam-letter blurred" data-delay="{{{ text_options[ index + 1 ] }}}">{{{ letter }}}</span>
									<# } else { #>
										<span class="vamtam-letter">{{{ letter }}}</span>
									<# } #>
								<# } ); #>
								<# if ( has_open_word ) { #>
									</span>
									<# has_open_word = false; #>
								<# } #>
							<# } else { #>
								<# if ( settings.before_text ) { #>
									<span class="elementor-headline-plain-text elementor-headline-text-wrapper">{{{ settings.before_text }}}</span>
								<# } #>

								<# if ( settings.rotating_text ) { #>
									<span class="elementor-headline-dynamic-wrapper elementor-headline-text-wrapper">
									<# if ( 'rotate' === settings.headline_style && settings.rotating_text ) {
										var rotatingText = ( settings.rotating_text || '' ).split( '\n' );
										for ( var i = 0; i < rotatingText.length; i++ ) {
											var statusClass = 0 === i ? 'elementor-headline-text-active' : ''; #>
											<span class="elementor-headline-dynamic-text {{ statusClass }}">
												{{{ rotatingText[ i ].replace( ' ', '&nbsp;' ) }}}
											</span>
										<# }
									}

									else if ( 'highlight' === settings.headline_style && settings.highlighted_text ) { #>
										<span class="elementor-headline-dynamic-text elementor-headline-text-active">{{ settings.highlighted_text }}</span>
									<# } #>
									</span>
								<# } #>

								<# if ( settings.after_text ) { #>
									<span class="elementor-headline-plain-text elementor-headline-text-wrapper">{{{ settings.after_text }}}</span>
									<# } #>
							<# } #>
						</{{{ tag }}}>
				<# if ( settings.link.url ) { #>
					</a>
				<# } #>
				<?php
			}

			protected function get_normalized_text_options( $text_options ) {
				if ( empty( $text_options ) ) {
					return [];
				}

				$text_options_arr = explode( "\n", $text_options );
				$normalized       = [];

				foreach ( $text_options_arr as $prop ) {
					// Trim in case users inserted unwanted spaces
					$prop_key_value = explode( '|', $prop );

					$prop_key = $prop_key_value[0];

					// Cover cases where key/value have spaces both before and/or after the actual value
					preg_match( '/[^=]+/', $prop_key, $prop_key_matches );

					$prop_key = isset( $prop_key_matches[0] ) ? trim( $prop_key_matches[0] ) : "";

					if ( isset( $prop_key_value[1] ) ) {
						$prop_value = trim( $prop_key_value[1] );
					} else {
						$prop_value = '';
					}

					$normalized[ $prop_key ] = $prop_value;
				}
				return $normalized;
			}
		}

		// Replace current animated-headline widget with our extended version.
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets_manager->unregister( 'animated-headline' );
		$widgets_manager->register( new Vamtam_Widget_Animated_Headline );
	}
	add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
}
