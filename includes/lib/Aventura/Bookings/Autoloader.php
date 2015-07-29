<?php

/**
 * The class autoloader.
 *
 * @version 1.0.0
 * @since 1.0.0
 * @package Aventura\Bookings
 */
class Aventura_Bookings_Autoloader {

	/**
	 * Constant for vendor name.
	 */
	const VENDOR = 'Aventura';

	/**
	 * Constant for package name.
	 */
	const PACKAGE = 'Bookings';

	/**
	 * The file extension used when search for class files.
	 */
	const FILE_EXT = '.php';

	/**
	 * The singleton instance.
	 * 
	 * @var Aventura_Bookings_Autoloader
	 */
	protected static $instance = NULL;

	/**
	 * The included directories where the autoloader will search for class files.
	 * 
	 * @var array
	 */
	protected $directories;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->directories = array();
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Returns the singleton instance.
	 * 
	 * @return Aventura_Bookings_Autoloader
	 */
	public static function getInstance() {
		if ( self::$instance === NULL ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Registers a directory to the autoloader.
	 * 
	 * @since 	1.0.0
	 * @param 	string		$directory		The path of the directory to be registered to the autoloader. Recommended to use absolute paths.
	 * @return 	bool        	      		True if the directory was registered, false if path does not refer to an existing directory.
	 */
	public function registerDirectory( $directory ) {
		if ( ! is_dir( $directory ) ) return false;
		$this->directories[] = rtrim($directory, DS) . DS;
		return true;
	}

	/**
	 * The autoload callback, called when a class is to be autoloaded.
	 * 
	 * @param  string 	$name 	The name of the class to be loaded.
	 * @return bool             True on successfuly loading, false on failure. Failure results from the class file not being found. 
	 */
	public function autoload( $fullclassname ) {
		// Split by underscore
		$components = explode( '_', $fullclassname );
		// If we didn't get at least three parts, stop
		if ( count( $components ) < 3 || $components[0] !== self::VENDOR || $components[1] !== self::PACKAGE ) return FALSE;

		$filepath = implode(DS, array_slice($components, 2)) . self::FILE_EXT;

		// Look for the file inside the registered directories
		foreach ( $this->directories as $dir ) {
			// Generate the full path to the file
			$full_path = $dir . $filepath;
			// If the file doesn't exist in this directory, continue to next iteration
			if ( ! file_exists( $full_path ) || is_dir( $full_path ) ) continue;
			// Load the file
			require_once $full_path;
			// Return true to indicate success
			return TRUE;
		}
		// Return false on failure.
		return FALSE;
	}

}
