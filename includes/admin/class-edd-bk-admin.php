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
	 * Constructor.
	 */
	public function __construct() {
		$this->load_dependancies();
		$this->define_hooks();
		$this->downloads_metabox_controller = new EDD_BK_Downloads_Metabox_Controller();
	}

	/**
	 * Returns the downloads metabox class instance.
	 * 
	 * @return EDD_BK_Downloads_Metabox
	 */
	public function get_downloads_metabox_controller() {
		return $this->downloads_metabox_controller;
	}

	/**
	 * Loads the required files and initializes any required data members. 
	 */
	private function load_dependancies() {
		require EDD_BK_WP_HELPERS_DIR . 'class-edd-bk-metabox.php';
		require EDD_BK_DOWNLOADS_DIR . 'class-edd-bk-downloads-metabox.php';
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
	}

	/**
	 * Prints a simple HTML tooltip.
	 */
	public function help_tooltip( $text ) {
		return EDD_BK_Utils::ob_include( EDD_BK_VIEWS_DIR . 'view-admin-help-tooltip.php', array( 'text' => $text ) );
	}

}
