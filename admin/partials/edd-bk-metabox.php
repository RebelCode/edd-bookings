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

$display	= $enabled ? '' : ' style="display: none;"';
$slots_type = $slots_type ? $slots_type : 'fixed';

// Use nonce for verification
$edd_bk_nonce = wp_create_nonce( basename( __FILE__ ) ); ?>
<input type="hidden" name="edd_bk_meta_box_nonce" value="<?php echo $edd_bk_nonce; ?>" />


<!-- ENABLER -->
<p>
	<input type="checkbox" name="edd_bk_enabled" id="edd_bk_enabled" value="1" <?php echo checked( true, $enabled ); ?> />
	<label for="edd_bk_enabled">
		<?php _e( 'Check to enable booking for this download', 'edd_bk' ); ?>
	</label>
</p>

<!-- AVAILABILITY OPTIONS -->
<fieldset id="edd-bk-availability-section" class="edd-bk-option-section">
	<legend>Availability Options:</legend>

	<!-- DATES AND TIMES -->
	<p>
		<input type="date" name="edd_bk_start_date" id="edd_bk_start_date" value="<?php echo esc_attr( $start_date ); ?>" />
		<input type="time" name="edd_bk_start_time" id="edd_bk_start_time" value="<?php echo esc_attr( $start_time ); ?>" />
		From <i class="fa fa-fw fa-long-arrow-right"></i> Till
		<input type="time" name="edd_bk_end_time" id="edd_bk_end_time" value="<?php echo esc_attr( $end_time ); ?>" />
		<input type="date" name="edd_bk_end_date" id="edd_bk_end_date" value="<?php echo esc_attr( $end_date ); ?>" />
	</p>

	<!-- ALL DAY OPTION -->
	<p>
		<!-- ALL DAY OPTION -->
		<input type="checkbox" name="edd_bk_all_day" id="edd_bk_all_day" <?php echo checked( true, $all_day ); ?> />
		<label for="edd_bk_all_day">
			<?php _e( 'All day event', 'edd_bk' ); ?> -
			<span class="description"><?php _e( 'this event does not require start and end times', 'edd_bk' ); ?></span>.
		</label>
	</p>
</fieldset>


<!-- BOOKING OPTIONS -->
<fieldset id="edd-bk-bookings-section" class="edd-bk-option-section">
	<legend>Booking Options:</legend>

	<p>
		<label for="edd_bk_slots_type_fixed">Booking slot durations</label>
		<input type="radio" id="edd_bk_slots_type_fixed" name="edd_bk_slots_type" value="fixed" <?php echo checked( 'fixed', $slots_type ); ?>>
		<label for="edd_bk_slots_type_fixed">are all fixed</label>
		<input type="radio" id="edd_bk_slots_type_variable" name="edd_bk_slots_type" value="variable" <?php echo checked( 'variable', $slots_type ); ?>>
		<label for="edd_bk_slots_type_variable">can be defined by the customer</label>
	</p>

	<p>
		<label for="edd_bk_fixed_slot_duration">
			Duration of each slot:
		</label>
		<input type="number" min="1" step="1" id="edd_bk_fixed_slot_duration" name="edd_bk_fixed_slot_duration" value="<?php echo esc_attr( $fixed_slot_duration ); ?>" />
		
		<select>
			<option>Hours</option>
			<option>Minutes</option>
			<option>Days</option>
			<option>Weeks</option>
			<option>Months</option>
		</select>
	</p>

	<p class="edd_bk_variable_slots_section">
		<label for="edd_bk_slot_min_duration">
			Number of slots:
		</label>

		<input type="number" placeholder="Minimum" min="1" step="1" id="edd_bk_slot_min_duration" name="edd_bk_slot_min_duration" value="<?php echo esc_attr( $min_slot_duration ); ?>" />
		<i class="fa fa-fw fa-long-arrow-right"></i>
		<input type="number" placeholder="Maximum" min="0" step="1" id="edd_bk_slot_max_duration" name="edd_bk_slot_max_duration" value="<?php echo esc_attr( $max_slot_duration ); ?>" />
	</p>

</fieldset>