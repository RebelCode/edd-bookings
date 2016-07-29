<?php

namespace Aventura\Edd\Bookings\Renderer;

use \Aventura\Diary\DateTime\Duration;
use \Aventura\Edd\Bookings\Model\Booking;

/**
 * Description of BookingRenderer
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class BookingRenderer extends RendererAbstract
{

    /**
     * {@inheritdoc}
     */
    public function render(array $data = array())
    {
        /* @var $booking Booking */
        $booking = $this->getObject();
        // Parse args
        $defaultArgs = array(
            'service_link'      => admin_url('post.php?post=%s&action=edit'),
            'payment_link'      => admin_url('edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=%s'),
            'customer_link'     => admin_url('edit.php?post_type=download&page=edd-customers&view=overview&id=%s'),
            'table_class'       => '',
            'advanced_times'    => true,
            'show_booking_link' => false
        );
        $args = wp_parse_args($data, $defaultArgs);
        $textDomain = eddBookings()->getI18n()->getDomain();
        $service = eddBookings()->getServiceController()->get($booking->getServiceId());
        $timeFormat = \get_option('time_format');
        $dateFormat = \get_option('date_format');
        $datetimeFormat = $service->isSessionUnit('hours', 'minutes', 'seconds')
            ? sprintf('%s %s', $timeFormat, $dateFormat)
            : $dateFormat;
        ob_start();
        ?>
        <table class="widefat edd-bk-booking-details <?php echo esc_attr($args['table_class']); ?>">
            <tbody>
                <tr>
                    <td>ID</td>
                    <td>#<?php echo $booking->getId() ?></td>
                </tr>
            <td>Service: </td>
            <td>
                <?php
                $serviceId = $booking->getServiceId();
                if (get_post($serviceId)) {
                    if (is_null($args['service_link'])) {
                        echo \get_the_title($serviceId);
                    } else {
                        $serviceLink = sprintf($args['service_link'], $serviceId);
                        ?>
                        <a href="<?php echo $serviceLink; ?>">
                            <?php echo \get_the_title($serviceId); ?>
                        </a>
                        <?php
                    }
                } else {
                    echo _x('None', 'no service/download for booking', 'eddbk');
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Payment</td>
            <td>
                <?php
                $paymentId = $booking->getPaymentId();
                $paymentIdString = sprintf('#%s', $paymentId);
                if (is_null($args['payment_link'])) {
                    echo $paymentIdString;
                } else {
                    $paymentLink = sprintf($args['payment_link'], $paymentId);
                    printf('<a href="%s">%s</a>', $paymentLink, $paymentIdString);
                }
                ?>
            </td>
        </tr>
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
                        <?php printf('<strong>%s</strong> %s', __('UTC Time:', $textDomain), $utcStart->format($datetimeFormat)); ?>
                        <br/>
                        <?php printf('<strong>%s</strong> %s', __('Customer Time:', $textDomain), $clientStart->format($datetimeFormat)); ?>
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
                        <?php printf('<strong>%s</strong> %s', __('UTC Time:', $textDomain), $utcEnd->format($datetimeFormat)); ?>
                        <br/>
                        <?php printf('<strong>%s</strong> %s', __('Customer Time:', $textDomain), $clientEnd->format($datetimeFormat)); ?>
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
        <tr>
            <td>Customer</td>
            <td>
                <?php
                $customerId = $booking->getCustomerId();
                $customer = new \EDD_Customer($customerId);
                if (is_null($args['customer_link'])) {
                    echo $customer->name;
                } else {
                    $customerLink = sprintf($args['customer_link'], $customerId);
                    printf('<a href="%s">%s</a>', $customerLink, $customer->name);
                }
                ?>
            </td>
        </tr>
        <?php if ($args['advanced_times']) : ?>
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
        <?php
        endif;
        if ($args['show_booking_link']) :
            $url = admin_url(sprintf('post.php?post=%s&action=edit', $booking->getId()));
            ?>
            <tr>
                <td colspan="2">
                    <a href="<?php echo $url; ?>" class="edd-bk-view-booking-details">
                        <?php echo _x('View more details', 'Link to the page that shows full details for a booking',
                                $textDomain); ?>
                    </a>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
        </table>
        <?php
        return ob_get_clean();
    }

}
