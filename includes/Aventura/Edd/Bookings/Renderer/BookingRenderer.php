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
                'advanced_times'    => true,
                'show_booking_link' => false
        );
        $args = wp_parse_args($data, $defaultArgs);
        $textDomain = eddBookings()->getI18n()->getDomain();
        $datetimeFormat = sprintf('%s %s', \get_option('time_format'), \get_option('date_format'));
        ob_start();
        ?>
        <table class="widefat edd-bk-booking-details">
            <tbody>
                <tr>
                    <td>ID</td>
                    <td><?php echo $booking->getId() ?></td>
                </tr>
            <td>Service: </td>
            <td>
                <?php
                $serviceId = $booking->getServiceId();
                $serviceLink = \admin_url(sprintf('post.php?post=%s&action=edit', $serviceId));
                ?>
                <a href="<?php echo $serviceLink; ?>">
                    <?php echo \get_the_title($serviceId); ?>
                </a>
            </td>
        </tr>
        <tr>
            <td>Payment</td>
            <td>
                <?php
                $paymentId = $booking->getPaymentId();
                $paymentLink = admin_url(
                        sprintf(
                                'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=%s',
                                $paymentId
                        )
                );
                printf('<a href="%s">#%s</a>', $paymentLink, $paymentId);
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
                        <?php printf('%s %s', __('UTC Time:', $textDomain), $utcStart->format($datetimeFormat)); ?>
                        <br/>
                        <?php printf('%s %s', __('Customer Time:', $textDomain), $clientStart->format($datetimeFormat)); ?>
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
                        <?php printf('%s %s', __('UTC Time:', $textDomain), $utcEnd->format($datetimeFormat)); ?>
                        <br/>
                        <?php printf('%s %s', __('Customer Time:', $textDomain), $clientEnd->format($datetimeFormat)); ?>
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
                $customerLink = admin_url(
                        sprintf(
                                'edit.php?post_type=download&page=edd-customers&view=overview&id=%s', $customerId
                        )
                );
                printf('<a href="%s">%s</a>', $customerLink, $customer->name);
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
                    <a href="<?php echo $url; ?>">
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
