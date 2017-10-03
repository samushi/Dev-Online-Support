<?php

/**
 *	Admin Dashboard
 *
 * @package   	Admin/Tickets List
 * @author    	Sami Maxhuni <samimaxhuni510@gmail.com>
 * @license   	GPL-2.0+
 * @link      	http://devsolution.info
 * @copyright 	2017 Sami Maxhuni
 **/

namespace DevSupport\Admin;

use DevSupport\Admin\RegisterMenu;
use DevSupport\Assets\Admin\Enqueue;

class Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Instance of scripts
	 *
	 * @since    1.0.0
	 * @var      object
	 */

	protected $enqueue;

	/**
	 * Name of the nonce used to secure custom fields.
	 *
	 * @var   object
	 * @since 1.0.0
	 */
	public static $nonce_name = 'dev_cf';

	/**
	 * Action of the custom nonce.
	 *
	 * @var   object
	 * @since 1.0.0
	 */
	public static $nonce_action = 'dev_update_cf';

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
			
			/* Load admin functions files */
			require_once( DEV_PATH . 'includes/Admin/RegisterMenu.php' );
			add_action( 'plugins_loaded', array( new RegisterMenu, 'get_instance' ), 11, 0 );

			/* Enqueue Admin Script & Styles */
			require_once( DEV_PATH . 'includes/Assets/Admin/Enqueue.php' );
			$this->enqueue = new Enqueue();

			add_action( 'add_meta_boxes', array( $this, 'metaboxes' ) ); // Register the metaboxes
			add_filter( 'postbox_classes_ticket_dev-mb-details', array( $this, 'add_metabox_details_classes' ) ); // Customizedetails metabox classes
			add_action( 'save_post_ticket', array( $this, 'save_ticket' ) ); // Save all custom fields
		}
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Flush rewrite rules.
	 *
	 * This is to avoid getting 404 errors
	 * when trying to view a ticket. We need to update
	 * the permalinks with our new custom post type.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function flush_rewrite_rules() {
		flush_rewrite_rules();
	}

	/**
	 * Register the metaboxes.
	 *
	 * The function below registers all the metaboxes used
	 * in the ticket edit screen.
	 *
	 * @since 1.0.0
	 */
	public function metaboxes() {
		
	}

	/**
	 * Add new class to the details metabox.
	 *
	 * @param array $classes Current metabox classes
	 *
	 * @return array The updated list of classes
	 */
	public function add_metabox_details_classes( $classes ) {
		array_push( $classes, 'submitdiv' );
		return $classes;
	}

	/**
	 * Metabox callback function.
	 *
	 * The below function is used to call the metaboxes content.
	 * A template name is given to the function. If the template
	 * does exist, the metabox is loaded. If not, nothing happens.
	 *
	 * @param  (integer) $post     Post ID
	 * @param  (string)  $template Metabox content template
	 *
	 * @return void
	 * @since  1.0.0
	 */
	public function metabox_callback( $post, $args ) {

		if ( ! is_array( $args ) || ! isset( $args['args']['template'] ) ) {
			_e( 'An error occurred while registering this metabox. Please contact the support.', TEXTDOMAIN );
		}

		$template = $args['args']['template'];

		if ( ! file_exists( DEV_PATH . "metaboxes/$template.php" ) ) {
			_e( 'An error occured while loading this metabox. Please contact the support.', TEXTDOMAIN );
		}

		/* Include the metabox content */
		include_once( DEV_PATH . "metaboxes/$template.php" );

	}

	/**
	 * Save ticket custom fields.
	 *
	 * This function will save all custom fields associated
	 * to the ticket post type. Be it core custom fields
	 * or user added custom fields.
	 * 
	 * @param  (int) $post_id Current post ID
	 * @since  1.0.0
	 */
	public function save_ticket( $post_id ) {

		/* We should already being avoiding Ajax, but let's make sure */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		/* Now we check the nonce */
		if ( ! isset( $_POST[ Admin::$nonce_name ] ) || ! wp_verify_nonce( $_POST[ Admin::$nonce_name ], Admin::$nonce_action ) ) {
			return;
		}

		/* Does the current user has permission? */
		if ( !current_user_can( 'edit_ticket', $post_id ) ) {
			return;
		}



	}

}