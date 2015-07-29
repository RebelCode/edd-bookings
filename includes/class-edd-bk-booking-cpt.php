<?php

/**
 * This class handles the Booking Custom Post Type.
 */
class EDD_BK_Booking_CPT {

	/**
	 * The custom post type slug name.
	 */
	const SLUG = 'edd_booking';

	/**
	 * The custom post type object.
	 * @var EDD_BK_Custom_Post_Type
	 */
	protected $cpt;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->cpt = new EDD_BK_Custom_Post_Type( self::SLUG );
		$this->cpt->generateLabels( 'Booking', 'Bookings' );
		$this->cpt->setProperties(
			array(
				'public'		=>	false,
				'show_ui'		=>	true,
				'has_archive'	=>	false,
				'show_in_menu'	=>	'edit.php?post_type=download'
			)
		);
		$this->define_hooks();
	}
	
	/**
	 * Registers the hooks to the loader.
	 */
	public function define_hooks() {
		$loader = EDD_Bookings::instance()->get_loader();
		// Hook to register CPT
		$loader->add_action( 'init', $this, 'register_cpt' );
		// Hooks for custom columns
		$loader->add_action( 'manage_edd_booking_posts_columns', $this, 'register_custom_columns' );
		$loader->add_action( 'manage_posts_custom_column', $this, 'fill_custom_columns', 10, 2 );
	}

	/**
	 * Registers the CPT.
	 * 
	 * @uses hook::action::init
	 */
	public function register_cpt() {
		$this->cpt->register();
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
			'edd-date'	=>	__( 'Date', 'edd_bk' ),
			'duration'	=>	__( 'Duration', 'edd_bk' ),
			'name'		=>	__( 'Name', 'edd_bk' ),
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
	public function fill_custom_columns( $column, $post_id ) {
		// Stop if post is not a booking post type
		if ( get_post_type( $post_id ) !== self::SLUG ) return;
		// Create the booking object
		$booking = EDD_BK_Bookings_Controller::get( $post_id );
		$download = EDD_BK_Downloads_Controller::get( $booking->getDownloadId() );
		if ( ! is_object( $download ) ) return;

		// Check column
		switch ( $column ) {
			case 'name':
				$customer = EDD_BK_Customers_Controller::get( $booking->getCustomerId() );
				$link = admin_url( 'edit.php?post_type=download&page=edd-customers&view=overview&id=' . $customer->getId() );
				echo "<a href=\"$link\">" . $customer->getName() . '</a>';
				break;

			case 'edd-date':
				$date = $booking->getDate();
				$format = 'D jS M, Y';
				if ( $download->isSessionUnit( EDD_BK_Session_Unit::HOURS, EDD_BK_Session_Unit::MINUTES  ) ) {
					$date += $booking->getTime();
					$format = 'h:ia ' . $format;
				}
				echo date( $format, $date );
				break;

			case 'duration':
				echo $booking->getNumSessions() . ' ' . $download->getSessionUnit();
				break;

			case 'download':
				$download_id = $booking->getDownloadId();
				$link = admin_url( 'edit.php?post_type=download&post_id=' . $download_id );
				$text = get_the_title( $download_id );
				echo "<a href=\"$link\">$text</a>";
				break;

			case 'payment':
				$payment_id = $booking->getPaymentId();
				$link = admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $payment_id );
				$text = __( 'View Order Details', 'edd' );
				echo "<a href=\"$link\">$text</a>";
				break;
		}
	}

}
