<?php

/**
* The main EDD Booking plugin class.
*/
class EDD_Booking {
	
	/**
	 * The loader class instance.
	 *
	 * @var EDD_BK_Loader
	 */
	private $loader;

	/**
	 * The admin class instance.
	 * 
	 * @var EDD_BK_Admin
	 */
	private $admin;

	/**
	 * The public class instance.
	 * 
	 * @var EDD_BK_Public
	 */
	private $public;

	/**
	 * The commons class instance.
	 * 
	 * @var EDD_BK_Commons
	 */
	private $commons;	

	/**
	 * The singleton instance of the class.
	 * @var EDD_Booking
	 */
	private static $instance = null;
	
	/**
	 * Instance constructor.
	 * 
	 * @throws Exception If the singleton instance is already instansiated.
	 */
	public function __construct() {
		// Singleton Instance Handling
		if ( self::$instance !== null )
			throw new EDD_BK_Singleton_Reinstantiaion_Exception();
		else self::$instance = $this;

		// Load required files
		$this->load_dependancies();
		// Set the plugin locale
		$this->set_locale();
		// Init the modules
		$this->init_modules();
	}

	/**
	 * Returns the admin class instance.
	 * 
	 * @return EDD_BK_Admin
	 */
	public function get_admin() {
		return $this->admin;
	}

	/**
	 * Returns the public class instance.
	 * 
	 * @return EDD_BK_Public
	 */
	public function get_public() {
		return $this->public;
	}

	/**
	 * Returns the commons class instance.
	 * 
	 * @return EDD_BK_Commons
	 */
	public function get_commons() {
		return $this->commons;
	}

	/**
	 * Loads all files required by the plugin.
	 */
	private function load_dependancies() {
		// The loader class - responsible for all action and filter hooks
		require_once EDD_BK_CORE_DIR . 'class-edd-bk-loader.php';
		// Load the i18n file
		require_once EDD_BK_CORE_DIR . 'class-edd-bk-i18n.php';
		// Load the admin class file
		require_once EDD_BK_ADMIN_DIR . 'class-edd-bk-admin.php';
		// Load the public class file
		require_once EDD_BK_PUBLIC_DIR . 'class-edd-bk-public.php';
		// Load the utility functions file
		require_once EDD_BK_UTILS_DIR . 'class-edd-bk-utils.php';
		// Load the commons file
		require_once EDD_BK_CORE_DIR . 'class-edd-bk-commons.php';

		// Initialize the loader
		$this->loader = new EDD_BK_Loader();
	}
	
	/**
	 * Sets the current locale and loads the plugin text domain.
	 */
	private function set_locale() {
		$edd_bk_i18n = new EDD_BK_i18n();
		$edd_bk_i18n->set_domain( self::plugin_name() );
		$this->loader->add_action( 'plugins_loaded', $edd_bk_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Initializes the three modules: commons, admin and public.
	 */
	private function init_modules() {
		// Initialize the commons class
		$this->commons = new EDD_BK_Commons();
		// Initialize the admin class instance, if requested a WP admin page
		if ( is_admin() ) {
			$this->admin = new EDD_BK_Admin();
		}
		// Initialize the public class instance, if not requesed a WP admin page or if an AJAX request
		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			$this->public = new EDD_BK_Public();
		}
	}

	/**
	 * Triggers the loader, which attaches all registered hooks to WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Returns the loader instance.
	 *
	 * @return EDD_BK_Loader
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Alias for the get_instance() method.
	 *
	 * @see EDD_Booking::get_instance()
	 * @uses EDD_Booking::get_instance()
	 * @return EDD_Booking
	 */
	public static function instance() {
		return self::get_instance();
	}

	/**
	 * Returns the singleton instance, instansiating it if not yet initialized.
	 * 
	 * @return EDD_Booking
	 */
	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new EDD_Booking();
		}
		return self::$instance;
	}

	/**
	 * Returns the plugin name
	 *
	 * @return string
	 */
	public static function plugin_name() {
		return EDD_BK_PLUGIN_NAME;
	}
	
	/**
	 * Returns the plugin version
	 *
	 * @return string
	 */
	public static function version() {
		return EDD_BK_PLUGIN_VERSION;
	}

}
