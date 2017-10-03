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

/**
 * dev_backend_history_content_before hook
 *
 * @since  1.0.0
 */
do_action( 'dev_backend_history_content_before', $row->ID );

/* Filter the content before we display it */
$content = apply_filters( 'the_content', $row->post_content );

/**
 * dev_backend_history_content_after hook
 *
 * @since  1.0.0
 */
do_action( 'dev_backend_history_content_after', $row->ID ); ?>

<td colspan="3">
	<span class="dev-action-author"><?php echo $user_name; ?>, <em class='dev-time'><?php printf( __( '%s ago', TEXTDOMAIN ), $date ); ?></em></span>
	<div class="dev-action-details"><?php echo $content; ?></div>
</td>