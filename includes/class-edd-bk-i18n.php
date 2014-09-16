<?php

/**
 * @todo file doc here
 */

/**
 * @todo class doc here
 */
class EDD_BK_i18n {
	
	/**
	 * @todo var doc here
	 */
	private $domain;
	
	/**
	 * @todo function doc here
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			$this->domain,
			false,
			EDD_BK_LANG_DIR
		);
	}
	
	/**
	 * @todo function doc here
	 */
	public function set_domain( $_domain ) {
		$this->domain = $_domain;
	}
	
}