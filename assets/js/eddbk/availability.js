/* global EddBk */

;(function (undefined) {
    /*
     * An availability controller - any object that can assert whether or not a particular session is available for
     * booking or not.
     *
     * The mechanics of how it decides this is not important, as long as it can give a boolean output for a session
     * instance input.
     */
    EddBk.newClass('EddBk.Availability', EddBk.Interface, {
        /**
         * Gets the sessions for a particular date or date range.
         *
         * @param {Date} date The date object.
         * @returns {Array} And array of EddBk.
         */
        getSessions: function(date) {
            this.__unimplemented();
        },
        /**
         * Checks if has sessions.
         *
         * @param {Date|Array} date The date or array path.
         * @returns {Boolean}
         */
        hasSessions: function(date) {
            this.__unimplemented();
        }
    });
})();

;(function (undefined) {
    /*
     * An availability controller that uses a session registry to determine whether or not a session is available.
     */
    EddBk.newClass('EddBk.Availability.Registry', EddBk.Availability, {
        init: function() {
            this._super({});
        },

        getSessions: function(date) {
            if (date === undefined) {
                return this.getData();
            }
            return this.resolve(this._breakDate(date));
        },
        hasSessions: function(date) {
            var sessions = this.getSessions(date);
            return (sessions !== null) && (typeof sessions === 'object') && (Object.keys(sessions).length > 0);
        },

        /**
         * Adds a single session.
         *
         * @param {integer} timestamp The timestamp.
         * @param {Date} date The date object.
         * @returns {EddBk.Availability.Registry} This instance.
         */
        addSession: function(timestamp, date) {
            var path = this._breakDate(date);
            path.push(timestamp);
            this.assign(path, date);
            return this;
        },
        /**
         * Adds multiple sessions.
         *
         * @param {Object} dates An object of timestamp keys and date object values.
         * @returns {EddBk.Availability.Registry} This instance.
         */
        addSessions: function(dates) {
            if (typeof dates === 'object') {
                for (var timestamp in dates) {
                    this.addSession(timestamp, dates[timestamp]);
                }
            }
        },
        /**
         * Sets the sessions.
         *
         * @param {Object} sessions The sessions object.
         * @returns {EddBk.Availability.Registry} The sessions.
         */
        setSessions: function(sessions) {
            return this.setData(sessions);
        },

        /**
         * Breaks a date into an array consisting of the various parts that make up a session's key path.
         *
         * @param {Date} date
         * @returns {Array}
         */
        _breakDate: function(date) {
            var path = Array.isArray(date)
                ? date
                : [date.getFullYear(), date.getMonth() + 1, date.getDate()];
            // Loop month indexes and increment year
            if (path.length > 1) {
                path[0] += Math.floor(path[1] / 13);
                path[1] = ((path[1] - 1) % 12) + 1;
            }
            return path;
        }
    });
})();
