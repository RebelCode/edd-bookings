<?php

use \Aventura\Edd\Bookings\Integration\Fes\Dashboard\BookingsCalendarPage;
use \Aventura\Edd\Bookings\Integration\Fes\Dashboard\BookingsPage;
use \Aventura\Edd\Bookings\Integration\Fes\Dashboard\EditBookingPage;
use \Aventura\Edd\Bookings\Integration\Fes\FesIntegration;

define('EDD_BK_FES_DIR', EDD_BK_INTEGRATIONS_DIR . 'fes/');
define('EDD_BK_FES_CONFIG_DIR', EDD_BK_FES_DIR . 'config/');
define('EDD_BK_FES_URL', EDD_BK_INTEGRATIONS_URL . 'assets/');
define('EDD_BK_FES_JS_URL', EDD_BK_FES_URL . 'js/');
define('EDD_BK_FES_CSS_URL', EDD_BK_FES_URL . 'css/');

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

// Create assets config instance
$assetsConfig = new EddBkAssetsConfig(eddBookings());
// Set base URLs
$assetsConfig->setBaseScriptUrl(EDD_BK_FES_JS_URL)
    ->setBaseStyleUrl(EDD_BK_FES_CSS_URL);
// Set to instance
$fes->setAssetsConfig($assetsConfig);

// Register the integration
eddBookings()->addIntegration('fes', $fes);
