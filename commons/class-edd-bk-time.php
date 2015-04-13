<?php

/**
 * Time class, for containing hours and minutes, used by the plugin
 * for availability time handling .
 */
class EDD_BK_Time {
	
	/**
	 * The hours.
	 * @var int
	 */
	private $hours;
	/**
	 * The minutes.
	 * @var int
	 */
	private $minutes;

	/**
	 * Constructor.
	 * 
	 * @param  int $hour The hours
	 * @param  int $min  The minutes
	 */
	public function __construct( $hours = 0, $mins = 0 ) {
		$excess_hours = intval( $minutes / 60 );
		$excess_minutes = intval( $minutes % 60 );
		$this->hours = $hours + $excess_hours;
		$this->minutes = $minutes + $excess_minutes;
	}

	/**
	 * Sets the hours.
	 * 
	 * If the given param is greater than 23, the hours will be looped.
	 * 
	 * Example: 28 hrs => 4 hrs
	 * 
	 * @param int $hours The hours.
	 */
	public function setHours( $hours ) {
		$this->hours = $hours % 24;
	}

	/**
	 * Sets the minutes.
	 *
	 * If the given param is greater than 59, the minutes will be looped, and
	 * any needed hours are added.
	 *
	 * Example: 85 minutes => 1 hr and 15 minutes
	 * 
	 * @param int $minutes The minutes.
	 */
	public function setMinutes( $minutes ) {
		// Calculate any excess hours and minutes
		$excess_hours = intval( $minutes / 60 );
		$excess_minutes = intval( $minutes % 60 );
		// Update the hours and minutes
		$this->hours = $hours + $excess_hours;
		$this->minutes = $minutes + $excess_minutes;
	}

	/**
	 * Gets the hours.
	 * 
	 * @return int The hours.
	 */
	public function getHours() {
		return $this->hours;
	}

	/**
	 * Gets the minutes.
	 * 
	 * @return int The minutes.
	 */
	public function getMinutes() {
		return $this->minutes;
	}

	/**
	 * Increases the hours by the given amount.
	 * 
	 * @param int $hours The amount of hours to increase.
	 */
	public function addHours( $hours ) {
		$this->setHours( $this->hours + $hours );
	}

	/**
	 * Increases the minutes by the given amount.
	 * 
	 * @param [type] $minutes [description]
	 */
	public function addMinutes( $minutes ) {
		$this->setMinutes( $this->minutes + $minutes );
	}

	public function add($time) {
		$this->addHours( $time->hours );
		$this->addMinutes( $time->minutes );
	}

}
