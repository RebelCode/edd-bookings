<?php
/**
* @todo file doc
*/

/**
* @todo class doc
*/
class EDD_Booking {
	
	/**
	 * @todo var doc
	 */
	private $loader;

	/**
	 * [$admin description]
	 * @var [type]
	 */
	private $admin;

	/**
	 * [$public description]
	 * @var [type]
	 */
	private $public;

	/**
	 * @todo var doc
	 */
	private $plugin_name;
	
	/**
	 * @todo var doc
	 */
	private $version;

	/**
	 * [$instance description]
	 * @var [type]
	 */
	private static $instance = null;
	
	/**
	 * @todo func doc
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

		if ( is_admin() ) {
			$this->define_admin_hooks();
		} else {
			$this->define_public_hooks();
		}
	}

	/**
	 * [instance description]
	 * @return [type] [description]
	 */
	public static function instance() {
		return self::get_instance();
	}

	/**
	 * [get_instance description]
	 * @return [type] [description]
	 */
	public static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new EDD_Booking();
		}
		return self::$instance;
	}

	/**
	 * @todo func doc
	 */
	private function load_dependancies() {
		// The loader class - responsible for all action and filter hooks
		require_once EDD_BK_INC_DIR . 'class-edd-bk-loader.php';
		// Load the i18n file
		require_once EDD_BK_INC_DIR . 'class-edd-bk-i18n.php';
		// Load the admin class file
		require_once EDD_BK_ADMIN_DIR . 'class-edd-bk-admin.php';
		// Load the public class file
		require_once EDD_BK_PUBLIC_DIR . 'class-edd-bk-public.php';
		// Load the utility functions file
		require_once EDD_BK_INC_DIR . 'class-edd-bk-utils.php';

		$this->loader = new EDD_BK_Loader();
	}
	
	/**
	 * @todo func doc
	 */
	private function set_locale() {
		$edd_bk_i18n = new EDD_BK_i18n();
		$edd_bk_i18n->set_domain( $this->get_plugin_name() );
		$this->loader->add_action( 'plugins_loaded', $edd_bk_i18n, 'load_plugin_textdomain' );
	}
	
	/**
	 * @todo func doc
	 */
	private function define_admin_hooks() {
		$this->admin = new EDD_BK_Admin();

		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_scripts' );
	}
	
	/**
	 * @todo func doc
	 */
	private function define_public_hooks() {
		$this->public = new EDD_BK_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $this->public, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->public, 'enqueue_scripts' );
	}
	
	/**
	 * @todo func doc
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * @todo func doc
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}
	
	/**
	 * @todo func doc
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * @todo func doc
	 */
	public function get_loader() {
		return $this->loader;
	}

}