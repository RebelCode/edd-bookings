<?php

class EDD_BK_Commons {
	
	public function __construct() {
		$this->prepare_directories();
		$this->define_hooks();
	}

	public function prepare_directories() {
		if ( !defined( 'EDD_BK_COMMONS_JS_URL' ) ) {
			define( 'EDD_BK_COMMONS_JS_URL',	EDD_BK_COMMONS_URL . 'js/' );
		}
		if ( !defined( 'EDD_BK_COMMONS_CSS_URL' ) ) {
			define( 'EDD_BK_COMMONS_CSS_URL',	EDD_BK_COMMONS_URL . 'css/' );
		}
	}

	public function enqueue_styles() {
		$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		wp_enqueue_style( 'edd-bk-admin-fa', EDD_BK_COMMONS_CSS_URL . 'font-awesome.min.css' );
		wp_enqueue_style( 'edd-bk-jquery-ui-theme', EDD_BK_COMMONS_CSS_URL . 'jquery-ui'.$suffix.'.css' );
	}

	public function enqueue_scripts() {
		// Load commons scripts
	}

	public function define_hooks() {
		$loader = EDD_Booking::get_instance()->get_loader();

		$style_hook = is_admin()? 'admin_enqueue_scripts' : 'wp_enqueue_scripts';

		$loader->add_action( $style_hook, $this, 'enqueue_styles' );
		$loader->add_action( $style_hook, $this, 'enqueue_scripts' );
	}

	/**
	 * [meta_fields description]
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
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
		if ( $post_id === null ) {
			return $fields;
		}
		$meta = array();
		foreach ( $fields as $i => $field ) {
			$meta[ $field ] = get_post_meta( $post_id, 'edd_bk_'.$field, TRUE );
		}
		return $meta;
	}


	private static function generate_times_for_range( $from, $to, $step ) {
		$times = array();
		// Begin iterating times
		for( $i = $from; $i < $to; $i += $step ) {
			$times[] = sprintf( '%02d:%02d', intval( $i / 60 ), $i % 60 );
		}
		return $times;
	}

	private static function time_str_to_minutes( $time ) {
		$parts = explode( ':', $time );
		return ( intval( $parts[0] ) * 60 ) + intval( $parts[1] );
	}

	/**
	 * @todo Add flattening with times from the availability builder table.
	 * 
	 * @param  [type] $post_id [description]
	 * @param  [type] $date    [description]
	 * @return [type]          [description]
	 */
	public static function get_times_for_date( $post_id, $date_str ) {
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
				case 'allweek':
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