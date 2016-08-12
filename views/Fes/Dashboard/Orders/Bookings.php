<?php
$payment = $data['payment'];
$paymentId = is_object($payment)
    ? $payment->ID
    : $payment;
$bookings = eddBookings()->getBookingController()->getBookingsForPayment($paymentId);
if ($bookings === NULL || count($bookings) === 0) {
    return;
}
$datetimeFormat = sprintf('%s %s', get_option('time_format'), get_option('date_format'));
$permalink = get_permalink();
?>
<h3><?php _e('Bookings', 'eddbk'); ?></h3>
<table id="edd_purchase_receipt_products">
    <thead>
        <tr>
            <th><?php _e('Name', 'eddbk'); ?></th>
            <th><?php _e('Date and Time', 'eddbk'); ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($bookings as $booking) : ?>
            <tr>
                <td>
                    <?php echo get_the_title($booking->getServiceId()); ?>
                </td>
                <td>
                    <?php echo eddBookings()->utcTimeToServerTime($booking->getStart())->format($datetimeFormat); ?>
                    - 
                    <?php echo $booking->getDuration(); ?>
                </td>
                <td>
                    <?php
                    $bookingUrlQueryArgs = array(
                        'task'       => 'edit-booking',
                        'booking_id' => $booking->getId()
                    );
                    $bookingUrl = add_query_arg($bookingUrlQueryArgs, $permalink);
                    printf('<a href="%2$s">%1$s</a>', __('Booking Details', 'eddbk'), $bookingUrl);
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
