<?php
// Post status dropdown
echo eddBookings()->renderView('Fragment.Dropdown', array(
    'id'       => 'post-status',
    'name'     => 'post_status',
    'items'    => array(
        'publish' => __('Confirmed', 'eddbk'),
        'draft'   => __('Draft')
    ),
    'selected' => get_post_status($data['id'])
));
echo eddBookings()->renderView('Admin.Tooltip', array(
    'text'  => sprintf(
        '%s<hr/>%s',
        __('Confirmed bookings are saved bookings that represent a booking that will happen. Its date(s) and time(s) will be blocked from the front-end calendar so other people cannot make bookings at that time. These bookings will also appear in your Calendar.', 'eddbk'),
        __('Draft bookings are bookings that are just saved in the database. The system does not acknowledge them and they are simply for your convenience. They can be set to "Confirmed" at a later date. These bookings will not appear on your Calendar.', 'eddbk')
    ),
    'icon'  => 'question-circle',
    'align' => array('left', 'bottom')
));
?>

<input type="submit" class="button button-primary right" value="<?php _e('Save Booking', 'eddbk') ?>">
<div class="clear"></div>
