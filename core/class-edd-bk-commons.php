<?php

/**
 * The Commons module class.
 * 
 * Contains methods, functions and values that are used by both the
 * public and admin modules of the plugin.
 */
class EDD_BK_Commons {

	/**
	 * The bookings handler.
	 * 
	 * @var EDD_BK_Bookings_Handler
	 */
	private $bookings;

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Prepare required files, directories and hooks
		$this->load_dependancies();
		$this->prepare_directories();
		$this->define_hooks();
		// Initialize the bookings handler
		$this->bookings = new EDD_BK_Bookings_Handler();
	}

	/**
	 * Loads required files.
	 */
	public function load_dependancies() {
		require( EDD_BK_CORE_DIR . 'class-edd-bk-bookings-handler.php' );
		require( EDD_BK_INCLUDES_DIR . 'class-edd-bk-download.php' );
		require( EDD_BK_UTILS_DIR . 'class-edd-bk-date-utils.php' );
	}

	/**
	 * Prepares the directory constants.
	 */
	public function prepare_directories() {
		if ( !defined( 'EDD_BK_COMMON_JS_URL' ) ) {
			define( 'EDD_BK_COMMON_JS_URL',	EDD_BK_CORE_URL . 'js/' );
		}
		if ( !defined( 'EDD_BK_COMMON_CSS_URL' ) ) {
			define( 'EDD_BK_COMMON_CSS_URL',	EDD_BK_CORE_URL . 'css/' );
		}
		if ( !defined( 'EDD_BK_COMMON_FONTS_URL' ) ) {
			define( 'EDD_BK_COMMON_FONTS_URL',	EDD_BK_CORE_URL . 'fonts/' );
		}
	}

	/**
	 * Prepares the hooks.
	 */
	public function define_hooks() {
		$loader = EDD_Booking::instance()->get_loader();
		// Determine which action hook to use (if in admin or not)
		$style_hook = is_admin()? 'admin_enqueue_scripts' : 'wp_enqueue_scripts';
		// Add the actions to the loader
		$loader->add_action( $style_hook, $this, 'enqueue_styles', 10 );
		$loader->add_action( $style_hook, $this, 'enqueue_scripts', 10 );
	}

	/**
	 * Enqueues the styles.
	 */
	public function enqueue_styles() {
		$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_style( 'edd-bk-admin-fa', EDD_BK_COMMON_CSS_URL . 'font-awesome.min.css' );
		wp_enqueue_style( 'edd-bk-jquery-ui-theme', EDD_BK_COMMON_CSS_URL . 'jquery-ui'.$suffix.'.css' );
	}

	/**
	 * Enqueues the scripts.
	 */
	public function enqueue_scripts() {
		// Register (but not enqueue) scripts
		wp_register_script( 'edd-bk-utils', EDD_BK_COMMON_JS_URL . 'edd-bk-utils.js', array( 'jquery' ), '1.0', true );
		wp_register_script( 'edd-bk-lodash', EDD_BK_COMMON_JS_URL . 'lodash.min.js', array(), '3.10.0', true );
	}

	/**
	 * Returns the meta field names or the meta field values if a post ID is given.
	 * 
	 * @param  string|int $post_id (Optional) The ID of a post. Default: null
	 * @return array               An array of meta field names, or and assoc array of
	 *                             field names => field values if a post ID is given.
	 */
	public static function meta_fields( $post_id = null ) {
		$fields = array(
			'enabled',
			'duration_type',
			'slot_duration',
			'slot_duration_unit',
			'min_slots',
			'max_slots',
			'availability',
			'availability_fill',
			'price_type',
			'base_cost',
			'cost_per_slot'
		);
		// If no post ID is given, return
		if ( $post_id === null ) return $fields;
		// Otherwise, generate meta assoc array
		$meta = array();
		foreach ( $fields as $i => $field ) {
			$meta[ $field ] = get_post_meta( $post_id, 'edd_bk_'.$field, TRUE );
		}
		return $meta;
	}

	/**
	 * [testing_new_function_generate_times description]
	 * 
	 * @param  [type] $post_id  [description]
	 * @param  [type] $date_str [description]
	 * @return [type]           [description]
	 */
	public static function get_times_for_date( $post_id, $date_str ) {
		// Get the download with this ID
		$download = EDD_BK_Download::from_id( $post_id );
		// Check if the session unit allows time picking
		if ( ! $download->isSessionUnit( 'hours', 'minutes' ) ) return array();
		
		// Calculate the session length in seconds (for timestamp operations)
		// Session unit is either hour or minutes (see 4 lines up). Multiply by
		// 60 for both cases (mins or hours), then check if the unit is hours
		// and multiply again if so.
		$slength = $download->getSessionLength() * 60;
		if ( $download->isSessionUnit( 'hours' ) ) $slength *= 60;

		// Minimum session length in seconds.
		$min_slength = $slength * $download->getMinSessions();
		// Maximum session length in seconds.
		$max_slength = $slength * $download->getMaxSessions();

		// Parse the date string into a timestamp
		$date_parts = explode( '/', $date_str );
		$timestamp = strtotime( $date_parts[2] . '-' . $date_parts[0] . '-' . $date_parts[1] );

		// The master list: Array of final times
		// Values in the array also contain the maximum number of sessions that can be chosen:
		// Ex. availability from 08:00-11:00 with 1-3hr variable length sessions:
		// 		0 => 08:00|3
		// 		1 => 09:00|2
		// 		2 => 10:00|1
		$master_list = array();
		// Iterate each availability entry
		foreach ( $download->getAvailability()->getEntries() as $i => $entry ) {
			//if ( ! $entry->matches( $timestamp ) ) continue;
			$type = strtolower( $entry->getType()->getGroup() );
			// Check if the entry type has the word 'day' in it
			// This is to be only iterate entries that have time values
			if ( stripos( $type, 'day' ) !== FALSE && $entry->partiallyMatches( $timestamp ) ) {
				$times = array();
				$start = $entry->getFrom();
				$end = $entry->getTo();
				$curr = $start;
				while ( $curr < $end && ($curr + $min_slength) <= $end ) {
					$max_seconds = min( $end, $curr + $max_slength );
					$max_num_sessions = ( $max_seconds - $curr ) / $slength;
					array_push( $times, $curr . "|". $max_num_sessions);
					$curr += $slength;
				}
				// Continue if times array is empty
				if ( count( $times ) == 0 ) continue;
				// If the entry is an available one
				if ( $entry->isAvailable() ) {
					// Add all times to the master list
					$master_list = array_unique( array_merge( $master_list, $times ) );
				} else {
					// Otherwise, remove all times from the master list
					$master_list = array_diff( $master_list, $times );
				}
			}
		}
		// Return the master list
		return $master_list;
	}


	public static function ajax_get_times_for_date() {
		if ( ! isset( $_POST['post_id'], $_POST['date'] ) ) {
			echo json_encode( array(
				'error' => 'A post ID and a valid date must be supplied!'
			) );
			die();
		}
		$post_id = $_POST['post_id'];
		$date = $_POST['date'];
		echo json_encode( self::get_times_for_date( $post_id, $date ) );
		die();
	}


}