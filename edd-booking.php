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
// Initialize Directories
define( 'EDD_BK_DIR',			plugin_dir_path( EDD_BK ) );
define( 'EDD_BK_BASE', 			plugin_basename( EDD_BK ) );
define( 'EDD_BK_ADMIN_DIR',		EDD_BK_DIR . 'admin/' );
define( 'EDD_BK_PUBLIC_DIR',	EDD_BK_DIR . 'public/' );
define( 'EDD_BK_INC_DIR',		EDD_BK_DIR . 'includes/' );
define( 'EDD_BK_LANG_DIR',		EDD_BK_DIR . 'languages/' );
// Initialize URLs
define( 'EDD_BK_PLUGIN_URL',	plugin_dir_url( EDD_BK ) );
define( 'EDD_BK_ADMIN_URL',		EDD_BK_PLUGIN_URL . 'admin/' );
define( 'EDD_BK_PUBLIC_URL',	EDD_BK_PLUGIN_URL . 'public/' );

/**
 * The plugin main class code
 */
require_once EDD_BK_INC_DIR . 'class-edd-bk.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_edd_booking() {
	$edd_booking = EDD_Booking::get_instance();
	$edd_booking->run();
}
run_edd_booking();
