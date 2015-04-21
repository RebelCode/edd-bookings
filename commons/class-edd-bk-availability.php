<?php

require( EDD_BK_COMMONS_DIR . 'class-edd-bk-availability-entry.php' );

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

	public function test( $timestamp, $session_length, $session_unit ) {
		$daystamp = EDD_BK_Date_Utils::daystamp_from_timestamp( $timestamp );
		$dotw_entries = array();
		foreach ($this->entries as $entry) {
			if ( is_a( $entry, 'EDD_BK_Availability_Entry_Dotw_Time' ) ) {
				array_push( $dotw_entries, $entry );
			} else {
				if ( ! $entry->matches( $timestamp ) ) {
					return NULL;
				}
			}
		}
		return TRUE;
	}

	/**
	 * Returns all entries in the table.
	 *
	 * @return array And array of EDD_BK_Availability_Entry objects.
	 */
	public function getEntries( $entry ) {
		return $this->entries;
	}

	public static function from_meta( $meta ) {
		$availability = new static();
		foreach ($meta as $i => $entry) {
			$availability->addEntry( EDD_BK_Availability_Entry::from_meta( $entry ) );
		}
		return $availability;
	}

}
