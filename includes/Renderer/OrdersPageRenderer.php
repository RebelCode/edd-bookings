<?php

namespace Aventura\Edd\Bookings\Renderer;

/**
 * Description of OrdersPageRenderer
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class OrdersPageRenderer extends RendererAbstract
{

    /**
     * {@inheritdoc}
     */
    public function render(array $data = array())
    {
        /* @var $bookings array */
        $bookings = $this->getObject();
        $datetimeFormat = sprintf('%s %s', get_option('time_format'), get_option('date_format'));
        // Merge args
        $defaultArgs = array(
            'booking_details_url' => admin_url('post.php?action=edit&post=%s')
        );
        $args = wp_parse_args($data, $defaultArgs);
        $bookingDetailsUrlTemplate = $args['booking_details_url'];
        // Start Render
        ob_start();
        ?>
        <div id="edd-bk-view-order-details" class="postbox">
            <h3 class="hndle">
                <span>Bookings</span>
            </h3>
            <div class="inside edd-clearfix">
                <table class="widefat">
                    <tbody>
                        <?php foreach ($bookings as $booking) { ?>
                            <tr>
                                <td>
                                    <?php echo \get_the_title($booking->getServiceId()); ?>
                                </td>
                                <td>
                                    <?php echo eddBookings()->utcTimeToServerTime($booking->getStart())
                                            ->format($datetimeFormat); ?>
                                    - 
                                    <?php echo $booking->getDuration(); ?>
                                </td>
                                <td>
                                    <?php
                                    $bookingUrl = sprintf($bookingDetailsUrlTemplate, $booking->getId());
                                    printf('<a href="%2$s">%1$s</a>', __('Booking Details', 'eddbk'), $bookingUrl);
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

}
