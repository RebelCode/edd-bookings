<?php

/**
 * Interface for the Aventura_Bookings_Booking class' controller.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Aventura\Bookings\Booking\Controller
 */
interface Aventura_Bookings_Booking_Controller_Interface {

	public function bookingExists( $id );
	public function getBookingById( $id );
	public function getAllBookings();
	public function insertBooking( Aventura_Bookings_Booking $booking );
	public function deleteBooking( $id );

}
