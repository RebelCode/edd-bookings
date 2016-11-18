<div>
    <label for="customer-name">
        <span><?php _e('Full Name', 'eddbk'); ?></span>
        <?php
        echo eddBookings()->adminTooltip(
            __("The first and last name of the new customer.", 'eddbk')
        );
        ?>
    </label>
    <input type="text" name="customer_name" id="customer-name" />
</div>
<div>
    <label for="customer-email">
        <span><?php _e('Email Address', 'eddbk'); ?></span>
        <?php
        echo eddBookings()->adminTooltip(
            __("The email address for the new customer.", 'eddbk')
        );
        ?>
    </label>
    <input type="email" name="customer_email" id="customer-email" />
</div>
<div>
    <label></label>
    <button class="button button-secondary" type="button" id="create-customer-btn">
        <span>
            <i class="fa fa-check"></i>
            <?php _e('Create New Customer', 'eddbk'); ?>
        </span>
        <i class="fa fa-spinner fa-spin edd-bk-loading"></i>
    </button>
    <span id="create-customer-error"></span>
</div>
