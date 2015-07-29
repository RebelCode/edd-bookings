<?php

class EDD_BK_Bookings_Page {

	public function __construct() {

	}

	public function render() {
		echo EDD_BK_Utils::ob_include( EDD_BK_ADMIN_VIEWS_DIR . 'view-bookings-page.php' );
	}

}
