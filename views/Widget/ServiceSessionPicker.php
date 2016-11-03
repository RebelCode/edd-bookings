<?php echo eddBookings()->renderView('Widget.SessionPicker', $data); ?>

<div class="edd-bk-session-picker-msg edd-bk-session-picker-session-unavailable">
    <?php _e('Your chosen session is unavailable. It may have been booked by someone else. If you believe this is a mistake, please contact the site administrator.', 'eddbk') ?>
</div>

<input class="edd-bk-fs-start" type="hidden" name="edd_bk_start" />
<input class="edd-bk-fs-duration" type="hidden" name="edd_bk_duration" />
<input class="edd-bk-fs-timezone" type="hidden" name="edd_bk_timezone" />
