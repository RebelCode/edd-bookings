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
		
		$loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_styles' );
		$loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_scripts' );

		$loader->add_action( 'edd_purchase_link_top', $this, 'render_download_booking' );
		$loader->add_action( 'wp_ajax_get_download_availability', $this, 'get_download_availability' );
		$loader->add_action( 'wp_ajax_nopriv_get_download_availability', $this, 'get_download_availability' );
		$loader->add_action( 'wp_ajax_get_times_for_date', 'EDD_BK_Commons', 'ajax_get_times_for_date' );
		$loader->add_action( 'wp_ajax_nopriv_get_times_for_date', 'EDD_BK_Commons', 'ajax_get_times_for_date' );

		$loader->add_filter( 'edd_add_to_cart_item', $this, 'cart_item_data' );
		$loader->add_filter( 'edd_cart_item_price', $this, 'cart_item_price', 10, 3 );
	}

	/**
	 * Adds data to the cart items
	 * 
	 * @param  array $item The original cart item.
	 * @return array       The filtered item, with added EDD Booking data.
	 */
	public function cart_item_data( $item ) {
		if ( ! empty( $_POST['post_data'] ) ) {
			parse_str( $_POST['post_data'], $post_data );
			if ( isset( $post_data['edd_bk_num_slots'] ) ) {
				$item['options']['edd_bk_num_slots'] = intval( $post_data['edd_bk_num_slots'] );
			}
			if ( isset( $post_data['edd_bk_date'] ) ) {
				$item['options']['edd_bk_date'] = intval( $post_data['edd_bk_date'] );
			}
		}
		return $item;
	}

	/**
	 * Modifies the cart item price.
	 * 
	 * @param  float $price       The item price.
	 * @param  int   $download_id The ID of the download.
	 * @param  array $options     The cart item options.
	 * @return float              The new filtered price.
	 */
	public function cart_item_price( $price, $download_id, $options ) {
		if ( isset( $options['edd_bk_date'] ) ) {
			$num_slots = isset( $options['edd_bk_num_slots'] )? intval( $options['edd_bk_num_slots'] ) : 1;
			$cost_per_slot = get_post_meta( $download_id, 'edd_bk_cost_per_slot', TRUE );
			$price = floatval( $cost_per_slot ) * $num_slots;
		} else {
			file_put_contents(EDD_BK_DIR.'log.txt', print_r($options, true));
		}
		return $price;
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
			wp_enqueue_style( 'edd-bk-datepicker', EDD_BK_PUBLIC_CSS_URL . 'datepicker.css' );
			wp_enqueue_style( 'edd-bk-timepicker', EDD_BK_PUBLIC_CSS_URL . 'timepicker.css' );
		}
	}

	/**
	 * @todo func doc
	 * @return [type] [description]
	 */
	public function enqueue_scripts() {
		if ( is_single() ) {
			wp_enqueue_script(
				'multi-datepicker', EDD_BK_COMMONS_JS_URL . 'jquery-ui.multidatespicker.js',
				array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-slider' ),
				'1.6.3'
			);
			wp_enqueue_script(
				'month-datepicker', EDD_BK_COMMONS_JS_URL . 'jquery-ui.monthpicker.js',
				array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'multi-datepicker' ),
				'1.0'
			);
			wp_enqueue_script(
				'edd-bk-download-public', EDD_BK_PUBLIC_JS_URL . 'edd-bk-front-end.js',
				array( 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-slider' ),
				'1.2',
				true
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