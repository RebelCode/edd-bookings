<?php

use \Interop\Container\ContainerInterface;
use \RebelCode\EddBookings\Controller\ServiceController;
use \RebelCode\EddBookings\Model\Service;
use \RebelCode\EddBookings\Model\ServiceResourceModel;

return array(
    'service' => function(ContainerInterface $c, $prev = null, array $config = array()) {
        $id = isset($config['id'])
            ? $config['id']
            : 0;

        return new Service($id, $c->get('service_resource_model'));
    },
    'service_resource_model' => function(ContainerInterface $c) {
        return new ServiceResourceModel(
            $c->get('service_cpt'),
            $c->get('storage_adapter')
        );
    },
    'service_controller' => function(ContainerInterface $c) {
        return new ServiceController(
            $c->get('service_cpt'),
            $c->get('service_resource_model'),
            $c->get('factory')
        );
    }
);
