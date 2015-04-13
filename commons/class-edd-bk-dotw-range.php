<?php

/**
 * Availability range type for days of the week.
 */
class EDD_BK_Dotw_Range extends EDD_BK_Range_Type {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( 'Days', EDD_BK_Range_Input_Type::DAYS, 'Common' );
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
