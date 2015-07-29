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
class EDD_BK_Bookings_Controller {

	// The prefix for meta fields for bookings
	const META_PREFIX = 'edd_bk_';

	/**
	 * Gets a single Booking by its ID.
	 * 
	 * @param  string|int          $id The ID of the booking to retrieve.
	 * @return EDD_BK_Booking|null     The booking with the matching ID, or NULL if not found.
	 */
	public static function get( $id ) {
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
	public static function get_for_download( $id, $date = NULL ) {
		if ( get_post( $id ) === FALSE ) return array();
		$args = array(
			'post_type'		=>	EDD_BK_Booking_CPT::SLUG,
			'meta_query'	=>	array(
				array(
					'key'		=>	self::META_PREFIX . 'service_id',
					'value'		=>	strval( $id ),
					'compare'	=>	'='	
				)
			)
		);
		if ( $date !== NULL ) {
			$args['meta_query'][] = array(
				'key'		=>	self::META_PREFIX . 'date',
				'value'		=>	$date,
				'compare'	=>	'='
			);
			$args['meta_query']['relation'] = 'AND';
		}
		$query = new WP_Query( $args );
		$bookings = array();
		while ( $query->have_posts() ) {
			$query->the_post();
			$bookings[] = self::get( $query->post->ID );
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
	public static function save_meta( $id, $meta ) {
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
	public static function create_from_payment( $payment_id ) {
		// Get the payment meta
		$payment_meta = edd_get_payment_meta( $payment_id );
		
		// Check if the Donwload ID exists
		if ( ! isset( $payment_meta['downloads'][0]['id'] ) ) return NULL;

		// Create the booking
		$booking = new EDD_BK_Booking();
		// Set the download id
		$booking->setDownloadId( $payment_meta['downloads'][0]['id']);
		// Set the payment id
		$booking->setPaymentId( $payment_id );
		// Get the booking info
		$info = $payment_meta['downloads'][0]['options'];
		// Set the number of sessions
		$num_sessions = isset( $info['edd_bk_num_sessions'] )? intval( $info['edd_bk_num_sessions'] ) : 1;
		$booking->setNumSessions( $num_sessions );
		// Set the date selected
		$date = isset( $info['edd_bk_date'] )? $info['edd_bk_date'] : null;
		$booking->setDate( $date );
		// Set the time
		$time = isset( $info['edd_bk_time'] )? $info['edd_bk_time'] : null;
		$booking->setTime( $time );
		// Set the customer ID
		$customer_id = edd_get_payment_customer_id( $payment_id );
		$booking->setCustomerId( $customer_id );

		return $booking;
	}

	/**
	 * Saves a new booking or updates an existing one.
	 * 
	 * @param  EDD_BK_Booking|string|int $booking The EDD_BK_Booking instance, or a Booking ID.
	 * @return int|WP_Error                       The ID of the saved booking on success, or a WP_Error instance on failure.
	 */
	public static function save( $booking ) {
		// If the argument is not a bookign instance, treat it as an ID adn get the booking
		if ( ! is_a( $booking, 'EDD_BK_Booking' ) ) {
			if ( ! is_numeric( $booking ) ) {
				return new WP_Error( 'invalid_booking_id', __('Invalid Booking ID passed to EDD_BK_Bookings_Controller::save_booking') );
			}
			$booking = self::get( $booking );
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
		self::save_meta( $id, $booking->toArray() );
		return intval( $id );
	}

}
