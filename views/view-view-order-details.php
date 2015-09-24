<div id="edd-bk-view-order-details" class="postbox">
	<h3 class="hndle">
		<span>Bookings</span>
	</h3>
	<div class="inside edd-clearfix">
		<table class="widefat">
			<thead>
				<tr>
					<th>Service</th>
					<th>Date and Time</th>
					<th>Duration</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $viewbag->bookings as $booking ) : ?>
					<tr>
						<td>
							<?php echo get_the_title( $booking->getServiceId() ); ?>
						</td>
						<td>
							<?php
								$unit = $booking->getSessionUnit();
								if ( $unit === EDD_BK_Session_Unit::HOURS || $unit === EDD_BK_Session_Unit::MINUTES ) {
									$time_format = get_option( 'time_format', 'H:i' );
									echo date( $time_format, $booking->getTime() );
									echo ', ';
								}
								$date_format = get_option( 'date_format', 'H:i' );
								echo date( $date_format, $booking->getDate() );
							?>
						</td>
						<td>
							<?php printf( '%s %s', $booking->getDuration(), $unit ); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>