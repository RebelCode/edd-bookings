<?php

/**
 * A class that represents a single booking, booked by a user after purchase.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Aventura\Bookings
 */
class Aventura_Bookings_Booking extends Aventura_Bookings_Object {

	/**
	 * The default values for the fields of this class, used by the constructor.
	 * @var array
	 */
	protected static $_defaultValues = array(
		'id'				=>	NULL,
		'service_id'		=>	NULL,
		'customer_id'		=>	NULL,
		'date'				=>	NULL,
		'time'				=>	NULL,
		'num_sessions'		=>	1
	);

	/**
	 * Constructor.
	 *
	 * @param array|string|int $id The Booking ID or an array with the properties of the booking.
	 */
	public function __construct( $arg ) {
		// If the argument is not an array, treat is as the ID
		if ( ! is_array( $arg ) ) {
			$arg = array( 'id' => $arg );
		}
		// Merge with defaults
		$data = array_merge(self::$_defaultValues, $arg);
		// Set the data
		$this->setDataUsingMethod($data);
	}

	/**
	 * Returns the booking date/start date.
	 * 
	 * @return int
	 */
	public function getDate() {
		$date = $this->getData('date');
		// If it is a string
		if ( is_string( $date ) ) {
			// assume it is in the format mm/dd/yy and parse it
			$date_parts = explode( '/', $date );
			$date = strtotime( $date_parts[2] . '-' . $date_parts[0] . '-' . $date_parts[1] );
		}
		return $date;
	}

	/**
	 * Returns the booking time, if applicable.
	 * 
	 * @return int|null The timestamp (without the date) or NULL if time does not apply.
	 */
	public function getTime() {
		$time = $this->getData('time');
		if ( is_string( $time ) ) {
			$time_parts = explode( ':', $time );
			$time = ( intval( $time_parts[0] ) * 3600 ) + ( intval( $time_parts[1] ) * 60 );
		}
		return $time;
	}

	/**
	 * Returns the number of sessions.
	 * 
	 * @return int
	 */
	public function getNumSessions() {
		return max( 1, $this->getData('num_sessions') );
	}

	/**
	 * Returns this booking as an array.
	 * 
	 * @return array
	 */
	public function toArray() {
		return $this->getData();
	}

}
