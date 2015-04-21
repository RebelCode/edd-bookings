<?php

/**
 * Availability range type for months.
 */
class EDD_BK_Availability_Entry_Months extends EDD_BK_Availability_Entry {

	/**
	 * Constructor.
	 *
	 * @param  mixed $type      IGNORED.
	 * @param  mixed $from      The range's start month
	 * @param  mixed $to        The range's end month
	 * @param  bool  $available Whether or not this date is available.
	 */
	public function __construct( $type, $from, $to, $available ) {
		parent::__construct( EDD_BK_Availability_Range_Type::$MONTHS, $from, $to, $available );
	}

	/**
	 * Sets the range's start.
	 *
	 * Overrides EDD_BK_Availability_Entry::set_from().
	 * 
	 * The time is saved as an index for the month, from 1 (January) through 12 (December),
	 * to match date('m').
	 * 
	 * @param mixed $from The range's start
	 */
	public function set_from( $from ) {
		$this->from = array_search( $from, array_keys( EDD_BK_Utils::month_options() ) ) + 1;
	}

	/**
	 * Sets the range's end.
	 *
	 * Overrides EDD_BK_Availability_Entry::set_to().
	 * 
	 * The time is saved as an index for the month, from 1 (January) through 12 (December),
	 * to match date('m').
	 * 
	 * @param mixed $from The range's end
	 */
	public function set_to( $to ) {
		$this->to = array_search( $to, array_keys( EDD_BK_Utils::month_options() ) ) + 1;
	}

	/**
	 * Checks if the given timestamp matches this availability range.
	 * 
	 * @param  int   $timestamp The timestamp to check.
	 * @return bool             True if the timestamp matches, false otherwise.
	 */
	public function matches( $timestamp ) {
		$month = intval( date( 'm', $timestamp ) );
		return $month >= $this->from && $month <= $this->to;
	}

}
