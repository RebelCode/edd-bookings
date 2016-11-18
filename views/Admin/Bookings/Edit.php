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
            <span><?php _e('Service', 'eddbk'); ?></span>
            <?php
                echo eddBookings()->adminTooltip(
                    __('The service being provided for this booking.', 'eddbk')
                );
            ?>
        </label>
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
        <label for="customer">
            <span><?php _e('Customer', 'eddbk'); ?></span>
            <?php
                echo eddBookings()->adminTooltip(
                    __('The customer associated with this booking.', 'eddbk')
                );
            ?>
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
    </div>

    <hr/>

    <div>
        <label for="start">
            <span><?php _e('Start', 'eddbk'); ?> *</span>
            <?php
                echo eddBookings()->adminTooltip(
                    __('The date and time when this booking begins, relative to your WordPress timezone.', 'eddbk')
                );
            ?>
        </label>
        <input
            id="start"
            name="start"
            class="edd-bk-datetime"
            type="text"
            value="<?php echo esc_attr($start->format('Y-m-d H:i:s')); ?>"
        />
    </div>
    <div class="advanced-times">
        <label></label>
        <div>
            <p id="end-utc" class="utc-time">
                <?php _e('UTC Time:', 'eddbk'); ?>
                <code>...</code>
            </p>
            <p id="end-customer" class="customer-time">
                <?php _e('Customer Time:', 'eddbk'); ?>
                <code>...</code>
            </p>
        </div>
    </div>

    <div>
        <label for="end">
            <span><?php _e('End', 'eddbk'); ?> *</span>
            <?php
                echo eddBookings()->adminTooltip(
                    __('The date and time when the booking ends, relative to your WordPress timezone.', 'eddbk')
                );
            ?>
        </label>
        <input
            id="end"
            name="end"
            class="edd-bk-datetime"
            type="text"
            value="<?php echo esc_attr($end->format('Y-m-d H:i:s')); ?>"
        />
    </div>
    <div class="advanced-times">
        <label></label>
        <div>
            <p id="end-utc" class="utc-time">
                <?php _e('UTC Time:', 'eddbk'); ?>
                <code>...</code>
            </p>
            <p id="end-customer" class="customer-time">
                <?php _e('Customer Time:', 'eddbk'); ?>
                <code>...</code>
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
            <span><?php _e('Payment #', 'eddbk'); ?></span>
            <?php
                echo eddBookings()->adminTooltip(
                    __('The EDD payment number for the associated transaction.', 'eddbk')
                );
            ?>
        </label>
        <input
            id="payment"
            name="payment_id"
            type="number"
            value="<?php echo esc_attr($booking->getPaymentId()); ?>"
        />
    </div>

    <div>
        <label for="customer_tz">
            <span><?php _e('Customer Timezone', 'eddbk'); ?></span>
            <?php
                echo eddBookings()->adminTooltip(
                    __("The customer's timezone difference, in hours, from UTC or GMT.", 'eddbk')
                );
            ?>
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
        <?php
            echo eddBookings()->adminTooltip(
                __("The customer's timezone difference, in hours, from UTC or GMT.", 'eddbk')
            );
        ?>
    </div>

</div>
