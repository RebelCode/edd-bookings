<?php

/**
 * The Bookings Controller class.
 *
 * This class is responsible managing and handling the Bookings and their meta data.
 *
 * @since 1.0.0
 * @version  1.0.0
 * @package EDD_Bookings\Bookings
 */
class EDD_BK_Bookings_Controller implements Aventura_Bookings_Booking_Controller_Interface {

	// The prefix for meta fields for bookings
	const META_PREFIX = 'edd_bk_';

	/**
	 * Constructor.
	 */
	public function __construct() {}

	/**
	 * Gets a single Booking by its ID.
	 * 
	 * @param  string|int          $id The ID of the booking to retrieve.
	 * @return EDD_BK_Booking|null     The booking with the matching ID, or NULL if not found.
	 */
	public function get( $id ) {
		if ( get_post( $id ) === FALSE ) return NULL;
		// Get all custom meta fields for the post
		$all_meta = get_post_custom( $id );
		// Prepare the new array and meta field key prefix
		$meta = array();
		$prefix_length = strlen( self::META_PREFIX );
		// Iterate all fields
		foreach ( $all_meta as $key => $value ) {
			// If the key begins with our prefix
			if ( stripos( $key, self::META_PREFIX ) === 0 ) {
				// Generate a key without the prefix
				$new_key = substr( $key, $prefix_length );
				// Add to new meta array
				$meta[ $new_key ] = $value[0];
			}
		}
		// Return the newly created booking with the meta data
		$booking = new EDD_BK_Booking( $meta );
		$booking->setId( $id );
		return $booking;
	}

	/**
	 * Gets all the bookings for a single Download.
	 * 
	 * @param  string|id $id   The ID of the Download.
	 * @param  string    $date (Optional) If given, the function will return only bookings for the
	 *                         download that are made on this date. Format: 'm/d/Y'
	 * @return array           An array of EDD_BK_Booking instances.
	 */
	public function getBookingsForService( $id, $date = NULL ) {
		if ( get_post( $id ) === FALSE ) return array();
		$args = array(
			'post_type'		=>	EDD_BK_Booking_CPT::SLUG,
			'post_status'	=>	'publish',
			'meta_query'	=>	array(
				array(
					'key'		=>	self::META_PREFIX . 'service_id',
					'value'		=>	strval( $id ),
					'compare'	=>	'='	
				)
			)
		);
		if ( $date !== NULL ) {
			$date_query = array(
				'key'		=>	self::META_PREFIX . 'date',
				'value' 	=>	$date,
				'compare'	=>	'='
			);
			if ( is_array($date) ) {
				$date_query['compare'] = 'BETWEEN';
			}
			$args['meta_query'][] = $date_query;
			$args['meta_query']['relation'] = 'AND';
		}
		$query = new WP_Query( $args );
		$bookings = array();
		while ( $query->have_posts() ) {
			$query->the_post();
			$bookings[] = $this->get( get_the_ID() );
		}
		wp_reset_postdata();
		return $bookings;
	}

	/**
	 * Gets all the bookings for a single Download.
	 * 
	 * @param  string|id $id   The ID of the Download.
	 * @param  string    $date (Optional) If given, the function will return only bookings for the
	 *                         download that are made on this date. Format: 'm/d/Y'
	 * @return array           An array of EDD_BK_Booking instances.
	 */
	public function getBookingsForPayemnt( $id ) {
		if ( get_post( $id ) === FALSE ) return array();
		$args = array(
			'post_type'		=>	EDD_BK_Booking_CPT::SLUG,
			'post_status'	=>	'publish',
			'meta_query'	=>	array(
				array(
					'key'		=>	self::META_PREFIX . 'payment_id',
					'value'		=>	strval( $id ),
					'compare'	=>	'='	
				)
			)
		);
		$query = new WP_Query( $args );
		$bookings = array();
		while ( $query->have_posts() ) {
			$query->the_post();
			$bookings[] = $this->get( get_the_ID() );
		}
		wp_reset_postdata();
		return $bookings;
	}

