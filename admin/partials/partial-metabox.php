<?php

/**
 * Contains the PHP and HTML rendering of the EDD Booking Metabox.
 *
 * @since 1.0.0
 * @package EDD_BK
 * @subpackage EDD_BK/admin
 */

global $post;

// Get the names of the meta fields
$meta_fields = self::meta_fields();
// Generate a new meta array, containing the actual meta values from the database
$meta = array();
foreach ($meta_fields as $i => $field) {
	// key: field name from $meta_fields
	// value: meta value from db
	$meta[$field] = get_post_meta( $post->ID, 'edd_bk_'.$field, true );
}
// Extract the meta fields into variables
extract($meta);

$duration_type = $duration_type ? $duration_type : 'fixed';
$price_type = $price_type ? $price_type : 'fixed';

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
<p>
	<input type="checkbox" name="edd_bk_enabled" id="edd_bk_enabled" value="1" <?php echo checked( true, $enabled ); ?> />
	<label for="edd_bk_enabled">
		<?php _e( 'Check to enable booking for this download', 'edd_bk' ); ?>
		<?php echo $admin->help_tooltip('This enables booking functionality for this download.'); ?>
	</label>
</p>

<?php
/**
 *	THE AVAILABILITIES SECTION.
 *
 *	In this section, the user should be able to set up the availabile times in which his/her
 *	customers can book. The options consist of an availability filler, which allows the user to
 *	add multiple entries that reflect the available and unavailable times.
 *	This section also consists of options that allow the user to set up their booking mechanism.
 *	This involves the user entering information about how his booking use case works, such as
 *	duration of bookings, simultaneous bookings, and customer defined flexibility.
 *	-----------------------------------------------------------------------------------------------
 */
?>
<fieldset id="edd-bk-availability-section" class="edd-bk-option-section">
	<legend>Availability</legend>

	<p>
		<label for="edd_bk_fixed_duration" class="edd-bk-fw">Booking Duration</label>

		<input type="radio" id="edd_bk_fixed_duration" name="edd_bk_duration_type" value="fixed" <?php echo checked( 'fixed', $duration_type ); ?>>
		<label for="edd_bk_fixed_duration">Fixed duration</label>
		&nbsp;
		<input type="radio" id="edd_bk_variable_duration" name="edd_bk_duration_type" value="variable" <?php echo checked( 'variable', $duration_type ); ?>>
		<label for="edd_bk_variable_duration">Customer can choose</label>
		<?php echo $admin->help_tooltip('Choose whether each booking\'s duration is always the same, or if the customer can choose the duration of the booking, in terms of slots.'); ?>
	</p>

	<p class="edd_bk_variable_slots_section">
		<label for="edd_bk_min_slots" class="edd-bk-fw">
			Customer can book from
		</label>

		<input type="number" placeholder="Minimum" min="1" step="1" id="edd_bk_min_slots" name="edd_bk_min_slots" value="<?php echo esc_attr( $min_slots ); ?>" />
		to
		<input type="number" placeholder="Maximum" min="0" step="1" id="edd_bk_max_slots" name="edd_bk_max_slots" value="<?php echo esc_attr( $max_slots ); ?>" />
		slots.
		<?php echo $admin->help_tooltip('The range of number of slots that a customer can book.'); ?>
	</p>

	<p>
		<label for="edd_bk_slot_duration" class="edd-bk-fw">
			Where each slot is
		</label>
		<input type="number" min="1" step="1" id="edd_bk_slot_duration" name="edd_bk_slot_duration" value="<?php echo esc_attr( $slot_duration ); ?>" />
		
		<select name="edd_bk_slot_duration_unit">
			<option value="hours">Hour(s)</option>
			<option value="minutes">Minute(s)</option>
			<option value="days">Day(s)</option>
			<option value="weeks">Week(s)</option>
			<option value="months">Month(s)</option>
			<option value="years">Year(s)</option>
		</select>

		<?php echo $admin->help_tooltip("Each slot's duration, i.e. How long each slot lasts."); ?>
	</p>

	<table class="widefat edd-bk-avail-table">
		<thead>
			<tr>
				<th id="edd-bk-sort-col"></th>
				<th id="edd-bk-range-type-col">Range Type</th>
				<th id="edd-bk-from-col">From</th>
				<th id="edd-bk-to-col">To</th>
				<th id="edd-bk-avail-col">
					Available
					<?php echo $admin->help_tooltip('If a range is available, it can be booked by customers. If not available, then it is not bookable.'); ?>
				</th>
				<th id="edd-bk-remove-col"></th>
			</tr>
		</thead>
		<tbody>
			<?php
				if ( is_array( $availability ) && count( $availability ) > 0 ) {
					foreach ( $availability as $range ) {
						include EDD_BK_ADMIN_PARTIALS_DIR.'partial-availability-table-row.php';
					}
				}
			?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="4">
					<span class="description">Rules further down the table will override those at the top.</span>
					<br/>
					<label>
						Dates not included above are
					</label>
					<?php
						if ( $availability_fill === '' ) $availability_fill = 'false';
						echo EDD_BK_Utils::array_to_select(
							array( 'true' => 'Available', 'false' => 'Not Available' ),
							array(
								'id'		=>	'edd-bk-availability-fill',
								'name'		=>	'edd_bk_availability_fill',
								'selected'	=>	$availability_fill
							)
						);
						echo $admin->help_tooltip( 'Use this option to choose whether the dates, that do not fall under the ranges specified above, are available or not. <hr/>For instance, if it is easier to specifiy when you are <em>not</em> available, set this option to <em>Available</em> and use the table to choose the dates that are unavailable.' );
					?>
				</th>
				<th colspan="2">
					<button id="edd-bk-avail-add-btn" class="button button-primary button-large" type="button">
						<i class="fa fa-fw fa-plus"></i> Add Range
					</button>
				</th>
			</tr>
		</tfoot>
	</table>

	<p>Learn <a href="#">how to use the Availability Table</a></p>

