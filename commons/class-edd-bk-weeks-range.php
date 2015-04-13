<?php

/**
 * Availability range type for week ranges.
 */
class EDD_BK_Weeks_Range extends EDD_BK_Range_Type {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( 'Weeks', EDD_BK_Range_Input_Type::WEEKS, 'Common' );
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
