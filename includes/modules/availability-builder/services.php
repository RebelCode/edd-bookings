<?php

use \Interop\Container\ContainerInterface;
use \RebelCode\EddBookings\Block\AvailabilityBuilderBlock;
use \RebelCode\EddBookings\Component\AvailabilityBuilderComponent;
use \RebelCode\EddBookings\Registry\RuleTypeRegistry;

return array(
    'availability_builder'       => function(ContainerInterface $c) {
        return new AvailabilityBuilderComponent(
            $c->get('plugin'),
            $c->get('event_manager'),
            $c->get('rule_type_registry')
        );
    },
    'availability_builder_block' => function(ContainerInterface $c, $p, $config = array()) {
        if (!isset($config['service'])) {
            throw new InvalidArgumentException(
                'Missing "service" config data in "availability_builder" service factory.'
            );
        }

        return new AvailabilityBuilderBlock(
            $config['service'],
            $c->get('rule_type_registry')
        );
    },
    'rule_type_registry' => function(ContainerInterface $c) {
        return new RuleTypeRegistry();
    }
);
