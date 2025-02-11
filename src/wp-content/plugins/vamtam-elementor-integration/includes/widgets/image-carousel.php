<?php
namespace VamtamElementor\Widgets\ImageCarousel;

// Called frontend & editor (editor after element loses focus).
add_action( 'elementor/widget/render_content', function( $content, $widget ) {
	if ( 'image-carousel' === $widget->get_name() ) {
		// lazyload images
		$content = str_replace( '<img', '<img loading="lazy"', $content );
	}

	return $content;
 }, 10, 2 );
