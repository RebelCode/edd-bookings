<?php

/**
 * Date-related utility functions.
 */
class Aventura_Bookings_Utils_Dates {

	/**
	 * Returns the number of seconds in a single 24-hour day.
	 * 
	 * @return int
	 */
	public static function dayInSeconds() {
		return 60 * 60 * 24;
	}

	/**
	 * Returns the day options in an associative array.
	 */
	public static function dayNames() {
		return array(
			'monday'	=>	'Monday',
			'tuesday'	=>	'Tuesday',
			'wednesday'	=>	'Wednesday',
			'thursday'	=>	'Thursday',
			'friday'	=>	'Friday',
			'saturday'	=>	'Saturday',
			'sunday'	=>	'Sunday',
		);
	}

	/**
	 * Returns the month options in an associative array.
	 */
	public static function monthNames() {
		return array(
			'january'	=> 'January',
			'february'	=> 'February',
			'march'		=> 'March',
			'april'		=> 'April',
			'may'		=> 'May',
			'june'		=> 'June',
			'july'		=> 'July',
			'august'	=> 'August',
			'september'	=> 'September',
			'october'	=> 'October',
			'november'	=> 'November',
			'december'	=> 'December',
		);
	}

	/**
	 * Returns the number of days in the timestamp.
	 * 
	 * @param  int $timestamp The timestamp
	 * @return int            The number of days.
	 */
	public static function num_days_from_timestamp( $timestamp ) {
		return floor( $timestamp / self::dayInSeconds() );
	}

	/**
	 * Returns the daystamp for the given timestamp.
	 *
	 * A daystamp is the timestamp for a day at zero hours, minutes and seconds.
	 * 
	 * @param  int $timestamp The timestamp
	 * @return int            The daystamp
	 */
	public static function daystamp_from_timestamp( $timestamp ) {
		return self::num_days_from_timestamp( $timestamp ) * self::dayInSeconds();
	}

	/**
	 * Returns the monthstamp for the given timestamp.
	 *
	 * A monthstamp is the timestamp for the first day of a month at zero hours,
	 * minutes and seconds.
	 * 
	 * @param  int $timestamp The timestamp
	 * @return int            The monthstamp
	 */
	public static function monthstamp_from_timestamp( $timestamp ) {
		$month = date( 'F Y', $timestamp );
		$firstday = strtotime( 'first day of ' . $month );
		return date( 'U', $firstday );
	}

	/**
	 * Returns the day-of-the-week index for the given day name.
	 * 
	 * @param  string   $dotw The name of the week day. (Ex. "monday")
	 * @return int|null       The day-of-the-week index for the day given, 1 through 7 for Monday to Sunday respectively.
	 *                        Returns NULL if the argument is not a valid week day name.
	 */
	public static function dotwIndex( $dotw ) {
		$i = array_search( strtolower( $dotw ), array_keys( self::dayNames() ) );
		return ( $i === false )? null : $i + 1;
	}

	/**
	 * Returns the month index for the given month name.
	 * 
	 * @param  string   $month The month name. (Ex. "january")
	 * @return int|null        The index of the month with the name given, 1 through 12 for January to December respectively.
	 *                         Returns NULL if the argument is not a valid month name.
	 */
	public static function monthIndex( $month ) {
		$i = array_search( strtolower( $month ), array_keys( self::monthNames() ) );
		return ( $i === false )? null : $i + 1;
	}

	/**
	 * Returns the name of the day of the week for the given index.
	 * 
	 * @param  integer $index       The index of the day, in the range [1, 7].
	 * @param  boolean $zero        If true, the index is treated as being the range [0, 7]
	 * @param  boolean $sundayFirst If true, the first index (0 or 1) is treated as Sunday. Otherwise it is Monday.
	 * @return string               The name of the day of the week for the given index.
	 */
	public static function dotwNameFromIndex( $index, $zero = FALSE, $sundayFirst = FALSE ) {
		$days = array_keys( self::dayNames() );
		$index = intval( $index );
		if ( $sundayFirst ) $index = ( $index + 6 ) % 7;
		if ( $zero ) return $days[ $index ];
		if ( isset($days[ $index - 1 ]) ) {
			return $days[ $index - 1];
		} else return ($zero? 0 : 1);
	}

	/**
	 * Returns the name of the month for the given index.
	 * 
	 * @param  integer $index       The index of the month, in the range [1, 12].
	 * @param  boolean $zero        If true, the index is treated as being the range [0, 11]
	 * @return string               The name of the month of the week for the given index.
	 */
	public static function monthNameFromIndex( $index, $zero = FALSE ) {
		$months = array_keys( self::monthNames() );
		$index = intval( $index );
		if ( $zero ) return $months[ $index ];
		if ( isset($months[ $index - 1 ]) ) {
			return $months[ $index - 1 ];
		} else return ($zero? 0 : 1);
	}

}
