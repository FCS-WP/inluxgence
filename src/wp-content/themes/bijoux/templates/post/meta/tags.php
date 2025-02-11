<?php
	$tags = get_the_tags();

	if ( $tags ) : ?>
		<div class="the-tags vamtam-meta-tax">
			<?php the_tags( '<strong class="' . ( is_single() ? '' : 'visuallyhidden' ) . '">' . esc_html__( 'Tags:', 'bijoux' ) . '</strong> ', ', ', '' ); ?>
		</div>
<?php
	endif;
