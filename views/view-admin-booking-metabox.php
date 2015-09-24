<?php $booking = EDD_Bookings::get_instance()->get_bookings_controller()->get( get_the_ID() ); ?>

<table class="widefat edd-bk-bookings-edit">
	<tbody>
		<tr>
			<td>ID</td>
			<td><?php echo $booking->getID() ?></td>
		</tr>
		<tr>
			<td>Service</td>
			<td>
				<?php
					$download_id = $booking->getDownloadId();
					$link = admin_url( 'post.php?action=edit&post=' . $download_id );
					$text = get_the_title( $download_id );
					echo "<a href=\"$link\">$text</a>";
				?>
			</td>
		</tr>
		<tr>
			<td>Customer</td>
			<td>
				<?php
					$customer = EDD_BK_Customers_Controller::get( $booking->getCustomerId() );
					$link = admin_url( 'edit.php?post_type=download&page=edd-customers&view=overview&id=' . $customer->getId() );
					echo "<a href=\"$link\">" . $customer->getName() . '</a>';
				?>
			</td>
		</tr>
		<tr>
			<td>Date</td>
			<td>
				<?php
					$date = $booking->getDate();
					$format = 'D jS M, Y';
					if ( $booking->isSessionUnit( EDD_BK_Session_Unit::HOURS, EDD_BK_Session_Unit::MINUTES  ) ) {
						$date += $booking->getTime();
						$format = 'h:ia ' . $format;
					}
					echo date( $format, $date );
				?>
			</td>
		</tr>
		<tr>
			<td>Duration</td>
			<td>
				<?php echo $booking->getDuration() . ' ' . $booking->getSessionUnit(); ?>
			</td>
		</tr>
		<tr>
			<td>Payment</td>
			<td>
				<?php
					$payment_id = $booking->getPaymentId();
					if ( $payment_id )
						printf( '<a href="%s">%s</a>', admin_url('edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $payment_id ), $payment_id );
				?>
			</td>
		</tr>
		<?php if ( EDD_BK_DEBUG ) : ?>
			<tr>
				<td>Raw Data</td>
				<td><pre><?php print_r( $booking->getData() ); ?></pre></td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>
