<?php

/**
 * The Customers Controller class.
 *
 * This class is responsible managing and handling the Customers and their meta data.
 *
 * @since 1.0.0
 * @version  1.0.0
 */
class EDD_BK_Customers_Controller {

	/**
	 * Gets a single customer by ID.
	 * 
	 * @param  string|int           $id The ID of the customer to retrieve.
	 * @return EDD_BK_Customer|null     The customer with the matching ID, or NULL if not found.
	 */
	public static function get( $id ) {
		$edd_customer = new EDD_Customer( absint( $id ) );
		$data = array(
			'id'	=>	$id,
			'name'	=>	$edd_customer->name,
			'email'	=>	$edd_customer->email
		);
		return new EDD_BK_Customer( $data );
	}
	
}
