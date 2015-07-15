<?php

/**
 * Bookings handler class .
 *
 * @since 1.0.0
 * @version  1.0.0
 * @package EDD_Booking
 * @subpackage Admin
 */
class EDD_BK_Bookings_Handler {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->define_hooks();
	}

	/**
	 * Registers the WordPress hooks to the loader.
	 */
	public function define_hooks() {
		$loader = EDD_Booking::instance()->get_loader();
		// Hook for adding bookings after they have been purchased
		$loader->add_action( 'edd_complete_purchase', $this, 'add_booking' );
	}

	/**
	 * Adds a booking after it has been purchased.
	 *
	 * @uses hook:action:edd_complete_purchase
	 * @param int $payment_id ID of the EDD payment.
	 */
	public function add_booking( $payment_id ) {
		$payment_meta = edd_get_payment_meta( $payment_id );
		file_put_contents( EDD_BK_DIR . 'log.txt', print_r( $payment_meta, true ) );
	}
	
}
