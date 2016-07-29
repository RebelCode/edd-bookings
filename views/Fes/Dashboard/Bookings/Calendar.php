<?php

use \Aventura\Edd\Bookings\Renderer\BookingsCalendarRenderer;

$renderer = new BookingsCalendarRenderer(eddBookings());
echo $renderer->render();
