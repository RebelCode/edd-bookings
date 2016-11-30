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
    'text'  => ' ',
    'icon'  => 'question-circle',
    'align' => array('left', 'bottom')
));
?>

<input type="submit" class="button button-primary right" value="<?php _e('Save Booking', 'eddbk') ?>">
<div class="clear"></div>
