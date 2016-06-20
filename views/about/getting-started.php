<?php $textDomain = eddBookings()->getI18n()->getDomain(); ?>

<div class="headline-feature feature-section one-col">
    <h2><?php _e('How to Create Your First Booking', $textDomain); ?></h2>
    <p>
        <?php
        _e('This is a basic introduction on how to use EDD Bookings. ', $textDomain);
        _e(
            sprintf(
                'For more details please <a %s>visit our full documentation</a>.',
                'href="http://docs.easydigitaldownloads.com/category/1100-bookings" target="_blank"'
            ),
            $textDomain
       );
        ?>
    </p>
</div>

<hr />

<div class="feature-section one-col">
    <h3><?php _e('Adding a Bookable Download', $textDomain); ?></h3>
    <p><i>[The image needs to change]</i></p>
    <img src="<?php echo EDD_BK_IMGS_URL; ?>booking-options.png" />

    <h4><?php _e('Step 1'); ?></h4>
    <p>
        <?php
        _e('<i>[text]</i>', $textDomain);
        ?>
    </p>

    <h4><?php _e('Step 2', $textDomain); ?></h4>
    <p>
        <?php
        _e('<i>[text]</i>', $textDomain);
        ?>
    </p>
</div>

<hr/>

<div class="feature-section">
    <h3><?php _e('Setting up Available Times', $textDomain); ?></h3>
    <p><i>[image needs to change]</i></p>
    <img src="<?php echo EDD_BK_IMGS_URL; ?>edit-availability.png" />
    <p>
        <?php
        _e('<i>[text]</i>', $textDomain);
        ?>
    </p>
</div>

