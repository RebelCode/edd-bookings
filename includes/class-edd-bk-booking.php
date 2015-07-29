<?php

/**
 * This class represents a single booking, booked by a customer.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
class EDD_BK_Booking extends Aventura_Bookings_Booking {
	
	/**
	 * Alias method for Aventura_Bookings_Booking::setDownloadId
	 *
	 * @see Aventura_Bookings_Booking::setDownloadId
	 * @uses Aventura_Bookings_Booking::setDownloadId
	 * @param string|int $id The ID of the download.
	 */
	public function setDownloadId( $id ) {
		$this->setServiceId( $id );
	}

	/**
	 * Alias method for Aventura_Bookings_Booking::getDownloadId
	 *
	 * @see Aventura_Bookings_Booking::getDownloadId
	 * @uses Aventura_Bookings_Booking::getDownloadId
	 * @return string|int The ID of the Download.
	 */
	public function getDownloadId() {
		return $this->getServiceId();
	}

}
