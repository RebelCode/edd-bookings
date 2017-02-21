<?php

use \Interop\Container\ContainerInterface;
use \RebelCode\EddBookings\Admin\Calendar\Block\CalendarPage;
use \RebelCode\EddBookings\Admin\Calendar\Calendar;
use \RebelCode\EddBookings\Admin\Calendar\CalendarAjax;
use \RebelCode\WordPress\Admin\Menu\SubMenu;
use \RebelCode\WordPress\Admin\Page;

return array(
    'admin_calendar' => function(ContainerInterface $c) {
        return new Calendar(
            $c->get('plugin'),
            $c->get('admin_menubar'),
            $c->get('event_manager'),
            $c->get('admin_calendar_menu')
        );
    },
    'admin_calendar_menu' => function(ContainerInterface $c) {
        return new SubMenu(
            'edit.php?post_type=edd_booking',
            'edd-bk-calendar',
            __('Calendar', 'eddbk'),
            $c->get('admin_calendar_page')
        );
    },
    'admin_calendar_page' => function(ContainerInterface $c) {
        return new Page(
            __('Calendar', 'eddbk'),
            $c->get('admin_calendar_page_block'),
            'manage_options'
        );
    },
    'admin_calendar_page_block' => function() {
        return new CalendarPage();
    },
    'admin_calendar_ajax' => function(ContainerInterface $c) {
        return new CalendarAjax(
            $c->get('plugin'),
            $c->get('ajax_manager'),
            $c->get('booking_controller'),
            $c->get('datetime_formatter')
        );
    }
);
