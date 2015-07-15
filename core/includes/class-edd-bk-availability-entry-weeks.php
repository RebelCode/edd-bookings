<?php

/**
 * Availability range type for weeks.
 */
class EDD_BK_Availability_Entry_Weeks extends EDD_BK_Availability_Entry {

	/**
	 * Constructor.
	 *
	 * @param  mixed $type       IGNORED.
	 * @param  mixed $from      The range's start week
	 * @param  mixed $to        The range's end week
	 * @param  bool  $available Whether or not this date is available.
	 */
	public function __construct( $type, $from, $to, $available ) {
		parent::__construct( EDD_BK_Availability_Range_Type::$WEEKS, $from, $to, $available );
	}

	/**
	 * Checks if the given timestamp matches this availability range.
	 * 
	 * @param  int   $timestamp The timestamp to check.
	 * @return bool             True if the timestamp matches, false otherwise.
	 */
	public function matches( $timestamp ) {
		$weeknum = intval( date( 'W', $timestamp ) );
		return $weeknum >= $this->from && $weeknum <= $this->to;
	}

}
