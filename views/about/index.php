<?php
$textDomain = eddBookings()->getI18n()->getDomain();
?>
<div class="feature-section">
    <h3 class="important-red">
        <i class="fa fa-warning"></i>
            <?php _e('Important update notice for v1.0.3 or earlier', $textDomain); ?>
    </h3> 
    <div class="two-col-text">
        <p>
            <?php
            _e('<b>This is a major update</b>, and a few things have changed. Your existing bookable downloads have
                been automatically converted to be compatible with this version. Don\'t worry, you won\'t lose your
                settings for these downloads; we\'ve just done some improvements behind the scenes.', $textDomain);
            ?>
        </p>
        <p>
            <?php
            _e('<b>However</b>, we\'ve also improved the usability of the availability builder so that some rules now
                behave more as you\'d expect. We <span class="important-red">highly recommend</span> that you go over
                your downloads and confirm the correctness of their available times. You might need to tweak them a
                bit.', $textDomain);
            ?>
        </p>
    </div>
</div>

<hr/>

<!-- Calendar headline -->
<div class="headline-feature feature-section one-col">
    <h2><?php _e('New Admin Calendar', $textDomain); ?></h2>
    <center>
        <div class="media-container">
            <img src="<?php echo EDD_BK_IMGS_URL; ?>admin-calendar.png" />
        </div>
        <p>
            <?php _e("We're excited to introduce the admin calendar, which can be found from the new admin <i>Bookings</i> menu.", $textDomain); ?>
            <br/>
            <?php _e("With full interaction and three different views, we think this will take your game to the next level.", $textDomain); ?>
        </p>
    </center>
</div>

<!-- Calendar features -->
<div class="feature-section three-col">
    <div class="col">
        <img src="<?php echo EDD_BK_IMGS_URL; ?>admin-calendar-week.png" />
        <h3><?php _e('Week View', $textDomain); ?></h3>
        <p>
            <?php _e('Get an overview of all your bookings for any given week.', $textDomain); ?>
        </p>
    </div>
    <div class="col">
        <img src="<?php echo EDD_BK_IMGS_URL; ?>admin-calendar-day.png" />
        <h3><?php _e('Day View', $textDomain); ?></h3>
        <p>
            <?php _e('Get an "agenda" style view of your bookings, for any given day.'); ?>
        </p>
    </div>
    <div class="col">
        <img src="<?php echo EDD_BK_IMGS_URL; ?>admin-calendar-popup.png" />
        <h3><?php _e('Booking Info', $textDomain); ?></h3>
        <p>
            <?php _e('A simple click reveals the important booking information.', $textDomain); ?>
        </p>
    </div>
</div>

<hr/>

<!-- "Use Customer Timezone" headline -->
<div class="headline-feature feature-section one-col">
    <h2><?php _e('New Timezone Control Option', $textDomain); ?></h2>
    <center>
        <div class="media-container">
            <img src="<?php echo EDD_BK_IMGS_URL; ?>admin-use-customer-tz.png" />
        </div>
        <p>
            <?php _e("Whether you're providing international services or local resources, EDD Bookings now hands over the controls to you.", $textDomain); ?>
            <br/>
            <?php _e("Choose whether customers book using their local time or your store's time.", $textDomain); ?>
        </p>
    </center>
</div>

<hr/>

<div class="changelog">
    <h2><?php _e('Roadmap', $textDomain); ?> <small class="about-text"><?php _e('Coming Soon', $textDomain); ?></small></h2>
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