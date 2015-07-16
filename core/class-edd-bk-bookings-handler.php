<?php

/**
 * Bookings handler class - handles bookings and the WordPress CPT.
 *
 * This class is loaded and instantiated by the EDD_BK_Commons class.
 *
 * @since 1.0.0
 * @version  1.0.0
 * @package EDD_Booking
 * @subpackage Admin
 */
class EDD_BK_Bookings_Handler {

	/**
	 * The formal slug name of the CPT.
	 */
	const CPT_SLUG = 'edd_booking';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->load_dependancies();
		$this->define_hooks();
	}

	/**
	 * Loads required files.
	 */
	public function load_dependancies() {
		require EDD_BK_INCLUDES_DIR . 'class-edd-bk-booking.php';
	}

	/**
	 * Registers the WordPress hooks to the loader.
	 */
	public function define_hooks() {
		$loader = EDD_Booking::instance()->get_loader();
		// Hook for registering the custom post type
		$loader->add_action( 'init', $this, 'register_cpt' );
		// Hook for adding bookings after they have been purchased
		$loader->add_action( 'edd_complete_purchase', $this, 'add_booking' );
		// Hooks for custom booking columns
		$loader->add_action( 'manage_edd_booking_posts_columns', $this, 'register_custom_columns' );
		$loader->add_action( 'manage_posts_custom_column', $this, 'fill_table_data' );
	}

	/**
	 * Returns the CPT options.
	 * 
	 * @return array
	 */
	public static function get_cpt_options() {
		return array(
			'labels'		=>	array(
				'name'			=>	__( 'Bookings', 'edd-bk' ),
				'singular_name'	=>	__( 'Booking', 'edd-bk' )
			),
			'public'		=>	false,
			'show_ui'		=>	true,
			'has_archive'	=>	false,
			'show_in_menu'	=>	'edit.php?post_type=download'
		);
	}

	/**
	 * Registers the CPT.
	 */
	public static function register_cpt() {
		register_post_type( self::CPT_SLUG, self::get_cpt_options() );
	}

	/**
	 * Registers the custom columns for the CPT.
	 * 
	 * @param  array $columns An array of columns
	 * @return array          An array of columns
	 */
	public function register_custom_columns( $columns ) {
		return array(
			'details'	=>	__( 'Details', 'edd-bk' )
		);
	}

	/**
	 * Given a column and a post ID, the function will echo the contents of the
	 * respective table cell, for the CPT table.
	 * 
	 * @param string     $column  The column slug name.
	 * @param string|int $post_id The ID of the post
	 */
	public function fill_table_data( $column, $post_id ) {
		// Stop if post is not a booking post type
		if ( get_post_type( $post_id ) !== self::CPT_SLUG ) return;
		// Create the booking object
		$booking = EDD_BK_Booking::from_id( $post_id );
		// Check column
		switch ( $column ) {
			case 'details':
				echo implode( ', ', $booking->toArray() );
				break;
		}
	}

	/**
	 * Adds a booking after it has been purchased.
	 *
	 * @uses hook:action:edd_complete_purchase
	 * @param int $payment_id ID of the EDD payment.
	 */
	public function add_booking( $payment_id ) {
		$booking = EDD_BK_Booking::from_payment_meta( $payment_id );
		$inserted_id = wp_insert_post( array(
			'post_status'	=>	'publish',
			'post_type'		=>	self::CPT_SLUG
		), true );
		if ( is_wp_error( $inserted_id ) ) {
			file_put_contents( EDD_BK_DIR . 'log.txt', 'An error occurred while trying to save the booking.' );	
		} else {
			update_post_meta( $inserted_id, 'booking_details', $booking->toArray() );
		}
		// $payment_meta = edd_get_payment_meta( $payment_id );
		// file_put_contents( EDD_BK_DIR . 'log.txt', print_r( $payment_meta, true ) );
	}
	
}
