<?php

/**
 * The Metaboxes Controller class for the Download Edit page.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Bookings\Downloads
 */
class EDD_BK_Downloads_Metabox_Controller {

	/**
	 * An array of EDD_BK_Metabox instances.
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
			new EDD_BK_Metabox( 'edd_bk_metabox', __( 'Booking', EDD_Bookings::TEXT_DOMAIN ), EDD_BK_VIEWS_DIR . 'view-admin-metabox.php', EDD_BK_Metabox::CONTEXT_NORMAL, EDD_BK_Metabox::PRIORITY_HIGH )
		);
	}

	/**
	 * Registers the WordPress hooks to the loader.
	 */
	private function define_hooks() {
		$loader = EDD_Bookings::get_instance()->get_loader();
		$loader->add_action( 'save_post', $this, 'on_submit', 8, 2 );
		$loader->add_action( 'add_meta_boxes', $this, 'register' );
		$loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_styles', 100 );
		$loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_scripts', 12 );
		// $loader->add_action( 'edd_downloads_contextual_help', $this, 'contextual_help' );
	}

	/**
	 * Registers the EDD Booking metabox to the EDD Download New/Edit page
	 */
	public function register() {
		// Do not show the metabox for bundle downloads
		if ( edd_get_download_type( get_the_ID() ) === 'bundle' ) return;

		// Iterate all metaboxes and register
		foreach ( $this->metaboxes as $metabox ) $metabox->register();
	}

	/**
	 * Enqueues the require stylesheet files for the metabox.
	 */
	public function enqueue_styles() {
		// Get current screen
		$screen = get_current_screen();
		if ( $screen->id === 'download' ) {
			wp_enqueue_style( 'edd-bk-download-edit-css', EDD_BK_CSS_URL . 'edd-bk-admin-download-edit.css' );
			wp_enqueue_style( 'edd-bk-jquery-chosen-css', EDD_BK_JS_URL . 'jquery-chosen/chosen.min.css' );
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
			// Enqueue jQuery Chosen
			wp_enqueue_script( 'edd-bk-jquery-chosen-js', EDD_BK_JS_URL . 'jquery-chosen/chosen.jquery.min.js', array( 'jquery' ) );

			// Register our admin download edit page script, localize with availability table row template, then enqueue it
			wp_register_script( 'edd-bk-sprintf-js', EDD_BK_JS_URL . 'sprintf.min.js' );
			wp_register_script( 'edd-bk-admin-download-edit-js', EDD_BK_JS_URL . 'edd-bk-admin-download-edit.js', array( 'edd-bk-sprintf-js', 'edd-bk-utils', 'jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker' ) );
			wp_localize_script( 'edd-bk-admin-download-edit-js', 'eddBkAvailabilityTableRow', EDD_BK_Utils::ob_include( EDD_BK_VIEWS_DIR.'view-admin-availability-table-row.php' ) );
			wp_localize_script( 'edd-bk-admin-download-edit-js', 'eddBkMsgs', array(
				'tableHelp'	=>	array(
					'months'			=>	__( 'from %1$s till %2$s', EDD_Bookings::TEXT_DOMAIN ),
					'days'				=>	__( 'from %1$s till %2$s all year', EDD_Bookings::TEXT_DOMAIN ),
					'weeks'				=>	__( 'from week #%1$s till week #%2$s', EDD_Bookings::TEXT_DOMAIN ),
					'custom'			=>	__( 'from %1$s till %2$s', EDD_Bookings::TEXT_DOMAIN ),
					'dotw'				=>	_x( 'on all %3$ss of the year from %1$s till %2$s', 'example: on all Mondays of the year', EDD_Bookings::TEXT_DOMAIN ),
					'all_week'			=>	__( 'all week, all year from %1$s till %2$s', EDD_Bookings::TEXT_DOMAIN ),
					'weekend'			=>	__( 'on all weekends of the year from %1$s till %2$s', EDD_Bookings::TEXT_DOMAIN ),
					'weekdays'			=>	__( 'on all week days of the year from %1$s till %2$s', EDD_Bookings::TEXT_DOMAIN ),
				),
				'available'			=>	__( 'available', EDD_Bookings::TEXT_DOMAIN ),
				'unavailable'		=>	__( 'unavailable', EDD_Bookings::TEXT_DOMAIN ),
			) );
			wp_enqueue_script( 'edd-bk-admin-download-edit-js' );
		}
	}

	/**
	 * Saves the Download meta data when it is submitted for creation for modification.
	 *
	 * @param string|int $post_id The ID of the post begin saved.
	 * @param object     $post    The post object.
	 */
	public function on_submit( $post_id, $post ) {
		if ( empty( $_POST ) || ! get_post( $post_id ) )
			return $post_id;
		// Check for auto save / bulk edit
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) )
			return $post_id;
		// Check post type
		if ( isset( $_POST['post_type'] ) && $_POST['post_type'] != 'download' )
			return $post_id;
		// Check user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;
		// verify nonce
		check_admin_referer( 'edd_bk_saving_meta', 'edd_bk_meta_nonce' );

		// Save the download meta
		$meta = edd_bk()->get_downloads_controller()->extract_meta_from_submitted_post_data();
		edd_bk()->get_downloads_controller()->save_meta( $post_id, $meta );
	}

	/**
	 * Adds WordPress contextual help.
	 *
	 * @param string $screen The screen where to add the contextual help.
	 */
	public function contextual_help( $screen ) {
		$help_content = EDD_BK_Utils::ob_include( EDD_BK_VIEWS_DIR . 'view-admin-contextual-help.php' );
		$screen->add_help_tab( array(
			'id'	    => 'edd-booking',
			'title'	    => __( 'Download Bookings', EDD_Bookings::TEXT_DOMAIN ),
			'content'	=> $help_content
		) );
	}
	
}
