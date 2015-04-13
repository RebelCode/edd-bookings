<?php

/**
 * Availability range type for time ranges for grouped days.
 */
class EDD_BK_Group_Time_Range extends EDD_BK_Range_Type {

	/**
	 * The days of the week for this range.
	 * 
	 * @var array
	 */
	private $dotw;

	/**
	 * Constructor.
	 */
	public function __construct( $dotw = '', $nice_name ) {
		parent::__construct( $nice_name, EDD_BK_Range_Input_Type::TIME, 'Day Groups' );
		$this->dotw = $dotw;
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
