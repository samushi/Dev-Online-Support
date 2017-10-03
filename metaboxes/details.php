<?php
/**
 * Ticket Status.
 *
 * This metabox is used to display the ticket current status
 * and change it in one click.
 *
 *
 * @since 1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $pagenow, $post;

/* Current status */
$ticket_status = get_post_meta( get_the_ID(), '_dev_status', true );

/** 
 * Status action link
 * 
 * @var string
 * @see admin/class-awesome-support-admin.php
 */

$action = ( in_array( $ticket_status, array( 'closed', '' ) ) ) ? DevSupport\Posts\Tickets::get_open_ticket_url( $post->ID ) : DevSupport\Posts\Tickets::get_close_ticket_url( $post->ID );

/**
 * Get available statuses.
 */
$statuses = DevSupport\Posts\Tickets::get_post_status();

/* Get post status */
$post_status = isset( $post ) ? $post->post_status : '';

/* Get time */
if ( isset( $post ) ) {
	$date = human_time_diff( get_the_time( 'U', $post->ID ), current_time( 'timestamp' ) );
}
?>
<div class="dev-ticket-status submitbox">
	<p>
		<strong><?php _e( 'Ticket status:', TEXTDOMAIN ); ?></strong>
		<?php if ( 'post-new.php' != $pagenow ):
			dev_cf_display_status( '', $post->ID );
			?>
		<?php else: ?>
			<span><?php _x( 'Creating...', 'Ticket creation', TEXTDOMAIN ); ?></span>
		<?php endif; ?>
	</p>
	<?php if ( isset( $post ) ): ?><p><strong><?php _e( 'Opened:', TEXTDOMAIN ); ?></strong> <em><?php printf( __( '%s ago', TEXTDOMAIN ), $date ); ?></em></p><?php endif; ?>
	<?php if ( 'open' === get_post_meta( $post->ID, '_dev_status', true ) ): ?>
		<label for="dev-post-status"><strong><?php _e( 'Current state:', TEXTDOMAIN ); ?></strong></label>
		<p>
			<select id="dev-post-status" name="post_status_override" style="width: 100%">
				<?php foreach ( $statuses as $status => $label ):
					$selected = ( $post_status === $status ) ? 'selected="selected"' : '';
					if ( 'auto-draft' === $post_status && 'processing' === $status ) { $selected = 'selected="selected"'; } ?>
					<option value="<?php echo $status; ?>" <?php echo $selected; ?>><?php echo $label; ?></option>
				<?php endforeach; ?>
			</select>
			<?php if ( isset( $_GET['post'] ) ): ?>
				<input type="hidden" name="dev_post_parent" value="<?php echo $_GET['post']; ?>">
			<?php endif; ?>
		</p>
	<?php endif; ?>
	
	<div id="major-publishing-actions">
		<?php if ( current_user_can( "delete_ticket", $post->ID ) ): ?>
			<div id="delete-action">
				<a class="submitdelete deletion" href="<?php echo $action; ?>">
					<?php
					if ( 'closed' === $ticket_status ) {
						_e( 'Re-open', TEXTDOMAIN );
					} elseif( '' === $ticket_status ) {
						_e( 'Open', TEXTDOMAIN );
					} else {
						_e( 'Close', TEXTDOMAIN );
					}
					?>
				</a>
			</div>
		<?php endif; ?>

		<?php if ( current_user_can( 'edit_ticket' ) ): ?>
			<div id="publishing-action">
				<span class="spinner"></span>
				<?php if ( isset( $_GET['action'] ) && 'edit' === $_GET['action'] ) : ?>
					<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Updating', TEXTDOMAIN ) ?>" />
					<?php submit_button( __( 'Update Ticket' ), 'primary button-large', 'publish', false, array( 'accesskey' => 'u' ) ); ?>
				<?php else:
					if ( current_user_can( 'create_ticket' ) ): ?>
						<input name="original_publish" type="hidden" id="original_publish" value="<?php esc_attr_e( 'Creating', TEXTDOMAIN ) ?>" />
						<?php submit_button( __( 'Open Ticket' ), 'primary button-large', 'publish', false, array( 'accesskey' => 'o' ) ); ?>
						<?php endif;
				endif; ?>
			</div>
		<?php endif; ?>
		<div class="clear"></div>
	</div>
</div>