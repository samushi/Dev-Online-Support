<?php

/**
 *	Register Menu class of plugin
 *
 * @package   	RegisterMenu
 * @author    	Sami Maxhuni <samimaxhuni510@gmail.com>
 * @license   	GPL-2.0+
 * @link      	http://devsolution.info
 * @copyright 	2017 Sami Maxhuni
 **/

namespace DevSupport\Admin;

use DevSupport\Posts\Tickets;

class RegisterMenu{

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 * @var      object
	 */
	protected static $instance = null;

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
			self::$instance->action_hooks();
		}

		return self::$instance;
	}

	/**
	 * Register the Menu
	 *
	 * @since  1.0.0
	 */

	public function register_parent_menu(){
		add_submenu_page( 'edit.php?post_type='.DEV_POST_TYPE, __('Debugging Tools', TEXTDOMAIN), __('Tools', TEXTDOMAIN), 'edit_posts', 'dev_support_tools', array($this, 'tools') );
		add_submenu_page( 'edit.php?post_type='.DEV_POST_TYPE, __('Dev Online Support Settings', TEXTDOMAIN), __('Settings', TEXTDOMAIN), 'edit_posts', 'dev_support_settings', array($this, 'settings') );
		add_submenu_page( 'edit.php?post_type='.DEV_POST_TYPE, __('Dev Online Support Addons', TEXTDOMAIN), __('Addons', TEXTDOMAIN), 'edit_posts', 'dev_support_addons', array($this, 'addons') );
	}

	/**
	 * Action hooks for menu & submenu etc.
	 *
	 * @since 1.0.0
	 */

	private function action_hooks(){
		add_action( 'admin_menu', array(self::$instance, 'register_parent_menu') );
		add_action( 'admin_menu', array(new Tickets, 'tickets_count') );
	}

	/**
	 * Settings
	 *
	 * @since  1.0.0
	 */

	public function settings(){
		echo "<h2>Settings</h2>";
	}

	/**
	 * Tools
	 *
	 * @since  1.0.0
	 */

	public function tools(){
		echo "<h2>Tools</h2>";
	}

	/**
	 * Addons
	 *
	 * @since  1.0.0
	 */

	public function addons(){
		global $dev_addons;

		print_r($dev_addons);
	}
}

