<?php

use \Interop\Container\ContainerInterface;
use \RebelCode\EddBookings\Admin\Debug\DebugPage;

return array(
    'debug' => function(ContainerInterface $c) {
        return new DebugPage(
            $c->get('plugin'),
            $c->get('admin_menubar'),
            $c->get('event_manager'),
            $c->get('factory')
        );
    }
);
