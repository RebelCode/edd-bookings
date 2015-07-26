<?php

/**
 * A single entry in the availability table.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Aventura\Bookings\Service\Availability
 */
class Aventura_Bookings_Service_Availability_Entry {

	/**
	 * The range type for this entry.
	 * 
	 * @var Aventura_Bookings_Service_Availability_Entry_Range_Type
	 */
	protected $type;

	/**
	 * The range lower boundary value.
	 * @var mixed
	 */
	protected $from;

	/**
	 * The range upper boundary value.
	 * @var mixed
	 */
	protected $to;

	/**
	 * The flag that determines if the entry is available or not.
	 * @var boolean
	 */
	protected $available;

	/**
	 * Constructor. Creates a new instance using the four parameters given, or the first parameter if it is an array.
	 *
	 * @param  Aventura_Bookings_Service_Availability_Entry_Range_Type $arg       The range type, or the array of values.
	 * @param  mixed                                                   $from      The range's start date/day/time
	 * @param  mixed                                                   $to        The range's end date/day/time
	 * @param  bool                                                    $available Whether or not this date is available.
	 */
	public function __construct( $arg, $from = null, $to = null, $available = null ) {
		// If the first arg is an array
		if ( is_array($arg) ) {
			// Get the variables from the array given
			list($type, $from, $to, $available) = array_values( $arg );
			// Get the range type
			if ( ! is_a($type, 'Aventura_Bookings_Service_Availability_Entry_Range_Type') ) {
				$type = Aventura_Bookings_Service_Availability_Entry_Range_Type::fromName( $type );
			}
		}
		// Set the data
		$this->type = $type;
		$this->setFrom($from);
		$this->setTo($to);
		$this->available = $available;
	}

	/**
	 * Sets the type.
	 *
	 * @param string|Aventura_Bookings_Service_Availability_Entry $type The range type to set.
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * Returns the range type for this entry.
	 * 
	 * @return Aventura_Bookings_Service_Availability_Entry_Range_Type The range type.
	 */
	public function getType() {
		$ret = NULL;
		if ( is_string($this->type) ) {
			$ret = Aventura_Bookings_Service_Availability_Entry_Range_Type::fromName( $this->type );
		} else if ( is_a($this->type, 'Aventura_Bookings_Service_Availability_Entry_Range_Type') ) {
			$ret = $this->type;
		}
		return $ret;
	}

	/**
	 * Sets the range's start.
	 * 
	 * @param mixed $from The range's start
	 */
	public function setFrom( $from ) {
		$this->from = $from;
	}

	/**
	 * Sets the range's end.
	 * 
	 * @param mixed $to The range's end
	 */
	public function setTo( $to ) {
		$this->to = $to;
	}

	/**
	 * Gets the range's start.
	 * 
	 * @return mixed $from The range's start
	 */
	public function getFrom() {
		return $this->sanitize($this->from);
	}

	/**
	 * Gets the range's end.
	 * 
	 * @return mixed $to The range's end
	 */
	public function getTo() {
		return $this->sanitize($this->to);
	}

	/**
	 * Returns whether this entry is available or not.
	 * 
	 * @return boolean True if the entry is available, False otherwise.
	 */
	public function isAvailable() {
		return $this->available;
	}

	/**
	 * Sanitizes the given value, according to the range type.
	 * 
	 * @param  mixed $field The value to sanitize.
	 * @return mixed        The sanitized value.
	 */
	protected function sanitize( $field ) {
		return call_user_func_array(array(__CLASS__, $this->getSanitizationFunction()), array($field));
	}

	/**
	 * Returns the sanitization function name that matches this entry's range type.
	 * 
	 * @return string The name of the function to be used for sanitization.
	 */
	protected function getSanitizationFunction() {
		return 'sanitize' . ucfirst($this->type->getUnit()) . 'Field';
	}

	/**
	 * Processes the entry into a range array, that can be used for checking
	 * dates and times.
	 * 
	 * @return array
	 */
	public function process() {
		// Get the unit
		$unit = $this->type->getUnit();
		// Generate the function name
		$fn = 'get' . ucfirst($unit) . 'Range';
		// Prepare the args array
		$args = array( $this->getFrom(), $this->getTo() );
		// If the unit is time
		if ($unit === 'time') {
			// Get the day of the week
			$dotw = (array) Aventura_Bookings_Utils_Dates::dotwIndex( $this->type->getSlugName() );
			// Check if the type is a single week day or a group
			if ( strtolower( $this->type->getGroup() ) === 'day groups' ) {
				// If a group, generate dotw indexes for the group
				switch ( $this->type->getSlugName() ) {
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
			// Add the day of the week to the args
			$args[] = $dotw;
		}
		// Finally add the available flag
		$args[] = $this->isAvailable();

		return call_user_func_array(array(__CLASS__, $fn), $args);
	}

	/**
	 * Sanitizes a field as a custom field.
	 * 
	 * @param  string $field The field, treated as a string date in the format mm/dd/yyyy.
	 * @return int           A timestamp representing the given string date.
	 */
	public static function sanitizeCustomField( $field ) {
		$parts = explode( '/', $field );
		return mktime(0, 0, 0, $parts[0], $parts[1], $parts[2]);
	}

	/**
	 * Sanitizes the field as a day field.
	 * 
	 * @param  string $field The field, treated as a day name.
	 * @return int           The day index. 1 through 7 for Monday through Sunday respectively.
	 */
	public static function sanitizeDayField( $field ) {
		return Aventura_Bookings_Utils_Dates::dotwIndex( $field );
	}

	/**
	 * Sanitizes the field as a month field.
	 * 
	 * @param  string $field The field, treated as a month name.
	 * @return int           The month index. 1 through 12 for January through December respectively.
	 */
	public static function sanitizeMonthField( $field ) {
		return Aventura_Bookings_Utils_Dates::monthIndex( $field );
	}

	/**
	 * Sanitizes the field as a time field.
	 * 
	 * @param  string $field The field, treated as a time string in the format HH:mm
	 * @return int           The sanitized timestamp.
	 */
	public static function sanitizeTimeField( $field ) {
		$time = strtotime( $field );
		$hours = date( 'H', $time );
		$mins = date( 'i', $time );
		return (($hours * 60) + $mins) * 60;
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
		$i = ( ( $to - $from ) / Aventura_Bookings_Utils_Dates::dayInSeconds() ) + 1;
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

	/**
	 * Returns the entry as an array.
	 * 
	 * @return array
	 */
	public function toArray() {
		return get_object_vars($this);
	}

}
