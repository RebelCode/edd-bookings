<?php

/**
 * Represents the availability.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Aventura\Bookings\Service
 */
class Aventura_Bookings_Service_Availability {

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
	 * @var array
	 */
	private $entries;

	/**
	 * Cached processed availability
	 * 
	 * @var array
	 */
	private $processedCache;

	/**
	 * Constructor.
	 */
	public function __construct( $arg = NULL ) {
		$this->fill = false;
		$this->entries = array();
		if ( is_array( $arg ) ) {
			if ( isset($arg['fill'] ) ) {
				$this->fill = $arg['fill'];
			}
			if ( isset($arg['entries']) ) {
				foreach ($arg['entries'] as $entry) {
					$this->addEntry($entry);
				}
			}
		}
		$this->processedCache = NULL;
	}

	/**
	 * Sets the availability fill flag.
	 * 
	 * @param bool $fill The new availability fill flag.
	 */
	public function setFill( $fill ) {
		$this->fill = ($fill == true);
	}

	/**
	 * Returns the value of the availability fill flag.
	 * 
	 * @return bool The value of the availability fill flag (true|false).
	 */
	public function getFill() {
		return (bool) $this->fill;
	}

	/**
	 * Adds an entry to the availabilty.
	 *
	 * @param Aventura_Bookings_Service_Availability_Entry $entry The entry to add.
	 */
	public function addEntry( $entry ) {
		if ( is_array($entry) ) {
			$this->entries[] = new Aventura_Bookings_Service_Availability_Entry($entry);
			// Invalidate processed availability cache
			$this->processedCache = NULL;
		} else if ( is_a($entry, 'Aventura_Bookings_Service_Availability_Entry') ) {
			$this->entries[] = $entry;
			// Invalidate processed availability cache
			$this->processedCache = NULL;
		} else {
			throw new InvalidArgumentException('Method addEntry expects argument to be array or of type '.
				'Aventura_Bookings_Service_Availability_Entry. Received: ' . $entry);
		}
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
		// Invalidate processed availability cache
		$this->processedCache = NULL;
		return true;
	}

	/**
	 * Goes through the user entries and processes them into a simple
	 * and expanded structure, to be used for both server-side PHP and
	 * client-side JS date checking.
	 */
	public function process() {
		// Stop if no entries
		if ( empty( $this->entries ) ) return array();

		// If the cache is invalid, process the availability
		if ( $this->processedCache === NULL ) {
			// Check which range types are included in the entries
			$n = count( $this->entries );
			$range_types = array();
			while ( $n-- ) {
				$type_unit = $this->entries[ $n ]->getType()->getUnit();
				$type_unit && ( $range_types[ $type_unit ] = true );
			}
			// Filter out empty values and remove duplicates
			$range_types = array_keys( $range_types );
			// Check if there are non-time entry types present
			$non_time_ranges = array(
				Aventura_Bookings_Service_Availability_Entry_Range_Type::UNIT_MONTH,
				Aventura_Bookings_Service_Availability_Entry_Range_Type::UNIT_WEEK,
				Aventura_Bookings_Service_Availability_Entry_Range_Type::UNIT_DAY,
				Aventura_Bookings_Service_Availability_Entry_Range_Type::UNIT_CUSTOM
			);
			$non_time_ranges = array_intersect( $range_types, $non_time_ranges );
			$has_only_time_ranges = count( $non_time_ranges ) === 0;

			// Iterate each entry
			$processed = array();
			foreach ( $this->entries as $entry ) {
				// Process the entry
				$processed_entry = $entry->process();

				// Ensure that the unit index exists
				$unit = $entry->getType()->getUnit();
				if ( ! isset( $processed[ $unit ] ) ) $processed[ $unit ] = array();

				if ( $unit === Aventura_Bookings_Service_Availability_Entry_Range_Type::UNIT_TIME ) {
					// If it's a time unit, add each entry in the processed array
					// to the 'time' index, while also merging it with existing
					// entries for that dotw
					foreach ( $processed_entry as $dotw => $info ) {
						$processed[ 'time' ][ $dotw ][] = $info;
					}
				} else {
					// Otherwise, simply merge-add
					$processed[ $unit ] = $processed[ $unit ] + $processed_entry;
				}

				// If it's a time entry, it's available and no other non-time entry is present, add the time entry's dotw as a day range
				if ( $unit === Aventura_Bookings_Service_Availability_Entry_Range_Type::UNIT_TIME && $entry->isAvailable() && $has_only_time_ranges ) {
					// Get the days of the week (array keys)
					$dotw = array_keys( $processed_entry );
					// Get the range limits and available flag
					$to = end( $dotw );
					$from = reset( $dotw );
					$available = $entry->isAvailable();
					// Produce a day range
					$days = Aventura_Bookings_Service_Availability_Entry_Days::getDayRange( $from, $to, $available );
					// If no day entry is set yet, create it
					if ( ! isset( $processed['day'] ) ) $processed['day'] = array();
					// Add the days
					$processed['day'] = $processed['day'] + $days;
				}
			}

			// Update the processed availability cache
			$this->processedCache = $processed;
		}

		return $this->processedCache;
	}

	/**
	 * Returns all entries in the table.
	 * 
	 * @return array And array of Aventura_Bookings_Service_Availability_Entry_Abstract objects.
	 */
	public function getEntries() {
		return $this->entries;
	}

	/**
	 * Returns the availability as an array.
	 * 
	 * @return array
	 */
	public function toArray() {
		$entries = array();
		foreach ($this->entries as $entry) $entries[] = $entry->toArray();
		return array(
			'fill'		=>	$this->fill,
			'entries'	=>	$entries
		);
	}

	/**
	 * Creates an Aventura_Bookings_Service_Availability instance form the given meta data.
	 * 
	 * @param  array               $meta The meta data array containing the availability data.
	 * @return Aventura_Bookings_Service_Availability
	 */
	public static function fromMeta( $meta ) {
		// If the meta is not an array, normalize it into an empty array
		if ( ! is_array( $meta ) ) $meta = array();
		// Create a new availability
		$availability = new self();
		// Add all entries from the meta
		foreach ($meta as $i => $entry) {
			$availability->addEntry( Aventura_Bookings_Service_Availability_Entry_Abstract::fromMeta( $entry ) );
		}
		return $availability;
	}

}
