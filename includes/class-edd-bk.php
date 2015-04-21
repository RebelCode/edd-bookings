<?php

/**
* The main EDD Booking plugin class.
*/
class EDD_Booking {
	
	/**
	 * The loader class instance.
	 */
	private $loader;

	/**
	 * The admin class instance.
	 * @var EDD_BK_Admin
	 */
	private $admin;

	/**
	 * The plugin class instance.
	 * @var EDD_BK_Public
	 */
	private $public;

	/**
	 * The plugin commons instance
	 * @var EDD_BK_Commons
	 */
	private $commons;

	/**
	 * The plugin name. Used for identification.
	 */
	private $plugin_name;
	
	/**
	 * The plugin version.
	 */
	private $version;

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
		if ( self::$instance !== null ) {
			throw new Exception( 'EDD_Booking class cannot be re-instansiated!' );
		} else {
			self::$instance = $this;
		}
		
		$this->plugin_name = 'edd-booking';
		$this->version = '1.0.0';
		
		$this->load_dependancies();
		$this->set_locale();

		// Initialize the commons class instance
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
	 * Alias for the get_instance() method.
	 * 
	 * @return EDD_Booking
	 */
	public static function instance() {
		return self::get_instance();
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
	 * Loads all files required by the plugin.
	 */
	private function load_dependancies() {
		// The loader class - responsible for all action and filter hooks
		require_once EDD_BK_INC_DIR . 'class-edd-bk-loader.php';
		// Load the i18n file
		require_once EDD_BK_INC_DIR . 'class-edd-bk-i18n.php';
		// Load the admin class file
		require_once EDD_BK_COMMONS_DIR . 'class-edd-bk-commons.php';
		// Load the admin class file
		require_once EDD_BK_ADMIN_DIR . 'class-edd-bk-admin.php';
		// Load the public class file
		require_once EDD_BK_PUBLIC_DIR . 'class-edd-bk-public.php';
		// Load the utility functions file
		require_once EDD_BK_COMMONS_DIR . 'class-edd-bk-utils.php';

		$this->loader = new EDD_BK_Loader();
	}
	
	/**
	 * Sets the current locale and loads the plugin text domain.
	 */
	private function set_locale() {
		$edd_bk_i18n = new EDD_BK_i18n();
		$edd_bk_i18n->set_domain( $this->get_plugin_name() );
		$this->loader->add_action( 'plugins_loaded', $edd_bk_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Triggers the loader, which attaches all registered hooks to WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Returns the plugin name
	 *
	 * @return string
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}
	
	/**
	 * Returns the plugin version
	 *
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Returns the loader instance.
	 *
	 * @return EDD_BK_Loader
	 */
	public function get_loader() {
		return $this->loader;
	}

}