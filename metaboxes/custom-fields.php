<?php
/**
 * Ticket Status.
 *
 * This metabox is used to display the ticket current status
 * and change it in one click.
 *
 * For more details on how the ticket status is changed,
 *
 * @see DEVSUPPORT::custom_actions()
 *
 * @since 1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
} ?>

<div class="dev-custom-fields">
	<?php
	global $dev_cf;

	do_action( 'dev_mb_details_before_custom_fields' );

	$dev_cf->submission_form_fields();

	do_action( 'dev_mb_details_after_custom_fields' );
	?>
</div>