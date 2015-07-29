<?php

/**
 * Enum style class for session types.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Aventura\Bookings\Service\Session
 */
class Aventura_Bookings_Service_Session_Type {
	
	const FIXED		= 'fixed';
	const VARIABLE	= 'variable';

	/**
	 * Returns the entire set of enum constants.
	 * 
	 * @return array An array containing the enum names as array keys and their values as array values.
	 */
	public static function getAll() {
		$refClass = new ReflectionClass(__CLASS__);
		return $refClass->getConstants();
	}

	/**
	 * Checks if the given string is a valid type.
	 * 
	 * @param  string  $string The string to check.
	 * @return boolean         True if a type value matches the given string, False otherwise.
	 */
	public static function isValid( $string ) {
		$key = strtolower($string);
		$constants = array_flip( self::getAll() );
		return isset( $constants[$key] );
	}

}
