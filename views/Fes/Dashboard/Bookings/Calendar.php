<?php

use \Aventura\Edd\Bookings\Renderer\BookingsCalendarRenderer;

$renderer = new BookingsCalendarRenderer(eddBookings());
echo $renderer->render(array(
    'wrap'   => false,
    'header' => false,
));
