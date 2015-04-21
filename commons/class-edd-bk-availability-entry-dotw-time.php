<?php

/**
 * Availability range type for times in days of the week.
 */
class EDD_BK_Availability_Entry_Dotw_Time extends EDD_BK_Availability_Entry {

	/**
	 * Constructor.
	 *
	 * @param  mixed $dotw      The day of the week.
	 * @param  mixed $from      The range's start time
	 * @param  mixed $to        The range's end time
	 * @param  bool  $available Whether or not this date is available.
	 */
	public function __construct( $dotw, $from, $to, $available ) {
		if ( is_string( $dotw ) ) {
			$dotw = strtoupper( $dotw );
			$dotw = EDD_BK_Availability_Range_Type::$$dotw;
		}
		parent::__construct( $dotw, $from, $to, $available );
	}

	/**
	 * Sets the range's start.
	 *
	 * Overrides EDD_BK_Availability_Entry::set_from().
	 * 
	 * The time is saved as a number of seconds.
	 * 
	 * @param mixed $from The range's start
	 */
	public function set_from( $from ) {
		$time = strtotime( $from );
		$hours = date( 'H', $time );
		$mins = date( 'i', $time );
		$this->from = ( ( $hours * 60 ) + $mins ) * 60;
	}

	/**
	 * Sets the range's end.
	 * 
	 * Overrides EDD_BK_Availability_Entry::set_to().
	 * 
	 * The time is saved as a number of seconds.
	 *
	 * @param mixed $to The range's end
	 */
	public function set_to( $to ) {
		$time = strtotime( $to );
		$hours = date( 'H', $time );
		$mins = date( 'i', $time );
		$this->to = ( ( $hours * 60 ) + $mins ) * 60;
	}

	/**
	 * Checks if the given timestamp matches this availability range.
	 * 
	 * @param  int   $timestamp The timestamp to check.
	 * @return bool             True if the timestamp matches, false otherwise.
	 */
	public function matches( $timestamp ) {
		// The timestamp without hours, minutes and seconds
		$daystamp = EDD_BK_Date_Utils::daystamp_from_timestamp( $timestamp );
		// The range limits are the daystamp added to the $from and $to variables,
		// which contain only hour and minute data.
		$lower = $daystamp + $this->from;
		$upper = $daystamp + $this->to;
		// Check if timestamp is in range.
		return $timestamp >= $lower && $timestamp <= $upper;
	}
}
