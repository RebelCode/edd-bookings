<?php

require dirname(__FILE__) . DS . 'Varien' . DS . 'Object.php';

/**
 * Padding class for Varien_Object.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Aventura\Bookings
 */
class Aventura_Bookings_Object extends Varien_Object {
	
	public function setDataUsingMethod($arg0, $args = array()) {
		if ( ! is_array($arg0) ) {
			return parent::setDataUsingMethod($arg0, $args);
		} else {
			foreach ($arg0 as $key => $value) {
				parent::setDataUsingMethod($key, $value);
			}
		}
		return $this;
	}

}
