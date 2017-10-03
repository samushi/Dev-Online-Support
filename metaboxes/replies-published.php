<?php
/**
 * @package   DevSupport/Admin/Reply
 * @author    Sami Maxhuni <samimaxhuni510@gmail.com>
 * @license   GPL-2.0+
 * @link      http://devsolution.info
 * @copyright 2017 DevSolution
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
?>

<td class="col1" style="width: 64px;">

	<?php
	/* Display avatar only for replies */
	if( 'ticket_reply' == $row->post_type ) {

		echo $user_avatar;

		/**
		 * Triggers an action right under the user avatar for ticket replies.
		 *
		 * @since 1.0.0
		 *
		 * @param int $row->ID The current reply ID
		 * @param int $user_id The reply author user ID
		 */
		do_action( 'dev_mb_replies_under_avatar', $row->ID, $user_id );

	}
	?>

</td>
<td class="col2">

	<?php if ( 'unread' === $row->post_status ): ?><div id="dev-unread-<?php echo $row->ID; ?>" class="dev-unread-badge"><?php _e( 'Unread', TEXTDOMAIN ); ?></div><?php endif; ?>
	<div class="dev-reply-meta">
		<div class="dev-reply-user">
			<strong class="dev-profilename"><?php echo $user_name; ?></strong> <span class="dev-profilerole">(<?php echo dev_get_user_nice_role( $user_data->roles[0] ); ?>)</span>
		</div>
		<div class="dev-reply-time">
			<time class="dev-timestamp" datetime="<?php echo get_the_date( 'Y-m-d\TH:i:s' ) . dev_get_offset_html5(); ?>"><span class="dev-human-date"><?php echo date( get_option( 'date_format' ), strtotime( $row->post_date ) ); ?> | </span><?php printf( __( '%s ago', TEXTDOMAIN ), $date ); ?></time>
		</div>
	</div>

	<div class="dev-ticket-controls">
		<?php

		$ticket_id = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );

		/**
		 * Fires before the ticket reply controls (mark as read, delete, edit...) are displayed
		 *
		 * @since 1.0.0
		 *
		 * @param int     $ticket_id ID of the current ticket
		 * @param WP_Post $row       Current reply post object
		 */
		do_action( 'dev_ticket_reply_controls_before', $ticket_id, $row );

		/**
		 * Ticket reply controls
		 *
		 * @since 3.2.6
		 */
		$controls = apply_filters( 'dev_ticket_reply_controls', array(), $ticket_id, $row );

		if ( ! empty( $controls ) ) {

			$output = array();

			foreach ( $controls as $control_id => $control ) {
				array_push( $output, $control );
			}

			echo implode( ' | ', $output );
		}

		/**
		 * Fires after the ticket reply controls (mark as read, delete, edit...) are displayed
		 *
		 * @since 1.0.0
		 *
		 * @param int     $ticket_id ID of the current ticket
		 * @param WP_Post $row       Current reply post object
		 */
		do_action( 'dev_ticket_reply_controls_after', $ticket_id, $row );
		?>
	</div>

	<?php
	/* Filter the content before we display it */
	$content = apply_filters( 'the_content', $row->post_content );

	/* The content displayed to agents */
	echo '<div class="dev-reply-content" id="dev-reply-' . $row->ID . '">';

	/**
	 * dev_backend_reply_content_before hook
	 *
	 * @since  1.0.0
	 */
	do_action( 'dev_backend_reply_content_before', $row->ID );

	echo wp_kses( $content, wp_kses_allowed_html( 'post' ) );

	/**
	 * dev_backend_reply_content_after hook
	 *
	 * @since  1.0.0
	 */
	do_action( 'dev_backend_reply_content_after', $row->ID );

	echo '</div>';
	?>
</td>
