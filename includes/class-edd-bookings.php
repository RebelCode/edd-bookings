<?php

/**
* The main EDD Booking plugin class.
*
* @since 1.0.0
* @version 1.0.0
* @package EDD_Bookings
*/
class EDD_Bookings {
	
	/**
	 * The text domain for i18n.
	 */
	const TEXT_DOMAIN = 'eddbk';

	/**
	 * The loader class instance.
	 *
	 * @var EDD_BK_Loader
	 */
	protected $loader;

	/**
	 * The internationalization class instance.
	 * 
	 * @var EDD_BK_i18n
	 */
	protected $i18n;
	/**
	 * The admin class instance.
	 * 
	 * @var EDD_BK_Admin
	 */
	protected $admin;

	/**
	 * The public class instance.
	 * 
	 * @var EDD_BK_Public
	 */
	protected $public;

	/**
	 * The booking cpt class instance.
	 * 
	 * @var EDD_BK_Booking_CPT
	 */
	protected $booking_cpt;	

	/**
	 * The downloads controller instance.
	 * 
	 * @var EDD_BK_Downloads_Controller
	 */
	protected $downloads_controller;

	/**
	 * The bookings controller instance.
	 *
	 * @var EDD_BK_Bookings_Controller
	 */
	protected $bookings_controller;

	/**
	 * The EDD license handler.
	 * 
	 * @var EDD_License
	 */
	protected $licenseHandler;

	/**
	 * The singleton instance of the class.
	 * 
	 * @var EDD_Booking
	 */
	protected static $instance = null;
	
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
		// Define hooks
		$this->define_hooks();

		// Load the EDD license handler and create the license handler instance
		if ( class_exists( 'EDD_License' ) )
			$this->license = new EDD_License( EDD_BK, EDD_BK_PLUGIN_NAME, EDD_BK_VERSION, 'Jean Galea' );

		// Init the Booking CPT
		$this->booking_cpt = new EDD_BK_Booking_CPT();
		// Init controllers
		$this->downloads_controller = new EDD_BK_Downloads_Controller();
		$this->bookings_controller = new EDD_BK_Bookings_Controller();

		// Initialize the admin class instance, if requested a WP admin page
		if ( is_admin() )
			$this->admin = new EDD_BK_Admin();
		// Initialize the public class instance, if not requesed a WP admin page or if an AJAX request
		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) )
			$this->public = new EDD_BK_Public();
	}

	/**
	 * Alias for the get_instance() method.
	 *
	 * @see EDD_Booking::get_instance()
	 * @uses EDD_Booking::get_instance()
	 * @return EDD_Bookings
	 */
	public static function instance() {
		return self::get_instance();
	}

	/**
	 * Returns the singleton instance, instansiating it if not yet initialized.
	 * 
	 * @return EDD_Bookings
	 */
	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
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
	 * Returns the loader instance.
	 *
	 * @return EDD_BK_Loader
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Gets the downloads controller.
	 * 
	 * @return EDD_BK_Downloads_Controller
	 */
	public function get_downloads_controller() {
		return $this->downloads_controller;
	}

	/**
	 * Gets the bookings controller.
	 * 
	 * @return EDD_BK_Bookings_Controller
	 */
	public function get_bookings_controller() {
		return $this->bookings_controller;
	}

	/**
	 * Loads all files required by the plugin.
	 */
	private function load_dependancies() {
		// The loader class - responsible for all action and filter hooks
		require_once EDD_BK_INCLUDES_DIR . 'class-edd-bk-loader.php';
		// Load the i18n file
		require_once EDD_BK_INCLUDES_DIR . 'class-edd-bk-i18n.php';
		// Load the utility functions file
		require_once EDD_BK_INCLUDES_DIR . 'class-edd-bk-utils.php';
		// Load the CPT helper class file
		require_once EDD_BK_WP_HELPERS_DIR . 'class-edd-bk-cpt.php';

		// Load the admin class file
		require_once EDD_BK_ADMIN_DIR . 'class-edd-bk-admin.php';
		// Load the public class file
		require_once EDD_BK_PUBLIC_DIR . 'class-edd-bk-public.php';

		// Load classes related to downloads
		require_once EDD_BK_DOWNLOADS_DIR . 'class-edd-bk-download.php';
		require_once EDD_BK_DOWNLOADS_DIR . 'class-edd-bk-downloads-controller.php';
		require_once EDD_BK_DOWNLOADS_DIR . 'class-edd-bk-session-unit.php';
		// Load classes related to bookings
		require_once EDD_BK_BOOKINGS_DIR . 'class-edd-bk-booking.php';
		require_once EDD_BK_BOOKINGS_DIR . 'class-edd-bk-booking-cpt.php';
		require_once EDD_BK_BOOKINGS_DIR . 'class-edd-bk-bookings-controller.php';
		// Load classes related to customers
		require_once EDD_BK_CUSTOMERS_DIR . 'class-edd-bk-customer.php';
		require_once EDD_BK_CUSTOMERS_DIR . 'class-edd-bk-customers-controller.php';

		// Initialize the loader
		$this->loader = new EDD_BK_Loader();
	}

	/**
	 * Registers hooks to the loader.
	 */
	private function define_hooks() {
		$hook = ( is_admin()? 'admin' : 'wp' ) . '_enqueue_scripts';
		// Script and style enqueuing hooks
		$this->loader->add_action( $hook, $this, 'enqueue_styles' );
		$this->loader->add_action( $hook, $this, 'enqueue_scripts' );
	}

	/**
	 * Enqueues or registers plugin-wide stylesheets.
	 */
	public function enqueue_styles() {
		// Font Awesome
		wp_enqueue_style( 'edd-bk-font-awesome-css', EDD_BK_CSS_URL . 'font-awesome.min.css' );
	}

	/**
	 * Enqueues or registers plugin-wide scripts.
	 */
	public function enqueue_scripts() {
		// Register lodash
		wp_register_script( 'edd-bk-utils', EDD_BK_JS_URL . 'edd-bk-utils.js', array(), '1.0', true );
		wp_register_script( 'edd-bk-lodash', EDD_BK_JS_URL . 'lodash.min.js', array(), '3.10.0', true );
	}
	
	/**
	 * Sets the current locale and loads the plugin text domain.
	 */
	private function set_locale() {
		$this->i18n = new EDD_BK_i18n();
		$this->i18n->setDomain( self::TEXT_DOMAIN );
		$this->loader->add_action( 'plugins_loaded', $this->i18n, 'loadPluginTextdomain' );
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
