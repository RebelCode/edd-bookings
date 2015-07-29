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
	 * Checks if a given date is available for users according to this
	 * availability's rules.
	 * 
	 * @param  int     $date The date to check, as a timestamp.
	 * @return boolean       True if the date is available, false if not.
	 */
	public function isDateAvailable( $date ) {
		$year	= absint( date( 'Y', $date ) );
		$month	= absint( date( 'm', $date ) );
		$day	= absint( date( 'd', $date ) );
		$dotw	= absint( date( 'N', $date ) );
		$week	= absint( date( 'W', $date ) );
		$available = $this->getAvailability()->getFill();
		$entries = $this->getAvailability()->process();

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
						break 2;
					}
				break;
				// If unit is month
				case 'week':
					// If the week rules contain a rule for the given date's week
					if ( isset( $rules[ $week ] ) ) {
						$available = $rules[ $week ];
						break 2;
					}
				break;
				// If unit is month
				case 'day':
					// If the day rules contain a rule for the given date's dotw
					if ( isset( $rules[ $dotw ] ) ) {
						$available = $rules[ $dotw ];
						break 2;
					}
				break;
				// If unit is month
				case 'custom':
					// If the custom rules contain a rule for the given date
					if ( isset( $rules[ $year ][ $month ][ $day ] ) ) {
						$available = $rules[ $year ][ $month ][ $day ];
						break 2;
					}
				break;
			}
		}

		return $available;
	}

	/**
	 * Compiles a list of available times for the given date.
	 * 
	 * @param  int   $date The timestamp of the date, for which to return the available times.
	 * @return array       An array of times, each in the format: "hh:mm|sessions", where sessions
	 *                     is the maximum allowed number of sessions that can be booked for this time.
	 */
	public function getTimesForDate( $date ) {
		// If the date is not available stop.
		if ( ! $this->isDateAvailable( $date ) ) return array();

		// Get the day of the week
		$day = absint( date( 'N', $date ) );

		// Get the processed entries
		$entries = $this->getAvailability()->process();
		// We only need the time entries. If they do not exist, stop.
		if ( ! isset( $entries['time'] ) ) return array();
		$entries = $entries['time'];

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
		// Check if rules for the date's DOTW exist in the time entries. If not, stop.
		if ( ! isset( $entries[ $day ] ) ) return array();

		foreach ( $entries[ $day ] as $i => $rules ) {
			list($from, $to, $available) = array_values( $rules );
			$c = $from;
			$buffer = array( 'time' => array(), 'sessions' => array() );
			while ( $c < $to && ( $c + $min_slength ) <= $to ) {
				// The maximum amount of seconds that are bookable for this time
				// Either the time + the maximum amount of seconds, of the `$to` time.
				$max_seconds = min( $c + $max_slength, $to );
				// Calculate the maximum number of allowed sessions to be booked
				$max_sessions = ( $max_seconds - $c ) / $slength;
				// Add to buffers
				$buffer['time'][] = $c;
				$buffer['sessions'][ $c ] = $max_sessions;
				// Add to $c to move to next time
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
	public function toArray() {
		$data = $this->getData();
		unset($data['availability']);
		return array_merge($data, array(
			'availability'	=>	$this->getAvailability()->toArray()
		));
	}

}
