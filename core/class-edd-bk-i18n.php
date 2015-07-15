<?php

/**
 * This file contains code that handles text localization used throughout the plugin.
 */

/**
 * This class is responsible for setting/loading the text domain for text localization.
 */
class EDD_BK_i18n {
	
	/**
	 * The plugin text domain in use.
	 */
	private $domain;
	
	/**
	 * Loads the plugin text domain.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			$this->domain,
			false,
			EDD_BK_LANG_DIR
		);
	}
	
	/**
	 * Sets the plugin text domain.
	 */
	public function set_domain( $_domain ) {
		$this->domain = $_domain;
	}
	
}