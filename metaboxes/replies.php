<?php
global $post;

$status = get_post_meta( $post->ID, '_dev_status', true );
?>

<table class="form-table dev-table-replies">
	<tbody>

		<?php
		/* If the post hasn't been saved yet we do not display the metabox's content */
		if( '' == $status ): ?>

			<div class="updated below-h2" style="margin-top: 2em;">
				<h2 style="margin: 0.5em 0; padding: 0; line-height: 100%;"><?php _e( 'Create Ticket', TEXTDOMAIN ); ?></h2>
				<p><?php _e( 'Please save this ticket to reveal all options.', TEXTDOMAIN ); ?></p>
			</div>

		<?php
		/* Now let's display the real content */
		else:

			/* We're going to get all the posts part of the ticket history */
			$replies_args = array(
				'posts_per_page' => - 1,
				'orderby'        => 'post_date',
				'order'          => dev_get_option( 'replies_order', 'ASC' ),
				'post_type'      => apply_filters( 'dev_replies_post_type', array(
					'ticket_history',
					'ticket_reply'
				) ),
				'post_parent'    => $post->ID,
				'post_status'    => apply_filters( 'dev_replies_post_status', array(
					'publish',
					'inherit',
					'private',
					'trash',
					'read',
					'unread'
				) )
			);

			$history = new \WP_Query( $replies_args );

			if ( ! empty( $history->posts ) ):

				foreach ( $history->posts as $row ):

					// Set the author data (if author is known)
					if ( $row->post_author != 0 ) {
						$user_data = get_userdata( $row->post_author );
						$user_id   = $user_data->data->ID;
						$user_name = $user_data->data->display_name;
					}

					// In case the post author is unknown, we set this as an anonymous post
					else {
						$user_name = __( 'Anonymous', TEXTDOMAIN );
						$user_id   = 0;
					}

					$user_avatar     = get_avatar( $user_id, '64', get_option( 'avatar_default' ) );
					$date            = human_time_diff( get_the_time( 'U', $row->ID ), current_time( 'timestamp' ) );
					$post_type       = $row->post_type;
					$post_type_class = ( 'ticket_reply' === $row->post_type && 'trash' === $row->post_status ) ? 'ticket_history' : $row->post_type;

					/**
					 * This hook is fired just before we open the post row
					 *
					 * @param WP_Post $row Reply post object
					 */
					do_action( 'dev_backend_replies_outside_row_before', $row );
					?>
					<tr valign="top" class="dev-table-row dev-<?php echo str_replace( '_', '-', $post_type_class ); ?> dev-<?php echo str_replace( '_', '-', $row->post_status ); ?>" id="dev-post-<?php echo $row->ID; ?>">
					
						<?php
						/**
						 * This hook is fired just after we opened the post row
						 *
						 * @param WP_Post $row Reply post object
						 */
						do_action( 'dev_backend_replies_inside_row_before', $row );

						switch( $post_type ):

							/* Ticket Reply */
							case 'ticket_reply':

								if ( 'trash' != $row->post_status ) {
									require( DEV_PATH . 'includes/metaboxes/replies-published.php' );
								} elseif ( 'trash' == $row->post_status ) {
									require( DEV_PATH . 'includes/metaboxes/replies-trashed.php' );
								}

								break;

							case 'ticket_history':
								require( DEV_PATH . 'includes/metaboxes/replies-history.php' );
								break;

						endswitch;

						/**
						 * This hook is fired just before we close the post row
						 *
						 * @param WP_Post $row Reply post object
						 */
						do_action( 'dev_backend_replies_inside_row_after', $row );
						?>

					</tr>

					<?php if ( 'ticket_reply' === $post_type && 'trash' !== $row->post_status ): ?>

						<tr class="dev-editor dev-editwrap-<?php echo $row->ID; ?>" style="display:none;">
							<td colspan="2">
								<div class="dev-wp-editor" style="margin-bottom: 1em;"></div>
								<input id="dev-edited-reply-<?php echo $row->ID; ?>" type="hidden" name="edited_reply">
								<input type="submit" id="dev-edit-submit-<?php echo $row->ID; ?>" class="button-primary dev-btn-save-edit" value="<?php _e( 'Save changes', TEXTDOMAIN ); ?>">
								<input type="button" class="dev-editcancel button-secondary" data-origin="#dev-reply-<?php echo $row->ID; ?>" data-replyid="<?php echo $row->ID; ?>" data-reply="dev-editwrap-<?php echo $row->ID; ?>" data-wysiwygid="dev-editreply-<?php echo $row->ID; ?>" value="<?php _e( 'Cancel', TEXTDOMAIN ); ?>">
							</td>
						</tr>

					<?php endif; ?>

					<?php
					/**
					 * dev_backend_replies_outside_row_after hook
					 */
					do_action( 'dev_backend_replies_outside_row_after', $row );
					?>

				<?php endforeach;
			endif;
		endif; ?>
	</tbody>
</table>

<?php
if( 'open' == $status ):

	if( current_user_can( 'reply_ticket' ) ):
		require( DEV_PATH . 'includes/metaboxes/replies-form.php' );
	else: ?>

		<p><?php _e( 'Sorry, you don\'t have sufficient permissions to reply to tickets.', TEXTDOMAIN ); ?></p>

	<?php endif;

/* The ticket was closed */
elseif( 'closed' == $status ): ?>

	<div class="updated below-h2" style="margin-top: 2em;">
		<h2 style="margin: 0.5em 0; padding: 0; line-height: 100%;"><?php _e('Ticket is closed', 'dev'); ?></h2>
		<p><?php printf( __( 'This ticket has been closed. If you want to write a new reply to this ticket, you need to <a href="%s">re-open it first</a>.', TEXTDOMAIN ), dev_get_open_ticket_url( $post->ID ) ); ?></p>
	</div>

<?php endif;
