<?php

/**
 * Contains the PHP and HTML rendering of the EDD Booking Metabox.
 *
 * @since 1.0.0
 * @package EDD_BK
 * @subpackage EDD_BK/admin
 */

global $post;

// Get meta data
$enabled	= get_post_meta( $post->ID, 'edd_bk_enabled', true ) ? true : false;
$start_date	= get_post_meta( $post->ID, 'edd_bk_start_date', true );
$start_time	= get_post_meta( $post->ID, 'edd_bk_start_time', true );
$end_date	= get_post_meta( $post->ID, 'edd_bk_end_date', true );
$end_time	= get_post_meta( $post->ID, 'edd_bk_end_time', true );
$all_day	= get_post_meta( $post->ID, 'edd_bk_all_day', true );

$slots_type				= get_post_meta( $post->ID, 'edd_bk_slots_type', true );
$fixed_slot_duration	= get_post_meta( $post->ID, 'edd_bk_fixed_slot_duration', true );
$min_slot_duration		= get_post_meta( $post->ID, 'edd_bk_min_slot_duration', true );
$max_slot_duration		= get_post_meta( $post->ID, 'edd_bk_max_slot_duration', true );
$slot_capacity			= get_post_meta( $post->ID, 'edd_bk_slot_capacity', true );

$pricing				= get_post_meta( $post->ID, 'edd_bk_pricing', true );
$base_cost				= get_post_meta( $post->ID, 'edd_bk_base_cost', true );
$cost_per_slot			= get_post_meta( $post->ID, 'edd_bk_cost_per_slot', true );

$display	= $enabled ? '' : ' style="display: none;"';
$slots_type = $slots_type ? $slots_type : 'fixed';
$pricing = $pricing ? $pricing : 'fixed';

// Use nonce for verification
$edd_bk_nonce = wp_create_nonce( basename( __FILE__ ) ); ?>
<input type="hidden" name="edd_bk_meta_box_nonce" value="<?php echo $edd_bk_nonce; ?>" />


<!-- ENABLER -->
<p>
	<input type="checkbox" name="edd_bk_enabled" id="edd_bk_enabled" value="1" <?php echo checked( true, $enabled ); ?> />
	<label for="edd_bk_enabled">
		<?php _e( 'Check to enable booking for this download', 'edd_bk' ); ?>
		<?php echo $admin->help_tooltip('This enables booking functionality for this download.'); ?>
	</label>
</p>

<!-- AVAILABILITY OPTIONS -->
<fieldset id="edd-bk-availability-section" class="edd-bk-option-section">
	<legend>Availability</legend>

	<!-- DATES AND TIMES -->
	<p>
		<input type="date" name="edd_bk_start_date" id="edd_bk_start_date" value="<?php echo esc_attr( $start_date ); ?>" />
		<input type="time" name="edd_bk_start_time" id="edd_bk_start_time" value="<?php echo esc_attr( $start_time ); ?>" />
		From <i class="fa fa-fw fa-long-arrow-right"></i> Till
		<input type="time" name="edd_bk_end_time" id="edd_bk_end_time" value="<?php echo esc_attr( $end_time ); ?>" />
		<input type="date" name="edd_bk_end_date" id="edd_bk_end_date" value="<?php echo esc_attr( $end_date ); ?>" />
		<?php echo $admin->help_tooltip('Enter the booking availability date and time range.'); ?>
	</p>

	<!-- ALL DAY OPTION -->
	<p>
		<!-- ALL DAY OPTION -->
		<input type="checkbox" name="edd_bk_all_day" id="edd_bk_all_day" <?php echo checked( true, $all_day ); ?> />
		<label for="edd_bk_all_day">
			<?php _e( 'All day event', 'edd_bk' ); ?>
			<?php echo $admin->help_tooltip('This event does not require starting and ending times.'); ?>
		</label>
	</p>
</fieldset>


<!-- BOOKING OPTIONS -->
<fieldset id="edd-bk-bookings-section" class="edd-bk-option-section">
	<legend>Booking Options:</legend>

	<p>
		<label class="edd-bk-fw">
			Bookings per slot
		</label>
		<input type="number" min="1" step="1" id="edd_bk_slot_capacity" name="edd_bk_slot_capacity" value="<?php echo esc_attr( $slot_capacity ); ?>" />
		<?php echo $admin->help_tooltip('The maximum number of bookings that can be booked in a single slot.'); ?>
	</p>

	<hr/>

	<p>
		<label for="edd_bk_slots_type_fixed" class="edd-bk-fw">Booking Duration</label>

		<input type="radio" id="edd_bk_slots_type_fixed" name="edd_bk_slots_type" value="fixed" <?php echo checked( 'fixed', $slots_type ); ?>>
		<label for="edd_bk_slots_type_fixed">Fixed duration</label>
		&nbsp;
		<input type="radio" id="edd_bk_slots_type_variable" name="edd_bk_slots_type" value="variable" <?php echo checked( 'variable', $slots_type ); ?>>
		<label for="edd_bk_slots_type_variable">Customer can choose</label>
		<?php echo $admin->help_tooltip('Choose whether each booking\'s duration is always the same, or if the customer can choose the duration of the booking, in terms of slots.'); ?>
	</p>

	<p>
		<label for="edd_bk_fixed_slot_duration" class="edd-bk-fw">
			Duration of each slot
		</label>
		<input type="number" min="1" step="1" id="edd_bk_fixed_slot_duration" name="edd_bk_fixed_slot_duration" value="<?php echo esc_attr( $fixed_slot_duration ); ?>" />
		
		<select>
			<option>Hours</option>
			<option>Minutes</option>
			<option>Days</option>
			<option>Weeks</option>
			<option>Months</option>
		</select>

		<?php echo $admin->help_tooltip("Each slot's duration, i.e. How long each slot lasts."); ?>
	</p>

	<p class="edd_bk_variable_slots_section">
		<label for="edd_bk_slot_min_duration" class="edd-bk-fw">
			Number of slots
		</label>

		<input type="number" placeholder="Minimum" min="1" step="1" id="edd_bk_slot_min_duration" name="edd_bk_slot_min_duration" value="<?php echo esc_attr( $min_slot_duration ); ?>" />
		<i class="fa fa-fw fa-long-arrow-right"></i>
		<input type="number" placeholder="Maximum" min="0" step="1" id="edd_bk_slot_max_duration" name="edd_bk_slot_max_duration" value="<?php echo esc_attr( $max_slot_duration ); ?>" />
		
		<?php echo $admin->help_tooltip('The range of number of slots that a customer can book.'); ?>
	</p>

</fieldset>


<!-- PRICING OPTIONS -->
<fieldset id="edd-bk-pricing-section" class="edd-bk-option-section">
	<legend>Costs</legend>

	<p>
		<label for="" class="edd-bk-fw">The price is</label>

		<input type="radio" id="edd_bk_fixed_pricing" name="edd_bk_pricing" value="fixed" <?php echo checked( 'fixed', $pricing ); ?>>
		<label for="edd_bk_fixed_pricing">always the same</label>
		&nbsp;
		<input type="radio" id="edd_bk_variable_pricing" name="edd_bk_pricing" value="variable" <?php echo checked( 'variable', $pricing ); ?>>
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