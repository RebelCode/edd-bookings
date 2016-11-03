<div class="edd-bk-session-picker-loading">
    <span><?php _e('Loading', 'eddbk'); ?></span>
</div>
<div class="edd-bk-date-picker-widget"></div>

<div class="edd-bk-session-options">
    <div class="edd-bk-if-time-unit">
        <div>
            <div class="edd-bk-time-picker-widget"></div>
        </div>
    </div>
    <div>
        <div class="edd-bk-duration-picker-widget"></div>
    </div>
    <div class="edd-bk-price">
        <strong><?php _e('Price:', 'eddbk'); ?></strong> <span></span>
    </div>
</div>

<div class="edd-bk-session-picker-msgs">
    <div class="edd-bk-session-picker-msg edd-bk-session-picker-date-error">
        <?php
            printf(
                _x(
                    'The date %1$s cannot accomodate %2$s. Kindly choose another date.',
                    '%1$s = selected date. %2$s = duration. Example: The date 29th June cannot accomodate 2 days.',
                    'eddbk'
                ),
                '<span class="edd-bk-invalid-date"></span>',
                '<span class="edd-bk-invalid-num-sessions"></span>'
            );
        ?>
    </div>
</div>
