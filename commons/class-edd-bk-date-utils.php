<?php

/**
 * Date-related utility functions.
 */
class EDD_BK_Date_Utils {

	/**
	 * Returns the number of days in the timestamp.
	 * 
	 * @param  int $timestamp The timestamp
	 * @return int            The number of days.
	 */
	public static function num_days_from_timestamp( $timestamp ) {
		return floor( $timestamp / DAY_IN_SECONDS );
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
		return self::num_days_from_timestamp() * DAY_IN_SECONDS;
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
}
