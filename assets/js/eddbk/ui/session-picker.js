;(function ($, window, document, undefined) {

    EddBk.newClass('EddBk.Object.Ui.SessionPicker', EddBk.Object, {
        init: function (element) {
            this.l = $(element);
            this._super({
                l: this.l,
                localTzOffset: (new Date()).getTimezoneOffset() * -60
            });
        },
        /**
         * Gets the local timezone offset, in seconds.
         *
         * @returns {Number}
         */
        getLocalTimezone: function () {
            return this.getData('localTimezone');
        },
        /**
         * Gets the container element.
         *
         * @returns {Element}
         */
        getElement: function () {
            return this.getData('l');
        },
        
        /**
         * Gets the selected date in the datepicker.
         *
         * @returns {Date}
         */
        getDate: function() {
            return this.datepicker.datepicker('getDate');
        },
        /**
         * Sets the selected date in the datepicker.
         *
         * @param {Date} date
         * @returns {EddBkSessionPicker}
         */
        setDate: function(date) {
            this.datepicker.datepicker('setDate', date);

            return this;
        },
        
        /**
         * Gets the selected time in the timepicker.
         *
         * @returns {integer} The timestamp.
         */
        getTime: function() {
            return parseInt(this.timepicker.val());
        },
        /**
         * Sets the selected time in the timepicker.
         *
         * @param {integer} timestamp
         * @returns {EddBkSessionPicker}
         */
        setTime: function(timestamp) {
            this.timepicker.val([]);
            this.timepicker.val([timestamp]);
            this.timepicker.trigger('change');

            return this;
        },
        
        /**
         * Gets the selected duration.
         *
         * @returns {integer}
         */
        getDuration: function () {
            var duration = parseInt(this.durationPicker.val());
            if (isNaN(duration)) {
                duration = parseInt(this.service.getMeta('min_sessions'));
                this.setDuration(duration);
            }
            return duration;
        },
        /**
         * Sets the selected duration.
         *
         * @param {integer} duration
         * @returns {EddBkSessionPicker}
         */
        setDuration: function(duration) {
            this.durationPicker.val(parseInt(duration));
            return this;
        },
        
        /**
         * Gets the price for the currently selected session.
         *
         * @returns {EddBkSessionPicker}
         */
        getPrice: function() {
            var duration = this.getDuration();
            var sessionLengthN = this.service.getMeta('session_length_n');
            var sessionCost = parseFloat(this.service.getMeta('session_cost'));
            var numSessions = (duration || 1) / sessionLengthN;
            return sessionCost * numSessions;
        },
        
        /**
         * Gets the currently selected session.
         *
         * @returns {EddBkSessionPicker}
         */
        getSession: function() {
            var timestamp = null;
            var unit = this.service.getMeta('session_unit');
            var sessionLength = this.service.getMeta('session_length');
            var sessionLengthN = this.service.getMeta('session_length_n');
            if (unit === 'days' || unit === 'weeks') {
                var datepickerAltFieldValue = this.datepickerAltField.val(),
                    startDateString = datepickerAltFieldValue.split(',')[0].trim(),
                    date = this.parseDatePickerDate(startDateString);
                // The UTC date for the selected date may be incorrect, even though we require timestamps as UTC
                // This is because the user might select 13-March-2016 on the calendar, but due to his local timezone,
                // date.getTime() will return 12-March-2016 @ 23:00 (for UTC+1) or 13-March-2016 @ 03:00 (UTC-3).
                // We only want the selected date, so we re-offset the timestamp with the timezone offset.
                var utcTimestamp = Math.floor(date.getTime() / 1000);
                var timezoneOffset = date.getTimezoneOffset() * 60;
                timestamp = utcTimestamp - timezoneOffset;
            } else {
                var selected = this.timepicker.find('option:selected');
                var val = selected.val();
                timestamp = parseInt(val);
            }
            var duration = parseInt(this.getDuration() / sessionLengthN) * sessionLength;

            return {
                start: timestamp,
                duration: duration
            };
        },
        
        /**
         * Internally used as a callback, in case service meta needs to be loaded.
         *
         * @returns {EddBkSessionPicker}
         */
        _init: function() {
            this.initDom();
            this.initElements();
            this.initEvents();
            this.loadDatePicker();
            this.triggerMonthYearChangeToday();

            this.trigger('init');
            return this;
        },
        /**
         * Initialzies the elements.
         *
         * @returns {EddBkSessionPicker}
         */
        initElements: function () {
            var datepickerContainer = this.l.find('.edd-bk-datepicker-container');
            var datePicker = datepickerContainer.find('.edd-bk-datepicker');

            var sessionOptionsElement = this.l.find('.edd-bk-session-options');
            var sessionOptionsLoading = this.l.find('.edd-bk-session-options-loading');
            var timePicker = sessionOptionsElement.find('.edd-bk-timepicker');
            var durationPicker = sessionOptionsElement.find('.edd-bk-duration');
            var priceElement = this.l.find('p.edd-bk-price span');

            var startField = this.l.find('.edd-bk-start-submit');
            var durationField = this.l.find('.edd-bk-duration-submit');
            var datepickerAltField = this.l.find('.edd-bk-datepicker-value');
            var timezoneField = this.l.find('.edd-bk-timezone');

            var msgContainer = this.l.find('.edd-bk-msgs');
            
            this.addData({
                datepickerContainer : datepickerContainer,
                datePicker: datePicker,
                sessionOptionsElement: sessionOptionsElement,
                sessionOptionsLoading: sessionOptionsLoading,
                timePicker: timePicker,
                durationPicker: durationPicker,
                priceElement: priceElement,
                startField: startField,
                durationField: durationField,
                datepickerAltField: datepickerAltField,
                timezoneField: timezoneField,
                msgContainer: msgContainer
            });
            
            this.trigger('init_elements');
            return this;
        },
        /**
         * Initializes the events.
         *
         * @returns {EddBkSessionPicker}
         */
        initEvents: function () {
            this.timepicker.bind('change', this.onTimePickerChange.bind(this));
            this.durationPicker.bind('change', this.onDurationChanged.bind(this));

            this.trigger('init_events');
            return this;
        },
        /**
         * Initializes the DOM.
         *
         * @returns {EddBkSessionPicker}
         */
        initDom: function() {
            // Loading container
            var loadingContainer = '<div class="edd-bk-loading-container"><span>Loading</span></div>';
            // Hidden fields
            var startSubmit = '<input type="hidden" class="edd-bk-start-submit" name="edd_bk_start" />';
            var durationSubmit = '<input type="hidden" class="edd-bk-duration-submit" name="edd_bk_duration" />';
            var timezoneSubmit = '<input type="hidden" class="edd-bk-timezone" name="edd_bk_timezone" />';
            // Datepicker container
            var datepicker =
                '<div class="edd-bk-datepicker-container">' +
                    '<div class="edd-bk-datepicker-skin">' +
                        '<div class="edd-bk-datepicker"></div>' +
                    '</div>' +
                    '<input type="hidden" class="edd-bk-datepicker-value" value="" />' +
                '</div>';
            // Messages container
            var msgsContainer =
                '<div class="edd-bk-msgs">'+
                    '<div class="edd-bk-msg datefix-msg">' +
                        '<p>' + EddBkSpI18n.dateFixMsg + '</p>' +
                    '</div>' +
                    '<div class="edd-bk-msg invalid-date-msg">' +
                        '<p>' + EddBkSpI18n.invalidDateMsg + '</p>' +
                    '</div>' +
                    '<div class="edd-bk-msg no-times-for-date">' +
                        '<p>' + EddBkSpI18n.noTimesForDateMsg + '</p>' +
                    '</div>' +
                    '<div class="edd-bk-msg booking-unavailable-msg">' +
                        '<p>' + EddBkSpI18n.bookingUnavailableMsg + '</p>' +
                    '</div>'+
                '</div>';
            // Timepicker loading container
            var timepickerLoading =
                '<div class="edd-bk-session-options-loading">' +
                    '<i class="fa fa-cog fa-spin"></i> ' + EddBkSpI18n.loading +
                '</div>';
            // Timepicker
            var timepicker =
                '<div class="edd-bk-session-options">' +
                    '<p class="edd-bk-if-time-unit">' +
                        '<label>' +
                            EddBkSpI18n.time + ': ' +
                            '<select class="edd-bk-timepicker"></select>' +
                        '</label>' +
                    '</p> '+
                    '<p>' +
                        '<label>' +
                            EddBkSpI18n.duration + ': ' +
                            '<input type="number" class="edd-bk-duration" ' +
                                'min="'+this.service.getMeta('min_sessions')+'"' +
                                'max="'+this.service.getMeta('max_sessions')+'"' +
                                'val="'+this.service.getMeta('min_sessions')+'" />' +
                        '</label>' +
                        '<span class="edd-bk-session-unit"></span>' +
                    '</p>' +
                    '<p class="edd-bk-price">' +
                        '<label>' +
                            EddBkSpI18n.price + ': <span></span>' +
                        '</label>' +
                    '</p>' +
                '</div>';

            this.l.addClass('edd-bk-service-container').get(0).innerHTML =
                loadingContainer +
                startSubmit +
                durationSubmit +
                timezoneSubmit +
                datepicker +
                msgsContainer +
                timepickerLoading +
                timepicker;

            $(document.createElement('br')).insertAfter(this.l);

            return this;
        },
        
        //===== DATEPICKER =====

        /**
         * Toggles the datepicker's loading state on or off.
         *
         * @param {Boolean} isLoading
         * @returns {EddBkSessionPicker}
         */
        setDatePickerLoading: function (isLoading) {
            this.l.toggleClass('edd-bk-loading', isLoading);

            return this;
        },
        /**
         * Loads the datepicker.
         *
         * @param {integer} range The number of days to be selected on the datepicker.
         * @returns {EddBkSessionPicker}
         */
        loadDatePicker: function (range) {
            // Get the session duration unit
            var unit = this.service.getMeta('session_unit').toLowerCase();
            // Check which datepicker function to use, depending on the unit
            var datepickerFunction = this.determineDatepickerFunction(unit);
            // Stop if the datepicker function returned is null
            if (datepickerFunction === null) {
                return;
            }

            // Apply the datepicker function on the HTML datepicker element
            var options = this.getDatepickerOptions(range);
            $.fn[datepickerFunction].apply(this.datepicker, [options]);

            this.trigger('loaded_datepicker');
            return this;
        },
        /**
         * Reloads the datepicker.
         *
         * @returns {EddBkSessionPicker}
         */
        reloadDatePicker: function () {
            // Get the range
            var range = this.getDuration();
            // Re-load the datepicker
            this.loadDatePicker(range);
            // Simulate user click on the selected date, to refresh the auto selected range
            this.datepicker.data('suppress-click-event', true)
                .find('.ui-datepicker-current-day').first()
                .find('>a').click();

            this.trigger('reloaded_datepicker');
            return this;
        },
        /**
         * Gets the datepicker options.
         *
         * @param {integer} range Range of selectable days. Only applicable if using multiDatesPicker addon.
         * @returns {Object}
         */
        getDatepickerOptions: function(range) {
            // Check if the range has been given. Default to the session duration
            range = (typeof range === 'undefined')
                ? this.service.getMeta('session_length_n')
                : range;
            // Multiply by 7 if unit is weeks. Otherwise by 1
            // The else case only matters if the unit is "days", since the "autoselectRange" option is ignored
            // by the vanilla datepicker. It is only used by the multiDatesPicker addon.
            range *= (this.service.getMeta('session_unit') === 'weeks' ? 7 : 1);
            return {
                // Hide the Button Panel
                showButtonPanel: false,
                // Options for multiDatePicker. These are ignored by the vanilla jQuery UI datepicker
                mode: 'daysRange',
                autoselectRange: [0, range],
                adjustRangeToDisabled: true,
                // Alt field
                altField: this.datepickerAltField,
                // Format
                altFormat: 'mm/dd/yy',
                // multiDatesPicker format
                dateFormat: 'mm/dd/yy',
                // Show dates from other months and allow selection
                showOtherMonths: true,
                selectOtherMonths: true,
                // Prepares the dates for availability
                beforeShowDay: this.isDateAvailable.bind(this),
                // When a date is selected by the user
                onSelect: this.onDateSelected.bind(this),
                // When the month of year changes
                onChangeMonthYear: this.onChangeMonthYear.bind(this)
            };
        },
        /**
         * Validates the selected date.
         *
         * @param {Date} date
         * @returns {Boolean}
         */
        validateSelectedDate: function (date) {
            var originalDate = new Date(date.getTime());
            var newDate = new Date(date.getTime());
            var unit = this.service.getMeta('session_unit');
            if (unit === 'weeks' || unit === 'days') {
                var newDate = this.performDateFix(date);
                if (newDate === null) {
                    if (this.determineDatepickerFunction(unit) === 'multiDatesPicker') {
                        this.datepicker.multiDatesPicker('resetDates');
                    }
                    this.showInvalidDateMessage(originalDate);
                    this.datepicker.datepicker('setDate', originalDate);
                    return false;
                }
                if (originalDate.getTime() !== newDate.getTime()) {
                    this.showDateFixMessage(newDate);
                }
                this.datepicker.datepicker('setDate', newDate);
                this.reloadDatePicker();
            }
            return true;
        },
        /**
         * Performs a date fix, if necessary.
         *
         * @param {Date} date
         * @returns {Date|null}
         */
        performDateFix: function (date) {
            var days = this.getDuration();
            var unit = this.service.getMeta('session_unit');
            if (unit === 'weeks') {
                days *= 7;
            }
            for (var u = 0; u < days; u++) {
                var tempDate = new Date(date.getTime());
                var allAvailable = true;
                for (var i = 1; i < days; i++) {
                    tempDate.setDate(tempDate.getDate() + 1);
                    var available = this.isDateAvailable(tempDate);
                    if (!available[0]) {
                        allAvailable = false;
                        break;
                    }
                }
                if (allAvailable) {
                    return date;
                }
                date.setDate(date.getDate() - 1);
                if (!this.isDateAvailable(date)[0]) {
                    return null;
                }
            }
            return null;
        },
        /**
         * Checks if a date is available.
         *
         * @param {Date} date
         * @returns {Array} First index is the availability boolean, second is an empty string for datepicker reasons.
         */
        isDateAvailable: function (date) {
            // Extract vars from date
            var sessions = this.getSessionsFromCache(date),
                available = Object.keys(sessions).length > 0;

            available = this.trigger('is_date_available', available, {
                date: date,
                sesions: sessions
            });

            return [available, ''];
        },

        /**
         * Triggered when a date is selected.
         *
         * @param {string} dateStr
         * @returns {EddBkSessionPicker}
         */
        onDateSelected: function(dateStr) {
            // If the element has the click-event suppression flag,
            if (this.datepicker.data('suppress-click-event') === true) {
                // Remove it and return
                this.datepicker.data('suppress-click-event', null);
                return;
            }
            // Parse the date
            var date = this.parseDatePickerDate(dateStr);

            this.trigger('before_date_selected', date);
            this.resetMessages();

            // @todo
            // Hide the purchase button
            // this.eddSubmitWrapper.hide();

            // Validate the date
            var dateValid = this.validateSelectedDate(date);
            if (!dateValid) {
                return;
            }
            // Check if sessions exist locally
            if (this.hasSessionsInCache(date)) {
                this.showTimePicker(this.getSessionsFromCache(date));
                this.updateTimezoneField(date);
            } else {
                this.showMessage('no-times-for-date');
            }
            this.updateTimePicker();

            this.trigger('after_date_selected', date);

            return this;
        },
        /**
         * Triggered when the month or year is changed.
         *
         * @param {integer} year
         * @param {integer} jqDpMonth
         * @returns {EddBkSessionPicker}
         */
        onChangeMonthYear: function (year, jqDpMonth) {
            this.setDatePickerLoading(true);
            // getMonthSessions() expects a 0-based month index, since it uses JS Date object (which
            // is also 0-based). jQuery UI Datepicker gives a 1-based month index.
            var month = jqDpMonth - 1;
            this.loadSessionsForMonth(year, month, function () {
                this.datepicker.datepicker('refresh');
                this.setDatePickerLoading(false);
            }.bind(this));

            return this;
        },
        /**
         * Triggers a month/year change event.
         *
         * @returns {EddBkSessionPicker}
         */
        triggerMonthYearChangeToday: function() {
            // Trigger month/year change callback
            var selectedDate = new Date(),
                year = selectedDate.getUTCFullYear(),
                // month incremented becuase jQuery UI datepicker passes a 1-based month index vs JS Date's 0-based date
                month = selectedDate.getUTCMonth() + 1;
            this.onChangeMonthYear(year, month);

            return this;
        },

        /**
         * Loads the sessions for a specific month.
         *
         * @param {integer} year
         * @param {integer} month
         * @param {Function} callback
         * @returns {EddBkSessionPicker}
         */
        loadSessionsForMonth: function(year, month, callback) {
            var cache = this.resolveSessionsInCache([year, month]);
            if (Object.keys(cache).length > 0) {
                if (callback) callback();
                return;
            }
            // Generate the range
            var start = Date.UTC(year, month, 1, 0, 0, 0); // first day of current month
            var end = Date.UTC(year, month + 2, 0, 23, 59, 59); // last day of next month
            var range = [
                Math.floor(start / 1000),
                Math.floor(end / 1000)
            ];
            this.service.getSessions(range, function (response) {
                if (response && response.success && response.sessions) {
                    this.addServerSessionsToCache(response.sessions);
                    if (callback) callback();
                }
                else {
                    console.error('Failed to get month sessions. Server replied with:', response.error);
                }
            }.bind(this));

            return this;
        },
        /**
         * Adds sessions retrieved from the server to the cache.
         *
         * @param {Object} data
         * @returns {EddBkSessionPicker}
         */
        addServerSessionsToCache: function (data) {
            var serverTz = this.service.getMeta('server_tz');
            var useCustomerTz = this.service.getMeta('use_customer_tz');
            // Group session data by date
            for (var utcTimestamp in data) {
                var utc = parseInt(utcTimestamp),
                    localDate = new Date(utc * 1000),
                    serverDate = new Date((utc + serverTz + localDate.getTimezoneOffset()*60) * 1000),
                    date = useCustomerTz? localDate : serverDate;
                // Add session to this date
                this.putSessionInCache(date, utc);
            }

            return this;
        },
    });

})(jQuery, top, document);
