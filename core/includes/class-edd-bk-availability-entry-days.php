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
	 * The time is saved as an index for the day of the week from 1 (Monday) through
	 * 7 (Sunday), to match date('N').
	 * 
	 * @param mixed $from The range's start
	 */
	public function set_from( $from ) {
		$this->from = EDD_BK_Date_Utils::day_of_the_week_index( $from );
	}

	/**
	 * Sets the range's end.
	 *
	 * Overrides EDD_BK_Availability_Entry::set_to().
	 * 
	 * The time is saved as an index for the day of the week from 1 (Monday) through
	 * 7 (Sunday), to match date('N').
	 * 
	 * @param mixed $to The range's start
	 */
	public function set_to( $to ) {
		$this->to = EDD_BK_Date_Utils::day_of_the_week_index( $to );
	}

	/**
	 * @see EDD_BK_Availability_Entry_Days::getDayRange
	 */
	public function process() {
		return self::getDayRange( $this->from, $this->to, $this->available );
	}

	/**
	 * Returns an availability range for the given range of days.
	 * 
	 * @param  int   $from  The range start day, as a timestamp. The non-day portions of the timestamp are ignored.
	 * @param  int   $to    The range end day, as a timestamp. The non-day portions of the timestamp are ignored.
	 * @param  bool  $avail Whether or not the range is available.
	 * @return array        An array of day availabilities for the given range.
	 */
	public static function getDayRange( $from, $to, $avail ) {
		$range = array();
		$day = $from;
		// Calculate number of days in range
		$n = $to - $from + 1;
		$n = ( $n < 0 )? $n + 7 : $n;
		// Iterate for each day
		while( $n-- ) {
			$range[ strval( $day++ ) ] = $avail;
			if ( $day > 7 ) $day = 1;
		}
		return $range;
	}

}
