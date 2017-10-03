<div id="dev-ticket-message" class="dev-ticket-content">
	<?php
	/**
	 * dev_frontend_ticket_content_before hook
	 *
	 * @since  1.0.0
	 */
	do_action( 'dev_backend_ticket_content_before', $post->ID, $post );

	echo apply_filters( 'the_content', $post->post_content );

	/**
	 * dev_backend_ticket_content_after hook
	 *
	 * @since  1.0.0
	 */
	do_action( 'dev_backend_ticket_content_after', $post->ID, $post );
	?>
</div>