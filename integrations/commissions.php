<?php

use \Aventura\Edd\Bookings\Integration\Commissions\CommissionsIntegration;
use \Aventura\Edd\Bookings\Integration\Commissions\Notifications\TemplateTag;
use \Aventura\Edd\Bookings\Model\Booking;

$commissions = new CommissionsIntegration(eddBookings());

$bookingTemplateTag = new TemplateTag(
    'booking',
    __('The booked date and time, if applicable.', 'eddk'),
    function($downloadId, $commissionId) {
        $paymentId = get_post_meta($commissionId, '_edd_commission_payment_id', true);
        $bookings = eddBookings()->getBookingController()->getBookingsForPayemnt($paymentId);
        $replacement = '';
        foreach ($bookings as $booking) {
            /* @var $booking Booking */
            $service = eddBookings()->getServiceController()->get($booking->getServiceId());
            $format = $service->isSessionUnit('hours', 'minutes')
                ? sprintf('%s %s', get_option('time_format'), get_option('date_format'))
                : get_option('date_format');
            $replacement .= sprintf('%s\n', $booking->format($format));
        }
        return $replacement;
    }
);

$commissions->addTemplateTag($bookingTemplateTag);

eddBookings()->addIntegration('commissions', $commissions);
