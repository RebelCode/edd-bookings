/* global EddBk */

;(function ($, undefined) {

    EddBk.newClass('EddBk.Availability.Session', EddBk.Object, {
        init: function(start, duration) {
            this.super(
                $.extend(this.getDefaults(), {
                    start: start,
                    duration: duration
                })
            );
            this._init();
        },
        /**
         * To be implemented by extending classes.
         */
        _init: function() {},
        /**
         * Gets the session starting date and time.
         *
         * @returns {Date} The date object instance.
         */
        getStart: function() {
            return this.getData('start');
        },
        /**
         * Gets the session duration.
         *
         * @returns {integer} The number of seconds.
         */
        getDuration: function() {
            return this.getData('duration');
        },
        /**
         * Sets the session starting date and time.
         *
         * @param {Date} start The date object instance.
         */
        setStart: function(start) {
            this.setData('start', start);
        },
        /**
         * Sets the session duration.
         *
         * @param {integer} duration The number of seconds.
         */
        setDuration: function(duration) {
            this.setData('duration', duration);
        },
        /**
         * Gets the default data.
         *
         * @returns {object}
         */
        getDefaults: function () {
            return {
                start: null,
                duration: null
            };
        }
    });

})(jQuery);
