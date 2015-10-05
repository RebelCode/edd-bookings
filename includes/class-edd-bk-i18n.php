<?php

/**
 * This class is responsible for setting/loading the text domain for text localization.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Bookings
 */
class EDD_BK_i18n {
	
	/**
	 * The plugin text domain in use.
	 */
	private $_domain;
	
	/**
	 * Loads the plugin text domain.
	 */
	public function loadPluginTextdomain() {
		load_plugin_textdomain(
			$this->_domain,
			false,
			EDD_BK_LANG_DIR
		);
	}
	
	/**
	 * Sets the plugin text domain.
	 */
	public function setDomain( $domain ) {
		$this->_domain = $domain;
	}
	
}