<?php

require( EDD_BK_INCLUDES_DIR . 'class-edd-bk-availability-entry.php' );

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
	private $fill;

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
		$this->fill = false;
		$this->entries = array();
	}

	/**
	 * Sets the availability fill flag.
	 * 
	 * @param bool $da The new availability fill flag.
	 */
	public function setAvailabilityFill( $da ) {
		$this->fill = ($da == true);
	}

	/**
	 * Returns the value of the availability fill flag.
	 * 
	 * @return bool The value of the availability fill flag (true|false).
	 */
	public function getAvailabilityFill() {
		return $this->fill;
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

	/**
	 * Removes an entry from the availability table.
	 * 
	 * @param  int  $index The index of the element to remove.
	 * @return bool        True on success, False on failure.
	 */
	public function removeEntry( $index ) {
		if ( $index >= count( $this->entries ) ) return false;
		unset( $this->entries[ $index ] );
		return true;
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
	public function getEntries() {
		return $this->entries;
	}

	public static function fromMeta( $meta ) {
		if ( !is_array( $meta ) ) $meta = array();
		$availability = new static();
		foreach ($meta as $i => $entry) {
			$availability->addEntry( EDD_BK_Availability_Entry::fromMeta( $entry ) );
		}
		return $availability;
	}

}
