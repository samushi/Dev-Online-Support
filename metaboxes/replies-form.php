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

<h2>
	<?php
	/**
	 * dev_write_reply_title_admin filter
	 *
	 * @since  1.0.0
	 *
	 * @param  string  Title to display
	 * @param  \WP_Post Current post object
	 */
	echo apply_filters( 'dev_write_reply_title_admin', sprintf( esc_html_x( 'Write a reply to %s', 'Title of the reply editor in the back-end', TEXTDOMAIN ), '&laquo;' . esc_attr( get_the_title( $post->ID ) ) . '&raquo;' ), $post ); ?>
</h2>
<?php
/**
 * Load the WordPress WYSIWYG with minimal options
 */
/* The edition textarea */
wp_editor( '', 'dev_reply', array(
				'media_buttons' => false,
				'teeny'         => true,
				'quicktags'     => true,
		)
);

/**
 * Add a hook after the WYSIWYG editor
 * for tickets reply.
 *
 * @WPAS_Quick_Replies::echoMarkup()
 */
do_action( 'dev_admin_after_wysiwyg' );

/**
 * Add a nonce for the reply
 */
wp_nonce_field( 'reply_ticket', 'dev_reply_ticket', false, true );
?>

<div class="dev-reply-actions">
	<?php
	/**
	 * Where should the user be redirected after submission.
	 *
	 * @var string
	 */
	global $current_user;
	$where = get_user_meta( $current_user->ID, 'dev_after_reply', true );

	switch ( $where ):

		case false:
		case '':
		case 'back': ?>
			<input type="hidden" name="dev_back_to_list" value="1">
			<button type="submit" name="dev_do" class="button-primary dev_btn_reply" value="reply"><?php _e( 'Reply', TEXTDOMAIN ); ?></button>
			<?php break;

			break;

		case 'stay':
			?>
			<button type="submit" name="dev_do" class="button-primary dev_btn_reply" value="reply"><?php _e( 'Reply', TEXTDOMAIN ); ?></button><?php
			break;

		case 'ask': ?>
			<fieldset>
				<strong><?php _e( 'After Replying', TEXTDOMAIN ); ?></strong><br>
				<label for="back_to_list"><input type="radio" id="back_to_list" name="where_after" value="back_to_list" checked="checked"> <?php _e( 'Back to list', TEXTDOMAIN ); ?></label>
				<label for="stay_here"><input type="radio" id="stay_here" name="where_after" value="stay_here"> <?php _e( 'Stay on ticket screen', TEXTDOMAIN ); ?></label>
			</fieldset>
			<button type="submit" name="dev_do" class="button-primary dev_btn_reply" value="reply"><?php _e( 'Reply', TEXTDOMAIN ); ?></button>
			<?php break;

	endswitch;
	?>

	<?php if ( current_user_can( 'close_ticket' ) ): ?>
		<button type="submit" name="dev_do" class="button-secondary dev_btn_reply_close" value="reply_close"><?php _e( 'Reply & Close', TEXTDOMAIN ); ?></button>
	<?php endif;

	/**
	 * Fired after all the submission form buttons were output
	 *
	 * @since 3.2.6
	 *
	 * @param int $post_id Ticket ID
	 */
	do_action( 'dev_post_reply_buttons_after', $post->ID );

	// Link to close the ticket
	if ( 'open' === get_post_meta( get_the_ID(), '_dev_status', true ) ) : ?>
		<a class="dev_btn_close_bottom" href="<?php echo dev_get_close_ticket_url( $post->ID ); ?>"><?php echo esc_html_x( 'Close', 'Close the ticket', TEXTDOMAIN ); ?></a>
	<?php endif; ?>
</div>