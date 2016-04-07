<?php /**
 * 	THE AVAILABILITY BUILDER SECTION.
 *
 * 	In this section, the user can set up their availability. The fields in this section include an
 * 	availability filler option and a table where users can enter rules that define what dates and
 * 	times customers are allowed to book.
 * 	-----------------------------------------------------------------------------------------------
 */ ?>

<div class="edd-bk-timetable-rules-container">
    <table class="widefat edd-bk-timetable-rules-table">
        <thead>
            <tr>
                <th id="edd-bk-sort-col"></th>
                <th id="edd-bk-range-type-col">
                    <?php _e('Range Type', EDD_Bookings::TEXT_DOMAIN); ?>
                </th>
                <th id="edd-bk-from-col">
                    <?php _e('From', EDD_Bookings::TEXT_DOMAIN); ?>
                </th>
                <th id="edd-bk-to-col">
                    <?php _e('To', EDD_Bookings::TEXT_DOMAIN); ?>
                </th>
                <th id="edd-bk-avail-col">
                    <?php _e('Available', EDD_Bookings::TEXT_DOMAIN); ?>
                </th>
                <th id="edd-bk-help-col">
                    <?php _e('Help', EDD_Bookings::TEXT_DOMAIN); ?>
                </th>
                <th id="edd-bk-remove-col"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $entries = $download->getAvailability()->getEntries();
            foreach ($entries as $i => $entry) {
                include EDD_BK_VIEWS_DIR . 'view-admin-availability-table-row.php';
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5">
                    <span class="description">
                        <?php _e('Rules further down the table will override those at the top.',
                                EDD_Bookings::TEXT_DOMAIN); ?>
                    </span>
                </th>
                <th colspan="2">
                    <button id="edd-bk-avail-add-btn" class="button button-primary button-large" type="button">
<?php _e('Add Rule', EDD_Bookings::TEXT_DOMAIN); ?>
                    </button>
                </th>
            </tr>
        </tfoot>
    </table>

    <p>
        <?php
        $format = sprintf('%1$s %2$s', get_option('time_format'), get_option('date_format'));
        $current_time = current_time($format);
        $gmt_offset = get_option('gmt_offset');
        ?>
        Any times set are treated as local time. You can change the server's local time from WordPress' <a href="<?php echo admin_url('options-general.php'); ?>">General Settings</a> page.<br/>
        Current Local time: <code><?php echo $current_time; ?> GMT<?php echo $gmt_offset; ?></code>
    </p>

<?php // <p><a id="edd-bk-avail-checker" href="#edd-bk-avail-checker">I want to check if this makes sense</a></p>  ?>

</div>