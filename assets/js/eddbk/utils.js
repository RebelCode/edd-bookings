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
        // Converts milliseconds into seconds, i.e. a proper timestamp
        msToSeconds: function(ms) {
            return Math.floor(ms / 1000);
        },
        // Creates a UTC timestamp
        utcTimestamp: function(year, month, date, hours, minutes, seconds, ms) {
            var d = Date.UTC(year, month, date, hours || 0, minutes || 0, seconds || 0, ms || 0);
            return EddBk.Utils.msToSeconds(d);
        }
    };

})(jQuery, top, document, EddBkAjaxLocalized);
