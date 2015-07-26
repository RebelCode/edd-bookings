<?php

class EDD_BK_Download extends Aventura_Bookings_Service {

	public function isEnabled() {
		return $this->getData('enabled') === TRUE;
	}
	
}
