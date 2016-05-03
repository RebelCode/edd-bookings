<?php

namespace Aventura\Edd\Bookings\Renderer;

use \Aventura\Diary\DateTime;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Diary\DateTime\Period;

/**
 * Renders the EDD Cart data.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class CartRenderer extends RendererAbstract
{

    /**
     * {@inheritdoc}
     */
    public function render(array $data = array())
    {
        // Get item
        $item = $this->getObject();
        // Get the service
        $id = $item['id'];
        $service = eddBookings()->getServiceController()->get($id);
        // Get text domain
        $textDomain = eddBookings()->getI18n()->getDomain();
        // Get item options
        $itemOptions = $item['options'];
        // Prepare output var
        $output = '';
        // If bookings enabled and item data has booking info
        if ($service->getBookingsEnabled() && isset($itemOptions['edd_bk'])) {
            // Get item booking info
            $bookingInfo = $itemOptions['edd_bk'];
            $start = new DateTime(intval($bookingInfo['start']));
            $duration = new Duration(intval($bookingInfo['duration']));
            // Offset with the correct timezone
            $customerTimezone = new Duration(intval($bookingInfo['timezone']));
            $serverTimezone = eddBookings()->getServerTimezoneOffsetDuration();
            $start->plus($service->getUseCustomerTimezone()? $customerTimezone : $serverTimezone);
            // Create period
            $period = new Period($start, $duration);
            // Get date and time formats
            $dateformat = get_option('date_format', 'd/m/y');
            $timeformat = get_option('time_format', 'H:i');
            $datetimeFormatPattern = $service->isSessionUnit('days', 'weeks')
                    ? '%s'
                    : '%s %s';
            $datetimeFormat = sprintf($datetimeFormatPattern, $dateformat, $timeformat);
            // Output
            ob_start();
            ?>
            <p class="edd-bk-cart-booking-start">
                <?php _e('Start:', $textDomain); ?>
                <em><?php echo $period->getStart()->format($datetimeFormat); ?></em>
            </p>
            <p class="edd-bk-cart-booking-end">
                <?php _e('End:', $textDomain); ?>
                <em><?php echo $period->getEnd()->format($datetimeFormat); ?></em>
            </p>
            <?php
            $output = ob_get_clean();
        }
        $filteredOutput = apply_filters('edd_bk_cart_item_output', $output, $item, $service);
        return $filteredOutput;
    }

}
