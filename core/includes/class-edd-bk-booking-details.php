<?php

/**
 * Class that represents the booking details of a purchased booking.
 * Details include time and date, number of booked sessions, etc.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Booking
 * @subpackage Core
 */
class EDD_BK_Download_Details {
	
	/**
	 * Constructor.
	 * @param array $info Booking information, as stored in the EDD payment meta.
	 */
	public function __construct( $info ) {
		// Get the number of sessions
		$this->num_sessions = intval( $info['edd_bk_num_slots'] );
		// Get the date selected
		// Parse the date string into a timestamp
		$date_parts = explode( '/', $info['edd_bk_date'] );
		$this->date = strtotime( $date_parts[2] . '-' . $date_parts[0] . '-' . $date_parts[1] );
		// Get the time
		$time_parts = explode( ':', $info['edd_bk_time'] );
		$this->time = intval( $time_parts[0] ) * 3600 intval( $time_parts[1] ) * 60;
	}

}
