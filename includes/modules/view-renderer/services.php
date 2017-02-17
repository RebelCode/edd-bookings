<?php

use \Interop\Container\ContainerInterface;
use \RebelCode\EddBookings\ViewRenderer;

return array(
    'view_renderer' => function(ContainerInterface $c) {
        return new ViewRenderer($c->get('plugin'), EDD_BK_VIEWS_DIR);
    }
);
