<?php

/**
 * Represents a bookable service.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Aventura\Bookings\Service
 */
class Aventura_Bookings_Service extends Aventura_Bookings_Object {

	/**
	 * The default values for the fields of this class, used by the constructor.
	 * @var array
	 */
	protected static $_defaultValues = array(
		'id'				=>	NULL,
		'session_length'	=>	1,
		'session_unit'		=>	Aventura_Bookings_Service_Session_Unit::HOURS,
		'session_type'		=>	Aventura_Bookings_Service_Session_Type::FIXED,
		'session_cost'		=>	0,
		'min_sessions'		=>	1,
		'max_sessions'		=>	1
	);

	/**
	 * Constructor.
	 *
	 * @param string|int|array $arg Optional The ID of the service or an array of data fields to set to the service.
	 */
	public function __construct( $arg = array() ) {
		if ( ! is_array( $arg ) ) $arg = array( 'id' => $arg );
		// Merge with defaults
		$data = array_merge(self::$_defaultValues, $arg);
		// Set the data
		$this->setDataUsingMethod($data);
		// If availability is null, create a new instance
		if ( !is_a($this->getData('availability'), 'Aventura_Bookings_Service_Availability') ) {
			$this->setData('availability', new Aventura_Bookings_Service_Availability());
		}
	}

	/**
	 * Returns the length of a single session.
	 * 
	 * @return int
	 */
	public function getSessionLength() {
		$length = $this->getData('session_length');
		return max(1, intval( trim($length) ));
	}

	/**
	 * Returns the unit of a session.
	 * 
	 * @return string (Aventura_Bookings_Service_Session_Unit)
	 */
	public function getSessionUnit() {
		$unit = $this->getData('session_unit');
		return Aventura_Bookings_Service_Session_Unit::isValid($unit)?
			$unit : Aventura_Bookings_Service_Session_Unit::HOURS;
	}

	/**
	 * Returns the session type.
	 * 
	 * @return string (Aventura_Bookings_Service_Session_Type)
	 */
	public function getSessionType() {
		$type = $this->getData('session_type');
		return Aventura_Bookings_Service_Session_Type::isValid($type)?
			$type : Aventura_Bookings_Service_Session_Type::FIXED;	
	}

	/**
	 * Returns the cost per session.
	 * 
	 * @return float
	 */
	public function getSessionCost() {
		$cost = $this->getData('session_cost');
		return floatval($cost);
	}

	/**
	 * Returns the minimum number of sessions bookable by a user.
	 * 
	 * @return int
	 */
	public function getMinSessions() {
		$min = $this->getData('min_sessions');
		return max(1, intval($min));
	}

	/**
	 * Returns the maximum number of sessions bookable by a user.
	 * 
	 * @return int
	 */
	public function getMaxSessions() {
		$max = $this->getData('max_sessions');
		return max(1, intval($max));
	}

	/**
	 * Sets the session availability.
	 * 
	 * @param Aventura_Bookings_Service_Availability|array $availability The availability instance to set or and array
	 *                                                                   containing the data to construct a new one.
	 */
	public function setAvailability( $availability ) {
		if ( is_array( $availability ) ) {
			$availability = new Aventura_Bookings_Service_Availability( $availability );
		}
		else if ( !is_a($availability, 'Aventura_Bookings_Service_Availability') ) {
			$availability = new Aventura_Bookings_Service_Availability();
		}
		$this->setData('availability', $availability);
	}

