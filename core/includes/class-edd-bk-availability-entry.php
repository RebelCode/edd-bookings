<?php

require (EDD_BK_INCLUDES_DIR . 'enum-edd-bk-availability-range-type.php');
require (EDD_BK_INCLUDES_DIR . 'class-edd-bk-availability-entry-days.php');
require (EDD_BK_INCLUDES_DIR . 'class-edd-bk-availability-entry-weeks.php');
require (EDD_BK_INCLUDES_DIR . 'class-edd-bk-availability-entry-months.php');
require (EDD_BK_INCLUDES_DIR . 'class-edd-bk-availability-entry-dotw-time.php');
require (EDD_BK_INCLUDES_DIR . 'class-edd-bk-availability-entry-custom.php');

/**
 * A single entry in the availability table.
 */
abstract class EDD_BK_Availability_Entry {

	/**
	 * The range type for this entry.
	 * 
	 * @var EDD_BK_Availability_Range_Type
	 */
	protected $type;
	protected $from;
	protected $to;
	protected $available;

	/**
	 * Constructor.
	 *
	 * @param  EDD_BK_Availability_Range_Type $type      The range type.
	 * @param  mixed                          $from      The range's start date/day/time
	 * @param  mixed                          $to        The range's end date/day/time
	 * @param  bool                           $available Whether or not this date is available.
	 */
	public function __construct( $type, $from, $to, $available ) {
		$this->type = $type;
		$this->set_from( $from );
		$this->set_to( $to );
		$this->available = $available;
	}

	/**
	 * Returns the range type for this entry.
	 * 
	 * @return EDD_BK_Availability_Range_Type The range type.
	 */
	public function getType() {
		return $this->type;
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
		return $this->from;
	}

	/**
	 * Gets the range's end.
	 * 
	 * @return mixed $to The range's end
	 */
	public function getTo() {
		return $this->to;
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
	 * Gets the range boundaries in an array.
	 * 
	 * @return array The range start as the first element, and the
	 *               range end as the second. Both inclusive.
	 */
	public function getRange() {
		return array($this->get_from(), $this->get_to());
	}

	/**
	 * Processes the entry into a range array, that can be used for checking
	 * dates and times.
	 * 
	 * @return array
	 */
	abstract public function process();

	/**
	 * Returns the textual representation of the this entry.
	 * 
	 * @return string
	 */
	public function getTextualHelp() {

	}

	/**
	 * Returns the textual representation of this entry.
	 *
	 * Calls `get_textual_help()` internally to generate the string.
	 *
	 * @uses EDD_BK_Availability_Entry::get_textual_help()
	 * @see EDD_BK_Availability_Entry::get_textual_help()
	 * @return string
	 */
	public function __toString() {
		return $this->getTextualHelp();
	}

	/*
	 * @param  [type] $meta [description]
	 * @return [type]       [description]
	 */
	public static function fromMeta( $meta ) {
		// Get the variables from the meta array given
		list($type, $from, $to, $available) = array_values( $meta );
		// Get the range type
		$rangetype = EDD_BK_Availability_Range_Type::from_name( $type );
		// Return null if the range type is invalid
		if ( $rangetype === NULL ) return NULL;
		// Get the class name of the range type
		$class = $rangetype->get_handler_class_name();
		// Change the 'available' field into a boolean
		$available = strtolower( $available ) === 'true';
		// Return a new instance of the entry
		return new $class( $rangetype, $from, $to, $available );
	}

}
