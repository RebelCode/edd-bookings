/* global moment */

(function ($, moment, document, undefined) {

    $(document).ready(function () {
        initDateTimeFields();
        updateDuration();
        $('#customer_tz').on('change', updateAdvancedTimes);
        updateAdvancedTimes();
    });

    // Initializes date time fields with pickers
    function initDateTimeFields() {
        var datePickerOptions = {
            dateFormat: 'yy-mm-dd'
        };
        var timePickerOptions = {
            timeFormat: "HH:mm:ss",
            showMillisec: false,
            showMicrosec: false,
            showTimezone: false,
            timeInput: true,
            timezone: 0,
            beforeShow: function (el, instance) {
                $(el).prop('disabled', true);
            },
            onClose: function (dateText, instance) {
                // 'this' refers to the input field.
                $(this).prop('disabled', false);
            },
            onSelect: function () {
                updateDuration();
                updateAdvancedTimesForElem($(this));
            }
        };
        var datetimepickerOptions = $.extend({}, datePickerOptions, timePickerOptions);
        $('input.edd-bk-datetime').datetimepicker(datetimepickerOptions);
    }

    /**
     * Gets the duration between selected start and end datetimes.
     *
     * @return integer The duration as a number of milliseconds.
     */
    function getDuration()
    {
        var start = $('#start').datetimepicker('getDate'),
            end = $('#end').datetimepicker('getDate');
        return (end - start) + 1000;
    }

    /**
     * Updates the duration text to match the selected start and end datetimes.
     */
    function updateDuration()
    {
        var duration = getDuration(),
            durationText = moment.preciseDiff(0, duration);
        $('#duration').text(durationText);
    }

    /**
     * Gets the server timezone.
     *
     * @returns integer
     */
    function getServerTz()
    {
        return parseInt($('#server-tz').val());
    }

    /**
     * Gets the customer timezone.
     *
     * @returns integer
     */
    function getCustomerTz()
    {
        return parseFloat($('#customer_tz').val()) * 3600;
    }

    /**
     * Updates the advanced times.
     */
    function updateAdvancedTimes() {
        updateAdvancedTimesForElem($('#start'));
        updateAdvancedTimesForElem($('#end'));
    }

    /**
     * Updates the advanced times for a given element (start or end input field).
     *
     * @param {Element} e
     */
    function updateAdvancedTimesForElem(e) {
        var date = $(e).datetimepicker('getDate'),
            timestamp = parseInt(moment(date).format('X')),
            serverTz = getServerTz(),
            customerTz = getCustomerTz(),
            advTimesContainer = $(e).parent().next().find('> div'),
            utcField = advTimesContainer.find('p.utc-time > code'),
            customerField = advTimesContainer.find('p.customer-time > code'),
            utcDate = moment.unix(timestamp - serverTz).utc(),
            customerDate = moment.unix(timestamp + customerTz);
        utcField.text(utcDate.format('YYYY-MM-DD HH:mm:ss'));
        customerField.text(customerDate.format('YYYY-MM-DD HH:mm:ss'));
    }

})(jQuery, moment, document);