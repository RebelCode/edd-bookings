<?php

use \Aventura\Edd\Bookings\Model\Booking;

// Ensure that the booking data exists
if (!isset($data['booking']) || !($data['booking'] instanceof Booking)) {
    return;
}

$booking = $data['booking'];
$eddHtml = new \EDD_HTML_Elements();
?>

<div class="edd-bk-booking-details">

    <div>
        <label for="service">Download</label>
        <?php
        echo $eddHtml->product_dropdown(array(
            'id'       => 'service',
            'class'    => 'service-id',
            'name'     => 'service_id',
            'selected' => $booking->getServiceId()
        ));
        ?>
    </div>

    <div>
        <label for="customer">Customer</label>
        <?php
        echo $eddHtml->customer_dropdown(array(
            'id'       => 'customer',
            'class'    => 'customer-id',
            'name'     => 'customer_id',
            'chosen'   => false,
            'selected' => $booking->getCustomerId()
        ));
        ?>
    </div>

    <hr/>

    <div>
        <label for="start">Start</label>
        <input
            id="start"
            name="start"
            class="edd-bk-datetime"
            type="text"
            value="<?php echo esc_attr($booking->getStart()->format('Y-m-d H:i:s')); ?>"
            />
    </div>


    <div>
        <label for="end">End</label>
        <input
            id="end"
            name="end"
            class="edd-bk-datetime"
            type="text"
            value="<?php echo esc_attr($booking->getEnd()->format('Y-m-d H:i:s')); ?>"
            />
    </div>

    <div>
        <label for="duration">Duration</label>
        <code id="duration"></code>
    </div>

    <hr/>

    <div>
        <label for="payment">Payment #</label>
            <input
                id="payment"
                name="payment_id"
                type="number"
                value="<?php echo esc_attr($booking->getPaymentId()); ?>"
            />
    </div>

    <div>
        <label for="customer_tz"><?php _e('Customer Timezone'); ?></label>
        <input
            id="customer_tz"
            name="customer_tz"
            type="number"
            min="-14"
            max="14"
            step="0.5"
            value="<?php echo esc_attr($booking->getClientTimezone() / 3600); ?>"
            />
    </div>

</div>
