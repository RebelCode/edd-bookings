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
	$enabled = get_post_meta( $post->ID, '_edd_bk_enabled', true ) ? true : false;
	$display = $enabled ? '' : ' style="display: none;"';

	// Use nonce for verification
	$edd_bk_nonce = wp_create_nonce( basename( __FILE__ ) );

	?>

	<input type="hidden" name="edd_bk_meta_box_nonce" value="<?php echo $edd_bk_nonce; ?>" />

	<script type="text/javascript">
		var edd_bk_toggler = function() {
			jQuery(".edd_bk_toggled_row").toggle( jQuery("#edd_bk_enabled").is(":checked") );
		};
		jQuery(document).ready( edd_bk_toggler );
		jQuery(document).ready( function($) {
			$("#edd_bk_enabled").on( "click", edd_bk_toggler );
		});
	</script>

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
		<!-- TEST OPTION -->
		<tr class="edd_bk_toggled_row">
			<td class="edd_field_type_text" colspan="2">
				<input type="text" name="edd_bk_test" id="edd_bk_test" placeholder="test"/>
				<label>
					Just a test label.
				</label>
			</td>
		</tr>
	</table>

	<?php
}
