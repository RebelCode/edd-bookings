<?php

/**
 * A class that represents a single booking, booked by a user after purchase.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_BK
 * @subpackage Core
 */
class EDD_BK_Booking {

	/**
	 * The ID of this booking.
	 * @var string|int
	 */
	private $id;

	/**
	 * The Id of the EDD payment.
	 * @var string|int
	 */
	private $payment_id;

	/**
	 * The ID of the Download.
	 * @var string|int
	 */
	private $download_id;

	/**
	 * The date (or start date) of the booking, as a timestamp without time.
	 * @var int
	 */
	private $date;

	/**
	 * The start time of the booking, as a timestamp without the date.
	 * @var int
	 */
	private $time;

	/**
	 * The number of booked sessions.
	 * @var int
	 */
	private $sessions;

	/**
	 * Constructor.
	 *
	 * @param string|int $id The payment ID of the purchase.
	 */
	public function __construct( $id, $payment_id, $download_id, $date, $time, $sessions ) {
		$this->id = id;
		$this->payment_id = $payment_id;
		$this->download_id = $download_id;
		$this->date = $date;
		$this->time = $time;
		$this->sessions = $sessions;
	}
	
	/**
	 * Returns the payment ID.
	 * 
	 * @return string|int
	 */
	public function getPaymentID() {
		return $this->payment_id;
	}

	/**
	 * Returns the download ID.
	 * 
	 * @return string|int
	 */
	public function getDownloadID() {
		return $this->download_id;
	}

	/**
	 * Returns the booking date/start date.
	 * 
	 * @return int
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * Sets the booking's date.
	 *
	 * @param string|int $date A date timestamp or a string in the format mm/dd/yy
	 */
	public function setDate( $date ) {
		// If it is a string
		if ( is_string( $date ) ) {
			// assume it is in the format mm/dd/yy and parse it
			$date_parts = explode( '/', $date );
			$date = strtotime( $date_parts[2] . '-' . $date_parts[0] . '-' . $date_parts[1] );
		}
		$this->date = $date;
	}

	/**
	 * Returns the booking time, if applicable.
	 * 
	 * @return int|null The timestamp (without the date) or NULL if time does not apply.
	 */
	public function getTime() {
		return $time;
	}

	/**
	 * Sets the booking's time.
	 *
	 * @param int|string $time A timestamp (without date) or a string in the format HH:mm
	 */
	public function setTime( $time ) {
		if ( is_string( $time ) ) {
			$time_parts = explode( ':', $time );
			$time = ( intval( $time_parts[0] ) * 3600 ) + ( intval( $time_parts[1] ) * 60 );
		}
		$this->time = $time;
	}

	/**
	 * Returns this booking as an array.
	 * 
	 * @return array
	 */
	public function toArray() {
		$vars = get_object_vars( $this );
		unset( $vars['id'] );
		return $vars;
	}

	/**
	 * Creates a new EDD_BK_Booking instance using the given booking ID.
	 * 
	 * @param  string|int     $id The booking ID
	 * @return EDD_BK_Booking     The created instance.
	 */
	public static function from_id( $id ) {
		$meta = get_post_meta( $id, 'booking_details', true );
		if ( $meta === '' ) return NULL;
		return new self( $id, $meta['payment_id'], $meta['download_id'], $meta['date'], $meta['time'], $meta['sessions'] );
	}

	/**
	 * Creates a new EDD_BK_Booking instance using the given payment ID.
	 * 
	 * @param  string|int     $payment_id ID of the EDD payment of the purchased booking.
	 * @return EDD_BK_Booking             The created EDD_BK_Booking instance.
	 */
	public static function from_payment_meta( $payment_id ) {
		// Get the payment meta
		$payment_meta = edd_get_payment_meta( $payment_id );
		// Get the download ID
		$download_id = $payment_meta['downloads'][0]['id'];
		
		// Create the instance
		$booking = new self( $payment_id, $download_id, null, null, null );

		// Set the number of sessions
		$num_sessions = isset( $info['edd_bk_num_slots'] )? intval( $info['edd_bk_num_slots'] ) : 1;
		$booking->setNumSessions( $num_sessions );
		// Set the date selected
		$date = isset( $info['edd_bk_date'] )? $info['edd_bk_date'] : null;
		$booking->setDate( $date );
		// Set the time
		$time = isset( $info['edd_bk_time'] )? $info['edd_bk_time'] : null;
		$booking->setTime( $time );

		return $booking;
	}

}
