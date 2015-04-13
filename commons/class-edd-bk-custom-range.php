<?php

/**
 * Availability range type for custom date ranges.
 */
class EDD_BK_Custom_Range extends EDD_BK_Range_Type {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( 'Custom', EDD_BK_Range_Input_Type::DATES, 'Common' );
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
