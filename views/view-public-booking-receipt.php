<?php
	$time_format = get_option('time_format');
	$date_format = get_option('date_format');
?>
<h3>
	<?php _e( 'Bookings', EDD_Bookings::TEXT_DOMAIN ); ?>
</h3>

<table>
	<thead>
		<tr>
			<th>Service</th>
			<th>Start</th>
			<th>End</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($bookings as $booking) : ?>
			<tr>
				<?php
					// Prepare str_to_time format for calculating end times
					$str_to_time_format = sprintf('+%1$s %2$s', $booking->getDuration(), $booking->getSessionUnit() );
					// Prepare check for time units
					$has_time = $booking->isSessionUnit( EDD_BK_Session_Unit::HOURS, EDD_BK_Session_Unit::MINUTES );
				?>
				<td>
					<strong>
						<?php echo get_the_title( $booking->getDownloadId() ) ?>
					</strong>
				</td>

				<td>
					<?php
						// Print time if booking has time
						if ( $has_time ) {
							date( $time_format, $booking->getLocalTime() );
						}
						// Print date
						echo date( $date_format, $booking->getDate() );
					?>
				</td>
				
				<td>
					<?php
						// Print time if booking has time
						if ( $has_time ) {
							echo date( $time_format, strtotime( $str_to_time_format, $booking->getLocalTime() ) );
						}
						// Print date
						echo date( $date_format, strtotime( $str_to_time_format, $booking->getDate() ) );
					?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
