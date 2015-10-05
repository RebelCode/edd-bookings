<?php

/**
 * General Exception class for the plugin.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Bookings\Exceptions
 */
class EDD_BK_Exception extends Exception {
	
	/**
	 * Constructor.
	 * 
	 * @param string $msg (Optional) Error message.
	 */
	public function __construct( $msg = "An error has occurred." ) {
		parent::__construct(
			sprintf(
				__( 'EDD Bookings Plugin: %s', EDD_Bookings::TEXT_DOMAIN ),
				$msg
			),
			1 // exception code
		);
	}

	/**
	 * Turns the exception into a WordPress Death Screen, showing the exception message, a friendly message to the user
	 * and the stack trace for the exception.
	 *
	 * This function does nothing when triggered from the site's public side.
	 */
	public function to_wp_die() {
		if (!is_admin()) return;
		$msg = __( 'This error can be caused by a bug in the plugin, other conflicting plugins or malconfiguration of the plugin. If you think that this is a bug, kindly report it to us.', EDD_Bookings::TEXT_DOMAIN );
		wp_die(
			$this->getMessage().'<br/>'.
			'<p>' . $msg . '</p>'.
			'<pre>' .
				__( 'Stack Trace:', EDD_Bookings::TEXT_DOMAIN ) . '<br/>' .
				$this->getTraceAsString() .
			'</pre>'
		);
	}

}
