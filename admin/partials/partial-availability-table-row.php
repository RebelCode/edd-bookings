<?php
$admin = EDD_Booking::get_instance()->get_admin();
if ( ! isset( $entry ) ) {
	$entry = new EDD_BK_Availability_Entry_Days( null, '', '', false );
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
			$range_types = EDD_BK_Availability_Range_Type::get_all_grouped();
			$options = array(
				'class'		=>	'edd-bk-range-type',
				'name'		=>	'edd_bk_availability[range_types][]',
				'selected'	=>	$entry->getType()->get_slug_name()
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
				$name = esc_attr( "edd_bk_availability[range_$part][]" );
				$method = 'get' . ucfirst( $part ); // 'getFrom' or 'getTo'
				$value = $entry->$method();
			?>
			<div data-if="days">
				<?php
					$day_options = EDD_BK_Utils::day_options();
					$day_options_keys = array_keys( $day_options );
					$selected = $day_options_keys[ ($value + 6) % 7 ];
					/* Dev Note:
					 * (($value + 6) % 7) is to shift the index of the days, since $value is an int in the range [0,6] with
					 * 0 representing Sunday. EDD_BK_Utils::day_options() has Monday at index 0. The (+6) moves each day to
					 * the previous one in the week and (%7) keeps the numbers in the range [0,6].
					 * E.g. value = 2 (Wednesday)	>>	(2 + 6) % 7 = 8 % 7 = 1 (Tuesday)
					 */
					echo EDD_BK_Utils::array_to_select(
						$day_options, array(
							'name'		=>	$name,
							'class'		=>	'edd-bk-avail-input',
							'selected'	=>	$selected
						)
					);
					?>
			</div>

			<div data-if="weeks">
				Week #<input type="number" min="1" step="1" max="52" name="<?php echo $name; ?>" class="edd-bk-week-num" value="<?php echo date( 'W', $value ); ?>" />
			</div>

			<div data-if="months">
				<?php
				$month_options = EDD_BK_Utils::month_options();
					$month_options_keys = array_keys( $month_options );
					$selected = $month_options_keys[ $value ];
					echo EDD_BK_Utils::array_to_select(
						$month_options, array(
							'name'		=>	$name,
							'class'		=>	'edd-bk-avail-input',
							'selected'	=>	$selected
						)
					);
				?>
			</div>
			
			<div data-if="all_week|weekdays|weekend|[Days]">
				<input type="time" class="edd-bk-avail-input" name="<?php echo $name; ?>" value="<?php echo date( 'H:i', $value ); ?>"/>
				<?php
					$tooltip = 'Use 24-hour format: hours:minutes.<br/>';
					if ( $part === 'from' ) {
						$tooltip .= 'Leave this field empty to indicate from the beginning of the day.';
					} else {
						$tooltip .= 'Leave this field empty to indicate until the end of the day.';
					}
					$tooltip .= ' If the other field is also left empty, the whole day will match.';
					echo $admin->help_tooltip( $tooltip );
				?>
			</div>
			
			<div data-if="custom">
				<input type="text" class="edd-bk-avail-input edd-bk-datepicker" name="<?php echo $name; ?>" value="<?php echo date('m/d/Y', $value); ?>"/>
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
		<input type="hidden" name="edd_bk_availability[range_available][]" value="true" />
	</td>

	<td class="edd-bk-help-td">
		<?php echo $admin->help_tooltip( "All <code>Mondays</code> from <code>12:00</code> to <code>17:00</code>." ); ?>
	</td>


	<?php // The Remove icon ?>
	<td class="edd-bk-remove-td">
		<i class="fa fa-fw fa-lg fa-times"></i>
	</td>
</tr>