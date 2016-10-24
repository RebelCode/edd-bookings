;(function($, undefined) {

    EddBk.newClass('EddBk.Widget.ServiceSessionPicker', EddBk.Widget.SessionPicker, {
        /**
         * Constructor.
         *
         * @param {jQuery|Element} element The widget element.
         * @param {EddBk.Service} service The service.
         */
        init: function(element, service) {
            this._super(element, {});
            this.addData({
                type: 'Widget.ServiceSessionPicker',
                service: service
            });
        },
        /**
         * Initializes elements.
         *
         * Adds hidden form submission elements and ensures that new messages are moved into the messages container.
         *
         * @override
         * @returns {EddBk.Widget.ServiceSessionPicker}
         */
        initElements: function() {
            this._super();

            this.fsStart = this.l.find('.edd-bk-fs-start');
            this.fsDuration = this.l.find('.edd-bk-fs-duration');
            this.fsTimezone = this.l.find('.edd-bk-fs-timezone');

            this.l.find('> .edd-bk-session-picker-msg').detach().appendTo(this.msgs);
            this.sessionUnavailableMsg = this.msgs.find('.edd-bk-session-picker-session-unavailable');

            return this;
        },
        /**
         * Updates the widget.
         *
         * Hides the "session unavailable" message.
         *
         * @override
         * @returns {EddBk.Widget.ServiceSessionPicker}
         */
        update: function() {
            this.sessionUnavailableMsg.hide();
            this._super();
        },
        /**
         * Loads the service data.
         *
         * @param {Function} callback
         */
        loadData: function(callback) {
            if (!this.getService().isDataLoaded()) {
                this.getService().loadData(this._loadDataCallback.bind(this, callback));
            }
        },

        /**
         * Callback for when the service data has been fetched from the server.
         *
         * @param {Function} callback
         */
        _loadDataCallback: function(callback) {
            // Save meta in data
            var meta = this.getService().getData();
            this.addData({
                unit: meta.session_unit,
                sessionLength: parseInt(meta.session_length),
                minSessions: parseInt(meta.min_sessions),
                maxSessions: parseInt(meta.max_sessions),
                stepSessions: parseInt(meta.session_length_n),
                sessionCost: parseFloat(meta.session_cost),
                serverTz: parseInt(meta.server_tz),
                useCustomerTz: meta.use_customer_tz === "1",
                currencySymbol: meta.currency
            });
            // Execute callback
            if (callback) callback();
        },

        /**
         * @override
         * Triggered when the month or year has changed in the date picker.
         */
        onChangeMonthYear: function(e, year, month) {
            this.maybeCacheMonthSessions(month, year);
        },

        /**
         * Checks if the availability controller has sessions for a given month and if not, fetches them from the server.
         *
         * @param {integer} month 0-based month index
         * @param {integer} year The full year number.
         */
        maybeCacheMonthSessions: function(month, year) {
            // If already has sessions in cache for this month, skip
            if (this.getAvailability().hasSessions([year, month])) {
                return;
            }
            this.setLoading(true);
            this.getMonthSessionsFromServer(month, year, function(sessions) {
                this.cacheSessions(sessions);
                this.setLoading(false);
            }.bind(this));
        },

        /**
         * Caches a set of sessions into the availability controller.
         *
         * @param {object} sessions
         */
        cacheSessions: function(sessions) {
            var serverTz = this.getData('serverTz'),
                useCustomerTz = this.getData('useCustomerTz');
            // Group session data by date
            for (var session in sessions) {
                var utc = parseInt(session),
                    localDate = new Date(utc * 1000),
                    serverTimestamp = utc + serverTz - EddBk.Utils.timezone(localDate),
                    serverDate = new Date(serverTimestamp * 1000),
                    date = useCustomerTz? localDate : serverDate;
                // Add session to this date
                this.getAvailability().addSession(utc, date);
            }
            this.getDatePicker().refresh();
        },

        /**
         *
         * @param {type} month
         * @param {type} year
         * @param {type} callback
         * @returns {undefined}
         */
        getMonthSessionsFromServer: function(month, year, callback) {
            var thisMonthStart = EddBk.Utils.utcTimestamp(year, month - 1, 1, 0, 0, 0),
                nextMonthEnd = EddBk.Utils.utcTimestamp(year, month + 1, 0, 23, 59, 59);
            this.getService().getSessions(thisMonthStart, nextMonthEnd, callback);
        },

        /**
         * Gets the service.
         *
         * @returns {EddBk.Service}
         */
        getService: function() {
            return this.getData('service');
        },

        // Validates the currently selected sessions
        validate: function(callback) {
            this.sessionUnavailableMsg.hide();
            var session = this.getSelectedSession();
            this.getService().canBook(session.start, session.duration, this.onValidate.bind(this, callback));
        },
        // Callback for the `validate` method
        onValidate: function(callback, valid) {
            if (valid) {
                var session = this.getSelectedSession();
                this.fsStart.val(session.start);
                this.fsDuration.val(session.duration);
                this.fsTimezone.val(session.timezone);
            } else {
                this.sessionUnavailableMsg.width(this.getWidgetWidth()).show();
            }
            // Invoke callback
            if (callback) {
                callback(valid);
            }
        }
    });

})(jQuery);
