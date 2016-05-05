<?php $textDomain = eddBookings()->getI18n()->getDomain(); ?>

<div class="headline-feature feature-section one-col">
    <h2><?php _e('How to Create Your First Booking', $textDomain); ?></h2>
    <p>
        <?php
        _e('This is a basic instroduction on how to use EDD Bookings. ', $textDomain);
        _e(sprintf('For more details please <a %s>visit our full documentation</a>.',
                        'href="http://docs.easydigitaldownloads.com/category/1100-bookings" target="_blank"'),
                $textDomain);
        ?>
    </p>
</div>

<hr />

<div class="feature-section one-col">
    <h3><?php _e('Adding a Bookable Download', $textDomain); ?></h3>
    <img src="<?php echo EDD_BK_IMGS_URL; ?>booking-options.png" />

    <h4><?php _e('Step 1'); ?></h4>
    <p>
        <?php
        echo htmlentities(
        __('To create your first Booking head over to "Downloads" > "Add New". After entering the required information
            for the Download\'s title, description, categories, tags and so on, you can get right down to the Booking
            Options. ', $textDomain)
        );
        _e('Scroll down to the "Booking Options" metabox and tick the "Enable bookings for this download" checkbox.
            This will automatically show more booking options as well as remove all the unrequired metaboxes', $textDomain);
        ?>
    </p>

    <h4><?php _e('Step 2', $textDomain); ?></h4>
    <p>
        <?php
        _e('Start by selecting the session length, the number of sessions each customer can book and the cost per
            session. Once these details have been filled in you can move on to "Schedule". This automatically selects an
            option titled "Create new schedule and availability". Leave this setting as is; we can change the titles and
            details of the auto-created schedule and availability later.', $textDomain);
        ?>
    </p>
    <p>
        <?php
        _e('Next you can choose if the service should use customer timezones. If you tick the option, date and times
           shown on the site will be relative to the customer\'s timezone. This can be useful for international services
           like web-cast consultancies but not recommended for local services, such as reservations.');
        ?>
    </p>
    <p>
        <?php
        _e('Last but not least you can choose whether to display the calendar in a multi-view page/post that use the
            EDD [downloads] shortcode. This depends mostly on your theme since some themes do not properly shown the
            calendar.', $textDomain);
        ?>
    </p>

    <h4><?php _e('Step 3', $textDomain); ?></h4>
    <p>
        <?php
        _e('Once the above details have been filled and the Download has been saved, you can move on to editing the
            schedule and availability that were autoamtically created.', $textDomain);
        ?>
    </p>
</div>

<hr/>

<div class="feature-section">
    <h3><?php _e('Adding and Editing Schedules', $textDomain); ?></h3>
    <p>
        <?php
        _e('Click on the "Schedules" option under the EDD Bookings menu and choose either "Add New" or open the
            automatically created schedule for your first booking. When adding a new schedule you can select which
            Availability to make use of. If you are editing an existing Schedule you will also see a Calender view
            of the bookings stored in this Schedule and two metaboxes on the right-hand side. These will show you
            the booking information when you click on an existing booking in the calendar and a list the bookable
            downloads using this schedule.', $textDomain);
        ?>
    </p>

    <img src="<?php echo EDD_BK_IMGS_URL; ?>edit-schedule.png" />
</div>

<hr/>

<div class="feature-section">
    <h3><?php _e('Adding and Editing Availabilities', $textDomain); ?></h3>
    <p>
        <?php
        _e('Next head to the "Availabilities" section of EDD Bookings menu and, once again, you can either create a
            new one or edit the one that was automatically created with your first booking. Within each Availability
            you\'ll be able to set the date and time rules and also view the Schedules that use this availability.',
            $textDomain);
        ?>
    </p>
    <p>
        <?php
        _e(sprintf('You can find more information on how to set up the Availability Rules <a %s>in our
            documentation.</a>', 'href="http://docs.easydigitaldownloads.com/category/1100-bookings" target="_blank"'),
                $textDomain);
        ?>
    </p>
    <p>
        <img src="<?php echo EDD_BK_IMGS_URL; ?>edit-availability.png" />
    </p>
</div>
