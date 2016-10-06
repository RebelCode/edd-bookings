(function ($, window, document, undefined) {
    
    EddBk.newClass('EddBk.Object.SessionStorage', EddBk.Object, {
        /**
         * Gets the sessions, or the sessions for a particular date.
         * 
         * @param {Date} date Optional date.
         * @returns {Object} The sessions.
         */
        getSessions: function(date) {
            if (date === undefined) {
                return this.getData();
            }
            return this.resolve(this._breakDate(date));
        },
        /**
         * Checks if has sessions.
         * 
         * @param {Date} date The date.
         * @returns {Boolean}
         */
        hasSessions: function(date) {
            var sessions = this.getSessions(date);
            return (typeof sessions === 'object') && (Object.keys(sessions).length > 0);
        },
        /**
         * Adds sessions or a single session for a specific date.
         * 
         * @param {Date} date The date object.
         * @param {Object|integer} timestamps The timestamp or an object of timestamp keys.
         * @returns {EddBk.Object.SessionStorage} This instance.
         */
        addSessions: function(date, timestamps) {
            if (typeof timestamps === 'object') {
                return this.assign(this._breakDate(date), timestamps);
            } else {
                var obj = {};
                obj[timestamps] = 1;
                return this.addSessions(date, obj);
            }
        },
        /**
         * Sets the sessions.
         * 
         * @param {Object} sessions The sessions object.
         * @returns {EddBk.Object.SessionStorage} The sessions.
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
            return [date.getFullYear(), date.getMonth() + 1, date.getDate()];
        },
    });
    
})(jQuery, top, document);
