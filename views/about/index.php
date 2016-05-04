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
            _e('<b>This is a major update!</b> If you\'re updating from EDD Bookings v1.0.3 or earlier, you\'ll find
            that we\'ve updated your settings for Downloads that had bookings enabled. We\'ve also automatically created a
            Schedule and a Timetable for each of these downloads to preserve their functionality on your site.
            <i>Nothing has changed, but everything is new!</i>', $textDomain);
            ?>
        </p>
        <p>
            <?php
            _e('Each of your Downloads\' calendar builder settings have been converted into Timetables, each of which is
            assigned to its own Schedule. However, we <span class="important-red">highly recommend</span> that you go
            over your Timetable settings to confirm their correctness. Some of the Calendar Builder rules now behave
            differently, so you might want to tweak them to your needs.', $textDomain);
            ?>
        </p>
    </div>
</div>

<hr/>

<div class="headline-feature feature-section one-col">
    <h2><?php _e('Schedules &amp; Timetables', $textDomain); ?></h2>
    <div class="media-container">
        <img src="" />
    </div>
    <center>
        <img src="<?php echo EDD_BK_IMGS_URL; ?>cpt-rels.png" />
    </center>
</div>

<hr/>

<div class="feature-section two-col">
    <div class="col">
        <center>
            <img src="<?php echo EDD_BK_IMGS_URL; ?>downloads-schedules-rel.png" />
        </center>
    </div>
    <div class="col">
        <h3><?php _e('Schedules', $textDomain); ?></h3>
        <p>
            <?php
            _e('Your bookable downloads make use of a <strong>Schedule</strong>, which works like a real-life diary 
            to provide a structured way of recording all the bookings being made.', $textDomain);
            ?>
        </p>
        <p>
            <?php
            _e('You can set up multiple bookable downloads to use the same schedule, so that booked times for one
            download cannot be booked for another download that uses the same schedule. This is ideal in situations
            where an individual offers multiple services, but not simultaneously.', $textDomain);
            ?>
        </p>
    </div>
</div>

<hr/>

<div class="feature-section two-col">
    <div class="col">
        <center>
            <img src="<?php echo EDD_BK_IMGS_URL; ?>schedules-timetables-rel.png" />
        </center>
    </div>
    <div class="col">
        <h3><?php _e('Timetables', $textDomain); ?></h3>
        <p>
            <?php
            _e(
            'A <strong>Timetable</strong> is a definition of the dates and times your customers are allowed to book.
            You\'ll be able to select the times, days, weeks and months that your service is available.', $textDomain);
            ?>
        </p>
        <p>
            <?php
            _e('Each one of your schedules is linked to a timetable, and multiple schedules can share a single
            timetable. This is very useful when you offer the same avaialable times for multiple services 
            since you won\'t need to create the same rules multiple times.', $textDomain);
            ?>
        </p>
    </div>
</div>

<hr/>

<div class="changelog">
    <h2><?php _e('Roadmap', $textDomain); ?></h2>
    <div class="under-the-hood three-col">
        <div class="col">
            <h4><?php _e('Timetable Calendar Preview', $textDomain); ?></h4>
            <p>
                <?php
                _e('A calendar preview for the Timetable editor is in the works to reflect changes while you edit.
                We\'re also looking to make a few adjustments to the interface in order to make it more intuitive.', $textDomain);
                ?>
            </p>
        </div>
        <div class="col">
            <h4><?php _e('Booking Handling', $textDomain); ?></h4>
            <p>
                <?php
                _e('Cancelling bookings, approving and confirming bookings, placing bookings manually from the
                backend, reporting of bookings and emailing to clients are all planned for future versions.
                Our aim is to make your WordPress Dashboard feel and function like a booking system.', $textDomain);
                ?>
            </p>
        </div>
        <div class="col">
            <h4><?php _e('Frontend Submissions Integration', $textDomain); ?></h4>
            <p>
                <a href="https://easydigitaldownloads.com/downloads/frontend-submissions/" target="_blank">
                    <?php _e('Frontend Submissions', $textDomain); ?>
                </a>
                <?php
                _e('is an EDD extension that turns your site into a complete marketplace. We\'re looking to
                integrate EDD Bookings with FES so that your users can create bookable downloads and manage their own
                bookings.', $textDomain);
                ?>
            </p>
        </div>
    </div>
</div>