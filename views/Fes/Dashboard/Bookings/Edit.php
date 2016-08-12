<?php if (!isset($data['booking_id']) || !$data['booking_id']) : ?>
    <div class="edd_errors">
        <p class="edd_error"><?php _e('Access Denied', 'edd_fes'); ?></p> 
    </div>
<?php else : ?>
    <?php
    $booking = eddBookings()->getBookingController()->get($data['booking_id']);
    $customer = new EDD_Customer($booking->getCustomerId());
    $permalink = get_permalink();
    $renderer = new \Aventura\Edd\Bookings\Renderer\BookingRenderer($booking);
    $paymentLink = EDD_FES()->vendors->vendor_can_view_orders()
        ? add_query_arg(array('task' => 'edit-order', 'order_id' => $booking->getPaymentId()), $permalink)
        : null;
    echo $renderer->render(array(
        'service_link'      => add_query_arg(array('task' => 'edit-product', 'post_id' => $booking->getServiceId()), $permalink),
        'payment_link'      => $paymentLink,
        'customer_link'     => null,
        'view_details_link' => null,
        'table_class'       => 'table fes-table table-condensed table-striped',
    ));
    ?>
<?php endif; ?>
