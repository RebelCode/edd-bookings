<?php

/**
 * @todo		file doc
 * @since		1.0.0
 * @package		EDD_BK
 * @subpackage	EDD_BK/admin
 */

/**
 * @todo class doc
 */
class EDD_BK_Admin {

	/**
	 * [$metaboxes description]
	 * @var [type]
	 */
	private $metaboxes;

	/**
	 * [__construct description]
	 * @param [type] $_name    [description]
	 * @param [type] $_version [description]
	 */
	public function __construct() {
		$this->prepare_directories();
		$this->load_dependancies();

		$this->define_hooks();
	}

	/**
	 * [load_dependancies description]
	 * @return [type] [description]
	 */
	private function load_dependancies() {
		require EDD_BK_ADMIN_DIR . 'class-edd-bk-metaboxes.php';
	}

	/**
	 * [prepare_directories description]
	 * @return [type] [description]
	 */
	private function prepare_directories() {
		if ( !defined( 'EDD_BK_ADMIN_PARTIALS_DIR' ) ) {
			define( 'EDD_BK_ADMIN_PARTIALS_DIR', EDD_BK_ADMIN_DIR . 'partials/' );
		}
		if ( !defined( 'EDD_BK_ADMIN_JS_URL' ) ) {
			define( 'EDD_BK_ADMIN_JS_URL', EDD_BK_ADMIN_URL . 'js/' );
		}
		if ( !defined( 'EDD_BK_ADMIN_CSS_URL' ) ) {
			define( 'EDD_BK_ADMIN_CSS_URL', EDD_BK_ADMIN_URL . 'css/' );
		}
	}

	/**
	 * [define_hooks description]
	 * @return [type] [description]
	 */
	private function define_hooks() {
		$this->metaboxes = new EDD_BK_Admin_Metaboxes();
		$loader = EDD_Booking::get_instance()->get_loader();
		
		$loader->add_action(      'save_post',  $this->metaboxes, 'save_post' );
		$loader->add_action( 'add_meta_boxes',	$this->metaboxes, 'add_meta_boxes' );
		$loader->add_action( 'edd_downloads_contextual_help', $this->metaboxes, 'contextual_help' );
	}

	/**
	 * @todo func doc
	 * @return [type] [description]
	 */
	public function enqueue_styles() {
		// Get current screen
		$screen = get_current_screen();
		if ( $screen->id === 'download' ) {
			wp_enqueue_style( 'edd-bk-download-edit-css', EDD_BK_ADMIN_CSS_URL . 'edd-bk-download-edit.css' );
			wp_enqueue_style( 'edd-bk-admin-fa', EDD_BK_ADMIN_CSS_URL . 'font-awesome.min.css' );
		}
	}

	/**
	 * @todo func doc
	 * @return [type] [description]
	 */
	public function enqueue_scripts() {
		// Get current screen
		$screen = get_current_screen();
		// Load for Downloads Edit Page
		if ( $screen->id === 'download' ) {
			wp_enqueue_script( 'edd-bk-download-edit-js', EDD_BK_ADMIN_JS_URL . 'edd-bk-download-edit.js', array( 'jquery' ) );
		}
	}

}