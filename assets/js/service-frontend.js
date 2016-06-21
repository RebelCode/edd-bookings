/* global edd_scripts */

(function ($) {

    var EddBkUtils = {
        
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
		return EddBkUtils.weekdays.indexOf( str.toLowerCase() );
	},

	/**
	 * Returns the month integer, from 0-11, for the given month name.
	 * 
	 * @param   {string} str The string for the month name
	 * @return {integer}     An integer, from 0-11 for the month number, or -1 if the string is not a month name.
	 */
	monthStrToInt: function( str ) {
		return EddBkUtils.months.indexOf( str.toLowerCase() );
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

    window.BookableDownload = function (element, serviceId) {
        this.element = $(element);
        this.serviceId = typeof serviceId === 'undefined'? null : serviceId;
        this.ajaxurl = window.ajaxurl? window.ajaxurl : edd_scripts.ajaxurl;
        this.sessions = {};
        this.meta = {};
        this.localTimezone = (new Date()).getTimezoneOffset() * 60;
        this.initScope();
        this.initElements();
        this.getServiceMeta(function() {
            // Update cost for the first time
            this.updateCost();
            // Init datepicker
            this.initDatepicker();
        }.bind(this));
    };

    /**
     * Initializes element pointers.
     */
    BookableDownload.prototype.initElements = function () {
        this.datepickerContainer = this.element.find('.edd-bk-datepicker-container');
        this.datepickerAltField = this.element.find('.edd-bk-datepicker-value');
        this.datepicker = this.datepickerContainer.find('.edd-bk-datepicker');
        
        this.startField = this.element.find('.edd-bk-start-submit');
        this.durationField = this.element.find('.edd-bk-duration-submit');

        this.sessionOptionsElement = this.element.find('.edd-bk-session-options');
        this.sessionOptionsLoading = this.element.find('.edd-bk-session-options-loading');
        this.timepicker = this.sessionOptionsElement.find('.edd-bk-timepicker');
        this.timepickerDuration = this.sessionOptionsElement.find('.edd-bk-duration');
        this.messagesContainer = this.element.find('.edd-bk-msgs');

        this.eddSubmitWrapper = this.element.parent().find('.edd_purchase_submit_wrapper');
        this.priceElement = this.element.find('p.edd-bk-price span');
        this.timezoneField = this.element.find('.edd-bk-timezone');

        // Hide EDD quantity field
        this.element.parent().find('div.edd_download_quantity_wrapper').hide();
        // Change EDD cart button text
        this.element.parent().find('.edd-add-to-cart-label').text("Purchase");
        // Hide the submit button
        this.eddSubmitWrapper.hide();

        // Bind duration on change event
        this.timepickerDuration.bind('change', function () {
            var val = parseInt(this.timepickerDuration.val());
            var min = parseInt(this.timepickerDuration.attr('min'));
            var max = parseInt(this.timepickerDuration.attr('max'));
            this.timepickerDuration.val(Math.max(min, Math.min(max, val)));
            this.updateCost();
        }.bind(this));
        
        if (this.eddSubmitWrapper.length) {
            var _this = this;
            this.eddAddToCart = this.eddSubmitWrapper.find('.edd-add-to-cart.edd-has-js');
            // Our intercepting callback function
            var cb = function (e) {
                // Get parent form
                var targetForm = $(e.target).parents('form.edd_download_purchase_form');
                // Check if in the same form as the add to cart button/link
                if (targetForm.length === 0 || targetForm.closest('form')[0] !== _this.eddAddToCart.closest('form')[0]) {
                    return;
                }
                _this.onSubmit(e, _this.eddAddToCart, eddBkGlobals.eddHandler.bind(_this.eddAddToCart));
            };
            // Add our click and submit bindings
            this.eddAddToCart.unbind('click').click(cb);
            this.eddAddToCart.closest('form').on('submit', cb);
        }
    };

    /**
     * Initializes the scope and retrieves the ID of this service.
     */
    BookableDownload.prototype.initScope = function () {
        this.serviceId = null;
        if (this.element.parents('div.edd_downloads_list').length > 0) {
            // Look for EDD containers. Case for multiple downloads in one page
            this.eddContainer = this.element.closest('div.edd_download');
            this.serviceId = this.eddContainer.attr('id').substr(this.eddContainer.attr('id').lastIndexOf('_') + 1);
        } else if (this.element.parents('.edd_download_purchase_form').length > 0) {
            // Look for EDD containers. Case for download [purchase_link] shortcode
            this.eddContainer = this.element.closest('.edd_download_purchase_form');
            this.serviceId = this.eddContainer.attr('id').substr(this.eddContainer.attr('id').lastIndexOf('_') + 1);
        }
        if (this.serviceId !== null) {
            var dash = this.serviceId.indexOf('-');
            if (dash !== -1) {
                this.serviceId = this.serviceId.substr(0, dash);
            }
        } else {
            // Look for id in the body tag. Case for a single download page
            var serviceId = parseInt((document.body.className.match(/(?:^|\s)postid-([0-9]+)(?:\s|$)/) || [0, 0])[1]);
            if (!serviceId && !this.serviceId) {
                throw "Failed to initialize scope!";
            }
            this.eddContainer = this.element.closest('article');
        }
    };

    /**
     * Initializes the datepicker.
     */
    BookableDownload.prototype.initDatepicker = function (range) {
        // Check if the range has been given. Default to the session duration
        if (typeof range === 'undefined') {
           range = this.meta.session_length_n;
        }
        // Get the session duration unit
        var unit = this.meta.session_unit.toLowerCase();
        // Check which datepicker function to use, depending on the unit
        var datepickerFunction = this.determineDatepickerFunction(unit);
        // Stop if the datepicker function returned is null
        if (datepickerFunction === null)  {
            return;
        }
        // Set range to days, if the unit is weeks
        if (unit === 'weeks') {
            range *= 7;
        }

        var options = {
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
            onChangeMonthYear: this.OnChangeMonthYear.bind(this)
        };

        // Apply the datepicker function on the HTML datepicker element
        $.fn[datepickerFunction].apply(this.datepicker, [options]);

        // Run for the first time
        var selectedDate = new Date(),
            year = selectedDate.getUTCFullYear(),
            // month incremented becuase jQuery UI datepicker passes a 1-based month index vs JS Date's 0-based date
            month = selectedDate.getUTCMonth() + 1;
        this.OnChangeMonthYear(year, month);
    };

    /**
     * Re-initializes the datepicker.
     */
    BookableDownload.prototype.reInitDatepicker = function () {
        // Get the range
        var range = parseInt(this.timepickerDuration.val());
        // Re-init the datepicker
        this.initDatepicker(range);
        // Simulate user click on the selected date, to refresh the auto selected range
        this.datepicker.data('suppress-click-event', true)
            .find('.ui-datepicker-current-day').first()
            .find('>a').click();
    };

    BookableDownload.prototype.isDateAvailable = function (date) {
        var year = date.getFullYear(),
            month = date.getMonth(),
            dayOfMonth = date.getDate(),
            available = false;
        if (this.sessions && this.sessions[year] && this.sessions[year][month]) {
            // Check session exists
            available = (dayOfMonth in this.sessions[year][month]) &&
                (Object.keys(this.sessions[year][month][dayOfMonth]).length > 0);
        }
        return [available, ''];
    };

    BookableDownload.prototype.onDateSelected = function (dateStr) {
        // If the element has the click-event suppression flag,
        if (this.datepicker.data('suppress-click-event') === true) {
            // Remove it and return
            this.datepicker.data('suppress-click-event', null);
            return;
        }
        this.resetMessages();
        // Hide the purchase button
        this.eddSubmitWrapper.hide();
        // Parse given date string
        var dateParts = dateStr.split('/'),
            date = new Date(dateParts[2], parseInt(dateParts[0]) - 1, dateParts[1]),
            dateValid = this.checkDateForInvalidDatesFix(date);
        if (!dateValid) {
            return;
        }
        var year = date.getFullYear(),
            month = date.getMonth(),
            dayOfMonth = date.getDate();
        // If we have sessions for this date
        if (this.sessions[year] && this.sessions[year][month] && this.sessions[year][month][dayOfMonth]) {
            this.updateSessionOptions(this.sessions[year][month][dayOfMonth]);
            this.timezoneField.val(date.getTimezoneOffset() * -60);
            this.eddSubmitWrapper.show();
        } else {
            this.showMessage('no-times-for-date');
        }
        this.updateDurationLimits();
    };

    BookableDownload.prototype.updateSessionOptions = function (sessions) {
        this.sessionOptionsElement.find('.edd-bk-if-time-unit').hide();
        this.setTimepickerLoading(true);
        switch (this.meta.session_unit) {
            case 'hours':
            case 'minutes':
            case 'seconds':
                setTimeout(function () {
                    this.timepicker.empty();
                    for (var timestamp in sessions) {
                        var session = sessions[timestamp];
                        var hours = ('0' + session.getHours()).substr(-2);
                        var mins = ('0' + session.getMinutes()).substr(-2);
                        var text = hours + ':' + mins;
                        $('<option></option>').val(timestamp).text(text).appendTo(this.timepicker);
                    }
                    this.sessionOptionsElement.find('.edd-bk-if-time-unit').show();
                    this.setTimepickerLoading(false);
                }.bind(this), 200);
                break;
            default:
                this.setTimepickerLoading(false);
                break;
        }
    };

    /**
     * Checks if the given date requires the date fix.
     * 
     * @param  {Date}    date The Date object to check
     * @return {boolean}      True if the date was fixed, false if not.
     */
    BookableDownload.prototype.checkDateForInvalidDatesFix = function (date) {
        var originalDate = new Date(date.getTime());
        var newDate = new Date(date.getTime());
        if (this.meta.session_unit === 'weeks' || this.meta.session_unit === 'days') {
            var newDate = this.invalidDayFix(date);
            if (newDate === null) {
                if (this.determineDatepickerFunction(this.meta.session_unit) === 'multiDatesPicker') {
                    this.datepicker.multiDatesPicker('resetDates');
                }
                this.showInvalidDateMessage(originalDate);
                return false;
            }
            if (originalDate.getTime() !== newDate.getTime()) {
                this.showDateFixMessage(newDate);
            }
            this.datepicker.datepicker('setDate', newDate);
            this.reInitDatepicker();
        }
        return true;
    };

    /**
     * Performs the date fix for the given date.
     * 
     * @param  {Date}      date The date to be fixed.
     * @return {Date|null}       The fixed date, or null if the given date is invalid and cannot
     *                           be selected or fixed.
     */
    BookableDownload.prototype.invalidDayFix = function (date) {
        var days = parseInt(this.timepickerDuration.val());
        if (this.meta.session_unit === 'weeks') {
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
    };

    /**
     * Shows the invalid date message.
     * 
     * @param  {Date} date The JS date object for the user's selection.
     */
    BookableDownload.prototype.showInvalidDateMessage = function (date) {
        var date_date = date.getDate();
        var date_month = date.getMonth();
        var dateStr = date_date + EddBkUtils.numberOrdinalSuffix(date_date) + ' ' +
            EddBkUtils.ucfirst(EddBkUtils.months[date_month]);
        var duration = parseInt(this.timepickerDuration.val());
        var sessionsStr = EddBkUtils.pluralize(this.meta.session_unit, duration);
        // Update the message
        var message = this.getMessage('invalid-date-msg');
        message.find('.edd-bk-invalid-date').text(dateStr);
        message.find('.edd-bk-invalid-length').text(sessionsStr);
        message.show();
    };

    /**
     * Shows the date fix message.
     * 
     * @param  {Date} date The JS date object that was used instead of the user's selection.
     */
    BookableDownload.prototype.showDateFixMessage = function (date) {
        var date_date = date.getDate();
        var date_month = date.getMonth();
        var dateStr = date_date + EddBkUtils.numberOrdinalSuffix(date_date) + ' ' +
            EddBkUtils.ucfirst(EddBkUtils.months[date_month]);
        var duration = parseInt(this.timepickerDuration.val());
        var sessionsStr = EddBkUtils.pluralize(this.meta.session_unit, duration);
        // update the message
        var message = this.getMessage('datefix-msg');
        message.find('.edd-bk-datefix-date').text(dateStr);
        message.find('.edd-bk-datefix-length').text(sessionsStr);
        message.show();
    };
    
    BookableDownload.prototype.updateDurationLimits = function() {
        var onChange = null;
        if (this.meta.session_unit === 'weeks' || this.meta.session_unit === 'days') {
            this.timepickerDuration.unbind('change').on('change', function(e) {
                this.eddSubmitWrapper.hide();
                this.resetMessages();
                var date = this.datepicker.datepicker('getDate');
                var valid = this.checkDateForInvalidDatesFix(date);
                if (valid) {
                    this.eddSubmitWrapper.show();
                }
                e.stopPropagation();
            }.bind(this))
            .on('change', this.updateCost.bind(this));
        } else {
            this.timepicker.unbind('change').on('change', function () {
                var session_length_seconds = parseInt(this.meta.session_length);
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
                    current += session_length_seconds;
                    // Calulcate difference, to see if the two option's timestamps touch
                    var diff = current - nextValue;
                    if (diff === 0) {
                        maxDurationCalculated++;
                    } else {
                        break;
                    }
                };
                // Now we have the max duration, in terms of how many sessions "touch" (without gaps) to the selected
                maxDurationCalculated *= this.meta.session_length_n;
                // Limit it against the max sessions allowed, from meta
                var maxDurationMeta = parseInt(this.meta.max_sessions) * parseInt(this.meta.session_length_n);
                var maxDuration = Math.min(maxDurationMeta, maxDurationCalculated);
                // Get the duration field and set the "max" attribute
                this.timepickerDuration.attr('max', maxDuration);
                // Value entered in the number roller
                var duration = parseInt(this.timepickerDuration.val());
                // If the value is greater than the max
                if (duration > maxDuration) {
                    // Set it to the max
                    this.timepickerDuration.val(maxDuration);
                    // Triger the change event
                    this.timepickerDuration.trigger('change');
                }
            }.bind(this));
        }
    };
    
    /**
     * Function that updates the cost of the booking.
     */
    BookableDownload.prototype.updateCost = function () {
        var numSessions = (parseInt(this.timepickerDuration.val()) || 1) / this.meta.session_length_n;
        var text = parseFloat(this.meta.session_cost) * numSessions;
        this.priceElement.html(this.meta.currency + text);
    };

    BookableDownload.prototype.setDatepickerLoading = function (isLoading) {
        this.element.toggleClass('edd-bk-loading', isLoading);
    };

    BookableDownload.prototype.setTimepickerLoading = function (isLoading) {
        this.sessionOptionsElement.toggle(!isLoading);
        this.sessionOptionsLoading.toggle(isLoading);
    };

    BookableDownload.prototype.OnChangeMonthYear = function (year, month) {
        // BookableDownload.getMonthSessions() expects a 0-based month index, since it uses JS Date object (which
        // is also 0-based). jQuery UI Datepicker gives a 1-based month index.
        this.getMonthSessions(year, month - 1, function () {
            this.datepicker.datepicker('refresh');
            this.setDatepickerLoading(false);
        }.bind(this));
    };

    BookableDownload.prototype.getMonthSessions = function (year, month, callback) {
        if (!this.sessions[year] || !this.sessions[year][month]) {
            // Get sessions from server
            this.setDatepickerLoading(true);
            // Generate the range
            var start = Date.UTC(year, month, 1, 0, 0, 0); // first day of current month
            var end = Date.UTC(year, month + 2, 0, 23, 59, 59); // last day of next month
            var range = [
                Math.floor(start / 1000),
                Math.floor(end / 1000)
            ];
            this.getSessions(range, function (response) {
                // If response indicates success
                if (response.success) {
                    // Prepare empty month session data indexes
                    this.prepareSessionDataIndex(year, month);
                    this.prepareSessionDataIndex(year, month + 1);
                    // Add data to internal sessions object
                    this.addSessionData(response.sessions);
                    // Call the callback
                    if (callback)
                        callback();
                    // Otherwise log error
                } else {
                    console.error('Failed to get month sessions. Server replied with:', response.error);
                }
            }.bind(this));
        }
    };
    
    BookableDownload.prototype.prepareSessionDataIndex = function(year, month, dayOfMonth) {
        // Create entry for the date
        if (year && !this.sessions[year]) {
            this.sessions[year] = {};
        }
        if (month && !this.sessions[year][month]) {
            this.sessions[year][month] = {};
        }
        if (dayOfMonth && !this.sessions[year][month][dayOfMonth]) {
            this.sessions[year][month][dayOfMonth] = {};
        }
    }
    
    BookableDownload.prototype.addSessionData = function (data) {
        // Group session data by date
        for (var utcTimestamp in data) {
            var utc = parseInt(utcTimestamp),
                localDate = new Date(utc * 1000),
                serverDate = new Date((utc + this.meta.server_tz + localDate.getTimezoneOffset()*60) * 1000),
                date = this.meta.use_customer_tz? localDate : serverDate,
                dayOfMonth = date.getDate(),
                month = date.getMonth(),
                year = date.getFullYear();
            // Prepare index
            this.prepareSessionDataIndex(year, month, dayOfMonth);
            // Add session to this date
            this.sessions[year][month][dayOfMonth][utc] = date;
        }
    };

    /**
     * Gets the sessions for a specific range for this service.
     * 
     * @param {array} range The range
     * @param {Function} callback The callback to call when the response is received.
     */
    BookableDownload.prototype.getSessions = function (range, callback) {
        this.ajax(
            'get_sessions',
            {
                range_start: range[0],
                range_end: range[1]
            },
            function (response, status, jqXHR) {
                if (typeof callback !== 'undefined') {
                    callback(response, status, jqXHR);
                }
            }.bind(this)
            );
    };

    /**
     * Gets the service meta data.
     * 
     * @param {Function} callback The function to call when the repsonse is received.
     */
    BookableDownload.prototype.getServiceMeta = function (callback) {
        this.ajax(
            'get_meta',
            {},
            function (response, status, jqXHR) {
                if (response && response.success) {
                    this.meta = response.meta;
                    this.meta.use_customer_tz = this.meta.use_customer_tz === "1";
                    if (typeof callback !== 'undefined') {
                        callback(response, status, jqXHR);
                    }
                } else {
                    this.element.append($('<p><code>'+response.error+'</code></p>'));
                }
            }.bind(this)
        );
    };
    
    BookableDownload.prototype.onSubmit = function (e, $this, callback) {
        e.preventDefault();
        // Disable button, preventing rapid additions to cart during ajax request
        $this.prop('disabled', true);

        // Update spinner
        var $spinner = $this.find('.edd-loading');
        var spinnerWidth = $spinner.width(),
            spinnerHeight = $spinner.height();
        $spinner.css({
            'margin-left': spinnerWidth / -2,
            'margin-top': spinnerHeight / -2
        });
        // Show the spinner
        $this.attr('data-edd-loading', '');

        // Hide the unavailable message
        this.resetMessages();

        var timestamp = null;
        if (this.meta.session_unit === 'days' || this.meta.session_unit === 'weeks') {
            var datepickerAltFieldValue = this.datepickerAltField.val(),
                startDateString = datepickerAltFieldValue.split(',')[0].trim(),
                dateParts = startDateString.split('/'),
                date = new Date(dateParts[2], parseInt(dateParts[0]) - 1, dateParts[1]);
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
        var duration = parseInt(this.timepickerDuration.val() / this.meta.session_length_n) * this.meta.session_length;

        this.startField.val(timestamp);
        this.durationField.val(duration);

        this.validateBooking(timestamp, duration, function (response, status, xhr) {
            if (response && response.success && response.available) {
                // EDD should take it from here ...
                callback(e);
            } else {
                // Hide loading spinners and re-enable button
                $this.removeAttr('data-edd-loading');
                $this.prop('disabled', false);
                // Show message
                this.getMessage('booking-unavailable-msg').show();
            }
        }.bind(this));

    };

    BookableDownload.prototype.getMessage = function(msg) {
        return this.messagesContainer.find('> .edd-bk-msg.' + msg);
    };

    BookableDownload.prototype.showMessage = function(msg) {
        this.getMessage(msg).show();
    };

    BookableDownload.prototype.resetMessages = function() {
        this.messagesContainer.find('> .edd-bk-msg').hide();
    };

    BookableDownload.prototype.validateBooking = function(start, duration, callback) {
        this.ajax('validate_booking', {
            start: start,
            duration: duration
        }, callback);
    };

    /**
     * Generic AJAX function.
     * 
     * @param {object} obj Optional object containing the AJAX params.
     */
    BookableDownload.prototype.ajax = function (request, args, callback) {
        obj = {
            url: this.ajaxurl,
            type: 'POST',
            data: {
                action: 'edd_bk_service_request',
                service_id: this.serviceId,
                request: request,
                args: args
            },
            success: callback,
            dataType: 'json',
            xhrFields: {withCredentials: true}
        };
        $.ajax(obj);
    };

    BookableDownload.prototype.determineDatepickerFunction = function (unit) {
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
    };

    $(document).ready(function () {
        // Get the EDD click handler function
        var eddHandler = $('body').data('events')['click.eddAddToCart'];
        // For more recent jquery versions:
        if (!eddHandler) {
            // Get all click bindings
            var bindings = $._data(document.body, 'events')['click'];
            // Search all bindings for those with the 'eddAddToCart' namespace
            for (var i in bindings) {
                if (bindings[i].namespace === 'eddAddToCart') {
                    eddHandler = bindings[i].handler;
                    break;
                }
            }
        }
        // Set globals
        window.eddBkGlobals = {
            eddHandler: eddHandler
        };
        
        // Initialize the instances
        var instances = {};
        $('.edd-bk-service-container').each(function (i, elem) {
            var instance = new BookableDownload(elem);
            if (instance.id !== null) {
                instances[i] = instance;
            }
        });
        if (Object.keys(instances).length) {
            // Remove the handle
            $('body').unbind('click.eddAddToCart');
        }
        window.eddBkInstances = instances;
    });

})(jQuery);
