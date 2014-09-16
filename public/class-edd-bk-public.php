<?php

/**
 * @todo		file doc
 * @since		1.0.0
 * @package		EDD_BK
 * @subpackage	EDD_BK/admin
 */

/**
 * @todo class doc
 */
class EDD_BK_Public {

	/**
	 * [$name description]
	 * @var [type]
	 */
	private $name;

	/**
	 * [$version description]
	 * @var [type]
	 */
	private $version;

	/**
	 * [__construct description]
	 * @param [type] $_name    [description]
	 * @param [type] $_version [description]
	 */
	public function __construct( $_name, $_version ) {
		$this->name = $_name;
		$this->version = $_version;
	}

	/**
	 * @todo func doc
	 * @return [type] [description]
	 */
	public function enqueue_styles() {
		// styles
	}

	/**
	 * @todo func doc
	 * @return [type] [description]
	 */
	public function enqueue_scripts() {
		// scripts
	}

}