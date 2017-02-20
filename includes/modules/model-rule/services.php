<?php

use \Interop\Container\ContainerInterface;
use \RebelCode\Bookings\Model\Rule\TimeRangeRule;
use \RebelCode\EddBookings\Block\Html\DumpBlock;
use \RebelCode\EddBookings\Model\CallbackRuleType;
use \RebelCode\EddBookings\Registry\RuleTypeRegistryInterface;

add_action(
    'avail_builder_rule_type_registration',
    function(RuleTypeRegistryInterface $registry) {
        $registry->register('time_range_rule', new CallbackRuleType(
            array(
                'id'       => 'time_range_rule',
                'name'     => __('Time Range Rule', 'eddbk'),
                'callback' => function($rule) {
                    return new DumpBlock($rule);
                }
            )
        ));
    }
);

return array(
    'monday_time_rule'    => function(ContainerInterface $c, $p, $config = array()) {
        return new TimeRangeRule($config);
    },
    'tuesday_time_rule'   => function(ContainerInterface $c, $p, $config = array()) {
        return new TimeRangeRule($config);
    },
    'wednesday_time_rule' => function(ContainerInterface $c, $p, $config = array()) {
        return new TimeRangeRule($config);
    },
    'thursday_time_rule'  => function(ContainerInterface $c, $p, $config = array()) {
        return new TimeRangeRule($config);
    },
    'friday_time_rule'    => function(ContainerInterface $c, $p, $config = array()) {
        return new TimeRangeRule($config);
    },
    'saturday_time_rule'  => function(ContainerInterface $c, $p, $config = array()) {
        return new TimeRangeRule($config);
    },
    'sunday_time_rule'    => function(ContainerInterface $c, $p, $config = array()) {
        return new TimeRangeRule($config);
    },
    'weekdays_time_rule'    => function(ContainerInterface $c, $p, $config = array()) {
        return new TimeRangeRule($config);
    },
    'weekend_time_rule'     => function(ContainerInterface $c, $p, $config = array()) {
        return new TimeRangeRule($config);
    },
    'all_week_time_rule'    => function(ContainerInterface $c, $p, $config = array()) {
        return new TimeRangeRule($config);
    }
);
