<?php

use \Interop\Container\ContainerInterface;
use \RebelCode\EddBookings\Controller\BookingController;
use \RebelCode\EddBookings\Model\Booking;
use \RebelCode\EddBookings\Model\BookingResourceModel;

return array(
    'booking' => function(ContainerInterface $c, $prev, array $config = array()) {
        $id = isset($config['id'])
            ? $config['id']
            : 0;

        return new Booking($id, $c->get('booking_resource_model'));
    },
    'booking_resource_model' => function(ContainerInterface $c) {
        return new BookingResourceModel(
            $c->get('booking_cpt'),
            $c->get('storage_adapter'),
            $c->get('factory')
        );
    },
    'booking_controller' => function(ContainerInterface $c) {
        return new BookingController(
            $c->get('booking_cpt'),
            $c->get('booking_resource_model'),
            $c->get('factory')
        );
    }
);
