<?php

class EDD_BK_AJAX_Handler {

	protected $handlers;

	public function __construct() {
		$this->handlers = array();
	}

	public function actionExists( $action ) {
		return isset( $this->handlers[ $action ] );
	}

	public function addHandler( $action, $context, $callback = NULL, $nopriv = FALSE, $priority = 10 ) {
		if ( ! $this->actionExists( $action ) ) {
			$this->handlers[ $action ] = array();
		}
		$this->handlers[ $action ][] = compact( 'context', 'callback', 'nopriv', 'priority' );
	}

	public function removeHandler( $action, $context, $callback ) {
		if ( ! $this->actionExists( $action ) ) return FALSE;
		foreach ( $this->handlers[ $action ] as $i => $handler ) {
			if ( $context === $handler['context'] && $callback === $handler['callback'] ) {
				unset( $this->handlers[ $action ][ $i ] );
				return TRUE;
			}
		}
		return FALSE;
	}

}

