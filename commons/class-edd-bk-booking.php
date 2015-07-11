<?php

require( EDD_BK_COMMONS_DIR . 'enum-edd-bk-session-unit.php' );
require( EDD_BK_COMMONS_DIR . 'enum-edd-bk-booking-duration.php' );
require( EDD_BK_COMMONS_DIR . 'class-edd-bk-availability.php' );

class EDD_BK_Booking {

	/**
	 * The ID.
	 * @var int
	 */
	private $id;

	/**
	 * The enabled flag.
	 * @var boolean
	 */
	private $enabled;

	/**
	 * The length of a single session.
	 * @var int
	 */
	private $session_length;

	/**
	 * The unit of the session length.
	 * @var EDD_BK_Session_Unit
	 */
	private $session_unit;

	/**
	 * The cost of a single session
	 * @var float
	 */
	private $session_cost;

	/**
	 * The duration of a booking: single or multiple sessions.
	 * @var EDD_BK_Booking_Duration
	 */
	private $booking_duration;

	/**
	 * The minimum number of sessions that a customer can book, if the
	 * booking duration is multiple sessions.
	 * @var int
	 */
	private $min_sessions;

	/**
	 * The maximum number of sessions that a customer can book, if the
	 * booking duration is multiple sessions.
	 * @var int
	 */
	private $max_sessions;

	/**
	 * Flag that determins whether or not unspecified dates in the availability
	 * table are to be available or not.
	 * True: available, False: unavailable.
	 * @var bool
	 */
	private $availability_fill;

