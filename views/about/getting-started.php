<?php $textDomain = eddBookings()->getI18n()->getDomain(); ?>
<div class="headline-feature feature-section one-col">
    <h2><?php _e('How to Create Your First Booking', $textDomain); ?></h2>
    <p>
        <?php
        _e('This is a basic instroduction on how to use EDD Bookings.', $textDomain);
        _e(sprintf('For more details please <a %s>visit our full documentation</a>.',
                        'href="http://docs.easydigitaldownloads.com/category/1100-bookings" target="_blank"'),
                $textDomain);
        ?>
    </p>
</div>

<div class="feature-section one-col">
    <h3><?php _e('Adding a Bookable Download', $textDomain); ?></h3>
    <img src="<?php echo EDD_BK_IMGS_URL; ?>booking-options.png" />

    <h4><?php _e('Step 1'); ?></h4>
    <p>
        <?php
        _e('To create your first Booking head over to "Downloads" > "Add New". After entering the required
                information for the Booking\'s title, description, categories, tags and so on, you can get right down
                to the Booking details.', $textDomain);
        _e('Upon opening the New Download page, scroll down to the "Booking Options" metabox and check the "Enable
                bookings for this download" checkbox. This will automatically remove all the unrequired metaboxes and
                display the new Bookings settings.', $textDomain);
        ?>
    </p>

    <h4><?php _e('Step 2', $textDomain); ?></h4>
    <p>
        <?php
        _e('Start by selecting the session length, the number of sessions each customer can book, as well as the cost
                per session.', $textDomain);
        _e('Once the above details have been filled in you can move on to "Schedule". This automatically selects an
                option titled "Create new schedule and timetable."', $textDomain);
        _e('Leave this setting as is. The schedule and timetable will automatically be created for you and we can later
                change the titles and details of the newly created schedule and timetable.', $textDomain);
        _e('Last but not least you can choose whether to display the calendar in a multi-view page or post.',
                $textDomain);
        ?>

    <h4><?php _e('Step 3', $textDomain); ?></h4>
    <p>
        <?php
        _e('Once the above details have been filled in you can move on to "Schedule". This automatically selects an
                option titled "Create new schedule and timetable."', $textDomain);
        _e('Leave this setting as is. The schedule and timetable will automatically be created for you and we can later
                change the titles and details of the newly created schedule and timetable.', $textDomain);
        ?>
    </p>
</div>

<hr/>

<div class="feature-section">
    <h3><?php _e('Adding and Editing Schedules', $textDomain); ?></h3>

    <div class="two-col-text">
        <h4><?php _e('Adding a Schedule', $textDomain); ?></h4>
        <p>
            <?php
            _e('Head over to the "Schedules" section of EDD Bookings and click on "Add New" or open the automatically
                created schedule for your first booking.', $textDomain);
            _e('Here you will see two metaboxes. The first one is titled "Options" and lists the Timetable that is
                associated with this Schedule.', $textDomain);
            _e('Below this is a list of the "Downloads using this schedule" and the associated bookings.', $textDomain);
            ?>
        </p>
        <p>
            <img src="<?php echo EDD_BK_IMGS_URL; ?>add-new-schedule.png" />
        </p>
    </div>

    <div class="col">
        <h4><?php _e('Editing a Schedule', $textDomain); ?></h4>
        <img src="<?php echo EDD_BK_IMGS_URL; ?>edit-schedule.png" />
    </div>
</div>

<hr/>

<div class="feature-section one-col">
    <h3><?php _e('Adding and Editing Timetables', $textDomain); ?></h3>
    <div class="two-col-text">
        <h4><?php _e('Adding a Timetable', $textDomain); ?></h4>
        <p>
            <?php
            _e('Next head on to the "Timetables" section and once again you can either create a new timetable or edit
                the one that was automatically created with your first booking.', $textDomain);
            _e('Within each Timetable you\'ll be able to set the Rules for this timetable and also view the Schedules
                    currently making use of this timetable and its rules.', $textDomain);
            _e(sprintf('You can find more information on how to set up the Timetable Rules <a %s>in our
                documentation</a>', 'href="#" target="_blank"'), $textDomain);
            ?>
        </p>
        <br>
        <img src="<?php echo EDD_BK_IMGS_URL; ?>add-new-timetable.png" />
    </div>
    <div>
        <h4><?php _e('Editing a Timetable', $textDomain); ?></h4>
        <p>
            <img src="<?php echo EDD_BK_IMGS_URL; ?>edit-timetable.png" />
        </p>
    </div>
</div>
