<?php

/**
 * Represents a customer.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package Aventura\Bookings
 */
class Aventura_Bookings_Customer extends Aventura_Bookings_Object {

	/**
	 * The default values for the fields of this class, used by the constructor.
	 * @var array
	 */
	protected static $_defaultValues = array(
		'id'		=>	NULL,
		'name'		=>	NULL,
		'email'		=>	NULL
	);

	/**
	 * Constructor.
	 * 
	 * @param string|int|array $arg The customer's ID, or an array containing the customer properties.
	 */
	public function __construct( $arg = NULL ) {
		// If the argument is not an array, treat is as the ID
		if ( ! is_array( $arg ) ) {
			$arg = array( 'id' => $arg );
		}
		// Merge with defaults
		$data = array_merge(self::$_defaultValues, $arg);
		// Set the data
		$this->setDataUsingMethod($data);
	}

}
