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
	 * [prepare_directories description]
	 * @return [type] [description]
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
	 * [load_dependancies description]
	 * @return [type] [description]
	 */
	private function load_dependancies() {
		require EDD_BK_ADMIN_DIR . 'class-edd-bk-metaboxes.php';
	}


	/**
	 * [define_hooks description]
	 * @return [type] [description]
	 */
	private function define_hooks() {
		$this->metaboxes = new EDD_BK_Admin_Metaboxes( $this );
		$loader = EDD_Booking::get_instance()->get_loader();
		
		$loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_styles' );
		$loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_scripts' );

		$loader->add_action( 'save_post', $this->metaboxes, 'save_post', 8, 2 );
		$loader->add_action( 'add_meta_boxes', $this->metaboxes, 'add_meta_boxes' );
		$loader->add_action( 'admin_enqueue_scripts', $this->metaboxes, 'enqueue_styles', 100 );
		$loader->add_action( 'admin_enqueue_scripts', $this->metaboxes, 'enqueue_scripts', 12 );
		$loader->add_action( 'edd_downloads_contextual_help', $this->metaboxes, 'contextual_help' );
	}

	/**
	 * @todo func doc
	 * @return [type] [description]
	 */
	public function enqueue_styles() {
		// Admin styles
	}

	/**
	 * @todo func doc
	 * @return [type] [description]
	 */
	public function enqueue_scripts() {
		// Admin scripts
	}

	/**
	 * [help_tooltip description]
	 * @return [type] [description]
	 */
	public function help_tooltip( $text ) {
		?>
		<div class="edd-bk-help">
			<i class="fa fa-fw fa-question-circle"></i>
			<div><?php _e( $text, 'edd' ); ?></div>
		</div>
		<?php
	}

}