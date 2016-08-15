<?php

namespace Aventura\Edd\Bookings\Integration\Commissions\Notifications;

use \Aventura\Edd\Bookings\Integration\Commissions\Notifications\TemplateTag\TemplateTagAbstract;
use \Aventura\Edd\Bookings\Model\Booking;

/**
 * Commissions email template tag for showing the booking associated with a purchased service.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class BookingTag extends TemplateTagAbstract
{

    /**
     * The tag.
     */
    const TAG = 'booking';

    /**
     * Constructs a new instance.
     * 
     * @param string $desc The tag description.
     */
    public function __construct($desc)
    {
        parent::__construct(static::TAG, $desc);
    }

    /**
     * {@inheritdoc}
     */
    public function process($downloadId, $commissionId)
    {
        // Get the payment and the bookings for that payment
        $paymentId = get_post_meta($commissionId, '_edd_commission_payment_id', true);
        $bookings = eddBookings()->getBookingController()->getBookingsForPayment($paymentId);

        // Find the booking for this download
        /* @var $booking Booking */
        $booking = null;
        foreach ($bookings as $bk) {
            if ((int) $bk->getServiceId() === (int) $downloadId) {
                $booking = $bk;
                break;
            }
        }
        
        // Make sure the booking was found
        if (is_null($booking)) {
            return;
        }
        
        // Check format for date only, or date and time
        $service = eddBookings()->getServiceController()->get($downloadId);
        $format = $service->isSessionUnit('hours', 'minutes')
            ? sprintf('%s %s', get_option('time_format'), get_option('date_format'))
            : get_option('date_format');
        // Check the timezone to show
        $start = $service->getUseCustomerTimezone()
            ? $booking->getClientStart()
            : eddBookings()->utcTimeToServerTime($booking->getStart());
        
        return sprintf('%s - %s', $start->format($format), $booking->format('%d'));
    }

}
