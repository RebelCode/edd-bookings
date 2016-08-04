<?php

use \Aventura\Edd\Bookings\Integration\Fes\Dashboard\BookingsCalendarPage;
use \Aventura\Edd\Bookings\Integration\Fes\Dashboard\BookingsPage;
use \Aventura\Edd\Bookings\Integration\Fes\Dashboard\EditBookingPage;
use \Aventura\Edd\Bookings\Integration\Fes\FesIntegration;

// Integration instance
$fes = new FesIntegration(eddBookings());

// Calendar Page
$bookingsCalendarPage = new BookingsCalendarPage(eddBookings(), 'bookings-calendar', __('Calendar', 'eddbk'), 'calendar');
$fes->addDashboardPage($bookingsCalendarPage);

// Bookings List Page
$bookingsPage = new BookingsPage(eddBookings(), 'bookings', __('Bookings', 'eddbk'), 'book');
$fes->addDashboardPage($bookingsPage);

// Edit Booking Page / Bookings Details Page
$editBookingPage = new EditBookingPage(eddBookings(), 'edit-booking', null);
$fes->addDashboardPage($editBookingPage);

// Register the integration
eddBookings()->addIntegration('fes', $fes);
