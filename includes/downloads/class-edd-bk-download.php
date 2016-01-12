<?php

/**
 * Represents a Download post with booking data.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Bookings\Downloads
 */
class EDD_BK_Download extends Aventura_Bookings_Service {

	/**
	 * Checks if the Download bookings are enabled.
	 * 
	 * @return boolean True if bookings are enabled, False if not.
	 */
	public function isEnabled() {
		return (bool) $this->getData('enabled') === TRUE;
	}

	/**
	 * Checks if the Download has multi view output enabled.
	 * 
	 * @return boolean True if multi view output is enabled, false if not.
	 */
	public function isEnabledMultiViewOutput() {
		return (bool) $this->getData('multi_view_output') === TRUE;
	}
	
}