	/**
	 * Gets the processed availability, with booked dates disabled.
	 * 
	 * @param  Aventura_Bookings_Booking_Controller_Interface|NULL $bookingsController The Bookings Controller used to retrieve the bookings.
	 * @param  string|int|array                                    $date               (Optional) The date for which to generate the processed
	 *                                                                                 availability, or an array of two dates for a range.
	 *                                                                                 Default: NULL.
	 * @return array                                                                   The processed availability.
	 */
	public function getProcessedAvailability( $bookingsController = NULL, $date = NULL ) {
		// Process the availability
		$processedAvailability = $this->getAvailability()->process();
		// If a controller was given, continue processing
		if ( $bookingsController !== NULL ) {
			// If the given controller is invalid, throw an exception
			if ( !$bookingsController instanceof Aventura_Bookings_Booking_Controller_Interface ) {
				throw new IllegalArgumentException(
					'Aventura_Bookings_Service::getProcessedAvailability() expects argument to implement Aventura_Bookings_Booking_Controller_Interface.'
				);
			}
			// Get the bookings
			$bookings = $bookingsController->getBookingsForService( $this->getId(), $date );
			// For each booking
			foreach ( $bookings as $booking ) {
				// Get the session unit for this service
				$unit = $booking->getSessionUnit();
				// Calculate the duration of the booking
				$duration = $booking->getDuration();
				if ( $booking->isSessionUnit(Aventura_Bookings_Service_Session_Unit::HOURS, Aventura_Bookings_Service_Session_Unit::MINUTES) ) {
					// Change duration to seconds
					$duration *= 60;
					if ($unit === Aventura_Bookings_Service_Session_Unit::HOURS) {
						$duration *= 60;
					}
					// Get the date and time
					$date = $booking->getDate();
					$from = $booking->getTime();
					// Create the custom range
					$range = Aventura_Bookings_Service_Availability_Entry::getCustomTimeRange($from, $from + $duration, $date, false);
					// Add to the processed availability
					if ( !isset($processedAvailability['time']) ) $processedAvailability['time'] = array();
					if ( !isset($processedAvailability['time']['custom']) ) $processedAvailability['time']['custom'] = array();
					if ( !isset($processedAvailability['time']['custom'][$date]) ) $processedAvailability['time']['custom'][$date] = array();
					$processedAvailability['time']['custom'][$date] = $processedAvailability['time']['custom'][$date] + $range;
				} else {
					if ($unit === Aventura_Bookings_Service_Session_Unit::WEEKS) {
						$duration *= 7;
					}
					// Remove 1 day for range lower boudary exclusivity
					$duration--;
					// Change days to seconds
					$duration *= Aventura_Bookings_Utils_Dates::dayInSeconds();
					// Set the `from` to the selected booking date (timestamp)
					$from = $booking->getDate();
					// Create a custom range for the dates
					$range = Aventura_Bookings_Service_Availability_Entry::getCustomRange($from, $from + $duration, false);
					// Add to the processed availability
					if ( !isset($processedAvailability['custom']) ) $processedAvailability['custom'] = array();
					$processedAvailability['custom'] = EDD_BK_Utils::array_merge_recursive_distinct( $processedAvailability['custom'], $range);
				}
			}
		}

		return array_reverse($processedAvailability);
	}

	/**
	 * Returns true if the session unit is equal to at least one
	 * of the given arguments.
	 *
	 * @param   string ... Any number of string arguments to check against
	 *                     this booking's session unit.
	 * @return boolean     True if this booking's session unit is equal to at
	 *                     least one of the given arguments, false otherwise.
	 */
	public function isSessionUnit(/* arg0, arg1, ... */) {
		$args = func_get_args();
		$bool = false;
		$unit = $this->getData('session_unit');
		foreach ( $args as $arg ) {
			$bool = $bool || ( $unit == $arg );
		}
		return $bool;
	}

	/**
	 * Checks if a given date is available for users according to this availability's rules.
	 * 
	 * @param  int                                                 $date               The date to check, as a timestamp.
	 * @param  Aventura_Bookings_Booking_Controller_Interface|NULL $bookingsController The Bookings Controller used to retrieve the bookings.
	 * @return boolean                                                                 True if the date is available, false if not.
	 */
	public function isDateAvailable( $date, $bookingsController = null ) {
		$year	= absint( date( 'Y', $date ) );
		$month	= absint( date( 'm', $date ) );
		$day	= absint( date( 'd', $date ) );
		$dotw	= absint( date( 'N', $date ) );
		$week	= absint( date( 'W', $date ) );
		$available = $this->getAvailability()->getFill();
		$matched = false;
		$entries = $this->getProcessedAvailability($bookingsController);

		// Iterate each entry in the processed availability
		foreach ( $entries as $unit => $rules ) {
			// $unit is entry's unit type
			// $rules are the compiled rules for that unit, in reverse order (last entry first)
			switch ( $unit ) {
				// If unit is month
				case 'month':
					// If the month rules contan a rule for the given date's month
					if ( isset( $rules[ $month ] ) ) {
						$available = $rules[ $month ];
						$matched = true;
						break 2;
					}
				break;
				// If unit is week
				case 'week':
					// If the week rules contain a rule for the given date's week
					if ( isset( $rules[ $week ] ) ) {
						$available = $rules[ $week ];
						$matched = true;
						break 2;
					}
				break;
				// If unit is day
				case 'day':
					// If the day rules contain a rule for the given date's dotw
					if ( isset( $rules[ $dotw ] ) ) {
						$available = $rules[ $dotw ];
						$matched = true;
						break 2;
					}
				break;
				// If unit is custom
				case 'custom':
					// If the custom rules contain a rule for the given date
					if ( isset( $rules[ $year ][ $month ][ $day ] ) ) {
						$available = $rules[ $year ][ $month ][ $day ];
						$matched = true;
						break 2;
					}
				break;
			}
		}

		$unavailable = ($matched && !$available);
		$timeUnit = $this->isSessionUnit(Aventura_Bookings_Service_Session_Unit::HOURS, Aventura_Bookings_Service_Session_Unit::MINUTES);
		$hasTimes = count($this->getTimesForDate($date, $bookingsController)) > 0;

		return ($timeUnit && $hasTimes && !$unavailable) || $available;
	}

