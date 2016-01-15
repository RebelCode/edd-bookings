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
		$loader->add_action( 'edd_checkout_cart_item_title_after', $this, 'cart_item_booking_details' );
		$loader->add_filter( 'edd_cart_item_price', $this, 'cart_item_price', 10, 3 );
		$loader->add_action( 'edd_complete_purchase', $this, 'on_purchase_completed' );
		$loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_styles', 11 );
	}

	/**
	 * Enqueues the cart styles.
	 */
	public function enqueue_styles() {
		if ( edd_is_checkout() ) {
			wp_enqueue_style( 'edd-bk-cart-styles', EDD_BK_CSS_URL . 'edd-bk-public-cart.css' );
		}
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
		// Check if the duration is set
		if ( isset( $post_data['edd_bk_duration'] ) ) {
			// If so, parse to an integer
			$item['options']['edd_bk_duration'] = intval( $post_data['edd_bk_duration'] );
		}
		// Check if the timezone is set
		if ( isset( $post_data['edd_bk_timezone'] ) ) {
			// IF so, parse as integer and add it
			$item['options']['edd_bk_timezone'] = intval( $post_data['edd_bk_timezone'] );
		}
		// Return the item.
		return $item;
	}

	/**
	 * Adds booking details to cart items that have bookings enabled.
	 * 
	 * @param  array $item The EDD cart item.
	 */
	public function cart_item_booking_details( $item ) {
		// Get the item details
		$id = $item['id'];
		$name = edd_get_cart_item_name( $item );
		$download = edd_bk()->get_downloads_controller()->get( $id );
		// Stop if bookings are not available
		if ( ! $download->isEnabled() ) return;
		// Print the booking data
		$options = wp_parse_args( $item['options'], 'edd_bk_time=&edd_bk_duration=1' );
		$date = $options['edd_bk_date'];
		$time = isset( $options['edd_bk_time'] )? $options['edd_bk_time'] : '';
		$duration = $options['edd_bk_duration'];
		$unit = $download->getSessionUnit();
		//if ( strlen( $time ) > 0 ) $time .= ' - ';
		//printf( '<span class="edd-bk-cart-booking-details">(%s %s - %s%s)</span>', $duration, $unit, $time, $date );
		
		// Create dummy booking - date and time setters convert string date/time values into timestamps
		$dummy = new EDD_BK_Booking();
		$dummy->setTime($time);
		$dummy->setDate($date);
		// strototime first argument: addition string
		$strtotimeString  = sprintf( '+%s %s', $duration, $unit );
		// Start and end dates
		$date_format = get_option( 'date_format', 'd/m/y' );
		$start_date = date( $date_format, $dummy->getDate() );
		$end_date = strtotime( $strtotimeString, $dummy->getDate() );
		$end_date = date( $date_format, $end_date );
		// Start and end times
		if ( $time === '' ) {
			$start_time = '';
			$end_time = '';
		} else {
			$time_format = get_option( 'time_format', 'H:i' );
			$start_time = 'at ' . date( $time_format, $dummy->getTime() );
			$end_time = strtotime( $strtotimeString, $dummy->getTime() );
			$end_time = 'at ' . date( $time_format, $end_time );
		}
		// Output
		printf( '<p class="edd-bk-cart-booking-details">Start: <em>%s %s</em><br/>End: <em>%s %s</em></p>', $start_date, $start_time, $end_date, $end_time );
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
			// Get the duration
			$duration = isset( $options['edd_bk_duration'] )? intval( $options['edd_bk_duration'] ) : 1;
			// Get the cost per session
			$download = edd_bk()->get_downloads_controller()->get( $download_id );
			$session_cost = $download->getSessionCost();
			// Calculate the new price
			$price = floatval( $session_cost ) * ( $duration / $download->getSessionLength() );
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
		$controller = edd_bk()->get_bookings_controller();
		// Create the bookings from the payment and save them
		$bookings = $controller->create_from_payment( $payment_id );
		foreach ( $bookings as $booking ) {
			$controller->save( $booking );
		}
	}

}
