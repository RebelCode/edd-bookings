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
class EDD_BK_Admin {

	/**
	 * Admin metaboxes for the New/Edit page for the Downloads Custom Post Type.
	 * 
	 * @var array
	 */
	private $metaboxes;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->prepare_directories();
		$this->load_dependancies();
		$this->define_hooks();
	}

	/**
	 * Prepares the directory constants.
	 */
	private function prepare_directories() {
		if ( !defined( 'EDD_BK_ADMIN_PARTIALS_DIR' ) ) {
			define( 'EDD_BK_ADMIN_PARTIALS_DIR', EDD_BK_ADMIN_DIR . 'partials/' );
		}
		if ( !defined( 'EDD_BK_ADMIN_JS_URL' ) ) {
			define( 'EDD_BK_ADMIN_JS_URL', EDD_BK_ADMIN_URL . 'js/' );
		}
		if ( !defined( 'EDD_BK_ADMIN_CSS_URL' ) ) {
			define( 'EDD_BK_ADMIN_CSS_URL', EDD_BK_ADMIN_URL . 'css/' );
		}
	}

	/**
	 * Loads the required files and initializes any required data members. 
	 */
	private function load_dependancies() {
		require EDD_BK_ADMIN_DIR . 'class-edd-bk-metaboxes.php';
		$this->metaboxes = new EDD_BK_Admin_Metaboxes( $this );
	}


	/**
	 * [define_hooks description]
	 * @return [type] [description]
	 */
	private function define_hooks() {
		$loader = EDD_Booking::get_instance()->get_loader();
		$loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_styles' );
		$loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_scripts' );
	}

	/**
	 * Enqueues CSS stylesheet files
	 */
	public function enqueue_styles() {
		// Admin styles
	}

	/**
	 * Enqueues JS script files
	 */
	public function enqueue_scripts() {
		// Admin scripts
	}

	/**
	 * Prints a simple HTML tooltip.
	 */
	public function help_tooltip( $text ) {
		?>
		<div class="edd-bk-help">
			<i class="fa fa-fw fa-question-circle"></i>
			<div><?php echo $text; ?></div>
		</div>
		<?php
	}

}