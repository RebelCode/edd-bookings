<?php

/**
 * This class handles the Booking Custom Post Type.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Bookings\Bookings
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
	 * An EDD_BK_Booking instance, cached for each row, to avoid retrieving
	 * it for each cell in the row on each fill_custom_columns() call.
	 * @var EDD_BK_Boking
	 */
	protected $table_row_booking_cache;

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
				'show_in_menu'	=>	'edit.php?post_type=download',
				'supports'		=>	array('title')
			)
		);
		$this->table_row_booking_cache = NULL;
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
		// Hooks for row actions
		$loader->add_filter( 'post_row_actions', $this, 'filter_row_actions', 10, 2 );
		$loader->add_action( 'edd_view_order_details_files_after', $this, 'order_view_page' );
		// Hook to force single column display
		$loader->add_filter( 'get_user_option_screen_layout_edd_booking', $this, 'set_screen_layout' );
	}

	/**
	 * Registers the CPT.
	 * 
	 * @uses hook::action::init
	 */
	public function register_cpt() {
		$this->cpt->register();
		$this->cpt->removeSupport(
			array(
				'title',
				'editor',
				'author',
				'thumbail',
				'excerpt',
				'revisions',
				'post-formats',
				'page-attributes',
				'comments'
			)
		);
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
			'edd-date'	=>	__( 'Date', EDD_Bookings::TEXT_DOMAIN ),
			'duration'	=>	__( 'Duration', EDD_Bookings::TEXT_DOMAIN ),
			'name'		=>	__( 'Name', EDD_Bookings::TEXT_DOMAIN ),
			'download'	=>	__( 'Download', EDD_Bookings::TEXT_DOMAIN ),
			'payment'	=>	__( 'Payment', EDD_Bookings::TEXT_DOMAIN ),
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

		// Get the booking from cache if the given ID and the cached ID are the same.
		// Otherwise, retrieve from DB and set the cache
		$booking = NULL;
		if ( $this->table_row_booking_cache !== NULL && $this->table_row_booking_cache->getId() == $post_id ) {
			$booking = $this->table_row_booking_cache;
		} else {
			$booking = EDD_Bookings::instance()->get_bookings_controller()->get( $post_id );
			$this->table_row_booking_cache = $booking;
		}

		// Check if the download for this booking exists. If not, stop
		$download = edd_bk()->get_downloads_controller()->get( $booking->getDownloadId() );
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
				if ( $booking->isSessionUnit( EDD_BK_Session_Unit::HOURS, EDD_BK_Session_Unit::MINUTES  ) ) {
					$date += $booking->getTime();
					$format = 'h:ia ' . $format;
				}
				echo date( $format, $date );
				break;

			case 'duration':
				echo $booking->getDuration() . ' ' . $booking->getSessionUnit();
				break;

			case 'download':
				$download_id = $booking->getDownloadId();
				$link = admin_url( 'post.php?action=edit&post=' . $download_id );
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

	/**
	 * Filters the row actions for the Bookings CPT.
	 *
	 * @param  array   $actions The row actions to filter.
	 * @param  WP_Post $post    The post for which the row actions will be filtered.
	 * @return array            The filtered row actions.
	 */
	public function filter_row_actions( $actions, $post ) {
		// Do not continue if post type is not our bookings cpt
		if ( $post->post_type !== self::SLUG ) return $actions;
		// Remove the quickedit
		unset( $actions['inline hide-if-no-js'] );
		return $actions;
	}

	/**
	 * [set_screen_options description]
	 */
	public function set_screen_layout() {
		return 1;
	}

	public function order_view_page( $payment_id ) {
		// Get the cart details for this payment
		$cart_items = edd_get_payment_meta_cart_details( $payment_id );
		// Stop if not an array
		if ( ! is_array( $cart_items ) ) return;
		// Get the bookings for this payment
		$bookings = EDD_Bookings::instance()->get_bookings_controller()->getBookingsForPayemnt( $payment_id );
		if ( $bookings === NULL || count( $bookings ) == 0 ) return;

		echo EDD_BK_Utils::render_view( 'view-order-details', array( 'bookings' => $bookings ) );
	}

}
