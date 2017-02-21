<?php

use \Interop\Container\ContainerInterface;
use \RebelCode\EddBookings\BookingPaymentBridge;

return array(
    'booking_payment_bridge' => function(ContainerInterface $c) {
        return new BookingPaymentBridge(
            $c->get('plugin'),
            $c->get('event_manager')
        );
    }
);