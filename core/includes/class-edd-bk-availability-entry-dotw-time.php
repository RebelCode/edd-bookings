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
			// $dotw = strtoupper( $dotw );
			$dotw = EDD_BK_Availability_Range_Type::from_name( $dotw );
		}
		parent::__construct( $dotw, $from, $to, $available );
	}

	/**
	 * Sets the range's start.
	 *
	 * Overrides EDD_BK_Availability_Entry::set_from().
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

	/**
	 * Checks if the given timestamp's day of the week matches this entry's
	 * day of the week range.
	 *
	 * @param  int   $timestamp The timestamp to check.
	 * @return bool             True if the timestamp partially matches, false otherwise.
	 */
	public function partiallyMatches( $timestamp ) {
		// Check if the type is a single week day
		if ( strtolower( $this->type->getGroup() ) === 'days' ) {
			// Check if the timestamp's day matches this entry's week day
			$this_day = $this->type->get_name();
			$timestamp_day = date( 'l', $timestamp );
			return strcasecmp( $this_day, $timestamp_day ) === 0;
		}
		// If it's not a single week day, then it is a day group ....
		
		// Get the day of the week index
		$day = intval( date( 'w', $timestamp ) );
		// Switch the day group
		switch( $this->type->get_slug_name() ) {
			case 'weekdays':	return $day > 0 && $day < 6;
			case 'weekends':	return $day === 0 || $day === 1;
			case 'all_week':	return true;
		}
		
		// Otherwise, return false. This should not occur.
		return false;
	}

}
