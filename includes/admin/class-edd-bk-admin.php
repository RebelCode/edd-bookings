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
	 * Admin metaboxes for the New/Edit page for the Downloads Custom Post Type.
	 * 
	 * @var EDD_BK_Admin_Metaboxes
	 */
	private $metaboxes;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->load_dependancies();
		$this->define_hooks();
		$this->metaboxes = new EDD_BK_Admin_Metaboxes();
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
	 * Loads the required files and initializes any required data members. 
	 */
	private function load_dependancies() {
		require EDD_BK_WP_HELPERS_DIR . 'class-edd-bk-metabox.php';
		require EDD_BK_ADMIN_DIR . 'class-edd-bk-metaboxes.php';
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
