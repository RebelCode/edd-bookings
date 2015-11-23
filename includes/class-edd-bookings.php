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
	 * 
	 * @since 1.0.0
	 */
	const TEXT_DOMAIN = 'eddbk';

	/**
	 * The loader class instance.
	 *
	 * @var EDD_BK_Loader
	 * @since 1.0.0
	 */
	protected $loader;

	/**
	 * The internationalization class instance.
	 * 
	 * @var EDD_BK_i18n
	 * @since 1.0.0
	 */
	protected $i18n;

	/**
	 * The admin class instance.
	 * 
	 * @var EDD_BK_Admin
	 * @since 1.0.0
	 */
	protected $admin;

	/**
	 * The public class instance.
	 * 
	 * @var EDD_BK_Public
	 * @since 1.0.0
	 */
	protected $public;

	/**
	 * The booking cpt class instance.
	 * 
	 * @var EDD_BK_Booking_CPT
	 * @since 1.0.0
	 */
	protected $booking_cpt;	

	/**
	 * The downloads controller instance.
	 * 
	 * @var EDD_BK_Downloads_Controller
	 * @since 1.0.0
	 */
	protected $downloads_controller;

	/**
	 * The bookings controller instance.
	 *
	 * @var EDD_BK_Bookings_Controller
	 * @since 1.0.0
	 */
	protected $bookings_controller;

	/**
	 * The EDD license handler.
	 * 
	 * @var EDD_License
	 * @since 1.0.0
	 */
	protected $licenseHandler;

	/**
	 * Reason for the plugin to be deactivated.
	 * 
	 * @var string
	 * @since 1.0.0
	 */
	protected static $deactivation_reason = '';

	/**
	 * The singleton instance of the class.
	 * 
	 * @var EDD_Booking
	 * @since 1.0.0
	 */
	protected static $instance = null;
	
	/**
	 * Instance constructor.
	 * 
	 * @throws Exception If the singleton instance is already instansiated.
	 * @since 1.0.0
	 */
	public function __construct() {
		// Singleton Instance Handling
		if ( self::$instance !== null )
			throw new EDD_BK_Singleton_Reinstantiaion_Exception();
		else self::$instance = $this;

		// Begin by initializing the loader
		$this->init_loader();
		// Define hooks
		$this->define_hooks();
		// Load required files
		$this->load_files();
		// Set the plugin locale
		$this->set_locale();

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
	 * Returns the admin class instance.
	 * 
	 * @return EDD_BK_Admin
	 * @since 1.0.0
	 */
	public function get_admin() {
		return $this->admin;
	}

	/**
	 * Returns the public class instance.
	 * 
	 * @return EDD_BK_Public
	 * @since 1.0.0
	 */
	public function get_public() {
		return $this->public;
	}

	/**
	 * Returns the loader instance.
	 *
	 * @return EDD_BK_Loader
	 * @since 1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Gets the downloads controller.
	 * 
	 * @return EDD_BK_Downloads_Controller
	 * @since 1.0.0
	 */
	public function get_downloads_controller() {
		return $this->downloads_controller;
	}

	/**
	 * Gets the bookings controller.
	 * 
	 * @return EDD_BK_Bookings_Controller
	 * @since 1.0.0
	 */
	public function get_bookings_controller() {
		return $this->bookings_controller;
	}

	/**
	 * Initializes the loader.
	 *
	 * @since 1.0.0
	 */
	private function init_loader() {
		// The loader class - responsible for all action and filter hooks
		require_once EDD_BK_INCLUDES_DIR . 'class-edd-bk-loader.php';
		// Initialize the loader
		$this->loader = new EDD_BK_Loader();
	}

	/**
	 * Loads all files required by the plugin.
	 *
	 * @since 1.0.0
	 */
	private function load_files() {
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
	}

	/**
	 * Registers hooks to the loader.
	 *
	 * @since 1.0.0
	 */
	private function define_hooks() {
		// Check for plugin dependancies
		$this->loader->add_action( 'admin_init', $this, 'check_plugin_dependancies' );
		// Script and style enqueuing hooks
		$hook = ( is_admin()? 'admin' : 'wp' ) . '_enqueue_scripts';
		$this->loader->add_action( $hook, $this, 'enqueue_styles' );
		$this->loader->add_action( $hook, $this, 'enqueue_scripts' );
	}

	/**
	 * Checks for the active presence of 3rd party plugins that this plugin depends on.
	 *
	 * @since 1.0.0
	 */
	public function check_plugin_dependancies() {
		if ( ! class_exists( EDD_BK_PARENT_PLUGIN_CLASS ) ) {
			self::deactivate( 'The <strong>Easy Digital Downloads</strong> plugin must be installed and activated.' );
		}
	}

	/**
	 * Enqueues or registers plugin-wide stylesheets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		// Font Awesome
		wp_enqueue_style( 'edd-bk-font-awesome-css', EDD_BK_CSS_URL . 'font-awesome.min.css' );
	}

	/**
	 * Enqueues or registers plugin-wide scripts.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		// Register scripts
		wp_register_script( 'edd-bk-utils', EDD_BK_JS_URL . 'edd-bk-utils.js', array(), '1.0', true );
		wp_register_script( 'edd-bk-lodash', EDD_BK_JS_URL . 'lodash.min.js', array(), '3.10.0', true );
		wp_register_script( 'edd-bk-moment', EDD_BK_JS_URL . 'moment.js', array(), '2.10.6', true );
	}
	
	/**
	 * Sets the current locale and loads the plugin text domain.
	 *
	 * @since 1.0.0
	 */
	private function set_locale() {
		$this->i18n = new EDD_BK_i18n();
		$this->i18n->setDomain( self::TEXT_DOMAIN );
		$this->loader->add_action( 'plugins_loaded', $this->i18n, 'loadPluginTextdomain' );
	}

	/**
	 * Triggers the loader, which attaches all registered hooks to WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Alias for the get_instance() method.
	 *
	 * @see EDD_Booking::get_instance()
	 * @uses EDD_Booking::get_instance()
	 * @return EDD_Bookings
	 * @since 1.0.0
	 */
	public static function instance() {
		return self::get_instance();
	}

	/**
	 * Returns the singleton instance, instansiating it if not yet initialized.
	 * 
	 * @return EDD_Bookings
	 * @since 1.0.0
	 */
	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Callback function triggered when the plugin is activated.
	 *
	 * @since 1.0.0
	 */
	public static function on_activate() {
		if ( version_compare( get_bloginfo('version'), EDD_BK_MIN_WP_VERSION, '<' ) ) {
			self::deactivate();
			wp_die( 'The EDD Bookings plugin failed to activate: WordPress version must be '.EDD_BK_MIN_WP_VERSION.' or later.', 'Error', array('back_link' => true) );
		}
	}

	/**
	 * Callback function triggered when the plugin is deactivated.
	 *
	 * @since 1.0.0
	 */
	public static function on_deactivate() {}

	/**
	 * Deactivates this plugin.
	 *
	 * @param callbable|string $arg The notice callback function, that will be hooked on `admin_notices` after deactivation, or
	 *                              a string specifying the reason for deactivation.
	 * @since 1.0.0
	 */
	public static function deactivate( $arg = NULL ) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		deactivate_plugins( EDD_BK_BASE );
		if ( $arg === NULL ) return;
		if ( is_callable( $arg ) ) {
			add_action( 'admin_notices', $arg );
		} else if ( is_string( $arg ) ) {
			self::$deactivation_reason = $arg;
			add_action( 'admin_notices', array( __CLASS__, 'show_deactivation_reason' ) );
		}
		
	}

	/**
	 * Prints an admin notice that tells the user that the plugin has been deactivated, and why.
	 *
	 * @since 1.0.0
	 */
	public static function show_deactivation_reason() {
		echo '<div class="error notice is-dismissible"><p>';
		echo 'The <strong>EDD Bookings</strong> plugin has been deactivated. ' . self::$deactivation_reason;
		echo '</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
	}

}
