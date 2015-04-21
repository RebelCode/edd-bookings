<?php

require (EDD_BK_COMMONS_DIR . 'enum-edd-bk-availability-range-type.php');
require (EDD_BK_COMMONS_DIR . 'class-edd-bk-availability-entry-days.php');
require (EDD_BK_COMMONS_DIR . 'class-edd-bk-availability-entry-weeks.php');
require (EDD_BK_COMMONS_DIR . 'class-edd-bk-availability-entry-months.php');
require (EDD_BK_COMMONS_DIR . 'class-edd-bk-availability-entry-dotw-time.php');
require (EDD_BK_COMMONS_DIR . 'class-edd-bk-availability-entry-custom.php');

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
	 * Sets the range's start.
	 * 
	 * @param mixed $from The range's start
	 */
	public function set_from( $from ) {
		$this->from = $from;
	}

	/**
	 * Sets the range's end.
	 * 
	 * @param mixed $to The range's end
	 */
	public function set_to( $to ) {
		$this->to = $to;
	}

	/**
	 * Gets the range's start.
	 * 
	 * @return mixed $from The range's start
	 */
	public function get_from() {
		return $this->from;
	}

	/**
	 * Gets the range's end.
	 * 
	 * @return mixed $to The range's end
	 */
	public function get_to() {
		return $this->to;
	}

	/**
	 * Gets the range boundaries in an array.
	 * 
	 * @return array The range start as the first element, and the
	 *               range end as the second. Both inclusive.
	 */
	public function get_range() {
		return array($this->get_from(), $this->get_to());
	}

	/**
	 * Checks if the given timestamp matches this availability range.
	 * 
	 * @param  int  $timestamp The timestamp to check.
	 * @return bool            True if it matches, false otherwise.
	 */
	abstract public function matches( $timestamp );

	/**
	 * Returns the textual representation of the this entry.
	 * 
	 * @return string
	 */
	public function get_textual_help() {

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
		return $this->get_textual_help();
	}

	/*
	 * @param  [type] $meta [description]
	 * @return [type]       [description]
	 */
	public static function from_meta( $meta ) {
		list($type, $from, $to, $available) = array_values( $meta );
		$rangetype = EDD_BK_Availability_Range_Type::from_name( $type );
		if ( $rangetype === NULL ) {
			return NULL;
		}
		$class = $rangetype->get_handler_class_name();
		$available = strtolower( $available ) === 'true';
		return new $class( $rangetype, $from, $to, $available );
	}

}
