<?php

use \Aventura\Edd\Bookings\Model\Booking;

// Ensure that the booking data exists
if (!isset($data['booking']) || !($data['booking'] instanceof Booking)) {
    return;
}

$booking = $data['booking'];
$eddHtml = new \EDD_HTML_Elements();
$start = eddBookings()->utcTimeToServerTime($booking->getStart());
$end = eddBookings()->utcTimeToServerTime($booking->getEnd());
$serverTz = eddBookings()->getServerTimezoneOffsetSeconds();
?>

<input type="hidden" id="server-tz" value="<?php echo esc_attr($serverTz); ?>" />

<div class="edd-bk-booking-details">

    <div>
        <label for="service">
            <?php _e('Service', 'eddbk'); ?>
        </label>
        <?php
            echo $eddHtml->product_dropdown(array(
                'id'       => 'service',
                'class'    => 'service-id',
                'name'     => 'service_id',
                'selected' => $booking->getServiceId()
        ));
        ?>
        <em><?php _e('Optional', 'eddbk'); ?></em>
        <?php echo eddBookings()->adminTooltip('Hello'); ?>
    </div>

    <div>
        <label for="customer">
            <?php _e('Customer', 'eddbk'); ?>
        </label>
        <?php
            echo $eddHtml->customer_dropdown(array(
                'id'       => 'customer',
                'class'    => 'customer-id',
                'name'     => 'customer_id',
                'chosen'   => false,
                'selected' => $booking->getCustomerId()
            ));
        ?>
        <em><?php _e('Optional', 'eddbk'); ?></em>
        <?php echo eddBookings()->adminTooltip('Hello'); ?>
    </div>

    <hr/>

    <div>
        <label for="start">
            <?php _e('Start', 'eddbk'); ?> *
        </label>
        <input
            id="start"
            name="start"
            class="edd-bk-datetime"
            type="text"
            value="<?php echo esc_attr($start->format('Y-m-d H:i:s')); ?>"
        />
        <?php echo eddBookings()->adminTooltip('Hello'); ?>
    </div>
    <div class="advanced-times">
        <label></label>
        <div>
            <p id="start-utc" class="utc-time">
                UTC Time: <code>...</code>
            </p>
            <p id="start-customer" class="customer-time">
                Customer Time: <code>...</code>
            </p>
        </div>
    </div>

    <div>
        <label for="end">
            <?php _e('End', 'eddbk'); ?> *
        </label>
        <input
            id="end"
            name="end"
            class="edd-bk-datetime"
            type="text"
            value="<?php echo esc_attr($end->format('Y-m-d H:i:s')); ?>"
        />
        <?php echo eddBookings()->adminTooltip('Hello'); ?>
    </div>
    <div class="advanced-times">
        <label></label>
        <div>
            <p id="end-utc" class="utc-time">
                UTC Time: <code>...</code>
            </p>
            <p id="end-customer" class="customer-time">
                Customer Time: <code>...</code>
            </p>
        </div>
    </div>

    <div>
        <label for="duration">
            <?php _e('Duration', 'eddbk'); ?>
        </label>
        <code id="duration"></code>
    </div>

    <hr/>

    <div>
        <label for="payment">
            <?php _e('Payment #', 'eddbk'); ?>
        </label>
        <input
            id="payment"
            name="payment_id"
            type="number"
            value="<?php echo esc_attr($booking->getPaymentId()); ?>"
        />
        <em><?php _e('Optional', 'eddbk'); ?></em>
        <?php echo eddBookings()->adminTooltip('Hello'); ?>
    </div>

    <div>
        <label for="customer_tz">
            <?php _e('Customer Timezone', 'eddbk'); ?>
        </label>
        <input
            id="customer_tz"
            name="customer_tz"
            type="number"
            min="-14"
            max="14"
            step="0.5"
            value="<?php echo esc_attr($booking->getClientTimezone() / 3600); ?>"
        />
        <em><?php _e('Optional', 'eddbk'); ?></em>
        <?php echo eddBookings()->adminTooltip('Hello'); ?>
    </div>

</div>
