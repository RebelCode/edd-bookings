<?php
$textDomain = eddBookings()->getI18n()->getDomain();
?>

<div class="headline-feature feature-section one-col">
    <h2><?php _e('Cleaner Options', 'eddbk'); ?></h2>
    <center>
        <p>
            <?php _e("Set up your bookable services quicker than before with the newly revised and re-organised booking options for Downloads.", 'eddbk'); ?>
        </p>
        <div class="media-container">
            <img src="<?php echo EDD_BK_IMGS_URL; ?>cleaner-options.png" />
        </div>
    </center>
</div>

<div class="headline-feature feature-section one-col">
    <h2><?php _e('Smart Notices', 'eddbk'); ?></h2>
    <center>
        <p>
            <?php _e("We've added a few notices to make sure you never miss anything important.", 'eddbk'); ?>
        </p>
        <div class="media-container">
            <img src="<?php echo EDD_BK_IMGS_URL; ?>no-avail-times-notice.png" />
        </div>
    </center>
</div>

<div class="headline-feature feature-section three-col">
    <h2><?php _e('Other Minor Changes', 'eddbk'); ?></h2>
    <div class="col">
        <p>
            <?php
            printf(
                __("We've fixed some calendar warning messages on the site that were showing %s instead of dates.", $textDomain),
                '<code>"%s"</code>'
            );
            ?>
        </p>
    </div>
    <div class="col">
        <p>
            <?php _e("We've fixed a bug where the calendar would occassionaly get stuck loading forever on December.", $textDomain); ?>
        </p>
    </div>
    <div class="col">
        <p>
            <?php _e("In case you don't like sliders, you can now manually enter time options in the availability table.", $textDomain); ?>
        </p>
    </div>
</div>

<hr/>

<!-- Previous version changes -->
<div class="headline-feature feature-section">
    <h2><?php _e('Previous Version', $textDomain); ?></h2>
    <div class="two-col-text">
        <p>
            <?php
            _e("<b>Version 2.0.0 was a major update</b>, and quite a few things changed from version 1.0.3. Your existing bookable downloads have been automatically converted to be compatible with the new version. Don't worry, you won't lose your settings for these downloads; we've just done some improvements behind the scenes.", $textDomain);
            ?>
        </p>
        <p>
            <?php
            _e("<b>However</b>, we've also improved the usability of the availability builder. Some changes have made the availability time rules work a bit differently, making them more intuitive. We highly recommend that you go over your downloads and confirm the correctness of their available times. They might need some tweaking.", $textDomain);
            ?>
        </p>
    </div>
</div>

<div class="headline-feature feature-section two-col">
    <div class="col">
        <h3><?php _e('New Admin Calendar', 'eddk'); ?></h3>
        <p><?php _e('Get a quick overview of your bookings for any month, week or day, with booking details available with just a simple click.', 'eddbk'); ?></p>
        <p><?php _e('Navigate to <em>Bookings &raquo; Calendar</em> to try it out!', 'eddbk'); ?></p>
    </div>
    <div class="col">
        <img src="<?php echo EDD_BK_IMGS_URL; ?>admin-calendar.png" />
    </div>
</div>

<div class="headline-feature feature-section two-col">
    <div class="col">
        <h3><?php _e('New Timezone Option', 'eddk'); ?></h3>
        <p><?php _e("Whether you're providing international services or local resources, EDD Bookings now hands over the controls to you.", $textDomain); ?></p>
        <p><?php _e("Choose whether customers can book using their local time or your store's time.", $textDomain); ?></p>
    </div>
    <div class="col">
        <img src="<?php echo EDD_BK_IMGS_URL; ?>admin-use-customer-tz.png" />
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
            <h4><?php _e('Frontend Submissions Integration', $textDomain); ?></h4>
            <p>
                <a href="https://easydigitaldownloads.com/downloads/frontend-submissions/" target="_blank">
                    <?php _e('Frontend Submissions', $textDomain); ?>
                </a>
                <?php _e("is an EDD extension that turns your site into a complete marketplace. We're looking to integrate EDD Bookings with FES so that your users can create bookable downloads and manage their own bookings.", $textDomain); ?>
            </p>
        </div>
    </div>
</div>