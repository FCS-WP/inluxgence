<?php
namespace VamtamElementor\Widgets\GoogleMaps;

// Extending the Icon Box widget.

// Called frontend & editor (editor after element loses focus).
add_filter( 'elementor/widget/render_content', function( $content, $widget ) {
	if ( 'google_maps' === $widget->get_name() ) {
		$content = str_replace( 'frameborder="0" scrolling="no" marginheight="0" marginwidth="0"', 'style="border:0;overflow:hidden;margin:0"', $content );
		$content = str_replace( '<iframe', '<iframe loading="lazy"', $content );
	}

	return $content;
 }, 10, 2 );
