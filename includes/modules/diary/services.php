<?php

use \Interop\Container\ContainerInterface;
use \RebelCode\Diary\DateTime\DateTime;
use \RebelCode\Diary\DateTime\Period;
use \RebelCode\EddBookings\Component\DiaryComponent;

return array(
    'diary' => function(ContainerInterface $c) {
        return new DiaryComponent($c->get('plugin'), $c->get('storage_adapter'));
    },
    'period' => function(ContainerInterface $c, $prev, array $config = array()) {
        $start = $config['start'];
        $end   = $config['end'];

        return new Period($start, $end);
    },
    'datetime' => function(ContainerInterface $c, $prev, array $config = array()) {
        $timestamp = isset($config[0])
            ? $config[0]
            : 0;
        $timezone = isset($config['timezone'])
            ? $config['timezone']
            : 'UTC';

        return DateTime::createFromTimestamp($timestamp, new DateTimeZone($timezone));
    }
);
