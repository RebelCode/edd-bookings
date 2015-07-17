<?php

/**
 * EDD Booking admin module class.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Booking
 * @subpackage Admin
 */
class EDD_BK_Admin {

	/**
	 * Admin metaboxes for the New/Edit page for the Downloads Custom Post Type.
	 * 
	 * @var EDD_BK_Admin_Metaboxes
	 */
	private $metaboxes;

	/**
	 * The Bookings pages.
	 * 
	 * @var EDD_BK_Bookings_Page
	 */
	private $bookings_page;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->prepare_directories();
		$this->load_dependancies();
		$this->define_hooks();
		$this->metaboxes = new EDD_BK_Admin_Metaboxes();
		$this->bookings_page = new EDD_BK_Bookings_Page();
	}

	/**
	 * Returns the metaboxes instance.
	 * 
	 * @return EDD_BK_Admin_Metaboxes
	 */
	public function get_metaboxes() {
		return $this->metaboxes;
	}

	/**
	 * Returns the bookings page instance.
	 * 
	 * @return EDD_BK_Bookings_Page
	 */
	public function get_bookings_page() {
		return $this->bookings_page;
	}

	/**
	 * Prepares the directory constants.
	 */
	private function prepare_directories() {
		if ( !defined( 'EDD_BK_ADMIN_VIEWS_DIR' ) ) {
			define( 'EDD_BK_ADMIN_VIEWS_DIR', EDD_BK_ADMIN_DIR . 'views/' );
		}
		if ( !defined( 'EDD_BK_ADMIN_JS_URL' ) ) {
			define( 'EDD_BK_ADMIN_JS_URL', EDD_BK_ADMIN_URL . 'js/' );
		}
		if ( !defined( 'EDD_BK_ADMIN_CSS_URL' ) ) {
			define( 'EDD_BK_ADMIN_CSS_URL', EDD_BK_ADMIN_URL . 'css/' );
		}
	}

	/**
	 * Loads the required files and initializes any required data members. 
	 */
	private function load_dependancies() {
		require EDD_BK_ADMIN_DIR . 'class-edd-bk-metabox.php';
		require EDD_BK_ADMIN_DIR . 'class-edd-bk-metaboxes.php';
		require EDD_BK_ADMIN_DIR . 'class-edd-bk-bookings-page.php';
	}


	/**
	 * Registers the WordPress hooks to the loader.
	 */
	private function define_hooks() {
		$loader = EDD_Booking::get_instance()->get_loader();
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
	}

	/**
	 * Prints a simple HTML tooltip.
	 */
	public function help_tooltip( $text ) {
		return EDD_BK_Utils::ob_include( EDD_BK_ADMIN_VIEWS_DIR . 'view-admin-help-tooltip.php', array( 'text' => $text ) );
	}

}
