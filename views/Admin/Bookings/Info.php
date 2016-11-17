<?php
// Parse args
$defaultArgs = array(
    'booking'           => null,
    'service_link'      => admin_url('post.php?post=%s&action=edit'),
    'payment_link'      => admin_url('edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=%s'),
    'customer_link'     => admin_url('edit.php?post_type=download&page=edd-customers&view=overview&id=%s'),
    'view_details_link' => admin_url('post.php?post=%s&action=edit'),
    'table_class'       => '',
    'advanced_times'    => true,
);
$args = wp_parse_args($data, $defaultArgs);
// check if booking was given
if (is_null($booking = $args['booking'])) {
    return;
}
$serviceId = $booking->getServiceId();
$service = eddBookings()->getServiceController()->get($serviceId);
$timeFormat = \get_option('time_format');
$dateFormat = \get_option('date_format');
$datetimeFormat = (is_null($service) || $service->isSessionUnit('hours', 'minutes', 'seconds'))
    ? sprintf('%s %s', $timeFormat, $dateFormat)
    : $dateFormat;
?>
<table class="widefat edd-bk-booking-details <?php echo esc_attr($args['table_class']); ?>">
    <tbody>
        <tr>
            <td>ID</td>
            <td>#<?php echo $booking->getId() ?></td>
        </tr>

        <?php if (($serviceId = $booking->getServiceId()) && get_post($serviceId)) : ?>
        <tr>
            <td>Service: </td>
            <td>
                <?php
                    $serviceTitle = \get_the_title($serviceId);
                    if ($args['service_link'] !== null) : ?>
                        <a href="<?php printf($args['service_link'], $serviceId); ?>">
                            <?php echo $serviceTitle; ?>
                        </a>
                    <?php else:
                        echo $serviceTitle;
                    endif;
                ?>
            </td>
        </tr>
        <?php endif; ?>

        <?php if (($paymentId = $booking->getPaymentId()) && get_post($paymentId)) : ?>
        <tr>
            <td>Payment</td>
            <td>
                <?php
                    $paymentIdString = sprintf('#%s', $paymentId);
                    if ($args['payment_link'] !== null) : ?>
                        <a href="<?php printf($args['payment_link'], $paymentId); ?>">
                            <?php echo $paymentIdString; ?>
                        </a>
                    <?php else:
                        echo $paymentIdString;
                    endif;
                ?>
            </td>
        </tr>
        <?php endif; ?>
        
        <tr>
            <td>Start:</td>
            <td>
                <?php
                $utcStart = $booking->getStart();
                $serverStart = eddBookings()->utcTimeToServerTime($utcStart);
                echo $serverStart->format($datetimeFormat);
                if ($args['advanced_times']) :
                $clientStart = $booking->getClientStart();
                ?>
                <div class="edd-bk-alt-booking-time">
                    <?php printf('<strong>%s</strong> %s', __('UTC Time:', 'eddbk'), $utcStart->format($datetimeFormat)); ?>
                    <br/>
                    <?php printf('<strong>%s</strong> %s', __('Customer Time:', 'eddbk'), $clientStart->format($datetimeFormat)); ?>
                </div>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>End:</td>
            <td>
                <?php
                $utcEnd = $booking->getEnd();
                $serverEnd = eddBookings()->utcTimeToServerTime($utcEnd);
                echo $serverEnd->format($datetimeFormat);
                if ($args['advanced_times']) :
                $clientEnd = $booking->getClientEnd();
                ?>
                <div class="edd-bk-alt-booking-time">
                    <?php printf('<strong>%s</strong> %s', __('UTC Time:', 'eddbk'), $utcEnd->format($datetimeFormat)); ?>
                    <br/>
                    <?php printf('<strong>%s</strong> %s', __('Customer Time:', 'eddbk'), $clientEnd->format($datetimeFormat)); ?>
                </div>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td>Duration</td>
            <td>
                <?php printf('%s', $booking->getDuration()); ?>
            </td>
        </tr>

        <?php if ($customerId = $booking->getCustomerId()) : ?>
            <tr>
                <td>Customer</td>
                <td>
                    <?php
                        $customer = new \EDD_Customer($customerId);
                        if ($args['customer_link'] !== null) : ?>
                            <a href="<?php printf($args['customer_link'], $customerId) ?>">
                                <?php echo $customer->name; ?>
                            </a>
                        <?php
                        else:
                            echo $customer->name;
                        endif;
                    ?>
                </td>
            </tr>
        <?php endif; ?>

        <?php if ($args['advanced_times']) : ?>
            <?php if (isset($custoimer)): ?>
                <tr>
                    <td>
                        Customer Time-zone
                    </td>
                    <td>
                        <?php
                        $offset = $booking->getClientTimezone() / Duration::hours(1, false);
                        $sign = $offset >= 0
                        ? '+'
                        : '-';
                        printf('UTC%s%s', $sign, $offset);
                        ?>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if (!is_null($args['view_details_link'])) :
            $detailsUrl = sprintf($args['view_details_link'], $booking->getId());
            ?>
            <tr>
                <td colspan="2">
                    <a href="<?php echo $detailsUrl; ?>" class="edd-bk-view-booking-details">
                        <?php echo _x('View/Edit Details', 'Link to the bookings edit or details page', 'eddbk');
                        ?>
                    </a>
                </td>
            </tr>
        <?php endif; ?>

    </tbody>
</table>