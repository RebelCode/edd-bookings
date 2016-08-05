<?php
$textDomain = eddBookings()->getI18n()->getDomain();
?>

<div class="headline-feature feature-section one-col">
    <h2><?php _e('Frontend Submissions Integration', 'eddbk'); ?></h2>
    <center>
        <p>
            <a href="https://easydigitaldownloads.com/downloads/frontend-submissions/" target="_blank">
                <?php _e('Frontend Submissions', $textDomain); ?>
            </a>
            <?php _e("is an EDD extension that turns your site into a complete marketplace.", 'eddbk'); ?>
            <?php _e("Now, your marketplace vendors can create bookable Downloads, sell their services and manage their bookings.", 'eddbk'); ?>
        </p>
        <div class="media-container">
            <img src="<?php echo EDD_BK_IMGS_URL; ?>fes-integration.png" />
        </div>
    </center>
</div>

<hr/>

<!-- Previous version changes -->
<div class="headline-feature feature-section one-col">
    <center>
        <h2><?php _e('Previous Version', $textDomain); ?></h2>
        <p class="eddbk-small"><?php _e("In case you've missed it", $textDomain); ?></p>
    </center>
</div>

<div class="headline-feature feature-section two-col">
    <div class="col">
        <h3><?php _e('Cleaner Options', 'eddk'); ?></h3>
        <p><?php _e('Set up your bookable services quicker than before with the newly revised and re-organised booking options for Downloads.', 'eddbk'); ?></p>
    </div>
    <div class="col">
        <img src="<?php echo EDD_BK_IMGS_URL; ?>cleaner-options.png" />
    </div>
</div>

<div class="headline-feature feature-section two-col">
    <div class="col">
        <h3><?php _e('Smart Notices', 'eddk'); ?></h3>
        <p><?php _e("We've added a few notices to make sure you never miss anything important.", $textDomain); ?></p>
    </div>
    <div class="col">
        <img src="<?php echo EDD_BK_IMGS_URL; ?>no-avail-times-notice.png" />
    </div>
</div>

<hr/>

<div class="headline-feature feature-section one-col">
    <center>
        <h2 class="eddbk-roadmap"><?php _e('Roadmap', $textDomain); ?></h2>
        <p class="eddbk-small"><?php _e('Coming Soon', $textDomain); ?></p>
    </center>
</div>

<div class="changelog">
    <div class="under-the-hood three-col">
        <div class="col">
            <h4><?php _e('Availability Calendar Preview', $textDomain); ?></h4>
            <p>
                <?php _e("A calendar preview for the Availability editor is in the works to reflect changes while you edit. We're also looking to make a few adjustments to the interface in order to make it more intuitive.", $textDomain); ?>
            </p>
        </div>
        <div class="col">
            <h4><?php _e('Booking Handling', $textDomain); ?></h4>
            <p>
                <?php _e("Cancelling bookings, approving and confirming bookings, placing bookings manually from the backend, reporting of bookings and emailing to clients are all planned for future versions. Our aim is to make your WordPress Dashboard feel and function like a booking system.", $textDomain); ?>
            </p>
        </div>
        <div class="col">
            <h4><?php _e('Parallel Bookings', $textDomain); ?></h4>
            <p>
                <?php _e("Allow more than 1 booking to be purchased for a specific date and time. This feature will be most useful for class-type services that provide a service to a group of people, rather than just a single person.", $textDomain); ?>
            </p>
        </div>
    </div>
</div>