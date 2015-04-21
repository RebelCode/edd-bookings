<?php

/**
 * Availability range type for days of the week.
 */
class EDD_BK_Availability_Entry_Days extends EDD_BK_Availability_Entry {

	/**
	 * Constructor.
	 *
	 * @param  mixed $rangetype IGNORED
	 * @param  mixed $from      The range's start day
	 * @param  mixed $to        The range's end day
	 * @param  bool  $available Whether or not this date is available.
	 */
	public function __construct( $rangetype, $from, $to, $available ) {
		parent::__construct( EDD_BK_Availability_Range_Type::$DAYS, $from, $to, $available );
	}

	/**
	 * Sets the range's start.
	 *
	 * Overrides EDD_BK_Availability_Entry::set_from().
	 * 
	 * The time is saved as an index for the day of the week from 0 (Sunday) through
	 * 6 (Saturday), to match date('w').
	 * 
	 * @param mixed $from The range's start
	 */
	public function set_from( $from ) {
		$this->from = array_search( $from, array_keys( EDD_BK_Utils::day_options() ) );
		// This is to move from 0 = monday to 0 = sunday
		$this->from = ($this->from + 1) % 7;
	}

	/**
	 * Sets the range's end.
	 *
	 * Overrides EDD_BK_Availability_Entry::set_to().
	 * 
	 * The time is saved as an index for the day of the week from 0 (Sunday) through
	 * 6 (Saturday), to match date('w').
	 * 
	 * @param mixed $to The range's start
	 */
	public function set_to( $to ) {
		$this->to = array_search( $to, array_keys( EDD_BK_Utils::day_options() ) );
		// This is to move from 0 = monday to 0 = sunday
		$this->to = ($this->to + 1) % 7;
	}

	/**
	 * Checks if the given timestamp matches this availability range.
	 * 
	 * @param  int   $timestamp The timestamp to check.
	 * @return bool             True if the timestamp matches, false otherwise.
	 */
	public function matches( $timestamp ) {
		$dotw = intval( date( 'w', $timestamp ) );
		return $dotw >= $this->from && $dotw <= $this->to;
	}

}
