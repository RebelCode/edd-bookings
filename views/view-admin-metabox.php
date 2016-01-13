<?php

/**
 * View file for rendering of the EDD Booking Metabox in the Downloads New/Edit page.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Booking\Views
 */

global $post;

// Get the download from the post ID
$download = edd_bk()->get_downloads_controller()->get( $post->ID );

// Use nonce for verification
wp_nonce_field( 'edd_bk_saving_meta', 'edd_bk_meta_nonce' );


/**
 * THE ENABLER
 *
 * This toggles the booking functionality on and off for this particular download.
 * When toggled on, the other options should appear to the user, while when toggled off, the
 * other options should be hidden, to prevent cluttering the UI with unneeded options.
 * -----------------------------------------------------------------------------------------------
 */
?>
<div class="edd-bk-p-div">
	<input type="checkbox" name="edd_bk_enabled" id="edd_bk_enabled" value="1" <?php echo checked( true, $download->isEnabled() ); ?> />
	<label for="edd_bk_enabled">
		<?php _e( 'Enable booking for this download', EDD_Bookings::TEXT_DOMAIN ); ?>
		<?php echo $admin->help_tooltip('This enables booking functionality for this download.'); ?>
	</label>
</div>

<?php
/**
 *	THE SESSION DETAILS SECTION.
 *
 *	In this section, the user can set up their sessions. The fields in this section relate to the
 *	sessions' length, cost and variability.
 *	-----------------------------------------------------------------------------------------------
 */ ?>
<fieldset id="edd-bk-sessions-section" class="edd-bk-option-section">
	<legend><?php _e( 'Session Details', EDD_Bookings::TEXT_DOMAIN ); ?></legend>

	<?php // Session Length and Unit ?>
	<div>
		<label for="edd_bk_session_length" class="edd-bk-fw">
			<?php _e( 'Session length', EDD_Bookings::TEXT_DOMAIN ); ?></label>
		<input type="number" min="1" step="1" id="edd_bk_session_length" name="edd_bk_session_length" value="<?php echo esc_attr( $download->getSessionLength() ); ?>" />

		<?php
			$all_units = array_values( Aventura_Bookings_Service_Session_Unit::getAll() );
			$all_units = array_combine( $all_units, $all_units );
			echo EDD_BK_Utils::array_to_select(
					$all_units,
					array(
						'name'		=>	'edd_bk_session_unit',
						'selected'	=>	$download->getSessionUnit()
					)
			);
		?>

		<?php
			echo $admin->help_tooltip(
				__(
					'Set how long a single session lasts. A "session" can either represent a single booking or a part of a booking, and can be anything from an hour, 15 minutes, to a whole day or even a number of weeks, depending on your use case.',
					EDD_Bookings::TEXT_DOMAIN
				)
			);
		?>
	</div>

	<?php // Session Cost ?>
	<div class="edd-bk-variable-pricing-section">
		<label for="edd_bk_session_cost" class="edd-bk-fw">
			<?php _e( 'Cost per session', EDD_Bookings::TEXT_DOMAIN ); ?>
		</label>
		<span class="edd-bk-price-currency"><?php echo edd_currency_symbol(); ?></span>
		<input type="text" id="edd_bk_session_cost" name="edd_bk_session_cost" value="<?php echo esc_attr( $download->getSessionCost() ); ?>" />
		<?php
			echo $admin->help_tooltip(
				__( 
					'The cost of each session. The calculated price will be this amount times each booked session, added to the base cost.',
					EDD_Bookings::TEXT_DOMAIN
				)
			);
		?>
	</div>

	<?php // Booking duration in terms of sessions ?>
	<div>
		<label for="edd_bk_fixed_duration" class="edd-bk-fw">
			<?php _e( 'Booking duration', EDD_Bookings::TEXT_DOMAIN ); ?>
		</label>
		<input type="radio" id="edd_bk_fixed_duration" name="edd_bk_session_type" value="fixed" <?php echo checked( 'fixed', $download->getSessionType() ); ?>>
		<label for="edd_bk_fixed_duration">
			<?php _e( 'Single session', EDD_Bookings::TEXT_DOMAIN ); ?>
		</label>
		&nbsp;
		<input type="radio" id="edd_bk_variable_duration" name="edd_bk_session_type" value="variable" <?php echo checked( 'variable', $download->getSessionType() ); ?>>
		<label for="edd_bk_variable_duration">
			<?php _e( 'Multiple sessions', EDD_Bookings::TEXT_DOMAIN ); ?>
		</label>
		<?php
			echo $admin->help_tooltip(
				__(
					'Choose whether your customers can only book a single session or if they can choose to book more than one session. The latter will make the bookings vary in duration according to the customer.',
					EDD_Bookings::TEXT_DOMAIN
				)
			);
		?>
	</div>

	<?php // Hidden options for booking duration, shown on selected of variable booking durations ?>
	<div class="edd_bk_variable_slots_section">
		<label for="edd_bk_min_sessions" class="edd-bk-fw">
			<?php _e( 'Customer can book from', EDD_Bookings::TEXT_DOMAIN ); ?>
		</label>

		<input type="number" placeholder="Minimum" min="1" step="1" id="edd_bk_min_sessions" name="edd_bk_min_sessions" value="<?php echo esc_attr( $download->getMinSessions() ); ?>" />
		to
		<input type="number" placeholder="Maximum" min="1" step="1" id="edd_bk_max_sessions" name="edd_bk_max_sessions" value="<?php echo esc_attr( $download->getMaxSessions() ); ?>" />
		sessions.
		<?php echo $admin->help_tooltip('The range of number of sessions that a customer can book.'); ?>
	</div>
	
