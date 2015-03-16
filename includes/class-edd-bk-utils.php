<?php

/**
 * Utility functions class.
 *
 * @since 1.0
 */
class EDD_BK_Utils {

	/**
	 * Returns the day options in an associative array.
	 */
	public static function day_options() {
		return array(
			'monday'	=>	'Monday',
			'tuesday'	=>	'Tuesday',
			'wednesday'	=>	'Wednesday',
			'thursday'	=>	'Thursday',
			'friday'	=>	'Friday',
			'saturday'	=>	'Saturday',
			'sunday'	=>	'Sunday',
		);
	}

	/**
	 * Returns the month options in an associative array.
	 */
	public static function month_options() {
		return array(
			'january'	=> 'January',
			'february'	=> 'February',
			'march'		=> 'March',
			'april'		=> 'April',
			'may'		=> 'May',
			'june'		=> 'June',
			'july'		=> 'July',
			'august'	=> 'August',
			'september'	=> 'September',
			'october'	=> 'October',
			'november'	=> 'November',
			'december'	=> 'December',
		);
	}

	/**
	 * Returns an array of radio elements for the given associative array.
	 * Array _must_ be associative.
	 * 
	 * @since 1.0
	 */
	public static function array_to_radio_buttons( $array, $pArgs = array() ) {
		// Merge the passed parameter arguments with the defaults
		$defaults = array(
			'id'					=>	'',
			'class' 				=> 	NULL,
			'name'					=>	NULL,
			'checked'				=>	NULL
		);
		$args = wp_parse_args( $pArgs, $defaults );

		// Prepare the variables
		$class = ( $args['class'] === NULL )? '' : ' class="'.$args['class'].'"';
		$name = ( $args['name'] === NULL )? '' : ' name="'.$args['name'].'"';

		$radios = array();
		$i = 0;
		foreach( $array as $key => $value ) {
			$id = $args['id'] . '-' . $i++;
			$checked = ( $args['checked'] !== NULL && $args['checked'] === $key )? 'checked="checked"': '';
			$radios[] = "<input type='radio' value='$key' id='$id' $name $class $checked /><label for='$id'>$value</label> ";
		}

		return $radios;
	}


	/**
	 * Returns a select element for the given associative array.
	 * Array _must_ be associative.
	 *
	 * @since 1.0
	 */
	public static function array_to_select( $array, $pArgs = array() ) {
		// Merge the passed parameter arguments with the defaults
		$defaults = array(
			'id'					=>	NULL,
			'class' 				=> 	NULL,
			'name'					=>	NULL,
			'selected'				=>	NULL,
			'options_only'			=>	FALSE,
			'add_default_option'	=>	FALSE,
			'multiple'				=>	FALSE,
			'disabled'				=>	FALSE,
		);
		$args = wp_parse_args( $pArgs, $defaults );

		// Prepare the variables
		$id = ( $args['id'] === NULL )? '' : ' id="'.$args['id'].'"';
		$class = ( $args['class'] === NULL )? '' : ' class="'.$args['class'].'"';
		$name = ( $args['name'] === NULL )? '' : ' name="'.$args['name'].'"';
		$disabled = ( $args['disabled'] === FALSE )? '' : 'disabled="disabled"';
		// Check multiple tag
		$multiple = '';
		if ( $args['multiple'] === TRUE ) {
			$multiple = ' multiple="multiple"';
			// If using a multiple tag, set the name to an array to accept multiple values
			if ( $args['name'] !== NULL ) {
				$name = ' name="'.$args['name'].'[]"';
			}
		}
		// WP MP6 responsiveness fix - set height to auto
		$fix = ( $args['multiple'] === TRUE )? 'style="height:auto;"' : '';
		
		$select = '';
		// Generate the select elements
		if ( $args['options_only'] !== TRUE )
			$select = "<select $id $class $name $fix $multiple $disabled>";
		if ( $args['add_default_option'] === TRUE ){
			$array = array_merge( array( '' => 'Use Default' ), $array );
		}
		
		if ( !is_array( $array ) ) $array = array();
		
		foreach ( $array as $key => $value ) {
			if ( is_array($value) ) {
				$select .= "<optgroup label='$key'>";
				$recursionArgs = $pArgs;
				$recursionArgs['options_only'] = TRUE;
				$select .= self::array_to_select( $value, $recursionArgs );
				$select .= "</optgroup>";
				continue;
			}
			$selected = FALSE;
			if ( is_array( $args['selected'] ) ) {
				$selected = in_array( $key, $args['selected'] );
			}
			else $selected = ( $args['selected'] !== NULL && $args['selected'] == $key );
			$selected = ( $selected == TRUE )? 'selected="selected"': '';

			$select .= "<option value='$key' $selected>$value</option>";
		}
		if ( $args['options_only'] !== TRUE )
			$select .= "</select>";

		// Return the generated select element.
		return $select;
	}
}


function str_sing_plur( $num, $str ) {
	$is_plural = substr( strtolower( $str ), -1 ) === 's';
	$singular = $is_plural ? substr( $str, 0, - 1 ) : $str;
	$plural = $is_plural ? $str : $str . 's';
	return floatval( $num ) > 1 ? $plural : $singular;
}

function str_sing( $str ) {
	return substr( strtolower( $str ), -1 ) === 's' ? substr( $str, 0, -1 ) : $string;
}

function str_plur( $str ) {
	return substr( strtolower( $str ), -1 ) === 's' ? $str : substr( $str, 0, -1 );
}