	/**
	 * The availability table entries.
	 * @var EDD_BK_Availability
	 */
	private $availability;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id = NULL;
		$this->enabled = FALSE;
		$this->session_length = 1;
		$this->session_unit = EDD_BK_Session_Unit::HOURS;
		$this->session_cost = 0;
		$this->booking_duration = EDD_BK_Booking_Duration::SINGLE;
		$this->min_sessions = 0;
		$this->max_sessions = 0;
		$this->availability_fill = FALSE;
		$this->availability = new EDD_BK_Availability();
	}

	/**
	 * Gets the ID.
	 *
	 * @return int
	 */
	public function getID() {
		return $this->id;
	}

	/**
	 * Sets the ID.
	 *
	 * @param int $id The ID
	 * @return self
	 */
	private function setID( $id ) {
		$this->id = intval( $id );
		return $this;
	}

	/**
	 * Gets the enabled flag.
	 *
	 * @return boolean
	 */
	public function isEnabled() {
		return $this->enabled;
	}

	/**
	 * Sets the enabled flag.
	 *
	 * @param boolean $enabled The enabled flag
	 * @return self
	 */
	private function setEnabled( $enabled ) {
		$this->enabled = $enabled;
		return $this;
	}

	/**
	 * Gets the length of a single session.
	 *
	 * @return int
	 */
	public function getSessionLength() {
		return $this->session_length;
	}

	/**
	 * Sets the length of a single session.
	 *
	 * @param int $session_length the session length
	 * @return self
	 */
	private function setSessionLength( $session_length ) {
		$this->session_length = intval( $session_length );
		return $this;
	}

	/**
	 * Gets the unit of the session length.
	 *
	 * @return EDD_BK_Session_Unit
	 */
	public function getSessionUnit() {
		return $this->session_unit;
	}

	/**
	 * Returns true if the session unit is equal to at least one
	 * of the given arguments.
	 *
	 * @param   string ... Any number of string arguments to check against
	 *                     this booking's session unit.
	 * @return boolean     True if this booking's session unit is equal to at
	 *                     least one of the given arguments, false otherwise.
	 */
	public function isSessionUnit(/* arg0, arg1, ... */) {
		$args = func_get_args();
		$bool = false;
		foreach ( $args as $arg ) {
			$bool = $bool || ( $this->session_unit == $arg );
		}
		return $bool;
	}

	/**
	 * Sets the unit of the session length.
	 *
	 * @param EDD_BK_Session_Unit $session_unit The session unit
	 * @return self
	 */
	private function setSessionUnit( $session_unit ) {
		$this->session_unit = $session_unit;
		return $this;
	}

	/**
	 * Gets the cost of a single session.
	 *
	 * @return float
	 */
	public function getSessionCost() {
		return $this->session_cost;
	}

	/**
	 * Sets the cost of a single session.
	 *
	 * @param float $session_cost The session cost
	 * @return self
	 */
	private function setSessionCost( $session_cost ) {
		$this->session_cost = $session_cost;
		return $this;
	}

	/**
	 * Gets the duration of a booking: single or multiple sessions.
	 *
	 * @return EDD_BK_Booking_Duration
	 */
	public function getBookingDuration() {
		return $this->booking_duration;
	}

	/**
	 * Sets the duration of a booking: single or multiple sessions.
	 *
	 * @param EDD_BK_Booking_Duration $booking_duration The booking duration
	 *
	 * @return self
	 */
	private function setBookingDuration( $booking_duration ) {
		$this->booking_duration = $booking_duration;
		return $this;
	}

	/**
	 * Gets the minimum number of sessions that a customer can book, if the
	 * booking duration is multiple sessions.
	 *
	 * @return int
	 */
	public function getMinSessions() {
		return $this->min_sessions;
	}

	/**
	 * Sets the minimum number of sessions that a customer can book, if the
	 * booking duration is multiple sessions.
	 *
	 * @param int $min_sessions the min sessions
	 * @return self
	 */
	private function setMinSessions( $min_sessions ) {
		$this->min_sessions = ( $min_sessions > 0 )? $min_sessions : 1;
		return $this;
	}

	/**
	 * Gets the maximum number of sessions that a customer can book, if the
	 * booking duration is multiple sessions.
	 *
	 * @return int
	 */
	public function getMaxSessions() {
		return $this->max_sessions;
	}

	/**
	 * Sets the maximum number of sessions that a customer can book, if the
	 * booking duration is multiple sessions.
	 *
	 * @param int $max_sessions the max sessions
	 * @return self
	 */
	private function setMaxSessions( $max_sessions ) {
		$this->max_sessions = ( $max_sessions > 0 )? $max_sessions : 1;
		return $this;
	}

	/**
	 * Gets the Flag that determins whether or not unspecified dates in the availability
	 * table are to be available or not
	 * True: available, False: unavailable.
	 *
	 * @return bool
	 */
	public function getAvailabilityFill() {
		return $this->availability_fill;
	}

	/**
	 * Sets the Flag that determins whether or not unspecified dates in the availability
	 * table are to be available or not
	 * True: available, False: unavailable.
	 *
	 * @param bool $availability_fill The availability fill
	 * @return self
	 */
	private function setAvailabilityFill( $availability_fill ) {
		$this->availability_fill = $availability_fill;
		return $this;
	}

	/**
	 * Gets the availability table entries.
	 *
	 * @return EDD_BK_Availability
	 */
	public function getAvailability() {
		return $this->availability;
	}

	/**
	 * Sets the availability table entries.
	 *
	 * @param EDD_BK_Availability $availability The availability
	 * @return self
	 */
	private function setAvailability( EDD_BK_Availability $availability ) {
		$this->availability = $availability;
		return $this;
	}

	/**
	 * Creates an instance using the meta data of the post with the given ID.
	 * The post must be a 'Download'.
	 * 
	 * @param  int|string          $id The ID of the post whose meta data will be used to create the instance.
	 * @return EDD_BK_Booking|null     The created EDD_BK_Booking instance, or NULL if the post with the given
	 *                                 ID does not exist or is not a 'Download' type.
	 */
	public static function from_id( $id ) {
		if ( get_post( $id ) === NULL || get_post_type( $id ) !== 'download' ) {
			return NULL;
		}
		$meta = EDD_BK_Commons::meta_fields( $id );
		$booking = new static();
		$booking->setID( $id );
		$booking->setEnabled( intval( $meta['enabled'] ) === 1 );
		$booking->setSessionLength( intval( $meta['slot_duration'] ) );
		$booking->setSessionUnit( $meta['slot_duration_unit'] );
		$booking->setSessionCost( floatval( $meta['cost_per_slot'] ) );
		$booking->setBookingDuration( $meta['duration_type'] );
		$booking->setMinSessions( intval( $meta['min_slots'] ) );
		$booking->setMaxSessions( intval( $meta['max_slots'] ) );
		$booking->setAvailabilityFill( strtolower( $meta['availability_fill'] ) === 'true' );
		$booking->setAvailability( EDD_BK_Availability::fromMeta( $meta['availability'] ) );
		return $booking;
	}

}
