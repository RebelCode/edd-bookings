<?php
$docsUrl = 'http://docs.easydigitaldownloads.com/category/1100-bookings';
$availBuilderDocUrl = 'http://docs.easydigitaldownloads.com/article/1101-bookings-availability-builder';
$docsUrlAttr = sprintf('href="%s" target="_blank"', $docsUrl);
$availBuilderLinkAttr = sprintf('href="%s" target="_blank"', $availBuilderDocUrl);
?>

<style>
    .about-wrap .feature-section {
        text-align: center;
    }
    .about-wrap .feature-section .col {
        margin-top: 0;
    }
</style>

<div class="headline-feature feature-section">
    <h2><?php _e('How to Create Your First Booking', 'eddbk'); ?></h2>
    <p>
        <?php _e('This is a basic introduction on how to use EDD Bookings. ', 'eddbk'); ?>
        <br/>
        <?php _e('For more details please visit our in-depth documentation.', 'eddbk'); ?>
        <br/>
        <a <?php echo $docsUrlAttr; ?>>
            <?php _e('EDD Bookings Documentation', 'eddbk'); ?>
        </a>
    </p>
</div>

<hr />

<div class="headline-feature feature-section">
    <h2><?php _e('Creating a bookable download', 'eddbk'); ?></h2>
    <p>
        <?php
        _e('Configuring the Bookings extension is simple and is done on a per-product basis. When creating or editing a Download look for the Booking meta box. It will look like this:', 'eddbk');
        ?>
    </p>
    <img src="<?php echo EDD_BK_IMGS_URL; ?>bookings-metabox-closed.png" />
    <p>
        <?php
        _e('After checking the box to enable booking, the normal pricing and inventory meta boxes will disappear and the Booking meta box will look like this:', 'eddbk');
        ?>
    </p>
    <img src="<?php echo EDD_BK_IMGS_URL; ?>bookings-metabox-open.png" />
</div>

<div class="headline-feature feature-section three-col">
    <div class="col">
        <h4><?php _e('Session length', 'eddbk'); ?></h4>
        <p>
            <?php _e("The first thing to configure is the session length, which is how long a single bookable session is.", 'eddbk'); ?>
        </p>
    </div>
    <div class="col">
        <h4><?php _e('Customers can book from ... to ...', 'eddbk'); ?></h4>
        <p>
            <?php _e("Next, we'll need to set the number of sessions a customer can book in a single booking.", 'eddbk'); ?>
        </p>
    </div>
    <div class="col">
        <h4><?php _e('Cost per session', 'eddbk'); ?></h4>
        <p>
            <?php _e("Following that, we'll need to set a price for a single session.", 'eddbk'); ?>
        </p>
    </div>
</div>

<hr />

<div class="headline-feature feature-section">
    <h2><?php _e('Setting up your availability', 'eddbk'); ?></h2>
    <p><?php _e("Finally, you'll need to set up the availability for this Download. The availability represents the dates and times that are available for booking by your customers.", 'eddbk'); ?></p>
    <p><?php _e("To keep this guide short and sweet, let's add one simple available time. Click the Add button in the Available Times table. This will add a row to the table. Under the Time Unit column, choose Weekdays and set the Start and End to, say, 08:00 and 20:00 respectively.", 'eddbk'); ?></p>
    <img src="<?php echo EDD_BK_IMGS_URL; ?>booking-availability-example.png" />
</div>

<hr/>

<div class="headline-feature feature-section">
    <h2><?php _e("You're Done!", 'eddbk'); ?></h2>
    <p><?php _e('You now have a bookable Download that allows bookings on week days (Monday to Friday) from 8am till 8pm!', 'eddbk'); ?></p>
    <p>
        <?php _e('Check out our in-depth documentation for more details on how to set up your availability using the availability builder.', 'eddbk'); ?>
        <br/>
        <a <?php echo $availBuilderLinkAttr; ?>>
            <?php _e('Availability Builder Documentation', 'eddbk'); ?>
        </a>
    </p>
</div>
