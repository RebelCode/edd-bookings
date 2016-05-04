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
        _e('To create your first Booking head over to "Downloads" > "Add New". After entering the required
                information for the Booking\'s title, description, categories, tags and so on, you can get right down
                to the Booking details. ', $textDomain);
        _e('Upon opening the New Download page, scroll down to the "Booking Options" metabox and check the "Enable
                bookings for this download" checkbox. This will automatically remove all the unrequired metaboxes and
                display the new Bookings settings.', $textDomain);
        ?>
    </p>

    <h4><?php _e('Step 2', $textDomain); ?></h4>
    <p>
        <?php
        _e('Start by selecting the session length, the number of sessions each customer can book, as well as the cost
                per session. ', $textDomain);
        _e('Once these details have been filled in you can move on to "Schedule". This automatically selects an
                option titled "Create new schedule and timetable." ', $textDomain);
        _e('Leave this setting as is. The schedule and timetable will automatically be created for you and we can later
                change the titles and details as needed. ', $textDomain);
        _e('Last but not least you can choose whether to display the calendar in a multi-view page or post where you have added the EDD shortcodes.',
                $textDomain);
        ?>

    <h4><?php _e('Step 3', $textDomain); ?></h4>
    <p>
        <?php
        _e('Once the above details have been filled in you can move on to editing the schedule and timetable that were autoamtically
            created for you, as explained below.', $textDomain);
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
            _e('Click on the "Schedules" option under EDD Bookings and either "Add New" or open the automatically
                created schedule for your first booking. ', $textDomain);
            _e('When adding a new schedule you can select which Timetable to make use of. 
                If you are editing an existing Schedule you will also see a Calender view of the existing bookings and 
                two metaboxes on the right hand side. These show you the booking information when you click on an existing
                booking in the calendar and list the bookable downloads using this schedule.', $textDomain);
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

<div class="feature-section">
    <h3><?php _e('Adding and Editing Timetables', $textDomain); ?></h3>
    
    <div class="two-col-text">
        <h4><?php _e('Adding a Timetable', $textDomain); ?></h4>
        <p>
            <?php
            _e('Next head to the "Timetables" section of EDD Bookings and once again you can either create a new one or edit
                the one that was automatically created with your first booking.', $textDomain);
            _e('Within each Timetable you\'ll be able to set its availability rules and also view the Schedules
                    currently making use of this timetable.', $textDomain);
            ?>
            <br/><br/>
            <?php
            _e(sprintf('You can find more information on how to set up the Timetable Rules <a %s>in our
                documentation.</a>', 'href="http://docs.easydigitaldownloads.com/category/1100-bookings" target="_blank"'),
                    $textDomain);
            ?>
        </p>
        <p>
            <img src="<?php echo EDD_BK_IMGS_URL; ?>add-new-timetable.png" />
        </p>
    </div>
    <div class="col">
        <h4><?php _e('Editing a Timetable', $textDomain); ?></h4>
        <p>
            <img src="<?php echo EDD_BK_IMGS_URL; ?>edit-timetable.png" />
        </p>
    </div>
</div>
