<?php
namespace VamtamElementor\Widgets\ProductMeta;

use \ElementorPro\Modules\Woocommerce\Widgets\Product_Meta as Elementor_Product_Meta;
// Extending the WC Product Meta widget.

// Is WC Widget.
if ( ! vamtam_has_woocommerce() ) {
	return;
}

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

if ( vamtam_theme_supports( 'woocommerce-product-meta--meta-filter' ) ) {

	function add_meta_filter_controls_style_tab( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'divider',
		] );
		$widget->add_control(
			'show_sku',
			[
				'label' => __( 'Show SKU', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'render_type' => 'template',
				'default' => 'yes',
			]
		);
		$widget->add_control(
			'show_category',
			[
				'label' => __( 'Show Category', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'render_type' => 'template',
				'default' => 'yes',
			]
		);
		$widget->add_control(
			'show_tags',
			[
				'label' => __( 'Show Tags', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'render_type' => 'template',
				'default' => 'yes',
			]
		);
		$widget->end_injection();
	}
	function update_controls_style_tab( $controls_manager, $widget ) {
		// Text Typography.
		\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'text_typography', [
			'selector' => '{{WRAPPER}} span.detail-label',
		], \Elementor\Group_Control_Typography::get_type() );
		// Text Color.
		\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'text_color', [
			'selectors' => [
				'{{WRAPPER}} span.detail-label' => 'color: {{VALUE}}',
			],
		] );
		// Link Typography.
		\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'link_typography', [
			'selector' => '{{WRAPPER}} span.detail-content a',
		], \Elementor\Group_Control_Typography::get_type() );
		// Link Color.
		\Vamtam_Elementor_Utils::replace_control_options( $controls_manager, $widget, 'link_color', [
			'selectors' => [
				'{{WRAPPER}} span.detail-content a' => 'color: {{VALUE}}',
			],
		] );
	}

	function add_captions_controls_style_tab( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'heading_category_caption',
		] );
		$widget->add_control(
			'show_category_label',
			[
				'label' => __( 'Show Label', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'render_type' => 'template',
				'default' => 'yes',
				'condition' => [
					'show_category!' => '',
				],
			]
		);
		$widget->end_injection();
		$widget->start_injection( [
			'of' => 'heading_tag_caption',
		] );
		$widget->add_control(
			'show_tag_label',
			[
				'label' => __( 'Show Label', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'render_type' => 'template',
				'default' => 'yes',
				'condition' => [
					'show_tags!' => '',
				],
			]
		);
		$widget->end_injection();
		$widget->start_injection( [
			'of' => 'heading_sku_caption',
		] );
		$widget->add_control(
			'show_sku_label',
			[
				'label' => __( 'Show Label', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'render_type' => 'template',
				'default' => 'yes',
				'condition' => [
					'show_sku!' => '',
				],
			]
		);
		$widget->end_injection();
	}
	function update_captions_controls_style_tab( $controls_manager, $widget ) {
		// Category Heading.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'heading_category_caption', [
			'condition' => [
				'show_category!' => '',
			],
		] );
		// Category Singular.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'category_caption_single', [
			'condition' => [
				'show_category_label!' => '',
				'show_category!' => '',
			],
		] );
		// Category Plural.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'category_caption_plural', [
			'condition' => [
				'show_category_label!' => '',
				'show_category!' => '',
			],
		] );
		// Tag Heading.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'heading_tag_caption', [
			'condition' => [
				'show_tags!' => '',
			],
		] );
		// Tag Singular.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'tag_caption_single', [
			'condition' => [
				'show_tag_label!' => '',
				'show_tags!' => '',
			],
		] );
		// Tag Plural.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'tag_caption_plural', [
			'condition' => [
				'show_tag_label!' => '',
				'show_tags!' => '',
			],
		] );
		// SKU Heading.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'heading_sku_caption', [
			'condition' => [
				'show_sku!' => '',
			],
		] );
		// Sku.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'sku_caption', [
			'condition' => [
				'show_sku_label!' => '',
				'show_sku!' => '',
			],
		] );
		// Sku Missing.
		\Vamtam_Elementor_Utils::add_control_options( $controls_manager, $widget, 'sku_missing_caption', [
			'condition' => [
				'show_sku_label!' => '',
				'show_tags!' => '',
			],
		] );
	}

	function section_product_meta_captions_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		add_captions_controls_style_tab( $controls_manager, $widget );
		update_captions_controls_style_tab( $controls_manager, $widget );
	}
	add_action( 'elementor/element/woocommerce-product-meta/section_product_meta_captions/before_section_end', __NAMESPACE__ . '\section_product_meta_captions_before_section_end', 10, 2 );

	// Vamtam_Widget_Product_Meta.
	function widgets_registered() {
		if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
			return;
		}

		if ( ! class_exists( '\ElementorPro\Modules\Woocommerce\Widgets\Product_Meta' ) ) {
			return; // Elementor's autoloader acts weird sometimes.
		}

		class Vamtam_Widget_Product_Meta extends Elementor_Product_Meta {
			// Override.
			protected function render() {
				global $product;

				$product = wc_get_product();

				if ( empty( $product ) ) {
					return;
				}

				$sku = $product->get_sku();

				$settings = $this->get_settings_for_display();
				$sku_caption = ! empty( $settings['sku_caption'] ) ? $settings['sku_caption'] : __( 'SKU', 'vamtam-elementor-integration' );
				$sku_missing = ! empty( $settings['sku_missing_caption'] ) ? $settings['sku_missing_caption'] : __( 'N/A', 'vamtam-elementor-integration' );
				$category_caption_single = ! empty( $settings['category_caption_single'] ) ? $settings['category_caption_single'] : __( 'Category', 'vamtam-elementor-integration' );
				$category_caption_plural = ! empty( $settings['category_caption_plural'] ) ? $settings['category_caption_plural'] : __( 'Categories', 'vamtam-elementor-integration' );
				$tag_caption_single = ! empty( $settings['tag_caption_single'] ) ? $settings['tag_caption_single'] : __( 'Tag', 'vamtam-elementor-integration' );
				$tag_caption_plural = ! empty( $settings['tag_caption_plural'] ) ? $settings['tag_caption_plural'] : __( 'Tags', 'vamtam-elementor-integration' );
				?>
				<div class="product_meta">

					<?php do_action( 'woocommerce_product_meta_start' ); ?>

					<?php if ( wc_product_sku_enabled() && ( $sku || $product->is_type( 'variable' ) ) && ! empty( $settings['show_sku'] ) ) : ?>
						<span class="sku_wrapper detail-container">
							<?php if ( ! empty( $settings['show_sku_label'] ) ) : ?>
								<span class="detail-label"><?php echo esc_html( $sku_caption ); ?></span>
							<?php endif; ?>
							<span class="sku"><?php echo $sku ? $sku : esc_html( $sku_missing ); ?></span>
						</span>
					<?php endif; ?>

					<?php if ( count( $product->get_category_ids() ) && ! empty( $settings['show_category'] ) ) : ?>
						<span class="posted_in detail-container">
							<?php if ( ! empty( $settings['show_category_label'] ) ) : ?>
								<span class="detail-label"><?php echo esc_html( $this->get_plural_or_single( $category_caption_single, $category_caption_plural, count( $product->get_category_ids() ) ) ); ?></span>
							<?php endif; ?>
							<span class="detail-content"><?php echo get_the_term_list( $product->get_id(), 'product_cat', '', ', ' ); ?></span>
						</span>
					<?php endif; ?>

					<?php if ( count( $product->get_tag_ids() ) && ! empty( $settings['show_tags'] ) ) : ?>
						<span class="tagged_as detail-container">
							<?php if ( ! empty( $settings['show_tag_label'] ) ) : ?>
								<span class="detail-label"><?php echo esc_html( $this->get_plural_or_single( $tag_caption_single, $tag_caption_plural, count( $product->get_tag_ids() ) ) ); ?></span>
							<?php endif; ?>
							<span class="detail-content"><?php echo get_the_term_list( $product->get_id(), 'product_tag', '', ', ' ); ?></span>
						</span>
					<?php endif; ?>

					<?php do_action( 'woocommerce_product_meta_end' ); ?>

				</div>
				<?php
			}

			private function get_plural_or_single( $single, $plural, $count ) {
				return 1 === $count ? $single : $plural;
			}
		}

		// Replace current products widget with our extended version.
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets_manager->unregister( 'woocommerce-product-meta' );
		$widgets_manager->register( new Vamtam_Widget_Product_Meta );
	}
	add_action( \Vamtam_Elementor_Utils::get_widgets_registration_hook(), __NAMESPACE__ . '\widgets_registered', 100 );
}

