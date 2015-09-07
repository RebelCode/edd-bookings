<?php

/**
 * The Bookings Controller class for the Bookings New/Edit page.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Bookings\Bookings
 */
class EDD_BK_Bookings_Metabox_Controller {
	
	/**
	 * An array of EDD_BK_Metabox instance.
	 * 
	 * @var array
	 */
	protected $metaboxes;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->define_hooks();
		$this->metaboxes = array(
			new EDD_BK_Metabox( 'edd_bk_booking_metabox',
				__( 'Booking Details', EDD_Bookings::TEXT_DOMAIN ),
				EDD_BK_VIEWS_DIR . 'view-admin-booking-metabox.php',
				EDD_BK_Metabox::CONTEXT_NORMAL,
				EDD_BK_Metabox::PRIORITY_CORE,
				EDD_BK_Booking_CPT::SLUG 
			)
		);
	}

	/**
	 * Registers hooks to the loader.
	 */
	public function define_hooks() {
		$loader = EDD_Bookings::get_instance()->get_loader();
		$loader->add_action( 'add_meta_boxes', $this, 'register' );
		$loader->add_action( 'admin_menu', $this, 'unregister' );
		$loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_styles', 100 );
		$loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_scripts', 12 );
		$loader->add_action( 'save_post', $this, 'on_submit', 10, 2 );
	}

	/**
	 * Registers the metaboxes.
	 */
	public function register() {
		foreach ( $this->metaboxes as $metabox ) $metabox->register();
	}

	/**
	 * Unregisters any other extra metaboxes.
	 */
	public function unregister() {
		remove_meta_box( 'submitdiv', EDD_BK_Booking_CPT::SLUG, EDD_BK_Metabox::CONTEXT_NORMAL );
	}

	/**
	 * Enqueues stylesheets.
	 */
	public function enqueue_styles() {
		if ( get_current_screen()->id !== EDD_BK_Booking_CPT::SLUG ) return;
		wp_enqueue_style( 'edd-bk-admin-bookings-edit', EDD_BK_CSS_URL . 'edd-bk-admin-bookings-edit.css' );
	}

	/**
	 * Enqueues script files.
	 */
	public function enqueue_scripts() {
		if ( get_current_screen()->id !== EDD_BK_Booking_CPT::SLUG ) return;
	}

	/**
	 * Callback function triggered when the metabox data is submitted.
	 *
	 * @param string|int $post_id The ID of the post.
	 * @param WP_Post    $post    The post object
	 */
	public function on_submit( $post_id, $post ) {
		// Check post id is valid and POST data is not empty
		if ( ! get_post( $post_id ) || empty( $_POST ) ) return;
		// Check for auto save / bulk edit
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) )
			return $post_id;
		// Check post type
		if ( isset( $_POST['post_type'] ) && $_POST['post_type'] != 'booking' )
			return $post_id;
		// Check user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;
		// verify nonce
		check_admin_referer( 'edd_bk_booking_saving_meta', 'edd_bk_booking_meta_nonce' );
	}

}
