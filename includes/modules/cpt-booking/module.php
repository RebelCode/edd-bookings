<?php

use \RebelCode\EddBookings\Block\Html\LinkTag;
use \RebelCode\EddBookings\Block\Html\RegularTag;

$bookingCptSlug = 'edd_booking';

$bookingCptHelpLabel = function() use ($bookingCptSlug) {
    $noBookingsText = __('You do not have any bookings!', 'eddbk');
    $helpText = _x(
        'To create a new bookable service go to %1$s and tick the %2$s option.', '%1$s = "Downloads > Add New". %2$s = "Enable Bookings".', 'eddbk'
    );

    $downloadsLabel = __('Downloads', 'eddbk');
    $addNewLabel = __('Add New', 'eddbk');
    $newServiceNav = implode(' &raquo; ', array($downloadsLabel, $addNewLabel));
    $newServiceUrl = admin_url(sprintf('post-new.php?post_type=%s', $bookingCptSlug));
    $newServiceLink = new LinkTag($newServiceNav, $newServiceUrl);

    $enableBookings = new RegularTag('em', array(), __('Enable Bookings', 'eddbk'));
    $helpTextFormatted = sprintf($helpText, $newServiceLink, $enableBookings);

    return sprintf('%1$s <br/> %2$s', $noBookingsText, $helpTextFormatted);
};

$config = array(
    'booking_cpt_slug'        => $bookingCptSlug,
    'booking_cpt_labels'      => array(
        'name'               => __('Bookings', 'eddbk'),
        'singular_name'      => __('Booking', 'eddbk'),
        'add_new'            => _x('Add New', 'eddbk', 'eddbk'),
        'add_new_item'       => __('Add New Booking', 'eddbk'),
        'edit_item'          => __('Edit Booking', 'eddbk'),
        'new_item'           => __('New Booking', 'eddbk'),
        'view_item'          => __('View Bookins', 'eddbk'),
        'all_items'          => __('All Bookings', 'eddbk'),
        'search_items'       => __('Search Bookings', 'eddbk'),
        'not_found'          => $bookingCptHelpLabel(),
        'not_found_in_trash' => __('No bookings found in trash', 'eddbk')
    ),
    'booking_cpt_properties'  => array(
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
    ),
    'booking_cpt_update_msgs' => array(
        1  => _x('Booking updated.', 'eddbk'),
        4  => _x('Booking updated.', 'eddbk'),
        6  => _x('Booking confirmed.', 'eddbk'),
        7  => _x('Booking saved.', 'eddbk'),
        8  => _x('Booking submitted.', 'eddbk'),
        10 => _x('Booking draft updated.', 'eddbk')
    )
);

foreach ($config as $_key => $_value) {
    $this->setData($_key, $_value);
}
