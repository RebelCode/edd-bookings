<?php

/**
 * Represents the availability table.
 */
class EDD_BK_Availability {

	/**
	 * Flag that controls whether the dates not specified are
	 * available or not.
	 * 
	 * @var bool
	 */
	private $default_available;

	/**
	 * The availability row entries.
	 * 
	 * @var EDD_BK_Availability_Entry
	 */
	private $entries;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->default_available = false;
		$this->entries = array();
	}

	/**
	 * Adds an entry to the availabilty.
	 *
	 * @param EDD_BK_Availability_Entry $entry The entry to add.
	 */
	public function addEntry( $entry ) {
		if ( ! is_a( $entry, 'EDD_BK_Availability_Entry' ) ) {
			return;
		}
		array_push( $this->entries, $entry );
	}

}
