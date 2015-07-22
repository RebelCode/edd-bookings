<?php

/**
 * "Enum" class for range types used in the Availability builder.
 */
class EDD_BK_Availability_Range_Type {

	const GROUP_COMMON = 'Common';
	const GROUP_DAYS = 'Days';
	const GROUP_DAY_GROUPS = 'Day Groups';

	const UNIT_TIME = 'time';
	const UNIT_MONTH = 'month';
	const UNIT_WEEK = 'week';
	const UNIT_DAY = 'day';
	const UNIT_CUSTOM = 'custom';

	/***** CONSTANTS ***********/
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
	private $nice_name;

	/**
	 * A string that represents the range's unit.
	 * @var string
	 */
	private $unit;

	/**
	 * The group that this range type belongs to.
	 * @var string
	 */
	private $group;

	/**
	 * The class name of EDD_BK_Availability_Entry subclass that handles
	 * this range type.
	 * @var string
	 */
	private $classname;

	/**
	 * Constructor.
	 * 
	 * @param string $nice_name  The string used for outputting this range type.
	 * @param string $group      This range type's group
	 */
	public function __construct( $nice_name, $unit, $group, $classname ) {
		$this->nice_name = $nice_name;
		$this->unit = $unit;
		$this->group = $group;
		$this->classname = $classname;
	}

	/**
	 * Returns the nice name for this range type.
	 * 
	 * @return string A string containing the nice name for this range type.
	 */
	public function get_name() {
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
	public function get_slug_name() {
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
	 * Returns the name of the class that handles this range type.
	 * 
	 * @return string A string containing the class name.
	 */
	public function get_handler_class_name() {
		return $this->classname;
	}

	/**
	 * Initializes the constants.
	 */
	public static function init_constants() {
		//  Enum                    Nice Name          Time Unit              Group                    Handler Class name
		  
		// Common
		self::$DAYS      = new self( 'Days',		self::UNIT_DAY,		self::GROUP_COMMON, 	'EDD_BK_Availability_Entry_Days' );
		self::$WEEKS     = new self( 'Weeks',		self::UNIT_WEEK,	self::GROUP_COMMON, 	'EDD_BK_Availability_Entry_Weeks' );
		self::$MONTHS    = new self( 'Months',		self::UNIT_MONTH,	self::GROUP_COMMON, 	'EDD_BK_Availability_Entry_Months' );
		// Custom
		self::$CUSTOM    = new self( 'Custom',		self::UNIT_CUSTOM,	self::GROUP_COMMON, 	'EDD_BK_Availability_Entry_Custom' );
		// Days
		self::$MONDAY    = new self( 'Monday',		self::UNIT_TIME,	self::GROUP_DAYS, 		'EDD_BK_Availability_Entry_Dotw_Time' );
		self::$TUESDAY   = new self( 'Tuesday',		self::UNIT_TIME,	self::GROUP_DAYS, 		'EDD_BK_Availability_Entry_Dotw_Time' );
		self::$WEDNESDAY = new self( 'Wednesday',	self::UNIT_TIME,	self::GROUP_DAYS, 		'EDD_BK_Availability_Entry_Dotw_Time' );
		self::$THURSDAY  = new self( 'Thursday',	self::UNIT_TIME,	self::GROUP_DAYS, 		'EDD_BK_Availability_Entry_Dotw_Time' );
		self::$FRIDAY    = new self( 'Friday',		self::UNIT_TIME,	self::GROUP_DAYS, 		'EDD_BK_Availability_Entry_Dotw_Time' );
		self::$SATURDAY  = new self( 'Saturday',	self::UNIT_TIME,	self::GROUP_DAYS, 		'EDD_BK_Availability_Entry_Dotw_Time' );
		self::$SUNDAY    = new self( 'Sunday',		self::UNIT_TIME,	self::GROUP_DAYS, 		'EDD_BK_Availability_Entry_Dotw_Time' );
		// Day Groups
		self::$ALL_WEEK  = new self( 'All Week',	self::UNIT_TIME,	self::GROUP_DAY_GROUPS, 'EDD_BK_Availability_Entry_Dotw_Time' );
		self::$WEEKDAYS  = new self( 'Weekdays',	self::UNIT_TIME,	self::GROUP_DAY_GROUPS, 'EDD_BK_Availability_Entry_Dotw_Time' );
		self::$WEEKENDS  = new self( 'Weekends',	self::UNIT_TIME,	self::GROUP_DAY_GROUPS, 'EDD_BK_Availability_Entry_Dotw_Time' );
	}

	/**
	 * Returns all the range types.
	 * 
	 * @return array An array containing all the range type instances/
	 */
	public static function get_all() {
		$class = new ReflectionClass( __CLASS__ );
		return $class->getStaticProperties();
	}

	/**
	 * Returns all the range types, grouped by their group property.
	 * 
	 * @return array An assoc array, with group names as keys and arrays of range type
	 *               nice names as values ('group' => ['type1', 'type2', ... ]).
	 */
	public static function get_all_grouped() {
		$grouped = array();
		foreach (self::get_all() as $rt) {
			$key = $rt->get_slug_name();
			$grouped[ $rt->getGroup() ][ $key ] = $rt->get_name();
		}
		return $grouped;
	}

	/**
	 * Finds the EDD_BK_Availability_Range_Type with the given name (case-insensitive).
	 *
	 * @param  string                              $name The nice name of the range type to find.
	 * @return EDD_BK_Availability_Range_Type|NULL       The found EDD_BK_Availability_Range_Type
	 *                                                   instance, or NULL if not found.
	 */
	public static function from_name( $name ) {
		$name = str_replace( ' ', '_', strtoupper( $name ) );
		return isset( self::$$name )? self::$$name : NULL;
	}
}

// Called once (when the file is loaded) to initialize the constants.
EDD_BK_Availability_Range_Type::init_constants();
