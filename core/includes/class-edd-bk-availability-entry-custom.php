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
	 * @see EDD_BK_Availability_Entry_Custom::getCustomRange
	 */
	public function process() {
		return self::getCustomRange( $this->from, $this->to, $this->available );
	}

	/**
	 * Returns an availability range for the given range of timestamps.
	 * 
	 * @param  int   $from  The range start date, as a timestamp. The time portion of the timestamp is ignored.
	 * @param  int   $to    The range end date, as a timestamp. The time portion of the timestamp is ignored.
	 * @param  bool  $avail Whether or not the range is available.
	 * @return array        An array containing the nested availabilities.
	 */
	public static function getCustomRange( $from, $to, $avail ) {
		if ( empty( $from ) || empty ( $to ) || $to < $from ) return null;

		$range = array();
		// Iterate for each day between $from and $to
		$i = ( ( $to - $from ) / DAY_IN_SECONDS ) + 1;
		while ( $i-- ) {
			// Add $i days to the $from date
			$added_day = strtotime( "+{$i} days", $from );
			// Get the date parts
			$year  = absint( date( 'Y', $added_day ) );
			$month = absint( date( 'n', $added_day ) );
			$day   = absint( date( 'j', $added_day ) );
			// Set the availability for this range
			$range[ strval( $year ) ][ strval( $month ) ][ strval( $day ) ] = $avail;
		}
		return $range;
	}

}
