<?php

require( EDD_BK_COMMONS_DIR . 'class-edd-bk-range-input-type.php' );

require( EDD_BK_COMMONS_DIR . 'class-edd-bk-dotw-range.php' );
require( EDD_BK_COMMONS_DIR . 'class-edd-bk-weeks-range.php' );
require( EDD_BK_COMMONS_DIR . 'class-edd-bk-months-range.php' );
require( EDD_BK_COMMONS_DIR . 'class-edd-bk-custom-range.php' );
require( EDD_BK_COMMONS_DIR . 'class-edd-bk-time-range.php' );
require( EDD_BK_COMMONS_DIR . 'class-edd-bk-group-time-range.php' );

/**
 * "Enum" class for range types used in the Availability builder.
 */
abstract class EDD_BK_Range_Type {

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
	 * 
	 * @var string
	 */
	private $group;

	/**
	 * Constructor.
	 * 
	 * @param string $nice_name  The string used for outputting this range type.
	 * @param string $group      This range type's group
	 */
	public function __construct( $nice_name, $group = '' ) {
		$this->nice_name = $nice_name;
		$this->group = $group;
	}

	/**
	 * Checks if a given timestamp matches the given time/date/day range.
	 *
	 * @param  int    $timestamp The timestamp to check.
	 * @param  mixed  $from      The start of the range.
	 * @param  mixed  $to        The end of the range.
	 * @return bool              True if the timestamp matches, false otherwise.
	 */
	abstract public function matches( $timestamp, $from, $to );

	/**
	 * Initializes the constants.
	 */
	public static function init_constants() {
		// Common
		self::$DAYS      = new EDD_BK_Dotw_Range();
		self::$WEEKS     = new EDD_BK_Weeks_Range();
		self::$MONTHS    = new EDD_BK_Months_Range();
		// Custom
		self::$CUSTOM    = new EDD_BK_Custom_Range();
		// Days
		self::$MONDAY    = new EDD_BK_Time_Range( 1, 'Monday' );
		self::$TUESDAY   = new EDD_BK_Time_Range( 2, 'Tuesday' );
		self::$WEDNESDAY = new EDD_BK_Time_Range( 3, 'Wednesday' );
		self::$THURSDAY  = new EDD_BK_Time_Range( 4, 'Thursday' );
		self::$FRIDAY    = new EDD_BK_Time_Range( 5, 'Friday' );
		self::$SATURDAY  = new EDD_BK_Time_Range( 6, 'Saturday' );
		self::$SUNDAY    = new EDD_BK_Time_Range( 0, 'Sunday' );
		// Day Groups
		self::$ALL_WEEK  = new EDD_BK_Group_Time_Range( '0123456',	'All Week' );
		self::$WEEKDAYS  = new EDD_BK_Group_Time_Range( '12345',	'Weekdays' );
		self::$WEEKENDS  = new EDD_BK_Group_Time_Range( '06',		'Weekends' );
	}

	/**
	 * @return array Returns all the range type constants.
	 */
	public static function get_all() {
		$class = new ReflectionClass( __CLASS__ );
		return $class->getStaticProperties();
	}
}

// Called once (when the file is loaded) to initialize the constants.
EDD_BK_Range_Type::init_constants();
