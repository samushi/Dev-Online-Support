<?php

/**
 *	Main class of plugin
 *
 * @package   	DevOnlineSupport
 * @author    	Sami Maxhuni <samimaxhuni510@gmail.com>
 * @license   	GPL-2.0+
 * @link      	http://devsolution.info
 * @copyright 	2017 Sami Maxhuni
 **/

namespace DevSupport;

use DevSupport\Admin\Admin;
use DevSupport\Session;
use DevSupport\Assets\FrontEnd\Enqueue as FrontEnqueue;

final class DevOnlineSupport{

	/**
	 * @var DevOnlineSupport Holds the unique instance of Dev Online Support
	 * @since 1.0.0
	 */

	private static $instance;

	/**
    * Admin Notices object
    *
    * @var object 
    * @since 1.0.0
    */

 	public $admin_notices;

 	/**
    * Admin object
    *
    * @var object Admin
    * @since 1.0.0
    */

    protected $admin;

    /**
    * Front End object
    *
    * @var object
    * @since 1.0.0
    */

    protected $front_enqueue;

    /**
	* Session object
	*
	* @since 3.2.6
	* @var $session
	*/
	public $session;


	/**
     * Instantiate and return the unique Dev Online Support object
     *
     * @since  1.0.0
     * @return object DevOnlineSupport Unique instance of Dev Online Support
     */
	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof DevOnlineSupport ) ) {

			// Instance self
			self::$instance 					= new DevOnlineSupport;

			// If is Admin Section
			if(is_admin()){
				self::$instance->admin 			= new Admin;
			}

			// If not is admin section
			if(!is_admin()){
				self::$instance->front_enqueue  = new FrontEnqueue();
			}

			// Call Session
			//self::$instance->session 			= new Session();
		}
		return self::$instance;

	}

	/**
	* Throw error on object clone
	*
	* The whole idea of the singleton design pattern is that there is a single
	* object therefore, we don't want the object to be cloned.
	*
	* @since 1.0.0
	* @return void
	*/
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', TEXTDOMAIN ), '1.0.0' );
	}
	/**
	* Disable unserializing of the class
	*
	* @since 1.0.0
	* @return void
	*/
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', TEXTDOMAIN ), '3.2.5' );
	}
}

/**
 * The main function responsible for returning the unique Dev Online Support instance
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @since 1.0.0
 * @return object DevOnlineSupport
 */

function DEVSUPPORT() {
	return DevOnlineSupport::instance();
}

// Get Dev Online Support Running
DEVSUPPORT();
