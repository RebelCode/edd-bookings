<?php

/**
 * General Exception class for the plugin.
 */
class EDD_BK_Exception extends Exception {
	
	/**
	 * Constructor.
	 * @param string $msg (Optional) Error message.
	 */
	public function __construct( $msg = "An error has occurred." ) {
		$msg = 'EDD Booking Plugin: ' . $msg;
		parent::__construct( $msg, 1 );
	}

	public function to_wp_die() {
		if (!is_admin()) return;
		wp_die(
			$this->getMessage().'<br/>'.
			'<p>This error can be caused by a bug in the plugin, other conflicting plugins or malconfiguration of the plugin.
			If you think that this is a bug, kindly report it to us at <code>[email address]</code>.</p>'.
			'<pre>Stack Trace:<br/>' . $this->getTraceAsString() . '</pre>'
		);
	}

}
