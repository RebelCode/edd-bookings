;(function($, TimePickerI18n, undefined) {

    $.timepicker.setDefaults(TimePickerI18n);

    // The "bookings enabled" field
    var enabledField = null;
    
    // Checks if bookings are enabled
    var isBookingsEnabled = function() {
        return (enabledField.attr('type') === 'checkbox' && enabledField.is(':checked')) ||
            (enabledField.attr('type') === 'hidden' && enabledField.val() === '1');
    };
    
    // Toggles any toggle-able section based on whether bookings are enabled
    var toggleSectionVisibility = function() {
        var enabled = isBookingsEnabled();
        $('div.edd-bk-hide-if-bookings-disabled').each(function() { $(this).toggle(enabled); });
    };
    
    // Initialization
    $(document).ready(function() {
        // Init enabled field
        enabledField = $('#edd-bk-bookings-enabled');
        // Bind on change event
        enabledField.on('change', toggleSectionVisibility);
        // Trigger section visibility update
        toggleSectionVisibility();
    });
    
})(jQuery, EddBkLocalized_TimePickerI18n);
