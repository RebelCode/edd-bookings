<?php

/**
 * Availability range type for custom dates.
 */
class EDD_BK_Availability_Entry_Custom extends EDD_BK_Availability_Entry {

	/**
	 * Constructor.
	 *
	 * @param  mixed $tye       IGNORED.
	 * @param  mixed $from      The range's start date
	 * @param  mixed $to        The range's end date
	 * @param  bool  $available Whether or not this date is available.
	 */
	public function __construct( $type, $from, $to, $available ) {
		parent::__construct( EDD_BK_Availability_Range_Type::$CUSTOM, $from, $to, $available );
	}

	/**
	 * Sets the range's start.
	 *
	 * Overrides EDD_BK_Availability_Entry::set_from().
	 * 
	 * The date is saved as a daystamp; a timestamp of the date at exactly 00:00:00.
	 * 
	 * @param mixed $from The range's start
	 */
	public function set_from( $from ) {
		$parts = explode( '/', $from );
		$this->from = mktime(0, 0, 0, $parts[0], $parts[1], $parts[2]);
	}

	/**
	 * Sets the range's start.
	 *
	 * Overrides EDD_BK_Availability_Entry::set_to().
	 * 
	 * The date is saved as a daystamp; a timestamp of the date at exactly 00:00:00.
	 * 
	 * @param mixed $from The range's start
	 */
	public function set_to( $to ) {
		$parts = explode( '/', $to );
		$this->to = mktime(0, 0, 0, $parts[0], $parts[1], $parts[2]);
	}

	/**
	 * Checks if the given timestamp matches this availability range.
	 * 
	 * @param  int   $timestamp The timestamp to check.
	 * @return bool             True if the timestamp matches, false otherwise.
	 */
	public function matches( $timestamp ) {
		return $timestamp >= $this->from && $timestamp <= $this->to;
	}

}
