<?php

/**
 * Exception thrown when the EDD_Booking singleton instance is already
 * instantiated and the class constructor was called a second time.
 */
class EDD_BK_Singleton_Reinstantiaion_Exception extends EDD_BK_Exception {

	/**
	 * Constructs the exception.
	 */
	public function __construct() {
		parent::__construct( "The EDD_Booking class cannot be re-instansiated!" );
	}
	
}
