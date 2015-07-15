<?php

/**
 * EDD Booking public module class.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Booking
 * @subpackage Public
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
		$this->prepare_directories();
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
	 * Prepares directory constants.
	 */
	public function prepare_directories() {
		if ( !defined( 'EDD_BK_PUBLIC_VIEWS_DIR' ) ) {
			define( 'EDD_BK_PUBLIC_VIEWS_DIR', EDD_BK_PUBLIC_DIR . 'views/' );
		}
		if ( !defined( 'EDD_BK_PUBLIC_JS_URL' ) ) {
			define( 'EDD_BK_PUBLIC_JS_URL', EDD_BK_PUBLIC_URL . 'js/' );
		}
		if ( !defined( 'EDD_BK_PUBLIC_CSS_URL' ) ) {
			define( 'EDD_BK_PUBLIC_CSS_URL', EDD_BK_PUBLIC_URL . 'css/' );
		}
	}

	/**
	 * Registers the WordPress hooks to the loader.
	 */
	private function define_hooks() {
		$loader = EDD_Booking::get_instance()->get_loader();
		// Script and style enqueuing hooks
		$loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_styles', 11 );
		$loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_scripts', 11 );
		// View render hook
		$loader->add_action( 'edd_purchase_link_top', $this, 'render_download_booking' );
	}

	/**
	 * Enqueues the required styles for the front-end.
	 */
	public function enqueue_styles() {
		if ( is_single() && get_post_type() == 'download' ) {
			wp_enqueue_style( 'edd-bk-datepicker', EDD_BK_PUBLIC_CSS_URL . 'datepicker.css' );
			wp_enqueue_style( 'edd-bk-timepicker', EDD_BK_PUBLIC_CSS_URL . 'timepicker.css' );
		}
	}

	/**
	 * Enqueues the required scripts for the front-end.
	 */
	public function enqueue_scripts() {
		if ( is_single() ) {
			wp_enqueue_script(
				'jquery-ui-multidatepicker', EDD_BK_COMMON_JS_URL . 'jquery-ui.multidatespicker.js',
				array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), '1.6.3'
			);
			wp_enqueue_script(
				'edd-bk-download-public', EDD_BK_PUBLIC_JS_URL . 'edd-bk-front-end.js',
				array( 'edd-bk-utils', 'edd-bk-lodash', 'jquery-ui-multidatepicker' ), '1.2', true
			);
		}
	}

	/**
	 * Renders the public front-end view.
	 */
	public function render_download_booking() {
		if ( ! is_single() || ! get_the_ID() ) return;
		include EDD_BK_PUBLIC_VIEWS_DIR.'view-booking-front-end.php';
	}

}