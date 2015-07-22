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
	 * @see EDD_BK_Availability_Entry_Months::getMonthRange
	 */
	public function process() {
		return self::getMonthRange( $this->from, $this->to, $this->available );
	}

	/**
	 * Returns an availability range for the given range of months.
	 * 
	 * @param  int   $from  The range start month, as a timestamp. The non-month portions of the timestamp are ignored.
	 * @param  int   $to    The range end month, as a timestamp. The non-month portions of the timestamp are ignored.
	 * @param  bool  $avail Whether or not the range is available.
	 * @return array        An array of month availabilities for the given range.
	 */
	public static function getMonthRange( $from, $to, $avail ) {
		$range = array();
		// Get month numbers [1 - 12]
		$month = $from;
		// Calculate number of months in range
		$n = $to - $from + 1;
		$n = ( $n < 0 )? $n + 12 : $n;
		// Iterate for each month
		while( $n-- ) {
			$range[ strval( $month++ ) ] = $avail;
			if ( $month > 12 ) $month = 1;
		}
		return $range;
	}

}
