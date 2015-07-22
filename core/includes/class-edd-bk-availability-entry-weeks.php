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
	 * @see EDD_BK_Availability_Entry_Weeks::getWeekRange
	 */
	public function process() {
		return self::getWeekRange( $this->from, $this->to, $this->available );
	}

	/**
	 * Returns an availability range for the given range of weeks.
	 * 
	 * @param  int   $from  The range start week, as a week number.
	 * @param  int   $to    The range end week, as a week number.
	 * @param  bool  $avail Whether or not the range is available.
	 * @return array        An array of week availabilities for the given range.
	 */
	public static function getWeekRange( $from, $to, $avail ) {
		$range = array();
		// Get week numbers [1 - 52]
		$week = $from;
		// Calculate number of weeks in range
		$n = $to - $from + 1;
		$n = ( $n < 0 )? $n + 52 : $n;
		// Iterate for each week
		while( $n-- ) {
			$range[ strval( $week++ ) ] = $avail;
			if ( $week > 52 ) $week = 1;
		}
		return $range;
	}

}
