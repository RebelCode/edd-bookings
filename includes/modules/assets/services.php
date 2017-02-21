<?php

use \Interop\Container\ContainerInterface;
use \RebelCode\EddBookings\Assets\AssetsController;

return array(
    'assets' => function(ContainerInterface $c) {
        return new AssetsController($c->get('plugin'));
    }
);