</fieldset>


<?php
/**
 *	THE COSTS SECTION.
 *
 *	This section allows the user to enter the costing parameters for bookings. Options such as
 *  cost per person, are dependant on options set in previous sections.
 *	-----------------------------------------------------------------------------------------------
 */
?>
<fieldset id="edd-bk-pricing-section" class="edd-bk-option-section">
	<legend>Costs</legend>

	<p>
		<label for="" class="edd-bk-fw">The price is</label>

		<input type="radio" id="edd_bk_fixed_pricing" name="edd_bk_price_type" value="fixed" <?php echo checked( 'fixed', $price_type ); ?>>
		<label for="edd_bk_fixed_pricing">always the same</label>
		&nbsp;
		<input type="radio" id="edd_bk_variable_pricing" name="edd_bk_price_type" value="variable" <?php echo checked( 'variable', $price_type ); ?>>
		<label for="edd_bk_variable_pricing">calculated per slot</label>

		<?php echo $admin->help_tooltip('Choose whether the price is the same, regardless of how many slots are booked, or depends on the number of slots booked.'); ?>
	</p>

	<p>
		<label for="edd_bk_base_cost" class="edd-bk-fw">Cost:</label>
		<input type="text" id="edd_bk_base_cost" name="edd_bk_base_cost" value="<?php echo esc_attr( $base_cost ); ?>" />

		<?php echo $admin->help_tooltip("The cost of the booking. If you've set the booking price to depend on the number of slots, then this will be the base cost."); ?>
	</p>

	<p class="edd-bk-variable-pricing-section">
		<label for="edd_bk_cost_per_slot" class="edd-bk-fw">Cost per slot</label>
		<input type="text" id="edd_bk_cost_per_slot" name="edd_bk_cost_per_slot" value="<?php echo esc_attr( $cost_per_slot ); ?>" />

		<?php echo $admin->help_tooltip("The cost of each slot. The calculated price will be this amount, times each booked slot, added to the base cost."); ?>
	</p>

	<p class="edd-bk-variable-pricing-section">
		<label class="edd-bk-fw">Total cost:</label class="edd-bk-fw">
		<code id="edd-bk-total-cost-preview"></code>
	</p>

</fieldset>
