<?php

class EDD_BK_Commons {
	
	public function __construct() {
		$this->prepare_directories();
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


	/**
	 * @todo Add flattening with times from the availability builder table.
	 * 
	 * @param  [type] $post_id [description]
	 * @param  [type] $date    [description]
	 * @return [type]          [description]
	 */
	public static function get_times_for_date( $post_id, $date ) {
		extract( self::meta_fields( $post_id ) );
		$start = 0;
		$end = 1439;
		$session = intval( $slot_duration );
		if ( $slot_duration_unit === 'hours' ) {
			$session *= 60;
		}
		for( $i = $start; $i < $end; $i += $session ) {
			$times[] = sprintf( '%02d:%02d', intval( $i / 60 ), $i % 60 );
		}
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