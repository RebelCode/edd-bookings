<div class="edd-bk-if-create-customer">
    <h4><?php _e('New Customer Details', 'eddbk'); ?></h4>
</div>
<div class="edd-bk-if-create-customer edd-bk-create-customer-msg">
    <span><?php _e('You are now creating a new customer for this booking.', 'eddbk'); ?></span>
    <a id="choose-customer" href="javascript:void(0)"><i class="fa fa-mouse-pointer"></i> <?php _e('Choose an existing customer', 'eddbk'); ?></a>
</div>
<div class="edd-bk-if-create-customer">
    <label for="customer-name">
        <span><?php _e('Full Name', 'eddbk'); ?></span>
        <?php
        echo eddBookings()->adminTooltip(
            __("The customer's first name.", 'eddbk')
        );
        ?>
    </label>
    <input type="text" name="customer_name" id="customer-name" />
</div>
<div class="edd-bk-if-create-customer">
    <label for="customer-email">
        <span><?php _e('Email Address', 'eddbk'); ?></span>
        <?php
        echo eddBookings()->adminTooltip(
            __("The customer's email address.", 'eddbk')
        );
        ?>
    </label>
    <input type="email" name="customer_email" id="customer-email" />
</div>
<div class="edd-bk-if-create-customer">
    <label></label>
    <button class="button button-secondary" type="button" id="create-customer-btn">
        <span>
            <i class="fa fa-check"></i>
            <?php _e('Create New Customer', 'eddbk'); ?>
        </span>
        <i class="fa fa-spinner fa-spin edd-bk-loading"></i>
    </button>
</div>
