<?php

/**
 * EDD Booking admin module class.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Bookings\Admin
 */
class EDD_BK_Admin {

	/**
	 * The downloads metabox class instance.
	 * 
	 * @var EDD_BK_Downloads_Metabox
	 */
	private $downloads_metabox_controller;

	/**
	 * The bookings metabox class instance.
	 * 
	 * @var EDD_BK_Bookings_Metabox
	 */
	private $bookings_metabox_controller;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->load_dependancies();
		$this->define_hooks();
		$this->downloads_metabox_controller = new EDD_BK_Downloads_Metabox_Controller();
		$this->bookings_metabox_controller = new EDD_BK_Bookings_Metabox_Controller();
	}

	/**
	 * Returns the downloads metabox class instance.
	 * 
	 * @return EDD_BK_Downloads_Metabox_Controller
	 */
	public function get_downloads_metabox_controller() {
		return $this->downloads_metabox_controller;
	}

	/**
	 * Returns the bookings metabox class instance.
	 * 
	 * @return EDD_BK_Bookings_Metabox_Controller
	 */
	public function get_bookings_metabox_controller() {
		return $this->bookings_metabox_controller;
	}

	/**
	 * Loads the required files and initializes any required data members. 
	 */
	private function load_dependancies() {
		require EDD_BK_WP_HELPERS_DIR . 'class-edd-bk-metabox.php';
		require EDD_BK_DOWNLOADS_DIR . 'class-edd-bk-downloads-metabox-controller.php';
		require EDD_BK_BOOKINGS_DIR . 'class-edd-bk-bookings-metabox-controller.php';
	}

	/**
	 * Registers the WordPress hooks to the loader.
	 */
	private function define_hooks() {
		$loader = EDD_Bookings::get_instance()->get_loader();
		$loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_styles' );
		$loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_scripts' );
	}

	/**
	 * Enqueues CSS stylesheet files
	 */
	public function enqueue_styles() {
	}

	/**
	 * Enqueues JS script files
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'edd-bk-admin-bookings', EDD_BK_JS_URL . 'edd-bk-admin-bookings.js' );
	}

	/**
	 * Prints a simple HTML tooltip.
	 */
	public function help_tooltip( $text ) {
		return EDD_BK_Utils::ob_include( EDD_BK_VIEWS_DIR . 'view-admin-help-tooltip.php', array( 'text' => $text ) );
	}

}
