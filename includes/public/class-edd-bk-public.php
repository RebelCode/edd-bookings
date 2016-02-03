<?php

/**
 * EDD Booking public module class.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Booking\Public
 */
class EDD_BK_Public {

	/**
	 * AJAX Handler.
	 * @var EDD_BK_Public_AJAX
	 */
	private $ajax;

	/**
	 * Cart Handler.
	 * @var EDD_BK_Public_Cart
	 */
	private $cart;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->load_dependancies();
		$this->define_hooks();
		$this->ajax = new EDD_BK_Public_AJAX();
		$this->cart = new EDD_BK_Public_Cart();
	}

	/**
	 * Returns the AJAX handler.
	 * 
	 * @return EDD_BK_Publix_AJAX
	 */
	public function get_ajax_handler() {
		return $this->ajax;
	}

	/**
	 * Returns the Cart Handler.
	 * 
	 * @return EDD_BK_Public_Cart
	 */
	public function get_cart_handler() {
		return $this->cart;
	}

	/**
	 * Loads required files.
	 */
	public function load_dependancies() {
		require EDD_BK_PUBLIC_DIR . 'class-edd-bk-public-ajax.php';
		require EDD_BK_PUBLIC_DIR . 'class-edd-bk-public-cart.php';
	}

	/**
	 * Registers the WordPress hooks to the loader.
	 */
	private function define_hooks() {
		$loader = EDD_Bookings::get_instance()->get_loader();
		// Script and style enqueuing hooks
		$loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_styles', 11 );
		$loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_scripts', 11 );
		// View render hook
		$loader->add_action( 'edd_purchase_link_top', $this, 'render_download_booking', 10, 2 );
		// Receipt hook
		$loader->add_action( 'edd_payment_receipt_after_table', $this, 'render_receipt_booking_info', 10, 2 );
		// Shortcode hooks
		$loader->add_action( 'shortcode_atts_purchase_link', $this, 'purchase_link_shortcode_atts', 10, 3 );
	}

	/**
	 * Enqueues the required styles for the front-end.
	 */
	public function enqueue_styles() {
		if ( ! wp_style_is( 'jquery-ui-style-css', 'enqueued' ) ) {
			if ( ! wp_style_is( 'jquery-ui-style-css', 'registered' ) ) {
				wp_register_style( 'jquery-ui-style-css', EDD_BK_CSS_URL . 'jquery-ui.min.css' );
			}
			wp_enqueue_style( 'jquery-ui-style-css' );
		}

		wp_enqueue_style( 'edd-bk-datepicker', EDD_BK_CSS_URL . 'edd-bk-datepicker.css' );
		wp_enqueue_style( 'edd-bk-timepicker', EDD_BK_CSS_URL . 'edd-bk-timepicker.css' );
	}

	/**
	 * Enqueues the required scripts for the front-end.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'jquery-ui-multidatepicker', EDD_BK_JS_URL . 'jquery-ui.multidatespicker.js',
			array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), '1.6.3'
		);
		wp_enqueue_script(
			'edd-bk-public-download-view', EDD_BK_JS_URL . 'edd-bk-public-download-view.js',
			array( 'edd-bk-utils', 'edd-bk-lodash', 'edd-bk-moment', 'jquery-ui-multidatepicker' ), '1.2', true
		);
		wp_enqueue_script(
			'edd-bk-public-download-archive', EDD_BK_JS_URL . 'edd-bk-public-download-archive.js',
			array( 'edd-bk-utils', 'edd-bk-lodash', 'edd-bk-moment', 'jquery-ui-multidatepicker' ), '1.2', true
		);
	}

	/**
	 * Renders the public front-end view.
	 */
	public function render_download_booking($id = null, $args = array()) {
		// If ID is not passed as parameter, get current loop post ID
		if ( $id === null ) {
			$id = get_the_ID();
		}
		// Get booking options from args param
		$booking_options = isset( $args['booking_options'] )
				? $args['booking_options']
				: true;
		// Stop if post is not a download or booking options are disabled
		if ( get_post_type($id) !== 'download' || ! $booking_options ) {
			return;
		}
		// Vars for view
		$post_id = $id;
		// Ensures that output is shown in multiviews
		$eddBkFromShortcode = true;
		// Load view
		include EDD_BK_VIEWS_DIR . 'view-public-booking-single.php';
	}

	/**
	 * Renders the booking informatio on receipt.
	 * 
	 * @param EDD_Payment $payment The payment object.
	 * @param array $receipt_args The receipt args
	 */
	public function render_receipt_booking_info( $payment, $receipt_args ) {
		$bookings_controller = edd_bk()->get_bookings_controller();
		$bookings = $bookings_controller->getBookingsForPayemnt( $payment->ID );
		if ( count( $bookings ) == 0 ) return;
		include EDD_BK_VIEWS_DIR . 'view-public-booking-receipt.php';
	}

	/**
	 * Adds processing of our `booking_options` attribute for the `[purchase_link]` shortcode.
	 * 
	 * @param  array $out The output assoc. array of attributes and their values.
	 * @param  array $pairs Hell if I know
	 * @param  array $atts The input assoc. array of attributes passed to the shortcode.
	 * @return array The resulting assoc. array of attributes and their values.
	 */
	public function purchase_link_shortcode_atts($out, $pairs, $atts) {
		if ( isset( $atts['booking_options'] ) ) {
			$out['booking_options'] = strtolower( $atts['booking_options'] ) !== 'no';
		}
		return $out;
	}

}