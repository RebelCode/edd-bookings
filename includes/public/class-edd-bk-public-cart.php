<?php

/**
 * Shopping cart handler for the public module of the plugin.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Booking\Public
 */
class EDD_BK_Public_Cart {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->define_hooks();
	}

	/**
	 * Registers the WordPress hooks into the loader.
	 */
	public function define_hooks() {
		$loader = EDD_Bookings::get_instance()->get_loader();
		// Cart item hooks
		$loader->add_filter( 'edd_add_to_cart_item', $this, 'cart_item_data' );
		$loader->add_filter( 'edd_cart_item_price', $this, 'cart_item_price', 10, 3 );
		$loader->add_action( 'edd_complete_purchase', $this, 'on_purchase_completed' );
	}

	/**
	 * Adds data to the cart items
	 * 
	 * @param  array $item The original cart item.
	 * @return array       The filtered item, with added EDD Booking data.
	 */
	public function cart_item_data( $item ) {
		// Stop if no data
		if ( empty( $_POST['post_data'] ) ) return $item;
		// Parse the post data
		parse_str( $_POST['post_data'], $post_data );
		// Check if the number of sessions is set
		if ( isset( $post_data['edd_bk_num_sessions'] ) ) {
			// If so, parse to an integer
			$item['options']['edd_bk_num_sessions'] = intval( $post_data['edd_bk_num_sessions'] );
		}
		// Check if the date is set
		if ( isset( $post_data['edd_bk_date'] ) ) {
			// If so, get it
			$date = $post_data['edd_bk_date'];
			// If there are multiple dates, only use the first
			$all_dates = explode( ',', $date );
			$date = $all_dates[0];
			// Add the date to the data
			$item['options']['edd_bk_date'] = $date;
		}
		// Check if the date is set
		if ( isset( $post_data['edd_bk_time'] ) ) {
			// If so, add it
			$item['options']['edd_bk_time'] = $post_data['edd_bk_time'];
		}
		// Return the item.
		return $item;
	}

	/**
	 * Modifies the cart item price.
	 * 
	 * @param  float $price       The item price.
	 * @param  int   $download_id The ID of the download.
	 * @param  array $options     The cart item options.
	 * @return float              The new filtered price.
	 */
	public function cart_item_price( $price, $download_id, $options ) {
		// Check if the date is set
		if ( isset( $options['edd_bk_date'] ) ) {
			// Get the number of sessions
			$num_sessions = isset( $options['edd_bk_num_sessions'] )? intval( $options['edd_bk_num_sessions'] ) : 1;
			// Get the cost per session
			$download = EDD_BK_Downloads_Controller::get( $download_id );
			$session_cost = $download->getSessionCost();
			// Calculate the new price
			$price = floatval( $session_cost ) * $num_sessions;
		}
		return $price;
	}

	/**
	 * Callback function for completed purchases. Creates the booking form the purchase
	 * and saves it in the DB.
	 *
	 * @uses hook::action::edd_complete_purchase
	 * @param string|int $payment_id The ID of the payment.
	 */
	public function on_purchase_completed( $payment_id ) {
		// Create the booking from the payment
		$booking = EDD_BK_Bookings_Controller::create_from_payment( $payment_id );
		// Save it
		EDD_BK_Bookings_Controller::save( $booking );
	}

}
