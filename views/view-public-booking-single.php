<?php

/**
 * View file for the rendering of the bookings options for a download on the public side of the site.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Booking\Views
 */


// Get the booking
$post_id = get_the_ID();
$download = EDD_BK_Downloads_Controller::get( $post_id );
$availability = $download->getAvailability()->process();

// If bookings are not enabled, stop.
if ( ! $download->isEnabled() ) return;

// Get the session unit - to be used to determine date/time picker type
$slot_duration_unit = strtolower( $download->getSessionUnit() );

// Add data to the JS script
wp_localize_script(
	'edd-bk-public-download-view',
	'edd_bk',
	array(
		'post_id'			=> $post_id,
		'ajaxurl'			=> admin_url( 'admin-ajax.php' ),
		'meta'				=> $download->toArray(),
		'availability'		=> $availability,
		'currency'			=> edd_currency_symbol()
	)
);

// Begin Output of front-end interface ?>

<?php
/**
 * THE DATE PICKER
 *
 * jQuery UI Datepicker with a custom blue skin and a refresh button (possibly deprecated).
 * -------------------------------------------------------------------------------------------
 */
?>
<div id="edd-bk-datepicker-container">
	<div class="edd-bk-dp-skin">
		<div id="edd-bk-datepicker"></div>
	</div>
	<input type="hidden" id="edd-bk-datepicker-value" name="edd_bk_date" value="" />
</div>


<?php
/**
 * THE TIME PICKER
 *
 * Custom element group consisting of a loading message and a time dropdown.
 * A price section is also shown below the time dropdown for cost previewing.
 * -------------------------------------------------------------------------------------------
 */
?>
<div id="edd-bk-timepicker-container">
	<p id="edd-bk-timepicker-loading"><i class="fa fa-cog fa-spin"></i> Loading</p>
	<div id="edd-bk-timepicker">
		<?php if ( $download->isSessionUnit( 'hours', 'minutes' ) ) : ?>
			<p>
				<label>
					<?php echo $download->getBookingDuration() === 'fixed'? 'Booking' : 'Start' ?>
					Time:
				</label>
				<select name="edd_bk_time"></select>
			</p>
		<?php endif; ?>

		<?php if ( $download->getBookingDuration() !== 'fixed' ) : ?>
			<?php
				$min = $download->getMinSessions();
				$max = $download->getMaxSessions();
				$step = $download->getSessionLength();
			?>
			<p>
				<label>Duration:</label>
				<input id="edd_bk_num_sessions" name="edd_bk_num_sessions" type="number" step="<?php echo $step ?>" min="<?php echo $min ?>" max="<?php echo $max ?>" value="<?php echo $min ?>" required />
				<?php echo $slot_duration_unit; ?>
			</p>
		<?php endif; ?>

		<p id="edd-bk-price">
			Price: <span></span>
		</p>
	</div>
</div>
<?php
/**
 * NO TIMES MESSAGES
 *
 * This message is show when a date is selected, and no times are returned by the server.
 * Such a case can occur for two reasons: either all times are booked by other customers or
 * if the admin incorrectly set up the availability, which causes dates to be available on
 * the calendar but not have any bookable times.
 * ---------------------------------------------------------------------------------------------
 */
?>
<div id="edd-bk-no-times-for-date">
	<p>No times are available for this date!</p>
</div>


<?php
/**
 * DEBUGGING
 *
 * Prints the booking data structure and session data.
 * ----------------------------------------------------------------------
 */
if ( !defined( 'EDD_BK_DEBUG' ) || !EDD_BK_DEBUG ) return;

function edd_bk_public_download_debug( $title, $data ) {
	echo '<hr />';
	echo '<h4>' . $title . '</h4>';
	echo '<div style="zoom: 0.8">';
	echo '<pre>' . print_r( $data, true ) . '</pre>';
	echo '</div>';
}

edd_bk_public_download_debug("This Download's Booking data", $download );
edd_bk_public_download_debug("Processed Availability", $availability );
edd_bk_public_download_debug("Bookings for this Download", edd_bk()->get_bookings_controller()->getBookingsForService( $download->getId(), array(1438387200, 1440892800) ) );
edd_bk_public_download_debug("Session", $_SESSION );
