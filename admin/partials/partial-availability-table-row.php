<?php

$admin = EDD_Booking::get_instance()->get_admin();

if ( ! isset( $range ) ) {
	$range = array();
}
$range = wp_parse_args( $range, array(
	'type'		=>	'',
	'from'		=>	'',
	'to'		=>	'',
	'available'	=>	''
) );
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
			$range_types = array (
				'Common'		=> array( 
					'days'			=>	'Days',
					'weeks'			=>	'Weeks',
					'months'		=>	'Months',
					'custom'		=>	'Custom',
				),
				'Days'			=>	EDD_BK_Utils::day_options(),
				'Day Groups'	=>	array(
					'allweek'		=>	'All Week',
					'weekdays'		=>	'Weekdays',
					'weekend'		=>	'Weekend'
				)
			);
			$options = array(
				'class'		=>	'edd-bk-range-type',
				'name'		=>	'edd_bk_availability[range_types][]',
				'selected'	=>	$range['type']
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
		<td class="edd-bk-from-td">
			<?php
				$name = esc_attr( "edd_bk_availability[range_$part][]" );
				$value = $range[ $part ];
			?>
			<div data-if="days">
				<?php echo EDD_BK_Utils::array_to_select( EDD_BK_Utils::day_options(), array(
							'name'		=>	$name,
							'selected'	=>	$value
				) ); ?>
			</div>

			<div data-if="weeks">
				Week #<input type="number" min="1" step="1" max="52" name="<?php echo $name; ?>" class="edd-bk-week-num" value="<?php echo $value; ?>" />
			</div>

			<div data-if="months">
				<?php echo EDD_BK_Utils::array_to_select( EDD_BK_Utils::month_options(), array(
							'name'		=>	$name,
							'selected'	=>	$value
				) ); ?>
			</div>
			
			<div data-if="allweek|weekdays|weekend|[Days]">
				<input type="time" name="<?php echo $name; ?>" value="<?php echo $value; ?>"/>
				<?php
					$tooltip = '';
					if ( $part === 'from' ) {
						$tooltip = 'Leave this field empty to indicate from the beginning of the day.';
					} else {
						$tooltip = 'Leave this field empty to indicate until the end of the day.';
					}
					$tooltip .= ' If the other field is also left empty, the whole day will match.';
					echo $admin->help_tooltip( $tooltip );
				?>
			</div>
			
			<div data-if="custom">
				<input type="date" name="<?php echo $name; ?>" value="<?php echo $value; ?>"/>
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
		<input type="checkbox" class="edd_bk_availability_checkbox" <?php checked( 'true', $range['available'] );  ?> />
		<input type="hidden" name="edd_bk_availability[range_available][]" value="true" />
	</td>


	<?php // The Remove icon ?>
	<td class="edd-bk-remove-td">
		<i class="fa fa-fw fa-lg fa-times"></i>
	</td>
</tr>