<?php

use \Interop\Container\ContainerInterface;

return array(
    'ajax_manager' => function(ContainerInterface $c) {
        return new RebelCode\EddBookings\AjaxManager(
            $c->get('plugin'),
            $c->get('event_manager'),
            'eddbk_ajax'
        );
    }
);