<?php

/**
 * Enqueue admin scripts
 *
 * @since 1.0
 */
function edd_bk_admin_scripts() {
	$screen = get_current_screen();

	if( !is_object( $screen ) || ( $screen->id !== 'download' && $screen->id !== 'download_page_edd-booking' ) ) {
		return;
 	}

	// Enqueue scripts here
	//wp_enqueue_script( 'edd-bk-admin', plugins_url( '/js/edd-bk-admin.js', EDD_BK_PLUGIN_FILE ), array( 'jquery' ) );

	if( $screen->id === 'download' ) {
		// Localize scripts here
		//wp_localize_script( 'edd-bk-admin', 'edd_bk', array(/* put edd_bk object data here */) );
	}
}

add_action( 'admin_enqueue_scripts', 'edd_bk_admin_scripts' );
