<?php

interface Aventura_Bookings_Booking_Controller_Interface {

	/**
	 * Gets all the bookings for a particular service, with optional date filter.
	 * 
	 * @param  string|int   $service_id The ID of the service.
	 * @param  string|array $date       (Optional) The date to check, or an array of two dates for range checking. Default: NULL
	 * @return array                    All bookings for the service with the given ID are returned as an array. If the date
	 *                                  param is specified, only the bookings for that date (or start date) are returned.
	 */
	public function getBookingsForService( $service_id, $date = NULL );
	
}
