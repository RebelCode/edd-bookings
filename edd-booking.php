<?php

/*
 * Plugin Name: Easy Digital Downloads - Booking
 * Plugin URL:
 * Description: Adds a simple booking system to Easy Digital Downloads
 * Version: 1.0
 * Author: Jean Galea
 * Author URI:
 * Contributors: Miguel Muscat
 */



if ( ! defined( 'EDD_BK_PLUGIN_DIR' ) ) {
	define( 'EDD_BK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'EDD_BK_PLUGIN_URL' ) ) {
	define( 'EDD_BK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'EDD_BK_PLUGIN_FILE' ) ) {
	define( 'EDD_BK_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'EDD_BK_VERSION' ) ) {
	define( 'EDD_BK_VERSION', '0.1' );
}


if( class_exists( 'EDD_Booking' ) ) {
	$edd_booking = new EDD_Booking();
}




/*
|--------------------------------------------------------------------------
| INTERNATIONALIZATION
|--------------------------------------------------------------------------
*/

function edd_booking_textdomain() {
	load_plugin_textdomain( 'edd_bk', false, dirname( plugin_basename( EDD_BK_PLUGIN_FILE ) ) . '/languages/' );
}
add_action( 'init', 'edd_booking_textdomain' );


/*
|--------------------------------------------------------------------------
| INCLUDES
|--------------------------------------------------------------------------
*/

if( is_admin() ) {
	include_once( EDD_BK_PLUGIN_DIR . 'includes/metaboxes.php' );
	include_once( EDD_BK_PLUGIN_DIR . 'includes/scripts.php' );
}
