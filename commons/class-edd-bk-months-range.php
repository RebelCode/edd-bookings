<?php

/**
 * Availability range type for month ranges.
 */
class EDD_BK_Months_Range extends EDD_BK_Range_Type {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( 'Months', EDD_BK_Range_Input_Type::MONTHS, 'Common' );
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
