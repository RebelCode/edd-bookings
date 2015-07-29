<?php

/**
 * The Downloads Controller class.
 *
 * This class is built management and handling of Downloads and their meta data.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Bookings\Downloads
 */
class EDD_BK_Downloads_Controller {
	
	/**
	 * The meta field name prefix used in the Admin Metaboxes for the Download Edit page.
	 */
	const ADMIN_METABOX_METADATA_PREFIX = 'edd_bk_';

	/**
	 * Returns the Download with the given ID.
	 * 
	 * @param  string|int           $id The ID of the Download.
	 * @return EDD_BK_Download|null     The EDD_BK_Download instance, or NULL if the given ID
	 *                                  does not match a Download post.
	 */
	public static function get( $id ) {
		if ( get_post( $id ) === FALSE ) return NULL;
		$meta = get_post_meta( $id, 'edd_bk', TRUE );
		$download = new EDD_BK_Download( $meta );
		$download->setId( $id );
		return $download;
	}

	/**
	 * Saves the given meta data to a specific Download.
	 * 
	 * @param  string|int $id   The ID of the Download.
	 * @param  array      $meta The meta data to save.
	 */
	public static function save_meta( $id, $meta ) {
		update_post_meta( $id, 'edd_bk', $meta );
	}

	/**
	 * Extracts the meta data of a Download from submitted POST data.
	 *
	 * The POST data is expected to be received from the Download "Add New" or "Edit" pages.
	 * 
	 * @return array The meta data extracted from the post_data
	 */
	public static function extract_meta_from_submitted_post_data() {
		$meta = array();
		// Gather all POST keys that start with our prefix
		foreach ( $_POST as $key => $value ) {
			// If the key starts with the known prefix
			if ( strpos( $key, self::ADMIN_METABOX_METADATA_PREFIX ) === 0 ) {
				// Skip any nonces
				if ( $key === 'edd_bk_meta_nonce' ) continue;
				// Add to the meta array
				$new_key = substr( $key, strlen( self::ADMIN_METABOX_METADATA_PREFIX ) );
				$meta[ $new_key ] = $value;
			}
		}
		if ( isset( $meta['availability'] ) ) {
			// Sanitize the availability fill into a boolean
			$meta['availability']['fill'] = EDD_BK_Utils::multiboolean( $meta['availability']['fill'] );
			// Re-arrange the availability entries into the correct format
			$newEntries = array();
			$entries = $_POST['edd_bk_availability']['entries'];
			$count = count( $entries['range_type'] );
			for ( $i = 0; $i < $count; $i++ ) {
				$newEntries[] = array(
					'range_type'	=>	$entries['range_type'][ $i ],
					'from'			=>	$entries['from'][ $i ],
					'to'			=>	$entries['to'][ $i ],
					'available'		=>	EDD_BK_Utils::multiboolean( $entries['available'][ $i ] )
				);
			}
			// Set the new entries
			$meta['availability']['entries'] = $newEntries;
		}

		return $meta;
	}

}