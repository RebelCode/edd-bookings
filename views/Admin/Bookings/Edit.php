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

    <h4><?php _e('Service and Payment', 'eddbk'); ?></h4>
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
        <a id="create-payment" href="javascript:void(0)">
            <i class="fa fa-plus"></i>
            <?php _e('Create new', 'eddbk'); ?>
        </a>
    </div>

    <hr/>

    <div class="edd-bk-if-choose-customer">
        <h4><?php _e('Choose a Customer', 'eddbk'); ?></h4>
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
        <a id="create-customer" href="javascript:void(0)">
            <i class="fa fa-plus"></i>
            <?php _e('Create new customer', 'eddbk'); ?>
        </a>
    </div>

    <div class="edd-bk-if-create-customer">
        <h4>
            <?php _e('Create a New Customer', 'eddbk'); ?>
            <small class="edd-bk-create-customer-msg">
                <a id="choose-customer" href="javascript:void(0)"><i class="fa fa-mouse-pointer"></i> <?php _e('Choose an existing customer', 'eddbk'); ?></a>
            </small>
        </h4>
    </div>
    <div class="edd-bk-if-create-customer edd-bk-inline-create-customer">
        <?php echo eddBookings()->renderView('Admin.Bookings.Edit.InlineCreateCustomer', $data); ?>
    </div>

    <hr/>

    <h4><?php _e('Booking Details', 'eddbk'); ?></h4>
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

    <div>
        <label for="customer_tz">
            <span><?php _e('Customer Timezone', 'eddbk'); ?></span>
            <?php
                echo eddBookings()->adminTooltip(
                    __("The customer's timezone difference, in hours, from UTC or GMT.", 'eddbk')
                );
            ?>
        </label>
        <?php
            echo eddBookings()->renderView('Fragment.TimezoneOffsetDropdown', array(
                'name'     => 'customer_tz',
                'id'       => 'customer_tz',
                'selected' => $booking->getClientTimezone()
            ));
        ?>
    </div>

</div>
