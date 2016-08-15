<?php

use \Aventura\Edd\Bookings\Integration\Commissions\CommissionsIntegration;
use \Aventura\Edd\Bookings\Integration\Commissions\Notifications\BookingTag;

// Initialize integration
$commissions = new CommissionsIntegration(eddBookings());

// Booking tag for emails
$bookingTag = new BookingTag(__('The booked date and time, if applicable.', 'eddk'));
$commissions->addTemplateTag($bookingTag);

// Register integration
eddBookings()->addIntegration('commissions', $commissions);
