/* global moment */

(function ($, moment, document, undefined) {

    $(document).ready(function () {
        initDateTimeFields();
        updateDuration();
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

})(jQuery, moment, document);
