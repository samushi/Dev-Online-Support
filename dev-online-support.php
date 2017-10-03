<?php
/**
 * @package   Dev Online Support
 * @author    Sami Maxhuni <samimaxhuni510@gmail.com>
 * @license   GPL-2.0+
 * @link      http://devsolution.info
 * @copyright 2017 Sami Maxhuni
 *
 * @wordpress-plugin
 * Plugin Name:       Dev Online Support
 * Plugin URI:        http://getdevonlinesupport.com
 * Description:       Dev Online Support is a great ticketing system that will help you improve your customer satisfaction by providing a unique customer support experience.
 * Version:           1.0.0
 * Author:            Sami Maxhuni
 * Author URI:        http://getdevonlinesupport.com
 * Text Domain:       dev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Shortcuts
 *----------------------------------------------------------------------------*/

define( 'DEV_VERSION',           '1.0.0' );
define( 'DEV_DB_VERSION',        '1' );
define( 'DEV_URL',               trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'DEV_PATH',              trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'DEV_ROOT',              trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) );
define( 'DEV_TEMPLATE_PATH',     'dev-online-support/' );
define( 'DEV_ADMIN_ASSETS_URL',  trailingslashit( plugin_dir_url( __FILE__ ) . 'assets/admin/' ) );
define( 'DEV_ADMIN_ASSETS_PATH', trailingslashit( plugin_dir_path( __FILE__ ) . 'assets/admin/' ) );
define( 'TEXTDOMAIN', 			 'dev_online_support_textdomain');
define( 'DEV_POST_TYPE',		 'ticket');

/*----------------------------------------------------------------------------*
 * Settings
 *----------------------------------------------------------------------------*/

define( 'DEV_FIELDS_DESC', apply_filters( 'dev_fields_descriptions', true ) );

/*----------------------------------------------------------------------------*
 * Addons
 *----------------------------------------------------------------------------*/

/**
 * Array of addons to load.
 *
 * @since  1.0.0
 * @var    array
 */

$dev_addons = array();

/*----------------------------------------------------------------------------*
 * Session Load
 *----------------------------------------------------------------------------*/

require_once DEV_PATH . 'includes/Sessions.php';

/*----------------------------------------------------------------------------*
 * General Functions
 *----------------------------------------------------------------------------*/

require_once DEV_PATH . 'functions/general-functions.php';
require_once DEV_PATH . 'functions/functions-user.php';

/* Load custom fields dependencies */
require_once( DEV_PATH . 'includes/custom-fields/CustomField.php' );
require_once( DEV_PATH . 'includes/custom-fields/CustomFields.php' );
require_once( DEV_PATH . 'includes/custom-fields/functions-custom-fields.php' );   // Submission form related functions

/**
 * Instantiate the global $dev_cf object containing all the custom fields.
 * This object is used throughout the entire plugin so it is capital to be able
 * to access it anytime and not to redeclare a second object when registering
 * new custom fields.
 *
 * @since  1.0.0
 * @var    $dev_cf CustomFields
 */

$dev_cf = new DevSupport\Field\CustomFields;

/*----------------------------------------------------------------------------*
 * Load theme's functionality
 *----------------------------------------------------------------------------*/

require_once DEV_PATH . 'includes/Assets/Front/Enqueue.php';
require_once DEV_PATH . 'includes/Agent.php';
require_once DEV_PATH . 'includes/posts/Tickets.php';

/*----------------------------------------------------------------------------*
 * Admin Section
 *----------------------------------------------------------------------------*/

require_once DEV_PATH . 'includes/Admin/Admin.php';
add_action( 'plugins_loaded', array( 'DevSupport\Admin\Admin', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Main Class
 *----------------------------------------------------------------------------*/

require_once DEV_PATH . 'includes/DevOnlineSupport.php';