</fieldset>

<?php
/**
 *	THE AVAILABILITY BUILDER SECTION.
 *
 *	In this section, the user can set up their availability. The fields in this section include an
 *	availability filler option and a table where users can enter rules that define what dates and
 *	times customers are allowed to book.
 *	-----------------------------------------------------------------------------------------------
 */ ?>
<fieldset id="edd-bk-availability-section" class="edd-bk-option-section">
	<legend>
		<?php _e( 'Calendar Builder', EDD_Bookings::TEXT_DOMAIN ); ?>
	</legend>

	<div>
		<?php
			$fill_enabled = apply_filters( 'edd_bk_availability_fill_enabled', false );
			$fill_field_name = 'edd_bk_availability[fill]';
			$avail_fill = ( $download->getAvailability()->getFill() === TRUE )? 'true' : 'false';
			if ( $fill_enabled ) : ?>
				<label>
					<?php _e( 'Dates not included in the below ranges are', EDD_Bookings::TEXT_DOMAIN ); ?>
				</label>
				<?php
				echo EDD_BK_Utils::array_to_select(
					array(
						'true' => __( 'available', EDD_Bookings::TEXT_DOMAIN ),
						'false' => __( 'not available', EDD_Bookings::TEXT_DOMAIN )
					),
					array(
						'id'		=>	'edd-bk-availability-fill',
						'name'		=>	$fill_field_name,
						'selected'	=>	$avail_fill
					)
				);
				echo $admin->help_tooltip(
					__(
						'Use this option to choose whether the dates that do not fall under the below ranges are available or not.
						<hr/>
						For instance, if it is easier to specifiy when you are <em>not</em> available,
						set this option to <em>Available</em> and use the table to choose the dates that are unavailable.',
						EDD_Bookings::TEXT_DOMAIN
					)
				);
			else : ?>
				<input type="hidden" name="<?php echo $fill_field_name; ?>" value="<?php echo $avail_fill; ?>" />
		<?php endif; ?>
	</div>

	<table class="widefat edd-bk-avail-table">
		<thead>
			<tr>
				<th id="edd-bk-sort-col"></th>
				<th id="edd-bk-range-type-col">
					<?php _e( 'Range Type', EDD_Bookings::TEXT_DOMAIN ); ?>
				</th>
				<th id="edd-bk-from-col">
					<?php _e( 'From', EDD_Bookings::TEXT_DOMAIN ); ?>
				</th>
				<th id="edd-bk-to-col">
					<?php _e( 'To', EDD_Bookings::TEXT_DOMAIN ); ?>
				</th>
				<th id="edd-bk-avail-col">
					<?php _e( 'Available', EDD_Bookings::TEXT_DOMAIN ); ?>
				</th>
				<th id="edd-bk-help-col">
					<?php _e( 'Help', EDD_Bookings::TEXT_DOMAIN ); ?>
				</th>
				<th id="edd-bk-remove-col"></th>
			</tr>
		</thead>
		<tbody>
			<?php
				$entries = $download->getAvailability()->getEntries();
				foreach ( $entries as $i => $entry ) {
					include EDD_BK_VIEWS_DIR . 'view-admin-availability-table-row.php';
				}
			?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="5">
					<span class="description">
						<?php _e( 'Rules further down the table will override those at the top.', EDD_Bookings::TEXT_DOMAIN ); ?>
					</span>
				</th>
				<th colspan="2">
					<button id="edd-bk-avail-add-btn" class="button button-primary button-large" type="button">
						<?php _e( 'Add Rule', EDD_Bookings::TEXT_DOMAIN ); ?>
					</button>
				</th>
			</tr>
		</tfoot>
	</table>

	<?php // <p><a id="edd-bk-avail-checker" href="#edd-bk-avail-checker">I want to check if this makes sense</a></p> ?>

</fieldset>


<?php
/**
 *	THE DISPLAY OPTIONS SECTION.
 *
 *	In this section, the user can configure the frontend display for this particular Download.
 *	-----------------------------------------------------------------------------------------------
 */ ?>
<fieldset id="edd-bk-display-section" class="edd-bk-option-section">
	<legend>
		<?php _e( 'Display Options', EDD_Bookings::TEXT_DOMAIN ); ?>
	</legend>
	<div>
		<label for="edd_bk_multi_view_output" class="edd-bk-fw">
			<?php _e( 'Show calendar in multiviews', EDD_Bookings::TEXT_DOMAIN ); ?>
		</label>
		<input type="hidden" name="edd_bk_multi_view_output" value="0" />
		<input type="checkbox" id="edd_bk_multi_view_output" name="edd_bk_multi_view_output" value="1" <?php checked($download->isEnabledMultiViewOutput(), TRUE); ?> />
	</div>
</fieldset>
