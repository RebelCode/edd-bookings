<?php

/**
 * @wordpress-plugin
 * Plugin Name: Easy Digital Downloads - Booking
 * Plugin URL:
 * Description: Adds a simple booking system to Easy Digital Downloads
 * Version: 1.0.0
 * Author: Jean Galea
 * Contributors: Miguel Muscat
 */

// If the file is called directly, or has already been called, abort
if ( ! defined('WPINC') || defined('EDD_BK') ) {
	die;
}

// Plugin File Constant
define( 'EDD_BK', __FILE__ );
// Plugin Version
define( 'EDD_BK_VERSION', '1.0.0' );
// Plugin Name
define( 'EDD_BK_PLUGIN_NAME', 'edd-booking' );

// Initialize Directories
define( 'EDD_BK_DIR',				plugin_dir_path( EDD_BK ) );
define( 'EDD_BK_BASE', 				plugin_basename( EDD_BK ) );
define( 'EDD_BK_LANG_DIR',			EDD_BK_DIR . 'languages/' );
define( 'EDD_BK_INCLUDES_DIR',		EDD_BK_DIR . 'includes/' );
define( 'EDD_BK_ADMIN_DIR',			EDD_BK_INCLUDES_DIR . 'admin/' );
define( 'EDD_BK_PUBLIC_DIR',		EDD_BK_INCLUDES_DIR . 'public/' );
define( 'EDD_BK_LIB_DIR',			EDD_BK_INCLUDES_DIR . 'lib/' );

// Initialize URLs
define( 'EDD_BK_PLUGIN_URL',		plugin_dir_url( EDD_BK ) );
define( 'EDD_BK_ASSETS_URL',		EDD_BK_PLUGIN_URL . 'assets/' );
define( 'EDD_BK_CSS_URL',			EDD_BK_ASSETS_URL . 'css/' );
define( 'EDD_BK_JS_URL',			EDD_BK_ASSETS_URL . 'js/' );
define( 'EDD_BK_FONTS_URL',			EDD_BK_ASSETS_URL . 'fonts/' );

// For Debugging
define( 'EDD_BK_DEBUG', 			FALSE );

/**
 * The Aventura Bookings library
 */
require EDD_BK_LIB_DIR . 'Aventura/Bookings/Main.php';

/**
 * The plugin main class code
 */
require EDD_BK_INCLUDES_DIR . 'class-edd-bookings.php';

/**
 * Exception classes.
 */
require EDD_BK_INCLUDES_DIR . 'class-edd-bk-exception.php';
require EDD_BK_INCLUDES_DIR . 'class-edd-bk-singleton-reinstantiation-exception.php';

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
