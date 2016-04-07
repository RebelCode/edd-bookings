<?php

namespace Aventura\Edd\Bookings\Renderer;

/**
 * Description of ReceiptRenderer
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class ReceiptRenderer extends RendererAbstract
{

    /**
     * {@inheritdoc}
     */
    public function render(array $data = array())
    {
        // Get text domain
        $textDomain = eddBookings()->getI18n()->getDomain();
        // Get bookings
        $payment = $this->getObject();
        $bookings = eddBookings()->getBookingController()->getBookingsForPayemnt($payment->ID);
        if (count($bookings) === 0) {
            return;
        }
        // Generate date/time format
        $datetimeFormat = sprintf('%s %s', get_option('time_format'), get_option('date_format'));
        ob_start();
        ?>
        <h3><?php _e('Bookings', $textDomain); ?></h3>
        <table>
            <thead>
                <tr><th>Service</th><th>Start</th><th>End</th></tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking) : ?>
                    <tr>
                        <td><strong><?php echo \get_the_title($booking->getServiceId()) ?></strong></td>
                        <td><?php echo $booking->getClientStart()->format($datetimeFormat); ?></td>
                        <td><?php echo $booking->getClientEnd()->format($datetimeFormat); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        return ob_get_clean();
    }

}
