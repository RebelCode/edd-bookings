<?php

use \Interop\Container\ContainerInterface;
use \RebelCode\EddBookings\Block\Html\LinkTag;
use \RebelCode\EddBookings\Block\Html\RegularTag;
use \RebelCode\EddBookings\CustomPostType\Booking\Block\DevDataBlock;
use \RebelCode\EddBookings\CustomPostType\Booking\Block\EditMetaBoxBlock;
use \RebelCode\EddBookings\CustomPostType\Booking\Block\SaveMetaBoxBlock;
use \RebelCode\EddBookings\CustomPostType\Booking\EditPageHandler;
use \RebelCode\EddBookings\CustomPostType\Booking\MetaBox\DevMetaBox;
use \RebelCode\EddBookings\CustomPostType\Booking\PostsTable;
use \RebelCode\WordPress\Admin\Metabox\MetaBox;
use \RebelCode\WordPress\Admin\Metabox\MetaBoxInterface;

return array(
    'booking_cpt' => function(ContainerInterface $c)  use ($module) {
        $cpt = $c->get('factory')->make('cpt', array(
            'slug'        => $module->getData('booking_cpt_slug'),
            'labels'      => $c->get('booking_cpt_labels'),
            'properties'  => $c->get('booking_cpt_properties'),
            'update_msgs' => $c->get('booking_cpt_update_msgs')
        ));

        return $cpt;
    },

    'booking_posts_table' => function(ContainerInterface $c) {
        return new PostsTable(
            $c->get('plugin'),
            $c->get('event_manager'),
            $c->get('booking_controller'),
            $c->get('booking_cpt'),
            $c->get('datetime_formatter')
        );
    },

    'booking_cpt_labels' => function(ContainerInterface $c) {
        return array(
            'name'               => __('Bookings', 'eddbk'),
            'singular_name'      => __('Booking', 'eddbk'),
            'add_new'            => _x('Add New', 'eddbk', 'eddbk'),
            'add_new_item'       => __('Add New Booking', 'eddbk'),
            'edit_item'          => __('Edit Booking', 'eddbk'),
            'new_item'           => __('New Booking', 'eddbk'),
            'view_item'          => __('View Bookins', 'eddbk'),
            'all_items'          => __('All Bookings', 'eddbk'),
            'search_items'       => __('Search Bookings', 'eddbk'),
            'not_found'          => $c->get('booking_cpt_help_label'),
            'not_found_in_trash' => __('No bookings found in trash', 'eddbk')
        );
    },

    'booking_cpt_update_msgs' => function(ContainerInterface $c) {
        return array(
            1  => _x('Booking updated.', 'eddbk'),
            4  => _x('Booking updated.', 'eddbk'),
            6  => _x('Booking confirmed.', 'eddbk'),
            7  => _x('Booking saved.', 'eddbk'),
            8  => _x('Booking submitted.', 'eddbk'),
            10 => _x('Booking draft updated.', 'eddbk')
        );
    },

    'booking_cpt_help_label' => function(ContainerInterface $c) {
        $serviceCptSlug = $c->get('service_cpt_slug');

        $noBookingsText    = __('You do not have any bookings!', 'eddbk');
        $helpText          = _x(
            'To create a new bookable service go to %1$s and tick the %2$s option.',
            '%1$s = "Downloads > Add New". %2$s = "Enable Bookings".',
            'eddbk'
        );

        $downloadsLabel    = __('Downloads', 'eddbk');
        $addNewLabel       = __('Add New', 'eddbk');
        $newServiceNav     = implode(' &raquo; ', array($downloadsLabel, $addNewLabel));
        $newServiceUrl     = admin_url(sprintf('post-new.php?post_type=%s', $serviceCptSlug));
        $newServiceLink    = new LinkTag($newServiceNav, $newServiceUrl);

        $enableBookings    = new RegularTag('em', array(), __('Enable Bookings', 'eddbk'));
        $helpTextFormatted = sprintf($helpText, $newServiceLink, $enableBookings);

        return sprintf('%1$s <br/> %2$s', $noBookingsText, $helpTextFormatted);
    },

    'booking_cpt_properties' => function() {
        return array(
            'public'       => false,
            'show_ui'      => true,
            'has_archive'  => false,
            'show_in_menu' => true,
            'menu_icon'    => 'dashicons-calendar',
            'supports'     => false,
            'capabilities' => array(
                'create_posts' => true
            ),
            'map_meta_cap' => true
        );
    },

    'booking_edit_page_handler' => function(ContainerInterface $c) {
        return new EditPageHandler(
            $c->get('plugin'),
            $c->get('event_manager'),
            $c->get('booking_cpt'),
            $c->get('factory')
        );
    },

    'booking_edit_metabox' => function(ContainerInterface $c) {
        return new MetaBox(
            $c->get('plugin'),
            $c->get('event_manager'),
            'edit-booking',
            __('Edit Booking Details', 'eddbk'),
            $c->get('booking_edit_metabox_content'),
            MetaBoxInterface::CTX_NORMAL,
            MetaBoxInterface::PRIORITY_CORE,
            $c->get('booking_cpt')->getSlug()
        );
    },

    'booking_edit_metabox_content' => function(ContainerInterface $c) {
        return new EditMetaBoxBlock(
            $c->get('booking_controller'),
            $c->get('edd_html'),
            $c->get('browser_datetime_formatter')
        );
    },

    'booking_save_metabox' => function(ContainerInterface $c) {
        return new MetaBox(
            $c->get('plugin'),
            $c->get('event_manager'),
            'save-booking',
            __('Save Booking', 'eddbk'),
            $c->get('booking_save_metabox_content'),
            MetaBoxInterface::CTX_SIDE,
            MetaBoxInterface::PRIORITY_HIGH,
            $c->get('booking_cpt')->getSlug()
        );
    },

    'booking_save_metabox_content' => function(ContainerInterface $c) {
        return new SaveMetaBoxBlock($c->get('booking_controller'));
    },

    'booking_developer_metabox' => function(ContainerInterface $c) {
        return new DevMetaBox(
            $c->get('plugin'),
            $c->get('event_manager'),
            'dev-booking-data',
            __('Developer Data', 'eddbk'),
            $c->get('booking_developer_metabox_content'),
            MetaBoxInterface::CTX_ADVANCED,
            MetaBoxInterface::PRIORITY_LOW,
            $c->get('booking_cpt')->getSlug()
        );
    },
    'booking_developer_metabox_content' => function(ContainerInterface $c) {
        return new DevDataBlock($c->get('booking_controller'));
    }
);
