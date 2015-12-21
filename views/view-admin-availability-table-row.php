<?php

/**
 * View file for a single row in the availability builder.
 *
 * @since 1.0.0
 * @version 1.0.0
 * @package EDD_Booking\Views
 */

$admin = EDD_Bookings::get_instance()->get_admin();
if ( ! isset( $entry ) ) {
	$entry = new Aventura_Bookings_Service_Availability_Entry( 'Days', 1, 1, true);
}
?>
<tr>

	<?php // The sorting handle ?>
	<td class="edd-bk-sort-td">
		<i class="fa fa-fw fa-lg fa-arrows-v"></i>
	</td>


	<?php
	/**
	 * RANGE TYPE
	 *
	 * The range type is a dropdown list, containing various different types of ranges. The user is to pick a range type, which
	 * upon selection will change the contents of the "FROM" and "TO" cells in that row to reflect the data type expected for
	 * that range type.
	 * --------------------------------------------------------------------------------------------------------------------------
	 */
	?>
	<td class="edd-bk-range-type-td">
		<?php
			$range_types = Aventura_Bookings_Service_Availability_Entry_Range_Type::getAllGrouped();
			$options = array(
				'class'		=>	'edd-bk-range-type',
				'name'		=>	'edd_bk_availability[entries][range_type][]',
				'selected'	=>	$entry->getType()->getSlugName()
			);
			echo EDD_BK_Utils::array_to_select( $range_types, $options );
		?>
	</td>


	<?php
	/**
	 * FROM + TO
	 *
	 * The from and to cells show the form controls required to allow the user to specify an availability range, for the chosen
	 * range type. This section uses a custom JS algorithm that utilizes the `data-if` attribute. The JS code will check
	 * this attribute and treat it as a pipe-delimited list of values for which the section will be visible if that value is
	 * selected by the range type dropdown.
	 * --------------------------------------------------------------------------------------------------------------------------
	 */
	$parts = array('from', 'to');
	foreach ( $parts as $part ) : ?>
		<td class="edd-bk-from-to">
			<?php
				$name = esc_attr( "edd_bk_availability[entries][$part][]" );
				$method = 'get' . ucfirst( $part ); // 'getFrom' or 'getTo'
				$value = $entry->$method();
			?>

			<?php // Fields shown if the selected range is of type 'days' ?>
			<div data-if="days">
				<?php
					$dayNames = Aventura_Bookings_Utils_Dates::dayNames();
					$selected = Aventura_Bookings_Utils_Dates::dotwNameFromIndex( $value );
					echo EDD_BK_Utils::array_to_select(
						$dayNames, array(
							'name'		=>	$name,
							'class'		=>	'edd-bk-avail-input',
							'selected'	=>	$selected
						)
					);
					?>
			</div>

			<?php // Fields shown if the selected range is of type 'weeks' ?>
			<div data-if="weeks">
				<?php
					$weekNum = intval( date('W', $value) );
					$clippedWeekNum = min( 1, max(52, $weekNum) );
				?>
				Week #<input type="number" min="1" step="1" max="52" name="<?php echo $name; ?>" class="edd-bk-week-num edd-bk-avail-input" value="<?php echo $clippedWeekNum; ?>" />
			</div>

			<?php // Fields shown if the selected range is of type 'months' ?>
			<div data-if="months">
				<?php
					$monthNames = Aventura_Bookings_Utils_Dates::monthNames();
					$selected = Aventura_Bookings_Utils_Dates::monthNameFromIndex( $value );
					echo EDD_BK_Utils::array_to_select(
						$monthNames, array(
							'name'		=>	$name,
							'class'		=>	'edd-bk-avail-input',
							'selected'	=>	$selected
						)
					);
				?>
			</div>
			
			<?php // Fields shown if the selected range uses 'time' fields ?>
			<div data-if="[Time]|[Time Groups]">
				<input type="time" class="edd-bk-avail-input" name="<?php echo $name; ?>" value="<?php echo date( 'H:i', $value ); ?>"/>
				<?php
					$tooltip = 'Use 24-hour format.<br/><br/>';
					if ( $part === 'from' ) {
						$tooltip .= __( 'Leave this field empty to indicate from the beginning of the day.', EDD_Bookings::TEXT_DOMAIN );
					} else {
						$tooltip .= __( 'Leave this field empty to indicate until the end of the day.', EDD_Bookings::TEXT_DOMAIN );
					}
					$tooltip .= ' '. __( 'If the other field is also left empty, the whole day will matched.', EDD_Bookings::TEXT_DOMAIN );
					echo $admin->help_tooltip( $tooltip );
				?>
			</div>
			
			<?php // Fields shown if the selected range is 'custom' ?>
			<div data-if="custom">
				<?php
					// If value is NULL or the date has passed, use the curent time
					$now = time();
					if ( $value === NULL || $value < $now ) $value = $now;
				?>
				<input type="text" class="edd-bk-avail-input edd-bk-datepicker" name="<?php echo $name; ?>" value="<?php echo date('n/j/Y', $value); ?>"/>
				<i class="fa fa-calendar"></i>
			</div>
		</td>
	<?php endforeach; ?>


	<?php
	/**
	 * FROM + TO
	 *
	 * The availability checkbox allows the user to specify if the entered range is a time range of availability or
	 * unavailability. In other words, if the range is bookable or not by customers.
	 * --------------------------------------------------------------------------------------------------------------------------
	 */
	?>
	<td class="edd-bk-available-td">
		<input type="checkbox" class="edd_bk_availability_checkbox" <?php checked( true, $entry->isAvailable() );  ?> />
		<input type="hidden" name="edd_bk_availability[entries][available][]" value="1" />
	</td>

	<td class="edd-bk-help-td">
		<?php echo $admin->help_tooltip( '' ); ?>
	</td>


	<?php // The Remove icon ?>
	<td class="edd-bk-remove-td">
		<i class="fa fa-fw fa-lg fa-times"></i>
	</td>
</tr>