<?php

use \Interop\Container\ContainerInterface;
use \RebelCode\EddBookings\CustomPostType\Service\Block\BookingOptionsBlock;
use \RebelCode\EddBookings\CustomPostType\Service\BookingOptionsMetaBox;
use \RebelCode\EddBookings\CustomPostType\Service\ServiceCpt;
use \RebelCode\WordPress\Admin\Metabox\MetaBoxInterface;

return array(
    'service_cpt_slug' => function() {
        return 'download';
    },
    'service_cpt' => function(ContainerInterface $c) {
        return new ServiceCpt(
            $c->get('plugin'),
            $c->get('event_manager'),
            $c->get('service_cpt_slug')
        );
    },
    'service_booking_options_metabox' => function(ContainerInterface $c) {
        return new BookingOptionsMetaBox(
            $c->get('plugin'),
            $c->get('event_manager'),
            'edd-bk-service',
            __('Booking Options', 'eddbk'),
            $c->get('service_booking_options_metabox_callback'),
            MetaBoxInterface::CTX_NORMAL,
            MetaBoxInterface::PRIORITY_HIGH,
            $c->get('service_cpt_slug'),
            array($c)
        );
    },

    'service_booking_options_metabox_callback' => function(ContainerInterface $c) {
        return function() use ($c) {
            global $post;

            $service = $c->get('service_controller')->get($post->ID);

            return $c->make('service_booking_options_metabox_content', array(
                'service' => $service
            ));
        };
    },

    'service_booking_options_metabox_content' => function(ContainerInterface $c, $p, $config = array()) {
        if (!isset($config['service'])) {
            throw new InvalidArgumentException(
                'Missing "service" config data for "service_booking_options_metabox_content factory.'
            );
        }

        $service = $config['service'];

        return new BookingOptionsBlock(
            $service,
            $c->get('browser_datetime_formatter'),
            $c->get('factory')->make('availability_builder_block', array(
                'service' => $service
            ))
        );
    },
);
