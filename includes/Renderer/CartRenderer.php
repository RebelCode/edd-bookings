<?php

namespace Aventura\Edd\Bookings\Renderer;

use \Aventura\Diary\DateTime;
use \Aventura\Diary\DateTime\Duration;
use \Aventura\Diary\DateTime\Period;
use \Aventura\Edd\Bookings\Model\Service;

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
        $service = $this->getCartItemService();

        // Stop if bookings not enabled
        if (!$service->getBookingsEnabled()) {
            return '';
        }

        // Get the cart item's session
        $session = $this->getCartItemSession();

        // If no session, render the "no session" view
        if (is_null($session)) {
            return eddBookings()->renderView('Frontend.Cart.Item.NoSession', array(
                'service' => $service,
                'index'   => $data['index']
            ));
        }

        return eddBookings()->renderView('Frontend.Cart.Item.BookingSession', $this->formatSession($service, $session));
    }

    /**
     * 
     * @return Service
     */
    public function getCartItemService()
    {
        $item = $this->getObject();
        return eddBookings()->getServiceController()->get($item['id']);
    }

    public function getCartItemSession()
    {
        // Check if item has session data
        $item = $this->getObject();
        $sessionData = isset($item['options']['edd_bk'])
            ? $item['options']['edd_bk']
            : null;
        // Stop if no data
        if (is_null($sessionData)) {
            return null;
        }
        // Get item session data to create a Period instance
        $start = new DateTime(intval($sessionData['start']));
        $duration = new Duration(intval($sessionData['duration']));

        // Offset with the correct timezone
        $customerTimezone = new Duration(intval($sessionData['timezone']));
        $serverTimezone = eddBookings()->getServerTimezoneOffsetDuration();
        $sessionTimezone = $this->getCartItemService()->getUseCustomerTimezone()
            ? $customerTimezone
            : $serverTimezone;
        $start->plus($sessionTimezone);

        return new Period($start, $duration);
    }

    public static function formatSession(Service $service, Period $booking)
    {
        // Get date and time formats
        $dateformat = get_option('date_format', 'd/m/y');
        $timeformat = get_option('time_format', 'H:i');
        $datetimeFormatPattern = $service->isSessionUnit('days', 'weeks')
                ? '%s'
                : '%s @ %s';
        $datetimeFormat = sprintf($datetimeFormatPattern, $dateformat, $timeformat);

        return array(
            'start' => $booking->getStart()->format($datetimeFormat),
            'end'   => $booking->getEnd()->format($datetimeFormat)
        );
    }

}
