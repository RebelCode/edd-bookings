<?php

/**
 * "Enum" class for range types used in the Availability builder.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Aventura\Bookings\Service\Availability\Entry\Range
 */
class Aventura_Bookings_Service_Availability_Entry_Range_Type {

	// Group constants
	const GROUP_COMMON = 'Common';
	const GROUP_DAYS = 'Days';
	const GROUP_DAY_GROUPS = 'Day Groups';

	// Unit constants
	const UNIT_TIME = 'time';
	const UNIT_MONTH = 'month';
	const UNIT_WEEK = 'week';
	const UNIT_DAY = 'day';
	const UNIT_CUSTOM = 'custom';

	// Enum Constants
	public static $DAYS;
	public static $WEEKS;
	public static $MONTHS;
	public static $MONDAY;
	public static $TUESDAY;
	public static $WEDNESDAY;
	public static $THURSDAY;
	public static $FRIDAY;
	public static $SATURDAY;
	public static $SUNDAY;
	public static $ALL_WEEK;
	public static $WEEKDAYS;
	public static $WEEKENDS;
	public static $CUSTOM;

	/**
	 * The nice name used when outputting the range type.
	 * @var string
	 */
	protected $nice_name;

	/**
	 * A string that represents the range's unit.
	 * @var string
	 */
	protected $unit;

	/**
	 * The group that this range type belongs to.
	 * @var string
	 */
	protected $group;

	/**
	 * Constructor.
	 * 
	 * @param string $nice_name  The string used for outputting this range type.
	 * @param string $group      This range type's group
	 */
	public function __construct( $nice_name, $unit, $group ) {
		$this->nice_name = $nice_name;
		$this->unit = $unit;
		$this->group = $group;
	}

	/**
	 * Returns the nice name for this range type.
	 * 
	 * @return string A string containing the nice name for this range type.
	 */
	public function getName() {
		return $this->nice_name;
	}

	/**
	 * Returns the range unit.
	 * @return string A string containg the unit of the range.
	 */
	public function getUnit() {
		return $this->unit;
	}

	/**
	 * Returns the slug-like name of this range type.
	 *
	 * This is used in the range type dropdown as the key for the entries.
	 * 
	 * @return string A string containing the slug-like name for this range type.
	 */
	public function getSlugName() {
		return str_replace( ' ', '_', strtolower( $this->nice_name ) );
	}

	/**
	 * Returns the group for this range type.
	 * 
	 * @return string A string containing the name of the group for this range type.
	 */
	public function getGroup() {
		return $this->group;
	}

	/**
	 * Returns all the range types.
	 * 
	 * @return array An array containing all the range type instances/
	 */
	public static function getAll() {
		$class = new ReflectionClass( __CLASS__ );
		return $class->getStaticProperties();
	}

	/**
	 * Returns all the range types, grouped by their group property.
	 * 
	 * @return array An assoc array, with group names as keys and arrays of range type
	 *               nice names as values ('group' => ['type1', 'type2', ... ]).
	 */
	public static function getAllGrouped() {
		$grouped = array();
		foreach (self::getAll() as $rt) {
			$key = $rt->getSlugName();
			$grouped[ $rt->getGroup() ][ $key ] = $rt->getName();
		}
		return $grouped;
	}

	/**
	 * Finds the Aventura_Bookings_Service_Availability_Entry_Range_Type with the given name (case-insensitive).
	 *
	 * @param  string                                                       $name The nice name of the range type to find.
	 * @return Aventura_Bookings_Service_Availability_Entry_Range_Type|NULL       The found Aventura_Bookings_Service_Availability_Entry_Range_Type
	 *                                                                            instance, or NULL if not found.
	 */
	public static function fromName( $name ) {
		$name = str_replace( ' ', '_', strtoupper( $name ) );
		return isset( self::$$name )? self::$$name : NULL;
	}

	/**
	 * Initializes the constants.
	 */
	public static function initConstants() {
		//  Enum                    Nice Name          Time Unit              Group
		
		// Common
		self::$DAYS      = new self( 'Days',		self::UNIT_DAY,		self::GROUP_COMMON );
		self::$WEEKS     = new self( 'Weeks',		self::UNIT_WEEK,	self::GROUP_COMMON );
		self::$MONTHS    = new self( 'Months',		self::UNIT_MONTH,	self::GROUP_COMMON );
		// Custom
		self::$CUSTOM    = new self( 'Custom',		self::UNIT_CUSTOM,	self::GROUP_COMMON );
		// Days
		self::$MONDAY    = new self( 'Monday',		self::UNIT_TIME,	self::GROUP_DAYS );
		self::$TUESDAY   = new self( 'Tuesday',		self::UNIT_TIME,	self::GROUP_DAYS );
		self::$WEDNESDAY = new self( 'Wednesday',	self::UNIT_TIME,	self::GROUP_DAYS );
		self::$THURSDAY  = new self( 'Thursday',	self::UNIT_TIME,	self::GROUP_DAYS );
		self::$FRIDAY    = new self( 'Friday',		self::UNIT_TIME,	self::GROUP_DAYS );
		self::$SATURDAY  = new self( 'Saturday',	self::UNIT_TIME,	self::GROUP_DAYS );
		self::$SUNDAY    = new self( 'Sunday',		self::UNIT_TIME,	self::GROUP_DAYS );
		// Day Groups
		self::$ALL_WEEK  = new self( 'All Week',	self::UNIT_TIME,	self::GROUP_DAY_GROUPS );
		self::$WEEKDAYS  = new self( 'Weekdays',	self::UNIT_TIME,	self::GROUP_DAY_GROUPS );
		self::$WEEKENDS  = new self( 'Weekends',	self::UNIT_TIME,	self::GROUP_DAY_GROUPS );
	}

}

// Called once (when the file is loaded) to initialize the constants.
Aventura_Bookings_Service_Availability_Entry_Range_Type::initConstants();
