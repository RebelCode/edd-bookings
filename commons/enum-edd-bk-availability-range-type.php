<?php

/**
 * "Enum" class for range types used in the Availability builder.
 */
class EDD_BK_Availability_Range_Type {

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
	public function __construct( $nice_name, $group, $classname ) {
		$this->nice_name = $nice_name;
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
	 * Returns the group for this range type.
	 * 
	 * @return string A string containing the name of the group for this range type.
	 */
	public function get_group() {
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
		// Common
		self::$DAYS      = new self( 'Days',		'Common', 'EDD_BK_Availability_Entry_Days' );
		self::$WEEKS     = new self( 'Weeks',		'Common', 'EDD_BK_Availability_Entry_Weeks' );
		self::$MONTHS    = new self( 'Months',		'Common', 'EDD_BK_Availability_Entry_Months' );
		// Custom
		self::$CUSTOM    = new self( 'Custom',		'Common', 'EDD_BK_Availability_Entry_Custom' );
		// Days
		self::$MONDAY    = new self( 'Monday',		'Days', 'EDD_BK_Availability_Entry_Dotw_Time' );
		self::$TUESDAY   = new self( 'Tuesday',		'Days', 'EDD_BK_Availability_Entry_Dotw_Time' );
		self::$WEDNESDAY = new self( 'Wednesday',	'Days', 'EDD_BK_Availability_Entry_Dotw_Time' );
		self::$THURSDAY  = new self( 'Thursday',	'Days', 'EDD_BK_Availability_Entry_Dotw_Time' );
		self::$FRIDAY    = new self( 'Friday',		'Days', 'EDD_BK_Availability_Entry_Dotw_Time' );
		self::$SATURDAY  = new self( 'Saturday',	'Days', 'EDD_BK_Availability_Entry_Dotw_Time' );
		self::$SUNDAY    = new self( 'Sunday',		'Days', 'EDD_BK_Availability_Entry_Dotw_Time' );
		// Day Groups
		self::$ALL_WEEK  = new self( 'All Week',	'Day Groups', 'EDD_BK_Availability_Entry_Dotw_Time' );
		self::$WEEKDAYS  = new self( 'Weekdays',	'Day Groups', 'EDD_BK_Availability_Entry_Dotw_Time' );
		self::$WEEKENDS  = new self( 'Weekends',	'Day Groups', 'EDD_BK_Availability_Entry_Dotw_Time' );
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
			$key = str_replace( ' ', '_', strtolower( $rt->get_name() ) );
			$grouped[ $rt->get_group() ][ $key ] = $rt->get_name();
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
		$name = strtoupper( $name );
		return isset( self::$$name )? self::$$name : NULL;
	}
}

// Called once (when the file is loaded) to initialize the constants.
EDD_BK_Availability_Range_Type::init_constants();
