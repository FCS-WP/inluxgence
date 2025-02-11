<?php
$show         = comments_open();
$comment_icon = vamtam_get_icon_html( array(
	'name'       => 'vamtam-theme-bubble',
	'link_hover' => false,
) );
?>
<?php if ( $show ) : ?>
	<div class="comment-count vamtam-meta-comments">
		<?php
			comments_popup_link(
				$comment_icon . wp_kses( __( '0 <span class="comment-word ">Comments</span>', 'bijoux' ), 'vamtam-a-span' ),
				$comment_icon . wp_kses( __( '1 <span class="comment-word ">Comment</span>', 'bijoux' ), 'vamtam-a-span' ),
				$comment_icon . wp_kses( __( '% <span class="comment-word ">Comments</span>', 'bijoux' ), 'vamtam-a-span' )
			);
		?>
	</div>
<?php endif; ?>
