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
                service: service,
                monthsLoaded: {}
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
            this.updateData();
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
                this.getService().loadData(callback);
            }
        },

        /**
         * Updates the internal options using the service's meta data.
         */
        updateData: function() {
            this.trigger('before_update_data');

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
                useCustomerTz: meta.use_customer_tz === true || meta.use_customer_tz === "1",
                currencySymbol: meta.currency
            });

            this.trigger('updated_data');
        },

        /**
         * @override
         * Triggered when the month or year has changed in the date picker.
         */
        onChangeMonthYear: function(e, year, month) {
            this.maybeCacheMonthSessions(month, year);
            this.maybeCacheMonthSessions(month + 1, year);
        },

        /**
         * Checks if the availability controller has sessions for a given month and if not, fetches them from the server.
         *
         * @param {integer} month 1-based month index
         * @param {integer} year The full year number.
         */
        maybeCacheMonthSessions: function(month, year) {
            // If already has sessions in cache for this month, skip
            if (this.resolve(['monthsLoaded', year, month]) === true) {
                return;
            }
            this.setLoading(true);
            this.getMonthSessionsFromServer(month, year, function(sessions) {
                if (Object.keys(sessions).length > 0) {
                    this.cacheSessions(sessions);
                } else {
                    var path = this.getAvailability()._breakDate([year, month]);
                    this.getAvailability().assign(path, {});
                }
                this.assign(['monthsLoaded', year, month], true);
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
        getMonthSessionsFromServer: function(month, year, callback, extra) {
            var thisMonthStart = EddBk.Utils.utcTimestamp(year, month - 1, 1, 0, 0, 0),
                nextMonthEnd = EddBk.Utils.utcTimestamp(year, month, 0, 23, 59, 59);
            this.getService().getSessions(thisMonthStart, nextMonthEnd, callback, extra);
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
