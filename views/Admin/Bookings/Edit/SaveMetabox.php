<?php
// Post status dropdown
echo eddBookings()->renderView('Fragment.Dropdown', array(
    'id'       => 'post-status',
    'name'     => 'post_status',
    'items'    => array(
        'publish' => __('Booked'),
        'draft'   => __('Draft'),
        'trash'   => __('Trash')
    ),
    'selected' => get_post_status($data['id'])
));
?>

<input type="submit" class="button button-primary right" value="<?php _e('Save Booking', 'eddbk') ?>">
<div class="clear"></div>
