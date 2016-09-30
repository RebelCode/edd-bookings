/* global EddBkSpI18n */

EddBkSessionPicker = (function ($) {
    
    /**
     * Constructor.
     * 
     * @param {EddBkService} service
     * @param {Element} element
     * @returns {EddBkSessionPicker}
     */
    function EddBkSessionPicker(service, element) {
        this.service = service;
        this.l = $(element);
        this.localTimezone = (new Date()).getTimezoneOffset() * 60;
        this.sessions = {};
    }

    /**
     * Prototype.
     */
    EddBkSessionPicker.prototype = {
        // Constructor pointer
        construct: EddBkSessionPicker,
        
        //===== GETTERS =====
        
        /**
         * Gets the service.
         * 
         * @returns {EddBkService}
         */
        getService: function () {
            return this.service;
        },
        /**
         * Gets the local timezone offset, in seconds.
         * 
         * @returns {Number}
         */
        getLocalTimezone: function () {
            return this.localTimezone;
        },
        /**
         * Gets the container element.
         * 
         * @returns {Element}
         */
        getElement: function () {
            return this.l;
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
            var duration = parseInt(this.timepickerDuration.val());
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
            this.timepickerDuration.val(parseInt(duration));
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
        getSelectedSession: function() {
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
        
        //===== SESSIONS CACHE =====
        
        /**
         * Checks if a date has sessions in cache.
         * 
         * @param {Date} date
         * @returns {Boolean}
         */
        hasSessionsInCache: function(date) {
            return Object.keys(this.getSessionsFromCache(date)).length > 0;
        },
        /**
         * Gets a date's sessions from cache.
         * 
         * @param {Date} date
         * @returns {Object}
         */
        getSessionsFromCache: function(date) {
            // Extract vars from date
            var year = date.getFullYear(),
                month = date.getMonth(),
                day = date.getDate();
            return this.resolveSessionsInCache([year, month, day]);
        },
        /**
         * Puts a session in the cache for a single date.
         * 
         * @param {Date} date
         * @param {integer} timestamp
         * @returns {EddBkSessionPicker}
         */
        putSessionInCache: function(date, timestamp) {
            var day = date.getDate(),
                month = date.getMonth(),
                year = date.getFullYear();
            this._deepObjectSet(this.sessions, date, [year, month, day, timestamp]);
            return this;
        },
        
        /**
         * Puts a set of sessions in the cache.
         * 
         * @param {Object} sessions
         * @returns {EddBkSessionPicker}
         */
        putSessionsInCache: function(sessions) {
            $.extend(this.sessions, sessions);
            
            return this;
        },
        
        /**
         * Resolves a path in the sessions cache to obtain an entry or set of entries.
         * 
         * @param {Array} path
         * @returns {EddBkSessionPicker}
         */
        resolveSessionsInCache: function(path) {
            var result = this._safeResolve(this.sessions, path);
            return (result)? result : {};
        },
        
        //===== INITIALIZATION =====
        
        /**
         * Initializes the session picker.
         * 
         * @returns {EddBkSessionPicker}
         */
        init: function () {
            if (this.service.isMetaLoaded()) {
                this._init();
            } else {
                this.service.loadMeta(this._init.bind(this));
            }
            return this;
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
            this.datepickerContainer = this.l.find('.edd-bk-datepicker-container');
            this.datepicker = this.datepickerContainer.find('.edd-bk-datepicker');

            this.sessionOptionsElement = this.l.find('.edd-bk-session-options');
            this.sessionOptionsLoading = this.l.find('.edd-bk-session-options-loading');
            this.timepicker = this.sessionOptionsElement.find('.edd-bk-timepicker');
            this.timepickerDuration = this.sessionOptionsElement.find('.edd-bk-duration');
            this.priceElement = this.l.find('p.edd-bk-price span');

            this.startField = this.l.find('.edd-bk-start-submit');
            this.durationField = this.l.find('.edd-bk-duration-submit');
            this.datepickerAltField = this.l.find('.edd-bk-datepicker-value');
            this.timezoneField = this.l.find('.edd-bk-timezone');

            this.messagesContainer = this.l.find('.edd-bk-msgs');

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
            this.timepickerDuration.bind('change', this.onDurationChanged.bind(this));

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
    
        //===== TIMEPICKER =====
        
        /**
         * Toggles the time picker loading state.
         * 
         * @param {boolean} isLoading
         * @returns {EddBkSessionPicker}
         */
        setTimePickerLoading: function (isLoading) {
            this.sessionOptionsElement.toggle(!isLoading);
            this.sessionOptionsLoading.toggle(isLoading);
            
            return this;
        },
        /**
         * Shows the timepicker.
         * 
         * @param {type} sessions
         * @returns {EddBkSessionPicker}
         */
        showTimePicker: function (sessions) {
            this.trigger('before_show_timepicker');
            
            this.sessionOptionsElement.find('.edd-bk-if-time-unit').hide();
            this.setTimePickerLoading(true);
            // If unit is a time unit
            var unit = this.service.getMeta('session_unit');
            if (['hours', 'minutes', 'seconds'].indexOf(unit) !== -1) {
                this.timepicker.empty();
                this.updateTimeSelectorSessions(sessions);
                this.sessionOptionsElement.find('.edd-bk-if-time-unit').show();
            }
            this.setTimePickerLoading(false);
            
            this.trigger('after_show_timepicker');
            return this;
        },
        /**
         * Updates the timepicker - used whenever the timepicker is shown after a date selection.
         * 
         * @returns {EddBkSessionPicker}
         */
        updateTimePicker: function() {
            this.trigger('before_update_timepicker');
            
            var unit = this.service.getMeta('session_unit');
            if (['hours', 'minutes', 'seconds'].indexOf(unit) !== -1) {
                this.timepicker.trigger('change');
            }
            this.updatePrice();
            
            this.trigger('update_timepicker');
            return this;
        },
        /**
         * Updates the timepicker selector's sessions with a given set of sessions.
         * 
         * @param {Object} sessions
         * @returns {EddBkSessionPicker}
         */
        updateTimeSelectorSessions: function(sessions) {
            for (var timestamp in sessions) {
                var session = sessions[timestamp];
                var hours = ('0' + session.getHours()).substr(-2);
                var mins = ('0' + session.getMinutes()).substr(-2);
                var text = hours + ':' + mins;
                $('<option></option>').val(timestamp).text(text).appendTo(this.timepicker);
            }
            
            return this;
        },
        /**
         * Calculates the maximum number of sessions allowed for the selected time.
         * 
         * @returns {Number}
         */
        calculateMaxSessionsAllowed: function() {
            var sessionLength = parseInt(this.service.getMeta('session_length'));
            var selected = this.timepicker.find('option:selected');
            var current = parseInt(selected.val());
            var maxDurationCalculated = 1;
            var next = selected;
            // Iterate while siblings exist
            while (next.next().length !== 0) {
                // Get next option element
                var next = next.next();
                // Get it's value
                var nextValue = parseInt(next.val());
                // Add a session's length to the current timestamp
                current += sessionLength;
                // Calulcate difference, to see if the two option's timestamps touch
                var diff = current - nextValue;
                if (diff === 0) {
                    maxDurationCalculated++;
                } else {
                    break;
                }
            };
            return maxDurationCalculated;
        },
        /**
         * Triggered whenever the timepicker's selected time has changed.
         * 
         * @param {Event} ev
         * @returns {EddBkSessionPicker}
         */
        onTimePickerChange: function(ev) {
            this.trigger('before_timepicker_change_time_unit');
            
            // Get min and max sessions from meta
            var sessionLengthN = parseInt(this.service.getMeta('session_length_n'));
            var minSessions = parseInt(this.service.getMeta('min_sessions'));
            var maxSessions = parseInt(this.service.getMeta('max_sessions'));
            // Calculate max duration allowed, given the selected time and proceeding available sessions
            var maxDurationCalculated = this.calculateMaxSessionsAllowed();
            // Now we have the max duration, in terms of how many sessions "touch" (without gaps) to the selected
            maxDurationCalculated *= sessionLengthN;
            // Limit it against the min and max sessions allowed, from meta
            var minDuration = minSessions * sessionLengthN;
            var maxDurationMeta = maxSessions * sessionLengthN;
            var maxDuration = Math.min(maxDurationMeta, maxDurationCalculated);
            // Get the duration field and set the "min" and "max" attributes
            this.timepickerDuration.attr({
                min: minDuration,
                max: maxDuration
            });
            // If the value is greater than the max
            if (this.getDuration() > maxDuration) {
                // Set it to the max
                this.setDuration(maxDuration);
            }
            this.timepickerDuration.trigger('change');
            
            this.trigger('after_timepicker_change_time_unit');
            
            return this;
        },
        
        //===== DURATION =====
        
        /**
         * Triggered whenever the duration value has changed.
         * 
         * @param {Event} ev
         * @returns {EddBkSessionPicker}
         */
        onDurationChanged: function(ev) {
            this.trigger('before_duration_changed');
            
            var unit = this.service.getMeta('session_unit');
            if (['days', 'weeks'].indexOf(unit) !== -1) {
                this.onDurationChangedDateUnit(ev);
            } else {
                this.onDurationChangedTimeUnit(ev);
            }
            this.updatePrice();
            
            this.trigger('duration_changed');
            return this;
        },
        /**
         * Triggered when the duration changes for a time-unit service.
         * 
         * @param {Event} ev
         * @returns {EddBkSessionPicker}
         */
        onDurationChangedTimeUnit: function (ev) {
            this.trigger('before_duration_changed_time');
            
            var value = this.getDuration();
            var min = parseInt(this.timepickerDuration.attr('min'));
            var max = parseInt(this.timepickerDuration.attr('max'));
            var clampedValue = Math.max(min, Math.min(max, value));
            this.setDuration(clampedValue);

            this.trigger('duration_changed_time');
            
            return this;
        },
        /**
         * Triggered when the duration changes for a date-unit service.
         * 
         * @param {Event} ev
         * @returns {EddBkSessionPicker}
         */
        onDurationChangedDateUnit: function (ev) {
           this.trigger('before_duration_changed_date');
            
            // @todo this.eddSubmitWrapper.hide();
            
            ev.stopPropagation();
            this.resetMessages();
            var date = this.datepicker.datepicker('getDate');
            var valid = this.validateSelectedDate(date);
            
            /*
             * @todo
            var date = this.datepicker.datepicker('getDate');
            var valid = this.validateSelectedDate(date);
            if (valid) {
                this.eddSubmitWrapper.show();
            }
            */
            
            this.trigger('duration_changed_date', valid, [date]);
            
            return this;
        },
        
        //===== PRICE =====
        
        /**
         * Updates the price field to match the calculated price for the selected session.
         * 
         * @returns {EddBkSessionPicker}
         */
        updatePrice: function () {
            var price = this.getPrice();
            var currency = this.service.getMeta('currency');
            
            price = this.trigger('update_price', price);
            var text = currency + price;
            this.priceElement.html(text);
            
            this.trigger('updated_price', text);
            return this;
        },
        
        //===== MISC =====
        
        /**
         * Updates the timezone field with the local timezone.
         * 
         * @param {Date} date
         * @returns {EddBkSessionPicker}
         */
        updateTimezoneField: function(date) {
            // Calculate and filter
            var timezone = date.getTimezoneOffset() * (-60);
            timezone = this.trigger('update_timezone_field', timezone);
            // Set value of hidden field
            this.timezoneField.val(timezone);
            return this;
        },
        /**
         * Validates the selected session with the server.
         * 
         * @param {Function} callback
         * @returns {EddBkSessionPicker}
         */
        validateSelectedSession: function(callback) {
            var session = this.getSelectedSession();
            this.service.canBook(session.start, session.duration, callback);
            
            return this;
        },
        
        //===== MESSAGES =====
        
        /**
        * Shows the invalid date message.
        * 
        * @param  {Date} date The JS date object for the user's selection.
        */
       showInvalidDateMessage: function (date) {
           var date_date = date.getDate();
           var date_month = date.getMonth();
           var dateStr = date_date + this.numberOrdinalSuffix(date_date) + ' ' +
               this.ucfirst(this.months[date_month]);
           var duration = this.getDuration();
           var sessionsStr = this.pluralize(this.service.getMeta('session_unit'), duration);
           // Update the message
           var message = this.getMessage('invalid-date-msg');
           message.find('.edd-bk-invalid-date').text(dateStr);
           message.find('.edd-bk-invalid-length').text(sessionsStr);
           message.show();
       },

        /**
         * Shows the date fix message.
         * 
         * @param  {Date} date The JS date object that was used instead of the user's selection.
         */
        showDateFixMessage: function (date) {
            var date_date = date.getDate();
            var date_month = date.getMonth();
            var dateStr = date_date + this.numberOrdinalSuffix(date_date) + ' ' +
                this.ucfirst(this.months[date_month]);
            var duration = this.getDuration();
            var sessionsStr = this.pluralize(this.service.getMeta('session_unit'), duration);
            // update the message
            var message = this.getMessage('datefix-msg');
            message.find('.edd-bk-datefix-date').text(dateStr);
            message.find('.edd-bk-datefix-length').text(sessionsStr);
            message.show();
        },
        /**
         * Gets a message by class.
         * 
         * @param {string} msgClass
         * @returns {Element}
         */
        getMessage: function(msgClass) {
            return this.messagesContainer.find('> .edd-bk-msg.' + msgClass);
        },
        /**
         * Shows a messaget by class.
         * 
         * @param {string} msgClass
         * @returns {EddBkSessionPicker}
         */
        showMessage: function(msgClass) {
            this.getMessage(msgClass).show();
            
            return this;
        },
        /**
         * Hides all the messages.
         * 
         * @returns {EddBkSessionPicker}
         */
        resetMessages: function() {
            this.messagesContainer.find('> .edd-bk-msg').hide();
            
            return this;
        },
        /**
         * Triggers an event.
         * 
         * @param {string} handle Event handle.
         * @param {unresolved} result A value that will be passed to event handlers for filtering.
         * @param {Array} params Additional params to pass to handlers.
         * @returns {unresolved} The result, if given, possibly modified.
         */
        trigger: function (handle, result, params) {
            var event = jQuery.Event('eddbk_sp_' + handle);
            event.instance = this;
            event.result = result;
            params = Array.isArray(params) ? params : [];
            params.unshift(result);
            this.l.trigger(event, params);

            // Triger a global event that signals the triggering of a local event
            var globalEvent = jQuery.Event('edd_bk_sp_trigged_event');
            globalEvent.instance = this;
            $(document).trigger(globalEvent, event, result, params);
            
            return (typeof event.result === 'undefined')
                ? result
                : event.result;
        },
        
        on: function(handle, callback) {
            this.l.on('eddbk_sp_' + handle, callback);
            
            return this;
        },
        
        //===== Utils =====
        
        /**
         * Parses a datepicker date string into a Date object.
         * 
         * @param {string} dateStr The date string.
         * @returns {Date} The parsed date.
         */
        parseDatePickerDate: function(dateStr) {
            // Parse given date string
            var dateParts = dateStr.split('/'),
                date = new Date(dateParts[2], parseInt(dateParts[0]) - 1, dateParts[1]);
            
            return this.trigger('parse_datepicker_date', date);
        },
        
        /**
         * Determines the datepicker function (possibly an addon) to use for a specific session unit.
         * 
         * @param {string} unit The unit.
         * @returns {String} The function name, or null if failed to determine.
         */
        determineDatepickerFunction: function (unit) {
            switch (unit) {
                case 'minutes':
                case 'hours':
                    return 'datepicker';
                case 'days':
                case 'weeks':
                    return 'multiDatesPicker';
                default:
                    return null;
            }
        },
        
        /**
         * Gets a value from an object using a path. Fails silently and safely.
         * 
         * @param {Object} object The object.
         * @param {Array} path The path.
         * @returns {unresolved} The resolved value, or null.
         */
        _safeResolve: function(object, path) {
            if (!Array.isArray(path) || path.length === 0) {
                throw "Path is not an array or is empty!";
            }
            var i = path[0];
            if (path.length === 1) {
                return (object[i])? object[i] : null;
            } else {
                if (!object[i]) object[i] = {};
                return this._safeResolve(object[i], path.slice(1));
            }
        },
        
        /**
         * Sets a value to a deep path inside an object.
         * 
         * @param {Object} object The object.
         * @param {unresolved} value The value.
         * @param {Array} path The path.
         * @returns {Object} The object.
         */
        _deepObjectSet: function(object, value, path) {
            if (!Array.isArray(path) || path.length === 0) {
                throw "Path is not an array or is empty!";
            }
            var i = path[0];
            if (path.length === 1) {
                object[i] = value;
            } else {
                if (!object[i]) object[i] = {};
                this._deepObjectSet(object[i], value, path.slice(1));
            }
            return object;
        },
        
        // Weekdays and Months - used for string to index conversions
	weekdays: [
            "Sunday",
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday"
        ],
	months: [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        ],
        
        /**
	 * Returns the ordinal suffix for the given number.
	 * 
	 * @param  {number} n The number
	 * @return {string}   The ordinal suffix
	 */
	numberOrdinalSuffix: function( n ) {
		var u = n % 10;
		switch(u) {
			case 1: return (n === 11)? 'th' : 'st';
			case 2: return (n === 12)? 'th' : 'nd';
			case 3: return (n === 13)? 'th' : 'rd';
		}
		return 'th';
	},
        
        /**
	 * Uppercases the first letter of the string.
	 * 
	 * @param  {string} str The string
	 * @return {string}
	 */
	ucfirst: function( str ) {
		return str.charAt(0).toUpperCase() + str.slice(1);
	},

	/**
	 * Generates a pluralized string using the given string and number.
	 * The resulting is in the form:
	 * n str(s)
	 * 
	 * @param  {string} str The string to optionally pluralize.
	 * @param  {number} n   The number to use to determine if pluralization is requred.
	 * @return {string}     A string in the form: "n str(s)" where "(s)" denotes an option "s" character.
	 */
	pluralize: function( str, n ) {
		var newStr = str.toLowerCase().charAt(str.length - 1) === 's'? str.slice(0, -1) : str;
		if ( n !== 1) newStr += 's';
		return n + ' ' + newStr;
	},

	/**
	 * Returns the date of the week as an integer, from 0-6, for the given day name.
	 * 
	 * @param   {string} str The string for the day of the week.
	 * @return {integer}     An integer, from 0-6 for the day of the week, or -1 if the string is not a weekday.
	 */
	weekdayStrToInt: function( str ) {
		return this.weekdays.indexOf( str.toLowerCase() );
	},

	/**
	 * Returns the month integer, from 0-11, for the given month name.
	 * 
	 * @param   {string} str The string for the month name
	 * @return {integer}     An integer, from 0-11 for the month number, or -1 if the string is not a month name.
	 */
	monthStrToInt: function( str ) {
		return this.months.indexOf( str.toLowerCase() );
	},

	/**
	 * Converts the given string into a boolean.
	 * 
	 * @param   {string} arg The string to convert. Must be either 'true' or 'false'.
	 * @return {boolean}     Returns true if str is 'true', and false otherwise.
	 */
	strToBool: function( arg ) {
		if ( typeof arg === 'boolean' ) return arg;
		return arg.toLowerCase() === 'true' ? true : false;
	}

    };

    return EddBkSessionPicker;

})(jQuery);
