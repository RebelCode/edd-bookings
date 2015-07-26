<?php

/**
 * Interface for the Aventura_Bookings_Service class' controller.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Aventura\Bookings\Service\Controller
 */
interface Aventura_Bookings_Service_Controller_Interface {

	public function serviceExists( $id );
	public function getServiceById( $id );
	public function getAllServices();
	public function insertService( Aventura_Bookings_Service $service );
	public function deleteService( $id );

}
