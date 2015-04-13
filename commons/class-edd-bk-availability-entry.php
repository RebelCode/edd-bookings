<?php

/**
 * A single entry in the availability table.
 */
class EDD_BK_Availability_Entry {

	private $type;
	private $from;
	private $to;
	private $available;

	/**
	 * Constructor.
	 *
	 * @param  EDD_BK_Range_Type $type      The range type.
	 * @param  mixed             $from      The range's start date/day/time
	 * @param  mixed             $to        The range's end date/day/time
	 * @param  bool              $available Whether or not this date is available.
	 */
	public function __construct( $type, $from, $to, $available ) {
		$this->type = $type;
		$this->from = intval( $from );
		$this->to = intval( $to );
		$this->available = $available;
	}

	/**
	 * Checks if the given timestamp matches this availability range.
	 * 
	 * @param  int  $timestamp The timestamp to check.
	 * @return bool            True if it matches, false otherwise.
	 */
	public function matches( $timestamp ) {
		return $this->type->matches( $timestamp, $this->from, $this->to );
	}

}

/**
 * Availability range type for days of the week.
 */
class EDD_BK_Availability_Days_Entry extends EDD_BK_Availability_Entry {

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

/**
 * Availability range type for weeks.
 */
class EDD_BK_Availability_Weeks_Entry extends EDD_BK_Availability_Entry {

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

/**
 * Availability range type for months.
 */
class EDD_BK_Availability_Months_Entry extends EDD_BK_Availability_Entry {

	/**
	 * Checks if the given timestamp matches this availability range.
	 * 
	 * @param  int   $timestamp The timestamp to check.
	 * @return bool             True if the timestamp matches, false otherwise.
	 */
	public function matches( $timestamp, $from, $to ) {
		$month = intval( date( 'm', $timestamp ) );
		return $month >= $this->from && $month <= $this->to;
	}
}

/**
 * Availability range type for times in days of the week.
 */
class EDD_BK_Availability_Dotw_Time_Entry extends EDD_BK_Availability_Entry {

	/**
	 * Checks if the given timestamp matches this availability range.
	 * 
	 * @param  int   $timestamp The timestamp to check.
	 * @return bool             True if the timestamp matches, false otherwise.
	 */
	public function matches( $timestamp ) {
		// Divide by days and floor, to remove hours, minutes and seconds.
		// This essentially gives the number of days in the timestamp.
		$days = floor( $timestamp / DAY_IN_SECONDS );
		// The timestamp without hours, minutes and seconds
		$daystamp = $days * DAY_IN_SECONDS;
		// The range limits are the daystamp added to the $from and $to variables,
		// which contain only hour and minute data.
		$lower = $daystamp + $this->from;
		$upper = $daystamp + $this->to;
		// Check if timestamp is in range.
		return $timestamp >= $lower && $timestamp <= $upper;
	}
}

/**
 * Availability range type for times for grouped days of the week.
 */
class EDD_BK_Availability_Dotw_Group_Time_Entry extends EDD_BK_Availability_Entry {

	/**
	 * Checks if the given timestamp matches this availability range.
	 * 
	 * @param  int   $timestamp The timestamp to check.
	 * @return bool             True if the timestamp matches, false otherwise.
	 */
	public function matches( $timestamp ) {
		return false;
	}
}

/**
 * Availability range type for custom dates.
 */
class EDD_BK_Availability_Custom_Entry extends EDD_BK_Availability_Entry {

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
