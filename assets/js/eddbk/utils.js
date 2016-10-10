/* global EddBk, top, EddBkAjaxLocalized */

;(function($, window, document, localized, undefined) {

    EddBk.Utils = {
        // Time units and their respective keys
        Units: {
            minutes: 'minutes',
            hours: 'hours',
            days: 'days',
            weeks: 'weeks'
        },
        // Time units and their respective lengths in seconds
        UnitLengths: {
            minutes: 60,
            hours: 3600,
            days: 86400,
            weeks: 604800
        },
        // Checks if the argument string is a time unit string name
        isTimeUnit: function(something) {
            return [EddBk.Utils.Units.hours, EddBk.Utils.Units.minutes].indexOf(something) !== -1;
        },
    };

})(jQuery, top, document, EddBkAjaxLocalized);
