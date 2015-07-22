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
		$day = intval( date( 'N', $timestamp ) );
		// Switch the day group
		switch( $this->type->get_slug_name() ) {
			case 'weekdays':	return $day > 0 && $day < 6;
			case 'weekends':	return $day === 6 || $day === 7;
			case 'all_week':	return true;
		}
		
		// Otherwise, return false. This should not occur.
		return false;
	}

	/**
	 * @see EDD_BK_Availability_Entry_Dotw_Time::getTimeRange
	 */
	public function process() {
		$dotw = (array) EDD_BK_Date_Utils::day_of_the_week_index( $this->type->get_slug_name() );
		// Check if the type is a single week day or a group
		if ( strtolower( $this->type->getGroup() ) === 'day groups' ) {
			// If a group, generate dotw indexes for the group
			switch ( $this->type->get_slug_name() ) {
				case 'all_week':
					$dotw = range(1, 7);
					break;
				case 'weekdays':
					$dotw = range(1, 5);
					break;
				case 'weekends':
					$dotw = range(6, 7);
					break;
				default:
					$dotw = array();
					break;
			}
		}
		return self::getTimeRange( $this->from, $this->to, $dotw, $this->available );
	}

	/**
	 * Returns an array containing the range data for this entry.
	 * 
	 * @param  int   $from  The range start time, in seconds.
	 * @param  int   $to    The range end time, in seconds.
	 * @param  array $dotw  An array of day-of-the-week indexes that match the range.
	 * @param  bool  $avail Whether or not the range is available.
	 * @return array        An array of day availabilities for the given range.
	 */
	public static function getTimeRange( $from, $to, $dotw, $available ) {
		$dotw = (array) $dotw;
		$result = array();
		foreach ( $dotw as $day ) {
			$result[ $day ] = array(
				'from'		=>	$from,
				'to'		=>	$to,
				'available'	=>	$available
			);
		}
		return $result;
	}
}
