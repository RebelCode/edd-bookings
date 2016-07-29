<?php

use \Aventura\Edd\Bookings\Integration\Fes\Dashboard\BookingsCalendarPage;
use \Aventura\Edd\Bookings\Integration\Fes\Dashboard\BookingsPage;
use \Aventura\Edd\Bookings\Integration\Fes\Dashboard\EditBookingPage;
use \Aventura\Edd\Bookings\Integration\Fes\FesIntegration;

// Integration instance
$fes = new FesIntegration(eddBookings());

// Dashboard pages
$bookingsPage = new BookingsPage(eddBookings(), 'bookings', __('Bookings', 'eddbk'), 'calendar');
$fes->addDashboardPage($bookingsPage);
$bookingsCalendarPage = new BookingsCalendarPage(eddBookings(), 'bookings-calendar', __('Calendar', 'eddbk'), 'calendar');
$fes->addDashboardPage($bookingsCalendarPage);
$editBookingPage = new EditBookingPage(eddBookings(), 'edit-booking', null);
$fes->addDashboardPage($editBookingPage);

// Register the integration
eddBookings()->addIntegration('fes', $fes);
