<?php

	// Check if booking is enabled. If not, stop.
	if ( ! get_post_meta( get_the_ID(), 'edd_bk_enabled', TRUE ) ) return;

	// Get the booking
	$post_id = get_the_ID();
	$booking = EDD_BK_Booking::from_id( $post_id );
	// Get the session unit - to be used to determine date/time picker type
	$slot_duration_unit = strtolower( $booking->getSessionUnit() );

	// Add data to the JS script
	wp_localize_script(
		'edd-bk-download-public',
		'edd_bk',
		array(
			'post_id'			=> $post_id,
			'ajaxurl'			=> admin_url( 'admin-ajax.php' ),
			'meta'				=> EDD_BK_Commons::meta_fields( $post_id ),
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
			<button class="button edd-bk-datepicker-refresh" type="button">
				<i class="fa fa-refresh"></i> Refresh
			</button>
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
			<?php if ( $booking->isSessionUnit( 'hours', 'minutes' ) ) : ?>
				<p>
					<label>
						<?php echo $booking->getBookingDuration() === 'fixed'? 'Booking' : 'Start' ?>
						Time:
					</label>
					<select name="edd_bk_time"></select>
				</p>
			<?php endif; ?>

			<?php if ( $booking->getBookingDuration() !== 'fixed' ) : ?>
				<?php
					$min = $booking->getMinSessions();
					$max = $booking->getMaxSessions();
					$step = $booking->getSessionLength();
				?>
				<p>
					<label>Duration:</label>
					<input name="edd_bk_num_slots" type="number" step="<?php echo $step ?>" min="<?php echo $min ?>" max="<?php echo $max ?>" value="<?php echo $min ?>" required />
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
	 * DEBUGGING
	 *
	 * Prints the booking data structure and session data.
	 * ----------------------------------------------------------------------
	 */
	if ( !defined( 'EDD_BK_DEBUG' ) || !EDD_BK_DEBUG ) return; ?>

	<hr />
	<h4>This Download's Booking Data</h4>
	<div style="zoom: 0.8"><?php var_dump( $booking ); ?></div>

	<hr />
	<h4>Session</h4>
	<div style="zoom: 0.8"><?php var_dump( $_SESSION ); ?></div>
