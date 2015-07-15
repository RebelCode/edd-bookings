<?php

/**
 * AJAX handler class for the public module of the plugin.
 * 
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Booking
 * @subpackage Public
 */
class EDD_BK_Public_AJAX {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->define_hooks();
	}

	/**
	 * Registers the WordPress hooks into the loader.
	 */
	public function define_hooks() {
		$loader = EDD_Booking::get_instance()->get_loader();
		// AJAX hook for retrieving the download availability
		$loader->add_action( 'wp_ajax_get_download_availability', $this, 'get_download_availability' );
		$loader->add_action( 'wp_ajax_nopriv_get_download_availability', $this, 'get_download_availability' );
		// AJAX hook for retrieving times for a selected date, for the timepicker on the front-end
		$loader->add_action( 'wp_ajax_get_times_for_date', 'EDD_BK_Commons', 'ajax_get_times_for_date' );
		$loader->add_action( 'wp_ajax_nopriv_get_times_for_date', 'EDD_BK_Commons', 'ajax_get_times_for_date' );
	}

	/**
	 * Retrieves the availability for the given download.
	 *
	 * This method is triggered through a WP AJAX hook.
	 */
	public function get_download_availability() {
		if ( ! isset( $_POST['post_id'] ) ) {
			echo json_encode( array(
				'error' => 'No post ID as given.'
			) );
		} else {
			$post_id = $_POST['post_id'];
			$availability = get_post_meta( $post_id, 'edd_bk_availability', TRUE );
			$availability = $availability == '' ? array() : $availability;
			echo json_encode( $availability );
		}
		die();
	}

}
