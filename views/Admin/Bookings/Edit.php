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
        <h4><?php _e('New Customer Details', 'eddbk'); ?></h4>
    </div>
    <div class="edd-bk-if-create-customer edd-bk-create-customer-msg">
        <span><?php _e('You are now creating a new customer for this booking.', 'eddbk'); ?></span>
        <a id="choose-customer" href="javascript:void(0)"><i class="fa fa-mouse-pointer"></i> <?php _e('Choose an existing customer', 'eddbk'); ?></a>
    </div>
    <div class="edd-bk-if-create-customer">
        <label for="customer-name">
            <span><?php _e('First Name', 'eddbk'); ?></span>
            <?php
                echo eddBookings()->adminTooltip(
                    __("The customer's first name.", 'eddbk')
                );
            ?>
        </label>
        <input type="text" name="customer_name" />
    </div>
    <div class="edd-bk-if-create-customer">
        <label for="customer-surname">
            <span><?php _e('Last Name', 'eddbk'); ?></span>
            <?php
                echo eddBookings()->adminTooltip(
                    __("The customer's last name.", 'eddbk')
                );
            ?>
        </label>
        <input type="text" name="customer_surname" />
    </div>
    <div class="edd-bk-if-create-customer">
        <label for="customer-email">
            <span><?php _e('Email Address', 'eddbk'); ?></span>
            <?php
                echo eddBookings()->adminTooltip(
                    __("The customer's email address.", 'eddbk')
                );
            ?>
        </label>
        <input type="email" name="customer_email" />
    </div>
    <div class="edd-bk-if-create-customer">
        <label></label>
        <button class="button button-secondary" type="button">
            <i class="fa fa-asterisk"></i>
            <?php _e('Create customer', 'eddbk'); ?>
            <i class="fa fa-spinner fa-spin edd-bk-create-customer-spinner"></i>
        </button>
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
