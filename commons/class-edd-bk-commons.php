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

}