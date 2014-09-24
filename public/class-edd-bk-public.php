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
		
		$loader->add_action( 'edd_after_download_content', $this, 'render_download_booking' );
	}

	/**
	 * [render_download_booking description]
	 * @return [type] [description]
	 */
	public function render_download_booking() {
		include EDD_BK_PUBLIC_PARTIALS_DIR.'edd-bk-public-booking.php';
	}

	/**
	 * @todo func doc
	 * @return [type] [description]
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'edd-bk-jquery-core-ui', EDD_BK_PUBLIC_CSS_URL . 'jquery-ui.css' );
		wp_enqueue_style( 'edd-bk-datepicker-skin', EDD_BK_PUBLIC_CSS_URL . 'datepicker-skin.css' );
	}

	/**
	 * @todo func doc
	 * @return [type] [description]
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'edd-bk-download-public', EDD_BK_PUBLIC_JS_URL . 'edd-bk-download-public.js',
			array( 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-slider' ),
			'1.2',
			true // print script in footer
		);
	}

}