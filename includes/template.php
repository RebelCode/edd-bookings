<?php


function edd_bk_calendar( $download_id ) {
	// Get Meta Data
	$enabled	= get_post_meta( $download_id, 'edd_bk_enabled', true ) ? true : false;
	$start_date	= get_post_meta( $download_id, 'edd_bk_start_date', true );
	$start_time	= get_post_meta( $download_id, 'edd_bk_start_time', true );
	$end_date	= get_post_meta( $download_id, 'edd_bk_end_date', true );
	$end_time	= get_post_meta( $download_id, 'edd_bk_end_time', true );
	$all_day	= get_post_meta( $download_id, 'edd_bk_all_day', true ) ? true : false;

	// Print date fields
	if ( $enabled ) : ?>
	<div style="position: relative;">
		<label for="#edd-bk-date">Date:</label>
		<input type="text" placeholder="Date" class="datepicker" />

		<script type="text/javascript">

			jQuery(document).ready( function($){
				var d = $('.datepicker').datepicker({
					maxDate: ''
				});
			});
		</script>
		<br/>
	</div>
	<hr/>

	<?php
	endif;

}
add_action( 'edd_purchase_link_top', 'edd_bk_calendar' );


function enqueue_template_scripts() {
	wp_enqueue_style( 'edd-bk-jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css' );
	wp_enqueue_script( 'jquery-ui-datepicker', plugins_url( '/js/picker.js', EDD_BK_PLUGIN_FILE ), array( 'jquery' ) );
}
add_action('wp_enqueue_scripts','enqueue_template_scripts');
