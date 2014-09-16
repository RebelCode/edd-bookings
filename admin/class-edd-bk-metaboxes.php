<?php

/**
 * @todo file doc
 */

/**
 * @todo class doc
 */
class EDD_BK_Admin_Metaboxes {

	/**
	 * [$admin description]
	 * @var [type]
	 */
	private $admin;

	/**
	 * [__construct description]
	 */
	public function __construct( $_admin ) {
		$this->admin = $_admin;
	}

	/**
	 * [add_meta_boxes description]
	 */
	public function add_meta_boxes() {
		if ( edd_get_download_type( get_the_ID() ) != 'bundle' ) {
			add_meta_box(
				'edd_bk_box',
				__( 'Booking', 'edd_bk' ),
				array( $this, 'render_meta_box' ),
				'download', 'normal', 'core'
			);
		}
	}

	/**
	 * [render_meta_box description]
	 * @return [type] [description]
	 */
	public function render_meta_box() {
		$admin = $this->admin;
		require EDD_BK_ADMIN_PARTIALS_DIR . 'edd-bk-metabox.php';
	}

	/**
	 * Save data from meta box
	 *
	 * @since 1.0
	 */
	public function save_post( $post_id ) {
		global $post;

		// verify nonce
		if ( ! isset( $_POST['edd_bk_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['edd_bk_meta_box_nonce'], basename( __FILE__ ) ) ) {
			return $post_id;
		}
		// Check for auto save / bulk edit
		if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX') && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
			return $post_id;
		}
		// Check post type
		if ( isset( $_POST['post_type'] ) && $_POST['post_type'] != 'download' ) {
			return $post_id;
		}
		// Check user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( isset( $_POST['edd_bk_enabled'] ) ) {
			update_post_meta( $post_id, 'edd_bk_enabled', true );
		} else {
			delete_post_meta( $post_id, 'edd_bk_enabled' );
		}

		if ( isset( $_POST['edd_bk_start_date'] ) ) {
			update_post_meta( $post_id, 'edd_bk_start_date', $_POST['edd_bk_start_date'] );
		} else {
			delete_post_meta( $post_id, 'edd_bk_start_date' );
		}

		if ( isset( $_POST['edd_bk_start_time'] ) ) {
			update_post_meta( $post_id, 'edd_bk_start_time', $_POST['edd_bk_start_time'] );
		} else {
			delete_post_meta( $post_id, 'edd_bk_start_time' );
		}

		if ( isset( $_POST['edd_bk_end_date'] ) ) {
			update_post_meta( $post_id, 'edd_bk_end_date', $_POST['edd_bk_end_date'] );
		} else {
			delete_post_meta( $post_id, 'edd_bk_end_date' );
		}

		if ( isset( $_POST['edd_bk_end_time'] ) ) {
			update_post_meta( $post_id, 'edd_bk_end_time', $_POST['edd_bk_end_time'] );
		} else {
			delete_post_meta( $post_id, 'edd_bk_end_time' );
		}

		if ( isset( $_POST['edd_bk_all_day'] ) ) {
			update_post_meta( $post_id, 'edd_bk_all_day', true );
		} else {
			delete_post_meta( $post_id, 'edd_bk_all_day' );
		}

	}


	public function contextual_help( $screen ) {
		ob_start();
		include EDD_BK_ADMIN_PARTIALS_DIR . 'edd-bk-contextual-help.php';
		$help_content = ob_get_clean();

		$screen->add_help_tab( array(
			'id'	    => 'edd-booking',
			'title'	    => __( 'Download Bookings', 'edd' ),
			'content'	=> $help_content
		) );
	}

}
