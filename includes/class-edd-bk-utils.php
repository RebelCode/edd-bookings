<?php

/**
 * Utility functions class.
 *
 * @since 1.0
 * @version 1.0.0
 * @package EDD_Bookings
 */
class EDD_BK_Utils {

	/**
	 * Compares the given variable for the various possible 'true' values.
	 * 
	 * @param  mixed $b The value to check.
	 * @return  bool    True if the given value matches a 'true' value. False otherwise.
	 */
	public static function multiboolean( $b ) {
		$sb = strtolower( $b );
		return $b === true || $sb === 'true' || $b === 1 || $sb === '1' || $sb === 'yes' || $sb === 'on';
	}

	/**
	 * Includes a file and buffers the contents, returning the
	 * rendered contents of the file.
	 * 
	 * @return string
	 */
	public static function ob_include( $file, $vars = array() ) {
		extract( $vars );
		ob_start();
		include $file;
		return ob_get_clean();
	}

	/**
	 * Renders a view file from the view directory.
	 * 
	 * @param  string $view    View name: the file name without path, 'view-' prefix and extension.
	 * @param  array  $viewbag Array of vars to pass to the view. Will be cast into an object for use in the view. Objects also accepted. Default: array
	 * @return string          The rendered view as a string.
	 */
	public static function render_view( $view, $viewbag = array() ) {
		$viewbag = (object) $viewbag;
		ob_start();
		include sprintf('%sview-%s.php', EDD_BK_VIEWS_DIR, $view );
		return ob_get_clean();
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

	/**
	 * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
	 * keys to arrays rather than overwriting the value in the first array with the duplicate
	 * value in the second array, as array_merge does. I.e., with array_merge_recursive,
	 * this happens (documented behavior):
	 *
	 * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
	 *     => array('key' => array('org value', 'new value'));
	 *
	 * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
	 * Matching keys' values in the second array overwrite those in the first array, as is the
	 * case with array_merge, i.e.:
	 *
	 * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
	 *     => array('key' => array('new value'));
	 *
	 * Parameters are passed by reference, though only for performance reasons. They're not
	 * altered by this function.
	 *
	 * @param array $array1
	 * @param array $array2
	 * @return array
	 * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
	 * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
	 */
	public static function array_merge_recursive_distinct( array &$array1, array &$array2 ) {
		$merged = $array1;
		foreach( $array2 as $key => &$value ) {
			if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) ) {
				$merged [$key] = self::array_merge_recursive_distinct ( $merged [$key], $value );
			} else {
				$merged [$key] = $value;
			}
		}
		return $merged;
	}

}
