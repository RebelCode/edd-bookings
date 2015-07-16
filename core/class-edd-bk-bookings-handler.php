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
		$loader->add_action( 'manage_posts_custom_column', $this, 'fill_table_data', 10, 2 );
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
			'cb'		=>	$columns['cb'],
			'id'		=>	__( 'ID', 'edd_bk' ),
			'name'		=>	__( 'Name', 'edd_bk' ),
			'from'		=>	__( 'From', 'edd_bk' ),
			'to'		=>	__( 'To', 'edd_bk' ),
			'download'	=>	__( 'Download', 'edd_bk' ),
			'payment'	=>	__( 'Payment', 'edd_bk' ),
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
		$download = EDD_BK_Download::from_id( $booking->getDownloadID() );
		if ( ! is_object( $download ) ) return;

		// Check column
		switch ( $column ) {
			case 'id':
				echo $booking->getID();
				break;

			case 'name':
				$payment_meta = edd_get_payment_meta( $booking->getPaymentID() );
				$customer = new EDD_Customer( $payment_meta['user_info']['id'] );
				echo $customer->name;
				break;

			case 'from':
			case 'to':
				$from = ( $column === 'from' );
				$sessions = $booking->getNumSessions();
				$time = $booking->getTime();
				$date = $booking->getDate();
				$date_format = get_option( 'date_format', 'F j, Y' );
				$date_time_format = 'H:i ' . $date_format;
				
				switch ( $download->getSessionUnit() ) {
					case EDD_BK_Session_Unit::MINUTES:
						$time += $from? 0 : $sessions * 60;
						echo date( $date_time_format, $date + $time );
						break;

					case EDD_BK_Session_Unit::HOURS:
						$time = ( $from )? $time : $time + ( $sessions * 3600 );
						echo date( $date_time_format, $date + $time );
						break;

					case EDD_BK_Session_Unit::DAYS:
						$date += ( $column === 'to' )? $booking->getNumSessions() * DAY_IN_SECONDS : 0;
						echo date( $date_format, $date );
						break;

					case EDD_BK_Session_Unit::WEEKS:
						$date += ( $column === 'to' )? $booking->getNumSessions() * WEEK_IN_SECONDS : 0;
						echo date( $date_format, $date );
						break;
				}
				break;

			case 'download':
				$download_id = $booking->getDownloadID();
				$link = admin_url( 'edit.php?post_type=download&post_id=' . $download_id );
				$text = get_the_title( $download_id );
				echo "<a href=\"$link\">$text</a>";
				break;

			case 'payment':
				$payment_id = $booking->getPaymentID();
				$link = admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $payment_id );
				$text = __( 'View Order Details', 'edd' );
				echo "<a href=\"$link\">$text</a>";
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
		file_put_contents( EDD_BK_DIR . 'log.txt', 'Beginning to create booking!' );
		$booking = EDD_BK_Booking::from_payment_meta( $payment_id );
		file_put_contents( EDD_BK_DIR . 'log.txt', 'Created Booking!', FILE_APPEND );

		$inserted_id = wp_insert_post( array(
			'post_content'	=>	'',
			'post_title'	=>	date( 'U' ),
			'post_excerpt'	=>	'N/A',
			'post_status'	=>	'publish',
			'post_type'		=>	self::CPT_SLUG
		), true );
		file_put_contents( EDD_BK_DIR . 'log.txt', 'Attempting to insert post!', FILE_APPEND );

		if ( is_wp_error( $inserted_id ) ) {
			file_put_contents( EDD_BK_DIR . 'log.txt', 'An error occurred while trying to save the booking.', FILE_APPEND );
		} else {
			update_post_meta( $inserted_id, 'booking_details', $booking->toArray() );
			file_put_contents( EDD_BK_DIR . 'log.txt', 'Post inserted!', FILE_APPEND );
		}
		// $payment_meta = edd_get_payment_meta( $payment_id );
		// file_put_contents( EDD_BK_DIR . 'log.txt', print_r( $payment_meta, true ) );
	}
	
}
