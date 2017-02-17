<?php

use \Interop\Container\ContainerInterface;
use \RebelCode\EddBookings\Utils\DateTimeFormatter;

return array(
    'datetime_formatter' => function(ContainerInterface $c) {
        return new DateTimeFormatter(
            $c->get('date_format'),
            $c->get('time_format'),
            $c->get('datetime_format_pattern')
        );
    },

    'browser_datetime_formatter' => function(ContainerInterface $c) {
        return new DateTimeFormatter(
            $c->get('browser_date_format'),
            $c->get('browser_time_format'),
            $c->get('browser_datetime_format_pattern')
        );
    },

    'date_format' => function() {
        return get_option('date_format');
    },
    'time_format' => function() {
        return get_option('time_format');
    },
    'datetime_format_pattern' => function() {
        return '%2$s - %1$s';
    },

    'browser_date_format' => function() {
        return 'Y-m-d';
    },
    'browser_time_format' => function() {
        return 'H:i:s';
    },
    'browser_datetime_format_pattern' => function() {
        return '%1$s %2$s';
    }
);
