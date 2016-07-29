<?php if (!isset($data['booking_id']) || !$data['booking_id']) : ?>
    <div class="edd_errors">
        <p class="edd_error"><?php _e('Access Denied', 'edd_fes'); ?></p> 
    </div>
<?php else : ?>
    <?php
    $booking = eddBookings()->getBookingController()->get($data['booking_id']);
    $customer = new EDD_Customer($booking->getCustomerId());
    $permalink = get_permalink();
    /*
      <h3><?php echo $booking->getStart(); ?></h3>
      <p>
      <strong><?php _e('Customer: ', 'eddbk'); ?></strong>
      <?php echo $customer->name; ?>
      </p>
     */
    $renderer = new \Aventura\Edd\Bookings\Renderer\BookingRenderer($booking);
    echo $renderer->render(array(
        'service_link'      => add_query_arg(array('task' => 'edit-product', 'post_id' => $booking->getServiceId()), $permalink),
        'payment_link'      => add_query_arg(array('task' => 'edit-order', 'order_id' => $booking->getPaymentId()), $permalink),
        'customer_link'     => null,
        'table_class'       => 'table fes-table table-condensed table-striped',
    ));
    ?>
<?php endif; ?>
