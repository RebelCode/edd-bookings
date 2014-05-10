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
	$enabled	= get_post_meta( $post->ID, '_edd_bk_enabled', true ) ? true : false;
	$start_date	= get_post_meta( $post->ID, '_edd_bk_start_date', true );
	$start_time	= get_post_meta( $post->ID, '_edd_bk_start_time', true );
	$end_date	= get_post_meta( $post->ID, '_edd_bk_end_date', true );
	$end_time	= get_post_meta( $post->ID, '_edd_bk_end_time', true );
	$display	= $enabled ? '' : ' style="display: none;"';

	// Use nonce for verification
	$edd_bk_nonce = wp_create_nonce( basename( __FILE__ ) ); ?>
	<input type="hidden" name="edd_bk_meta_box_nonce" value="<?php echo $edd_bk_nonce; ?>" />


	<script type="text/javascript">
	(function($){
		$(document).ready( function() {
			// TOGGLERS
			var togglers = {

				// Main toggler
				"#edd_bk_enabled": [
					"click",
					function(){
						$(".edd_bk_toggled_row").toggle( $("#edd_bk_enabled").is(":checked") );
					}
				],

				// All day toggler
				"#edd_bk_all_day": [
					"click",
					function() {
						$("#edd_bk_start_time, #edd_bk_end_time").toggle( $("#edd_bk_all_day").is(":not(:checked)") );
					}
				],

			};

			// Initialize togglers
			for( selector in togglers ) {
				var toggler = togglers[selector];
				var event = toggler[0];
				var fn = toggler[1];
				$(selector).on( event, fn );
				fn();
			}

		});
	})(jQuery);
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

		<!-- DATES AND TIMES -->
		<tr class="edd_bk_toggled_row">
			<td class="edd_field_type_text" colspan="2">
				<!-- START -->
				<!--label for="edd_bk_start_date">From </label-->
				<input type="date" name="edd_bk_start_date" id="edd_bk_start_date" />
				<input type="time" name="edd_bk_start_time" id="edd_bk_start_time" />
				<!-- END -->
				<label for="edd_bk_end_date">till </label>
				<input type="time" name="edd_bk_end_time" id="edd_bk_end_time" />
				<input type="date" name="edd_bk_end_date" id="edd_bk_end_date" />
			</td>
		</tr>

		<!-- ALL DAY OPTION -->
		<tr class="edd_bk_toggled_row">
			<td class="edd_field_type_text" colspan="2">
				<!-- ALL DAY OPTION -->
				<input type="checkbox" name="edd_bk_all_day" id="edd_bk_all_day" />
				<label for="edd_bk_all_day">
					All day event - <span class="description">this event does not require start and end times</span>.
				</label>
			</td>
		</tr>

	</table>

	<?php
}
