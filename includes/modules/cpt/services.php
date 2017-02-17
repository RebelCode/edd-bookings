<?php

use \Interop\Container\ContainerInterface;
use \RebelCode\EddBookings\CustomPostType;

return array(
    'cpt' => function(ContainerInterface $c, $prev, array $config = array()) {
        $defaults = array(
            'slug'        => 'post',
            'labels'      => array(),
            'properties'  => array(),
            'update_msgs' => array()
        );
        $data = array_merge($defaults, $config);

        return new CustomPostType(
            $c->get('plugin'),
            $c->get('event_manager'),
            $data['slug'],
            $data['labels'],
            $data['properties'],
            $data['update_msgs']
        );
    }
);
