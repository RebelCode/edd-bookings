<?php


add_action( 'add_meta_boxes', 'edd_bk_add_booking_meta_box', 100 );
/**
 * Add License Meta Box
 *
 * @since 1.0
 */
function edd_bk_add_booking_meta_box() {
	global $post;
	// Add meta box if the download is not a bundle
	if ( edd_get_download_type( get_the_ID() ) != 'bundle' ) {
		add_meta_box(
			'edd_bk_box',
			__( 'Booking', 'edd_bk' ),
			'edd_bk_render_booking_meta_box',
			'download', 'normal', 'core'
		);
	}
}



/**
 * Renders the EDD Booking metabox
 *
 * @since 1.0
 */
function edd_bk_render_booking_meta_box() {
	global $post;

	// Get meta data
	$enabled	= get_post_meta( $post->ID, 'edd_bk_enabled', true ) ? true : false;
	$start_date	= get_post_meta( $post->ID, 'edd_bk_start_date', true );
	$start_time	= get_post_meta( $post->ID, 'edd_bk_start_time', true );
	$end_date	= get_post_meta( $post->ID, 'edd_bk_end_date', true );
	$end_time	= get_post_meta( $post->ID, 'edd_bk_end_time', true );
	$all_day	= get_post_meta( $post->ID, 'edd_bk_all_day', true );
	$display	= $enabled ? '' : ' style="display: none;"';

	// Use nonce for verification
	$edd_bk_nonce = wp_create_nonce( basename( __FILE__ ) ); ?>
	<input type="hidden" name="edd_bk_meta_box_nonce" value="<?php echo $edd_bk_nonce; ?>" />

	<script type="text/javascript" src="<?php echo EDD_BK_PLUGIN_URL; ?>js/edd-bk-admin.js"></script>

	<table class="form-table">

		<!-- ENABLER -->
		<tr>
			<td class="edd_field_type_text" colspan="2">
				<input type="checkbox" name="edd_bk_enabled" id="edd_bk_enabled" value="1" <?php echo checked( true, $enabled ); ?> />
				<label for="edd_bk_enabled">
					<?php _e( 'Check to enable booking for this download', 'edd_bk' ); ?>
				</label>
			</td>
		</tr>

		<!-- DATES AND TIMES -->
		<tr class="edd_bk_toggled_row">
			<td class="edd_field_type_text" colspan="2">
				<!-- START -->
				<!--label for="edd_bk_start_date">From </label-->
				<input type="date" name="edd_bk_start_date" id="edd_bk_start_date" value="<?php echo esc_attr( $start_date ); ?>" />
				<input type="time" name="edd_bk_start_time" id="edd_bk_start_time" value="<?php echo esc_attr( $start_time ); ?>" />
				<!-- END -->
				<label for="edd_bk_end_date"><?php _e( 'till', 'edd_bk' ); ?></label>
				<input type="time" name="edd_bk_end_time" id="edd_bk_end_time" value="<?php echo esc_attr( $end_time ); ?>" />
				<input type="date" name="edd_bk_end_date" id="edd_bk_end_date" value="<?php echo esc_attr( $end_date ); ?>" />
			</td>
		</tr>

		<!-- ALL DAY OPTION -->
		<tr class="edd_bk_toggled_row">
			<td class="edd_field_type_text" colspan="2">
				<!-- ALL DAY OPTION -->
				<input type="checkbox" name="edd_bk_all_day" id="edd_bk_all_day" <?php echo checked( true, $all_day ); ?> />
				<label for="edd_bk_all_day">
					<?php _e( 'All day event', 'edd_bk' ); ?> -
					<span class="description"><?php _e( 'this event does not require start and end times', 'edd_bk' ); ?></span>.
				</label>
			</td>
		</tr>

	</table>

	<?php
}




/**
 * Save data from meta box
 *
 * @since 1.0
 */
function edd_bk_meta_box_save( $post_id ) {

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
add_action( 'save_post', 'edd_bk_meta_box_save' );