	/**
	 * Saves the given meta data to a specific Booking.
	 * 
	 * @param  string|int $id   The ID of the Booking.
	 * @param  array      $meta The meta data to save.
	 */
	public function save_meta( $id, $meta ) {
		foreach ($meta as $key => $value) {
			if ( $key === 'id' ) continue;
			update_post_meta( $id, self::META_PREFIX . $key, $value );
		}
	}

	/**
	 * Creates a new booking using the information from an EDD payment.
	 * 
	 * @param  string|int     $payment_id The ID of the payment
	 * @return EDD_BK_Booking             The created EDD_BK_Booking instance.
	 */
	public function create_from_payment( $payment_id ) {
		// Get the payment meta
		$payment_meta = edd_get_payment_meta( $payment_id );
		
		// Get all items that were in the checkout cart for this payment
		$items = $payment_meta['downloads'];
		// Prepare the bookings array to return
		$bookings = array();

		// Iterate items
		foreach ( $items as $item ) {
			// Check if the Donwload ID exists
			if ( ! isset( $item['id'] ) ) continue;
			// check if the item is a service
			$download = edd_bk()->get_downloads_controller()->get( $item['id'] );
			if ( ! $download->isEnabled() ) continue;

			// Get the booking info
			$info = $item['options'];
			// Create the booking
			$booking = new EDD_BK_Booking();
			// Set the foreign ids
			$booking->setDownloadId( $item['id'] );
			$booking->setPaymentId( $payment_id );
			// Set the duration
			$duration = isset( $info['edd_bk_duration'] )? intval( $info['edd_bk_duration'] ) : 1;
			$booking->setDuration( $duration );
			// Set the session unit
			$session_unit = $download->getSessionUnit();
			$booking->setSessionUnit( $session_unit );
			// Set the date selected
			$date = isset( $info['edd_bk_date'] )? $info['edd_bk_date'] : null;
			$booking->setDate( $date );
			// Set the time
			$time = isset( $info['edd_bk_time'] )? $info['edd_bk_time'] : null;
			$booking->setTime( $time );
			// Set the timezone
			$timezone = isset( $info['edd_bk_timezone'] )? $info['edd_bk_timezone'] : 0;
			$booking->setTimezoneOffset( $timezone );
			// Set the customer ID
			$customer_id = edd_get_payment_customer_id( $payment_id );
			$booking->setCustomerId( $customer_id );
			// Add to return array
			$bookings[] = $booking;
		}

		return $bookings;
	}

	/**
	 * Saves a new booking or updates an existing one.
	 * 
	 * @param  EDD_BK_Booking|string|int $booking The EDD_BK_Booking instance, or a Booking ID.
	 * @return int|WP_Error                       The ID of the saved booking on success, or a WP_Error instance on failure.
	 */
	public function save( $booking ) {
		// If the argument is not a bookign instance, treat it as an ID adn get the booking
		if ( ! is_a( $booking, 'EDD_BK_Booking' ) ) {
			if ( ! is_numeric( $booking ) ) {
				return new WP_Error( 'invalid_booking_id', __( 'Invalid Booking ID passed to EDD_BK_Bookings_Controller::save_booking', EDD_Bookings::TEXT_DOMAIN ) );
			}
			$booking = $this->get( $booking );
		}
		$id = $booking->getId();
		// If the ID is null, then the booking does not exist yet and needs to be created.
		if ( $id === NULL ) {
			$inserted_id = wp_insert_post( array(
				'post_content'	=>	'',
				'post_title'	=>	date( 'U' ),
				'post_excerpt'	=>	'N/A',
				'post_status'	=>	'publish',
				'post_type'		=>	EDD_BK_Booking_CPT::SLUG
			), true );
			// check for error
			if ( is_wp_error( $inserted_id ) )
				return $inserted_id;
			else $id = $inserted_id;
		}
		$this->save_meta( $id, $booking->toArray() );
		return intval( $id );
	}

}
