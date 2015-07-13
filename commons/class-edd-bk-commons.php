<?php

require( EDD_BK_COMMONS_DIR . 'class-edd-bk-booking.php' );
require( EDD_BK_COMMONS_DIR . 'class-edd-bk-date-utils.php' );

/**
 * The Commons class. Contains methods and functions that are used by both the
 * public and admin parts of the plugin.
 */
class EDD_BK_Commons {
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->prepare_directories();
		$this->define_hooks();
	}

	/**
	 * Prepares the directory constants.
	 */
	public function prepare_directories() {
		if ( !defined( 'EDD_BK_COMMONS_JS_URL' ) ) {
			define( 'EDD_BK_COMMONS_JS_URL',	EDD_BK_COMMONS_URL . 'js/' );
		}
		if ( !defined( 'EDD_BK_COMMONS_CSS_URL' ) ) {
			define( 'EDD_BK_COMMONS_CSS_URL',	EDD_BK_COMMONS_URL . 'css/' );
		}
	}

	/**
	 * Enqueues the styles.
	 */
	public function enqueue_styles() {
		$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_style( 'edd-bk-admin-fa', EDD_BK_COMMONS_CSS_URL . 'font-awesome.min.css' );
		wp_enqueue_style( 'edd-bk-jquery-ui-theme', EDD_BK_COMMONS_CSS_URL . 'jquery-ui'.$suffix.'.css' );
	}

	/**
	 * Enqueues the scripts.
	 */
	public function enqueue_scripts() {
		// Register (but not enqueue) scripts
		wp_register_script(
			'edd-bk-utils', EDD_BK_COMMONS_JS_URL . 'edd-bk-utils.js',
			array( 'jquery' ),
			'1.0',
			true
		);
		wp_register_script(
			'edd-bk-lodash', EDD_BK_COMMONS_JS_URL . 'lodash.min.js',
			array(),
			'3.10.0',
			true
		);
	}

	/**
	 * Prepares the hooks.
	 */
	public function define_hooks() {
		// Get the loader class
		$loader = EDD_Booking::get_instance()->get_loader();
		// Determine which action hook to use (if in admin or not)
		$style_hook = is_admin()? 'admin_enqueue_scripts' : 'wp_enqueue_scripts';
		// Add the actions to the loader
		$loader->add_action( $style_hook, $this, 'enqueue_styles', 10 );
		$loader->add_action( $style_hook, $this, 'enqueue_scripts', 10 );
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
	 * Generates an array of time strings for the given availability range (row).
	 * 
	 * @param  int $from The starting time
	 * @param  int $to   The ending time
	 * @param  int $step The interval
	 * @return array
	 */
	private static function generate_times_for_range( $from, $to, $step ) {
		$times = array();
		// Begin iterating times
		for ( $i = $from; $i < $to; $i += $step ) {
			$times[] = sprintf( '%02d:%02d', intval( $i / 60 ), $i % 60 );
		}
		return $times;
	}

	private static function time_str_to_minutes( $time ) {
		$parts = explode( ':', $time );
		return ( intval( $parts[0] ) * 60 ) + intval( $parts[1] );
	}

	/**
	 * [testing_new_function_generate_times description]
	 * @param  [type] $post_id  [description]
	 * @param  [type] $date_str [description]
	 * @return [type]           [description]
	 */
	public static function get_times_for_date( $post_id, $date_str ) {
		// Get the booking with this ID
		$booking = EDD_BK_Booking::from_id( $post_id );
		// Check if the session unit allows time picking
		if ( ! $booking->isSessionUnit( 'hours', 'minutes' ) ) return array();
		
		// Calculate the session length in seconds (for timestamp operations)
		$session_length = $booking->getSessionLength();
		// Session unit is either hour or minutes (see 4 lines up). Multiply by
		// 60 for both cases (mins or hours), then check if the unit is hours
		// and multiply again if so.
		$session_length_seconds = $session_length * 60;
		if ( $booking->isSessionUnit( 'hours' ) ) $session_length_seconds *= 60;
		// Minimum session length in seconds.
		$min_session_length_seconds = $session_length_seconds * $booking->getMinSessions();
		// Maximum session length in seconds.
		$max_session_length_seconds = $session_length_seconds * $booking->getMaxSessions();

		// Parse the date
		$date_parts = explode( '/', $date_str );
		$timestamp = strtotime( $date_parts[2] . '-' . $date_parts[0] . '-' . $date_parts[1] );

		$master_list = array();
		foreach ( $booking->getAvailability()->getEntries() as $i => $entry ) {
			//if ( ! $entry->matches( $timestamp ) ) continue;
			$type = strtolower( $entry->getType()->getGroup() );
			if ( stripos( $type, 'day' ) !== FALSE) {
				$times = array();
				$start = $entry->getFrom();
				$end = $entry->getTo();
				$curr = $start;
				while ( $curr < $end && ($curr + $min_session_length_seconds) <= $end ) {
					$max_seconds = min( $end, $curr + $max_session_length_seconds );
					$max_num_sessions = ( $max_seconds - $curr ) / $session_length_seconds;
					array_push( $times, $curr . "|". $max_num_sessions);
					$curr += $session_length_seconds;
				}
				if ( count( $times ) > 0 ) {
					if ( $entry->isAvailable() ) {
						$master_list = array_unique( array_merge( $master_list, $times ) );
					} else {
						$master_list = array_diff( $master_list, $times );
					}
				}
			}
		}

		return $master_list;
	}

	/**
	 * @todo Add flattening with times from the availability builder table.
	 * 
	 * @param  [type] $post_id [description]
	 * @param  [type] $date    [description]
	 * @return [type]          [description]
	 */
	public static function old_get_times_for_date( $post_id, $date_str ) {
		$meta = self::meta_fields( $post_id );
		$slot_duration_unit = $meta['slot_duration_unit'];

		if ( $slot_duration_unit !== 'hours' && $slot_duration_unit !== 'minutes' ) {
			return array();
		}
		
		$slot_duration = $meta['slot_duration'];
		$availability_fill = $meta['availability_fill'];
		$availability = $meta['availability'];

		// Prepare the times
		$start = 0; // 00:00
		$end = self::time_str_to_minutes( '23:59' );
		$session = floatval( $slot_duration ) * ( $slot_duration_unit === 'hours' ? 60 : 1 );

		// Prepare the date
		$date_parts = explode( '/', $date_str );
		$new_date_str = $date_parts[2] . '-' . $date_parts[0] . '-' . $date_parts[1];
		$timestamp = strtotime( $new_date_str );
		
		// Prepare the default times
		// If the fill option is set to true, then fill the times array with all times in the day
		// from midnight 00:00 till exactly 23:59
		// Otherwise, no times are available by default - use an empty array.
		if ( strtolower( $availability_fill ) === 'true' ) {
			$times = self::generate_times_for_range( $start, $end, $session );
		} else {
			$times = array();
		}

		// Check the availabilities
		foreach ( $availability as $av ) {
			$av_times = array();
			$match = FALSE;

			switch ( $av['type'] ) {
				// ALL WEEK
				case 'all_week':
					$match = TRUE;
					break;
				// WEEKDAYS
				case 'weekdays':
					$wd = date( 'w', $timestamp );
					$match = ( $wd != '0' && $wd != '6' );
					break;
				// WEEKEND
				case 'weekend':
					$wd = date( 'w', $timestamp );
					$match = ( $wd == '0' || $wd == '6' );
					break;
				// Others: SPECIFIC WEEK DAY
				default:
					$wd = strtolower( date( 'l', $timestamp ) );
					$match = ( $av['type'] == $wd );
					break;
			}
			
			// If the date matches the availability entry's criteria,
			// calculate the times available for the date
			if ( $match ) {
				// Generate minutes for the 'from' and 'to' fields
				$from = self::time_str_to_minutes( $av['from'] );
				$to = self::time_str_to_minutes( $av['to'] );
				// Generate the times
				$av_times = self::generate_times_for_range( $from, $to, $session );
				// Add or remove the availability times, depending if the availability entry
				// is marked as available or not.
				if ( strtolower( $av['available'] ) === 'true' ) {
					$times = array_unique( array_merge( $times, $av_times ) );
				} else {
					$times = array_diff( $times, $av_times );
				}
			}

		} // End of availabilities iterationz

		return $times;
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