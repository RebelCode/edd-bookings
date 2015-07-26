<?php

/**
 * Interface for the Aventura_Bookings_Customer class' controller.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Aventura\Bookings\Customer\Controller
 */
interface Aventura_Bookings_Customer_Controller_Interface {

	public function getCustomerById( $id );
	public function getAllCustomers();
	public function insertCustomer( Aventura_Bookings_Customer $customer );
	public function deleteCustomer( $id );

}
