<?php $textDomain = eddBookings()->getI18n()->getDomain(); ?>

<style>
    .about-wrap .feature-section {
        text-align: center;
    }
    .about-wrap .feature-section .col {
        margin-top: 0;
    }
</style>

<div class="headline-feature feature-section one-col">
    <h2><?php _e('How to Create Your First Booking', $textDomain); ?></h2>
    <p>
        <?php
        _e('This is a basic introduction on how to use EDD Bookings. ', $textDomain);
        printf(
            __('For more details please <a %s>visit our full documentation</a>.', $textDomain),
            'href="http://docs.easydigitaldownloads.com/category/1100-bookings" target="_blank"'
        );
        ?>
    </p>
</div>

<hr />

<div class="headline-feature feature-section">
    <h2><?php _e('Creating a bookable download', $textDomain); ?></h2>
    <p>
        <?php
        _e('Configuring the Bookings extension is simple and is done on a per-product basis. When creating or editing a Download look for the Booking meta box. It will look like this:', $textDomain);
        ?>
    </p>
    <img src="<?php echo EDD_BK_IMGS_URL; ?>bookings-metabox-closed.png" />
    <p>
        <?php
        _e('After checking the box to enable booking, the normal pricing and inventory meta boxes will disappear and the Booking meta box will look like this:', $textDomain);
        ?>
    </p>
    <img src="<?php echo EDD_BK_IMGS_URL; ?>bookings-metabox-open.png" />
</div>

<div class="headline-feature feature-section three-col">
    <div class="col">
        <h4><?php _e('Session Length', $textDomain); ?></h4>
        <p>
            <?php _e("The first thing to configure is the session length, which is how long a single bookable session is.", $textDomain); ?>
        </p>
    </div>
    <div class="col">
        <h4><?php _e('Customers can book from ... to ...', $textDomain); ?></h4>
        <p>
            <?php _e("Next, we'll need to set the number of sessions a customer can book in a single booking.", $textDomain); ?>
        </p>
    </div>
    <div class="col">
        <h4><?php _e('Cost per session', $textDomain); ?></h4>
        <p>
            <?php _e("Following that, we'll need to set a price for a single session.", $textDomain); ?>
        </p>
    </div>
</div>

<hr />

<div class="headline-feature feature-section">
    <h2><?php _e('Setting up your availability', $textDomain); ?></h2>
    <p><?php _e("Finally, you'll need to set up the availability for this Download. The availability represents the dates and times that are available for booking by your customers.", $textDomain); ?></p>
    <p><?php _e("To keep this guide short and sweet, let's add one simple available time. Click the Add button in the Available Times table. This will add a row to the table. Under the Time Unit column, choose Weekdays and set the Start and End to, say, 08:00 and 20:00 respectively.", $textDomain); ?></p>
    <img src="<?php echo EDD_BK_IMGS_URL; ?>booking-availability-example.png" />
</div>

<hr/>

<div class="headline-feature feature-section">
    <h2><?php _e("You're Done!", $textDomain); ?></h2>
    <p><?php _e('You now have a bookable Download that allows bookings on week days (Monday to Friday) from 8am till 8pm!', $textDomain); ?></p>
    <p><?php sprintf(_e('Be sure to check out our <a %s>full documentation</a> for more details on how to set up your availability.', $textDomain)); ?></p>
</div>