if ( vamtam_theme_supports( 'woocommerce-product-meta--fitness-meta-style' ) ) {
	// Called frontend & editor (editor after element loses focus).
	function render_content( $content, $widget ) {
		if ( 'woocommerce-product-meta' === $widget->get_name() ) {
			$settings = $widget->get_settings();

			if ( ! empty( $settings['use_theme_style'] ) ) {
				// Removing the commas.
				$content = str_replace( ',', '', $content );
			}
		}

		return $content;
	}
	add_filter( 'elementor/widget/render_content', __NAMESPACE__ . '\render_content', 10, 2 );

	function use_theme_btn_style_control( $controls_manager, $widget ) {
		$widget->start_injection( [
			'of' => 'wc_style_warning',
		] );
		$widget->add_control(
			'use_theme_style',
			[
				'label' => __( 'Use Theme Style', 'vamtam-elementor-integration' ),
				'type' => $controls_manager::SWITCHER,
				'prefix_class' => 'vamtam-has-',
				'return_value' => 'theme-style',
				'default' => 'theme-style',
				'render_type' => 'template',
				'condition' => [
					'view' => 'stacked',
				],
			]
		);
		$widget->end_injection();
	}
}

if ( vamtam_theme_supports( [ 'woocommerce-product-meta--meta-filter', 'woocommerce-product-meta--fitness-meta-style' ] ) ) {
	function section_product_meta_style_before_section_end( $widget, $args ) {
		$controls_manager = \Elementor\Plugin::instance()->controls_manager;
		if ( vamtam_theme_supports( 'woocommerce-product-meta--meta-filter' ) ) {
			add_meta_filter_controls_style_tab( $controls_manager, $widget );
			update_controls_style_tab( $controls_manager, $widget );
		}
		if ( vamtam_theme_supports( 'woocommerce-product-meta--fitness-meta-style' ) ) {
			use_theme_btn_style_control( $controls_manager, $widget );
		}
	}
	add_action( 'elementor/element/woocommerce-product-meta/section_product_meta_style/before_section_end', __NAMESPACE__ . '\section_product_meta_style_before_section_end', 10, 2 );
}