	/**
	 * Compiles a list of available times for the given date.
	 * 
	 * @param  int                                                 $date               The timestamp of the date, for which to return the available times.
	 * @param  Aventura_Bookings_Booking_Controller_Interface|NULL $bookingsController The Bookings Controller used to retrieve the bookings.
	 * @return array                                                                   An array of times, each in the format: "hh:mm|sessions", where sessions
	 *                                                                                 is the maximum allowed number of sessions that can be booked for this time.
	 */
	public function getTimesForDate($date, $bookingsController = NULL) {
		// Get the day of the week
		$day = absint( date( 'N', $date ) );
		// Remove the time from the date
		$date = mktime(0, 0, 0, date('n', $date), date('j', $date), date('Y', $date));

		// Get the processed entries
		$entries = $this->getProcessedAvailability($bookingsController);
		// And filter out only the time entries
		$entries = isset($entries['time'])? $entries['time'] : array();
		// Get the availability fill
		$fill = $this->getAvailability()->getFill();

		// Calculate the session length in seconds
		// Session unit is either hour or minutes for time ranges.
		$slength = $this->getSessionLength() * ( $this->isSessionUnit( 'hours' )? 3600 : 60 );
		// Minimum session length in seconds.
		$min_slength = $slength * $this->getMinSessions();
		// Maximum session length in seconds.
		$max_slength = $slength * $this->getMaxSessions();

		// Prepare the master list
		// time subarray holds timestmaps
		// sessions subarray holds matching max number of sessions selectable for each time entry
		$master_list = array( 'time' => array(), 'sessions' => array() );

		// Time entries to iterate over
		$time_entries = array();
		// Add a full day entry for the fill
		$time_entries[] = array( 'from' => 0, 'to' => Aventura_Bookings_Utils_Dates::dayInSeconds(), 'available' => $fill );
		// Add time entries for this day, if they exist
		if ( isset($entries[$day]) ) {
			$time_entries = array_merge($time_entries, $entries[$day]);
		}
		// Also include custom entries, which can be booked sessions
		if ( isset($entries['custom']) && isset($entries['custom'][$date]) ) {
			$time_entries = array_merge($time_entries, $entries['custom'][$date]);
		}

		foreach ( $time_entries as $i => $rules ) {
			list($from, $to, $available) = array_values( $rules );
			$c = $from;
			$buffer = array( 'time' => array(), 'sessions' => array() );
			while ( $c < $to && ( $c + $min_slength ) <= $to ) {
				$diff = $to - $c;
				$sessionsInDiff = floor( $diff / $slength );
				// Add to buffers
				$buffer['time'][] = $c;
				$buffer['sessions'][ $c ] = $sessionsInDiff;
				// Increment to time of next session
				$c += $slength;
			}
			if ( $available ) {
				$master_list['time'] = array_unique( array_merge( $master_list['time'], $buffer['time'] ) );
				$master_list['sessions'] = $master_list['sessions'] + $buffer['sessions'];
			} else {
				$master_list['time'] = array_diff( $master_list['time'], $buffer['time'] );
			}
		}

		$final_list = array();
		foreach ( $master_list['time'] as $time ) {
			$final_list[] = $time . '|' . $master_list['sessions'][ $time ];
		}
		return $final_list;
	}

	/**
	 * Returns the service as an array.
	 * 
	 * @return arrray
	 */
	public function toArray(array $attrs = array()) {
		$data = $this->getData();
		unset($data['availability']);
		return array_merge($data, array(
			'availability'	=>	$this->getAvailability()->toArray()
		));
	}

}
