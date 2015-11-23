<?php

/**
 * @wordpress-plugin
 * Plugin Name: Easy Digital Downloads - Booking
 * Plugin URL: http://eddbookings.com
 * Description: Adds a simple booking system to Easy Digital Downloads
 * Version: 0.9.4-RC1
 * Author: Jean Galea
 * Contributors: Miguel Muscat
 */

// If the file is called directly, or has already been called, abort
if ( ! defined('WPINC') || defined('EDD_BK') ) die;

// Plugin File Constant
define( 'EDD_BK', __FILE__ );
// Plugin Version
define( 'EDD_BK_VERSION', '0.9.4.RC.1' );
// Plugin Name
define( 'EDD_BK_PLUGIN_NAME', 'EDD Bookings' );
// Parent Plugin Path
define( 'EDD_BK_PARENT_PLUGIN_CLASS', 'Easy_Digital_Downloads' );
// Minimum WordPress version
define( 'EDD_BK_MIN_WP_VERSION', '4.0' );

// Initialize Directories
define( 'EDD_BK_DIR',				plugin_dir_path( EDD_BK ) );
define( 'EDD_BK_BASE', 				plugin_basename( EDD_BK ) );
define( 'EDD_BK_LANG_DIR',			EDD_BK_DIR . 'languages/' );
define( 'EDD_BK_INCLUDES_DIR',		EDD_BK_DIR . 'includes/' );
define( 'EDD_BK_VIEWS_DIR', 		EDD_BK_DIR . 'views/' );
define( 'EDD_BK_ADMIN_DIR',			EDD_BK_INCLUDES_DIR . 'admin/' );
define( 'EDD_BK_PUBLIC_DIR',		EDD_BK_INCLUDES_DIR . 'public/' );
define( 'EDD_BK_LIB_DIR',			EDD_BK_INCLUDES_DIR . 'libraries/' );
define( 'EDD_BK_DOWNLOADS_DIR',		EDD_BK_INCLUDES_DIR . 'downloads/' );
define( 'EDD_BK_BOOKINGS_DIR',		EDD_BK_INCLUDES_DIR . 'bookings/' );
define( 'EDD_BK_CUSTOMERS_DIR',		EDD_BK_INCLUDES_DIR . 'customers/' );
define( 'EDD_BK_EXCEPTIONS_DIR',	EDD_BK_INCLUDES_DIR . 'exceptions/' );
define( 'EDD_BK_WP_HELPERS_DIR',	EDD_BK_INCLUDES_DIR . 'wp-helpers/' );

// Initialize URLs
define( 'EDD_BK_PLUGIN_URL',		plugin_dir_url( EDD_BK ) );
define( 'EDD_BK_ASSETS_URL',		EDD_BK_PLUGIN_URL . 'assets/' );
define( 'EDD_BK_CSS_URL',			EDD_BK_ASSETS_URL . 'css/' );
define( 'EDD_BK_JS_URL',			EDD_BK_ASSETS_URL . 'js/' );
define( 'EDD_BK_FONTS_URL',			EDD_BK_ASSETS_URL . 'fonts/' );

// For Debugging
define( 'EDD_BK_DEBUG', 			FALSE );

// The Aventura Bookings library
require EDD_BK_LIB_DIR . 'Aventura/Bookings/Main.php';

//The plugin main class code
require EDD_BK_INCLUDES_DIR . 'class-edd-bookings.php';

// Exception classes
require EDD_BK_EXCEPTIONS_DIR . 'class-edd-bk-exception.php';
require EDD_BK_EXCEPTIONS_DIR . 'class-edd-bk-singleton-reinstantiation-exception.php';

// Activation/Deactivation hooks
register_activation_hook( __FILE__, array( 'EDD_Bookings', 'on_activate' ) );
register_deactivation_hook( __FILE__, array( 'EDD_Bookings', 'on_deactivate' ) );

/**
 * Begins execution of the plugin.
 *
 * Instantiating the plugin instance will register all plugin
 * hooks to the loader. The run method will in-turn push all
 * registered hooks into the WordPress Hook System.
 *
 * @since    1.0.0
 */
function run_edd_booking() {
	try {
		$instance = EDD_Bookings::get_instance();
		$instance->run();
	} catch (EDD_BK_Exception $e) {
		$e->to_wp_die();
	}
}
run_edd_booking();

/**
 * Gets the EDD_Bookings singleton instance.
 * 
 * @return EDD_Bookings
 */
function edd_bk() {
	return EDD_Bookings::get_instance();
}
