<?php

/**
 * @todo class doc
 */
class EDD_BK_Admin_Metaboxes {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->define_hooks();
	}

	/**
	 * Registers the WordPress hooks to the loader.
	 */
	private function define_hooks() {
		$loader = EDD_Booking::get_instance()->get_loader();
		$loader->add_action( 'save_post', $this, 'save_post', 8, 2 );
		$loader->add_action( 'add_meta_boxes', $this, 'add_meta_boxes' );
		$loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_styles', 100 );
		$loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_scripts', 12 );
		$loader->add_action( 'edd_downloads_contextual_help', $this, 'contextual_help' );
	}

	/**
	 * Returns the list of metaboxes.
	 * 
	 * @return array An array of EDD_BK_Metabox instances.
	 */
	public function get_metaboxes() {
		return array(
			new EDD_BK_Metabox( 'edd_bk_metabox', __( 'Booking', 'edd_bk' ), EDD_BK_ADMIN_VIEWS_DIR . 'view-metabox.php' )
		);
	}

	/**
	 * Registers the EDD Booking metabox to the EDD Download New/Edit page
	 */
	public function add_meta_boxes() {
		// Do not show the metabox for bundle downloads
		if ( edd_get_download_type( get_the_ID() ) == 'bundle' ) return;

		// Iterate all metaboxes and register
		foreach ( $this->get_metaboxes() as $metabox ) {
			$metabox->register();
		}
	}

	/**
	 * Renders the booking metabox.
	 */
	public function render_meta_box() {
		$admin = EDD_Booking::instance()->get_admin();
		require EDD_BK_ADMIN_VIEWS_DIR . 'view-metabox.php';
	}

	/**
	 * Enqueues the require stylesheet files for the metabox.
	 */
	public function enqueue_styles() {
		// Get current screen
		$screen = get_current_screen();
		if ( $screen->id === 'download' ) {
			wp_enqueue_style( 'edd-bk-download-edit-css', EDD_BK_ADMIN_CSS_URL . 'edd-bk-download-edit.css' );
			wp_enqueue_style( 'edd-bk-jquery-chosen-css', EDD_BK_ADMIN_JS_URL . 'jquery-chosen/chosen.min.css' );
		}
	}

	/**
	 * Enqueues the required script files for the metabox.
	 */
	public function enqueue_scripts() {
		// Get current screen
		$screen = get_current_screen();
		// Load for Downloads Edit Page
		if ( $screen->id === 'download' ) {
			wp_enqueue_script( 'edd-bk-jquery-chosen-js', EDD_BK_ADMIN_JS_URL . 'jquery-chosen/chosen.jquery.min.js', array( 'jquery' ) );
			wp_register_script( 'edd-bk-download-edit-js', EDD_BK_ADMIN_JS_URL . 'edd-bk-download-edit.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker' ) );
			ob_start(); include( EDD_BK_ADMIN_VIEWS_DIR.'view-availability-table-row.php' );
			wp_localize_script( 'edd-bk-download-edit-js', 'availabilityTableRow', ob_get_clean() );
			wp_enqueue_script( 'edd-bk-download-edit-js' );
		}
	}


	/**
	 * Saves the Download meta data when it is submitted for creation for modification.
	 *
	 * @todo clean up
	 */
	public function save_post( $post_id, $post ) {
		if ( empty( $_POST ) ) {
			return $post_id;
		}
		if ( ! get_post( $post_id ) ) {
			return $post_id;
		}

		// Check for auto save / bulk edit
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
			return $post_id;
		}
		// Check post type
		if ( isset( $_POST['post_type'] ) && $_POST['post_type'] != 'download' ) {
			return $post_id;
		}

		// verify nonce
		check_admin_referer( 'edd_bk_saving_meta', 'edd_bk_meta_nonce' );

		// Check user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
		
		$meta_fields = EDD_BK_Commons::meta_fields();
		foreach ( $meta_fields as $field ) {
			$key = 'edd_bk_'.$field;
			if ( isset( $_POST[$key] ) ) {
				$value = $_POST[$key];
				if ( is_string( $value ) ) {
					update_post_meta( $post_id, $key, $value );
				}
			} else {
				delete_post_meta( $post_id, $key );
			}
		}

		$avail_meta = array();
		if ( isset( $_POST['edd_bk_availability'] ) ) {
			$availability = $_POST['edd_bk_availability'];
			$range_types = $availability['range_types'];
			$range_from = $availability['range_from'];
			$range_to = $availability['range_to'];
			$range_available = $availability['range_available'];
			for ( $i = 0; $i < count( $range_types ); $i++ ) {
				$avail_meta[$i] = array(
					'type'		=>	$range_types[$i],
					'from'		=>	$range_from[$i],
					'to'		=>	$range_to[$i],
					'available'	=>	$range_available[$i],
				);
			}
		}
		update_post_meta( $post_id, 'edd_bk_availability', $avail_meta );
	}

	/**
	 * Adds WordPress contextual help.
	 */
	public function contextual_help( $screen ) {
		$help_content = EDD_BK_Utils::ob_include( EDD_BK_ADMIN_VIEWS_DIR . 'view-contextual-help.php' );
		$screen->add_help_tab( array(
			'id'	    => 'edd-booking',
			'title'	    => __( 'Download Bookings', 'edd' ),
			'content'	=> $help_content
		) );
	}

}
