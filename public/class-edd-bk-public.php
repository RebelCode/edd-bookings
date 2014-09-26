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
class EDD_BK_Public {

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
	public function load_dependancies() {

	}

	/**
	 * [prepare_directories description]
	 * @return [type] [description]
	 */
	public function prepare_directories() {
		if ( !defined( 'EDD_BK_PUBLIC_PARTIALS_DIR' ) ) {
			define( 'EDD_BK_PUBLIC_PARTIALS_DIR', EDD_BK_PUBLIC_DIR . 'partials/' );
		}
		if ( !defined( 'EDD_BK_PUBLIC_JS_URL' ) ) {
			define( 'EDD_BK_PUBLIC_JS_URL', EDD_BK_PUBLIC_URL . 'js/' );
		}
		if ( !defined( 'EDD_BK_PUBLIC_CSS_URL' ) ) {
			define( 'EDD_BK_PUBLIC_CSS_URL', EDD_BK_PUBLIC_URL . 'css/' );
		}
	}

	/**
	 * [define_hooks description]
	 * @return [type] [description]
	 */
	private function define_hooks() {
		$loader = EDD_Booking::get_instance()->get_loader();
		
		$loader->add_action( 'edd_purchase_link_top', $this, 'render_download_booking' );
		$loader->add_action( 'wp_ajax_get_download_availability', $this, 'get_download_availability' );
		$loader->add_action( 'wp_ajax_nopriv_get_download_availability', $this, 'get_download_availability' );
	}

	/**
	 * [render_download_booking description]
	 * @return [type] [description]
	 */
	public function render_download_booking() {
		include EDD_BK_PUBLIC_PARTIALS_DIR.'partial-booking-front-end.php';
	}

	/**
	 * @todo func doc
	 * @return [type] [description]
	 */
	public function enqueue_styles() {
		if ( is_single() && get_post_type() == 'download' ) {
			wp_enqueue_style( 'edd-bk-fontawesome', EDD_BK_ADMIN_URL . 'css/font-awesome.min.css' );
			wp_enqueue_style( 'edd-bk-jquery-core-ui', EDD_BK_PUBLIC_CSS_URL . 'jquery-ui.css' );
			wp_enqueue_style( 'edd-bk-datepicker-skin', EDD_BK_PUBLIC_CSS_URL . 'datepicker-skin.css' );
		}
	}

	/**
	 * @todo func doc
	 * @return [type] [description]
	 */
	public function enqueue_scripts() {
		if ( is_single() ) {
			wp_enqueue_script(
				'edd-bk-download-public', EDD_BK_PUBLIC_JS_URL . 'edd-bk-front-end.js',
				array( 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-slider' ),
				'1.2',
				true // print script in footer
			);
		}
	}


	public function get_download_availability() {
		if ( ! isset( $_POST['post_id'] ) ) {
			echo json_encode( array(
				'error' => 'No post ID as given.'
			) );
			die();
		}
		$post_id = $_POST['post_id'];
		$availability = get_post_meta( $post_id, 'edd_bk_availability', TRUE );
		$availability = $availability == '' ? array() : $availability;
		echo json_encode( $availability );
		die();
	}

}