<?php
class Vamtam_Elementor_Widgets_Handler {
	protected static $styles_appended = [];
	protected static $widgets_checked = [];

	private static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	public function __construct() {
		$should_handle = current_theme_supports( 'vamtam-elementor-widgets' );

		if ( $should_handle ) {
			$this->handle();
		}
	}

	public function handle() {
		$this->register_custom_widgets();

		// if ( VamtamElementorIntregration::theme_loaded() ) {
		// 	$this->register_custom_widget_styles_frontend();
		// }
	}

	/*
		Registers our custom widgets.

		! Not to be confused with the overrides of current widgets. !

		The styles for custom widgets don't have to come from the theme (check vamtam-instagram-feed widget implementation)
		but each theme could potentially override them. In the latter case, register_custom_widget_styles_frontend() would
		be used to add those styles.
	*/
	public function register_custom_widgets() {
		// Base Vamtam Widget
		require_once VAMTAM_ELEMENTOR_INT_DIR . 'includes/widgets/vamtam/base/vamtam-widget.php';

		// Vamtam Widgets.
		foreach( glob( VAMTAM_ELEMENTOR_INT_DIR . 'includes/widgets/vamtam/*.php' ) as $widget ) {
			require_once $widget;
		}
	}

	// Registers the custom theme-based styles for elementor widgets.
	public function register_custom_widget_styles_frontend() {
		// Frontend-only hook.
		add_action( 'elementor/element/parse_css', [ __CLASS__, 'append_custom_widget_styles_frontend' ], 100, 2 );
		add_action( 'wp_print_styles', [ __CLASS__, 'fix_stylesheet_dummy_breakpoints' ] );
	}


	// TODO: Remove this hack if they ever provide this: https://github.com/elementor/elementor/issues/11156
	public static function fix_stylesheet_dummy_breakpoints() {
		$stylesheet_handle = 'elementor-frontend';
		$has_inline_styles = isset( wp_styles()->registered[ $stylesheet_handle ] ) && isset( wp_styles()->registered[ $stylesheet_handle ]->extra['after'] );
		if ( $has_inline_styles ) {
			$inline_css = &wp_styles()->registered[ $stylesheet_handle ]->extra['after'];
			foreach( $inline_css as &$css ) {
				$pattern = '/(@media\((min|max)-width: (11|12|13)px)/';
				if ( preg_match( $pattern, $css ) ) {
					$medium_breakpoint = VamtamElementorBridge::get_site_breakpoints( 'lg' );
					$small_breakpoint  = VamtamElementorBridge::get_site_breakpoints( 'md' );

					//layout-small replacment.
					$css = str_replace( '@media(max-width: 11px', '@media(max-width: ' . ( $small_breakpoint - 1 ) . 'px', $css );
					//layout-below-max replacment.
					$css = str_replace( '@media(max-width: 12px', '@media(max-width: ' . ( $medium_breakpoint - 1 ) . 'px', $css );
					//layout-max replacment.
					$css = str_replace( '@media(max-width: 13px', '@media(min-width: ' . $medium_breakpoint . 'px', $css );
				}
			}
		}
	}

	// This is required so the responsive styles we enqueue for the frontend
	// are aligned with the theme's (max/below-max/small) breakpoints.
	public static function align_stylesheet_breakpoint_with_theme( $post_css, $element ) {
		// Safer to define our own device breakpoints.
		$post_css->get_stylesheet()->add_device( 'layout-small', 11 ); // dummy cause same value device breakpoint creates a conflict, also to disable styles in post-{$id}.css.
		$post_css->get_stylesheet()->add_device( 'layout-below-max', 12 ); // dummy cause same value device breakpoint creates a conflict, also to disable styles in post-{$id}.css.
		$post_css->get_stylesheet()->add_device( 'layout-max', 13 ); // dummy used for easy replacement cause we can't recreate theme's min-width for layout-max, also to disable styles in post-{$id}.css..
	}

	// Appends the custom widget styles (theme based) to the post's css (inline).
	public static function append_custom_widget_styles_frontend( $post_css, $element ) {
		if ( $post_css instanceof Dynamic_CSS ) {
			return;
		}

		$widget_name  = $element->get_name();

		// Have we already checked or appended the styles for this widget?
		if ( in_array( $widget_name, self::$styles_appended ) || in_array( $widget_name, self::$widgets_checked ) ) {
			return;
		}

		// Do we have custom (theme) styles for this widget?
		$styles_dir   = VAMTAM_ELEMENTOR_STYLES_DIR . "widgets/{$widget_name}";
		$styles_exist = file_exists( $styles_dir );

		if ( $styles_exist ) {
			// Map with theme's breakpoints.
			$device_map = [
				'mobile'  => 'layout-small',
				'tablet'  => 'layout-below-max',
				'desktop' => 'layout-max',
			];

			// Append widget styles.
			foreach ( [ '', '.desktop', '.tablet', '.mobile' ] as $device ) {
				$css_file = $styles_dir . "/{$widget_name}{$device}.css";
				if ( file_exists( $css_file ) ) {
					self::align_stylesheet_breakpoint_with_theme( $post_css, $element );
					$css        = file_get_contents( $css_file );
					$breakpoint = ! empty( $device ) ? $device_map[ ltrim( $device, '.') ] : '';
					$post_css->get_stylesheet()->add_raw_css( $css, $breakpoint );
				}
			}

			// Don't append for every widget instance.
			self::$styles_appended[] = $widget_name;
		} else {
			// Don't check again for every widget instance.
			self::$widgets_checked[] = $widget_name;
		}
	}

	// Editor (preview) styles, coming from the theme.
	public static function enqueue_theme_editor_styles() {
		if ( VamtamElementorIntregration::theme_loaded() ) {
			$medium_breakpoint = VamtamElementorBridge::get_site_breakpoints( 'lg' );
			$small_breakpoint  = VamtamElementorBridge::get_site_breakpoints( 'md' );

			$media = array(
				'desktop' => "(min-width: {$medium_breakpoint}px)",
				'tablet'  => '(max-width: ' . ( $medium_breakpoint - 1 ) . 'px)',
				'mobile'  => '(max-width: ' . ( $small_breakpoint - 1 ) . 'px)',
				''        => 'all',
			);

			foreach ( [ '', '.desktop', '.tablet', '.mobile' ] as $device ) {
				wp_enqueue_style(
					'vamtam-theme-elementor-editor-' . ltrim( $device, '.'),
					VAMTAM_ELEMENTOR_STYLES_URI . "editor/editor{$device}.css",
					[],
					VamtamElementorIntregration::PLUGIN_VERSION,
					$media[ ltrim( $device, '.') ]
				);
			}
		}
	}
}
