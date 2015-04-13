<?php

/**
 * Availability range type for time ranges in a day.
 */
class EDD_BK_Time_Range extends EDD_BK_Range_Type {

	/**
	 * The day of the week number.
	 * From 0 for Sunday, till 6 for Saturday.
	 * @var int
	 */
	private $dotw;

	/**
	 * Constructor.
	 */
	public function __construct( $dotw, $nice_name ) {
		parent::__construct( $nice_name, EDD_BK_Range_Input_Type::TIME, 'Days' );
		$this->dotw = intval( $dotw );
	}

	/**
	 * @todo
	 * @param  [type] $timestamp [description]
	 * @param  [type] $from      [description]
	 * @param  [type] $to        [description]
	 * @return [type]            [description]
	 */
	public function matches( $timestamp, $from, $to ) {
		return false;
	}

}
